<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncEnquiryToGhl;
use App\Jobs\SyncEnquiryToSheet;
use App\Models\Enquiry;
use App\Models\Setting;
use App\Support\SheetRow;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public const RANGES = [7, 30, 90];

    public function __invoke(Request $request)
    {
        $range = in_array((int) $request->query('range'), self::RANGES, true) ? (int) $request->query('range') : 30;
        $from = now()->subDays($range - 1)->startOfDay();
        $prevFrom = $from->copy()->subDays($range);

        $cols = ['id', 'form_name', 'package_name', 'status', 'tracking_first', 'tracking_last', 'created_at', 'phone_e164'];
        $cur = Enquiry::where('created_at', '>=', $from)->get($cols);
        $prev = Enquiry::where('created_at', '>=', $prevFrom)->where('created_at', '<', $from)->get(['id', 'status', 'tracking_first', 'tracking_last']);

        $source = fn (Enquiry $e) => self::sourceLabel($e);
        $paid = fn ($e) => filled(($e->tracking_last['gclid'] ?? null) ?: ($e->tracking_first['gclid'] ?? null)) || strtolower((string) (($e->tracking_last['utm_medium'] ?? null) ?: ($e->tracking_first['utm_medium'] ?? ''))) === 'cpc';

        // Enquiries per day, every day of the range present.
        $perDay = $cur->groupBy(fn ($e) => $e->created_at->format('Y-m-d'))->map->count();
        $days = collect(range(0, $range - 1))->mapWithKeys(function ($i) use ($from, $perDay) {
            $d = $from->copy()->addDays($i);

            return [$d->format('Y-m-d') => ['label' => $d->format('D j M'), 'count' => (int) ($perDay[$d->format('Y-m-d')] ?? 0)]];
        });

        $top = fn (Collection $items, int $n = 6) => $items->filter()->countBy()->sortDesc()->take($n);

        $won = $cur->where('status', 'won')->count();
        $statuses = collect(Enquiry::STATUSES)->mapWithKeys(fn ($s) => [$s => $cur->where('status', $s)->count()]);

        $keysAreTest = ($key = Setting::get('recaptcha_site_key', config('goaustria.recaptcha.site_key'))) === '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI' || blank($key);
        $placeholders = collect(Setting::all_())->filter(fn ($v) => is_string($v) && str_contains($v, '[CONFIRM'))->count();
        $phone = trim((string) Setting::get('phone', ''));

        return view('admin.dashboard', [
            'range' => $range,
            'ranges' => self::RANGES,
            'total' => $cur->count(),
            'prevTotal' => $prev->count(),
            'newCount' => Enquiry::where('status', 'new')->count(),
            'stale' => Enquiry::where('status', 'new')->where('created_at', '<', now()->subDay())->count(),
            'paidCount' => $cur->filter($paid)->count(),
            'prevPaid' => $prev->filter($paid)->count(),
            'won' => $won,
            'winRate' => $cur->count() ? round($won / $cur->count() * 100) : 0,
            'days' => $days,
            'maxDay' => max(1, $days->max('count')),
            'byForm' => $cur->countBy(fn ($e) => SheetRow::TABS[$e->form_name] ?? $e->form_name)->sortDesc(),
            'bySource' => $top($cur->map($source)),
            'byCampaign' => $top($cur->map(fn ($e) => ($e->tracking_last['utm_campaign'] ?? null) ?: ($e->tracking_first['utm_campaign'] ?? null))),
            'byPackage' => $top($cur->pluck('package_name')),
            'statuses' => $statuses,
            'failedSync' => Enquiry::where(fn ($q) => $q->where('ghl_status', 'failed')->orWhere('sheet_status', 'failed'))->count(),
            'checks' => [
                ['ok' => ! $keysAreTest, 'label' => 'Real reCAPTCHA keys', 'hint' => 'Google\'s public test keys are in use, so spam protection is off.', 'route' => 'admin.trust.edit'],
                ['ok' => SyncEnquiryToGhl::enabled(), 'label' => 'GoHighLevel connected', 'hint' => 'Enquiries are not being sent to your CRM.', 'route' => 'admin.ghl.edit'],
                ['ok' => SyncEnquiryToSheet::enabled(), 'label' => 'Google Sheet log connected', 'hint' => 'Enquiries are not being added to the sheet.', 'route' => 'admin.sheets.edit'],
                ['ok' => $phone !== '' && ! str_contains($phone, '[CONFIRM'), 'label' => 'Phone number set', 'hint' => 'The call buttons and the mobile phone icon stay hidden.', 'route' => 'admin.trust.edit'],
                ['ok' => filled(Setting::get('gtm_id')), 'label' => 'Google Tag Manager ID set', 'hint' => 'Conversion events (enquiry_submit, enquiry_thank_you) are not being sent.', 'route' => 'admin.trust.edit'],
                ['ok' => filled(Setting::get('notification_emails')), 'label' => 'Notification email set', 'hint' => 'Nobody is emailed when an enquiry arrives.', 'route' => 'admin.settings.edit'],
                ['ok' => $placeholders === 0, 'label' => 'No [CONFIRM] placeholders in settings', 'hint' => $placeholders.' setting(s) still hold a placeholder (hidden on the public site).', 'route' => 'admin.settings.edit'],
            ],
            'recent' => Enquiry::latest('id')->take(8)->get(),
        ]);
    }

    /** "google / cpc", "facebook", "Google (click id)", the referring site, or "direct": latest touch first. */
    public static function sourceLabel(Enquiry $e): string
    {
        $first = array_filter($e->tracking_first ?? []);
        $last = array_filter($e->tracking_last ?? []);
        $get = fn ($k) => ($last[$k] ?? null) ?: ($first[$k] ?? null);

        if ($src = $get('utm_source')) {
            return strtolower($src).(($m = $get('utm_medium')) ? ' / '.strtolower($m) : '');
        }
        if ($get('gclid') || $get('gbraid') || $get('wbraid')) {
            return 'google / cpc';
        }
        if ($ref = $get('referrer')) {
            return preg_replace('/^www\./', '', (string) parse_url($ref, PHP_URL_HOST) ?: $ref);
        }

        return 'direct';
    }
}
