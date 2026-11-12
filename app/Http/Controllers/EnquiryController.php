<?php

namespace App\Http\Controllers;

use App\Jobs\SyncEnquiryToGhl;
use App\Mail\NewEnquiryMail;
use App\Models\Enquiry;
use App\Models\Package;
use App\Models\QuizOption;
use App\Models\QuizStep;
use App\Models\Setting;
use App\Services\RecaptchaVerifier;
use App\Support\Consent;
use App\Support\Countries;
use App\Support\Tracking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/** Single entry point for EVERY enquiry form on the site. */
class EnquiryController extends Controller
{
    public const FORMS = ['hero-quiz', 'header-modal', 'package-quote', 'ask-question', 'contact'];

    public function store(Request $request, RecaptchaVerifier $recaptcha): RedirectResponse
    {
        // Honeypot: bots fill it. Pretend success, save nothing.
        if (filled($request->input('website'))) {
            return redirect()->route('thank-you');
        }

        $data = $request->validate([
            'form_name' => ['required', Rule::in(self::FORMS)],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'country' => ['required', Rule::in(array_keys(Countries::LIST))],
            'phone' => ['required', 'string', 'max:30'],
            'travel_date' => ['nullable', 'string', 'max:80'],
            'second_quote' => ['nullable', Rule::in(['Yes', 'No'])],
            'special_request' => ['nullable', 'string', 'max:2000'],
            'season' => ['nullable', 'string', 'max:20'],
            'group_size' => ['nullable', 'string', 'max:10'],
            'nights' => ['nullable', 'string', 'max:10'],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'page_url' => ['nullable', 'string', 'max:1000'],
            'quiz_answers' => ['nullable', 'array'],
            'quiz_answers.*' => ['nullable', 'string', 'max:100'],
            'consent' => ['accepted'],
        ], [
            'consent.accepted' => 'Please tick the consent box to continue.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $fail = fn (array $errors) => back()->withInput()->withErrors($errors)->with('error_form', $request->input('form_name'));

        // Server-side reCAPTCHA. Fails closed.
        if (! $recaptcha->passes($request->input('g-recaptcha-response'), $request->ip())) {
            return $fail(['g-recaptcha-response' => 'Please confirm you are not a robot and try again.']);
        }

        // Phone to E.164 using the chosen country.
        $phone = null;
        try {
            $util = PhoneNumberUtil::getInstance();
            $parsed = $util->parse($data['phone'], $data['country']);
            if ($util->isValidNumber($parsed)) {
                $phone = $util->format($parsed, PhoneNumberFormat::E164);
            }
        } catch (NumberParseException) {
        }
        if (! $phone) {
            return $fail(['phone' => 'Please enter a valid phone number for the selected country.']);
        }

        $package = ! empty($data['package_id']) ? Package::find($data['package_id']) : null;
        [$first, $last] = Tracking::fromCookies($request);

        $answers = $this->quizAnswers($data['quiz_answers'] ?? []);
        foreach (['season' => 'Time of year', 'group_size' => 'Group size', 'nights' => 'Nights'] as $field => $label) {
            if (! empty($data[$field]) && ! isset($answers[$field])) {
                $answers[$field] = $data[$field];
            }
        }

        $consentText = Consent::text();

        $enquiry = Enquiry::create([
            'form_name' => $data['form_name'],
            'package_id' => $package?->id,
            'package_name' => $package?->title,
            'first_name' => $this->clean($data['first_name']),
            'last_name' => $this->clean($data['last_name']),
            'email' => mb_strtolower(trim($data['email'])),
            'country_code' => Countries::LIST[$data['country']][0],
            'phone_e164' => $phone,
            'travel_date' => $this->clean($data['travel_date'] ?? null),
            'second_quote' => $data['second_quote'] ?? null,
            'special_request' => $this->clean($data['special_request'] ?? null),
            'quiz_answers' => $answers ?: null,
            // Consent evidence (chargeback / compliance)
            'consent_given' => true,
            'consent_text' => $consentText,
            'terms_version' => config('goaustria.terms_version'),
            'consented_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
            'page_url' => $this->clean($data['page_url'] ?? $request->headers->get('referer'), 1000),
            'tracking_first' => $first ?: null,
            'tracking_last' => $last ?: null,
            'ghl_status' => SyncEnquiryToGhl::enabled() ? 'pending' : 'disabled',
        ]);

        $recipients = array_filter(array_map('trim', explode(',', (string) Setting::get('notification_emails', ''))));
        if ($recipients) {
            Mail::to($recipients)->queue(new NewEnquiryMail($enquiry));
        }

        if (SyncEnquiryToGhl::enabled()) {
            SyncEnquiryToGhl::dispatch($enquiry->id)->afterCommit();
        }

        return redirect()->route('thank-you')->with('enquiry_form', $enquiry->form_name);
    }

    /** Keep only answers for real quiz steps; store the human label when the value matches an option. */
    private function quizAnswers(array $raw): array
    {
        if (! $raw) {
            return [];
        }
        $out = [];
        $steps = QuizStep::with('options')->whereIn('key', array_keys($raw))->get()->keyBy('key');
        foreach ($raw as $key => $value) {
            $step = $steps->get($key);
            if (! $step || $value === null || $value === '') {
                continue;
            }
            $option = $step->options->firstWhere('value', $value);
            $out[$key] = $option?->label ?? $this->clean($value, 100);
        }

        return $out;
    }

    private function clean(?string $value, int $max = 2000): ?string
    {
        $value = trim(strip_tags((string) $value));

        return $value === '' ? null : mb_substr($value, 0, $max);
    }
}
