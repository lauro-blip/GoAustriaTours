<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncEnquiryToGhl;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SettingController extends Controller
{
    /** group => [key => [label, type]] */
    public const GROUPS = [
        'Brand' => [
            'brand_name' => ['Brand name', 'text'], 'legal_company_name' => ['Legal company name (used in the consent text and legal pages)', 'text'],
            'site_url' => ['Site URL', 'text'], 'logo' => ['Logo', 'image'], 'favicon' => ['Favicon', 'image'],
            'footer_text' => ['Footer text', 'textarea'],
        ],
        'Contact' => [
            'phone' => ['Phone (as displayed)', 'text'], 'phone_tel' => ['Phone for tap-to-call (digits and +, e.g. +18005550100)', 'text'],
            'email' => ['Public email', 'text'], 'address' => ['Address', 'textarea'],
            'social_facebook' => ['Facebook URL', 'text'], 'social_instagram' => ['Instagram URL', 'text'], 'social_youtube' => ['YouTube URL', 'text'],
        ],
        'Enquiries' => [
            'notification_emails' => ['Staff notification emails (comma separated)', 'text'],
            'modal_discount' => ['Header modal discount figure (shown as "save up to X")', 'text'],
        ],
        'Tracking & security' => [
            'gtm_id' => ['Google Tag Manager ID (GTM-XXXXXXX; leave blank to switch off)', 'text'],
            'recaptcha_site_key' => ['reCAPTCHA v2 site key (blank = use .env value)', 'text'],
            'recaptcha_secret_key' => ['reCAPTCHA v2 secret key (blank = use .env value)', 'password'],
        ],
        'SEO' => [
            'default_meta_description' => ['Default meta description', 'textarea'], 'default_social_image' => ['Default social sharing image', 'image'],
        ],
    ];

    public function edit()
    {
        return view('admin.settings', [
            'groups' => self::GROUPS, 'values' => Setting::all_(), 'ghlOn' => SyncEnquiryToGhl::enabled(),
        ]);
    }

    public function update(Request $request, ImageService $images)
    {
        $rules = [];
        foreach (self::GROUPS as $keys) {
            foreach ($keys as $key => [, $type]) {
                $rules[$key] = $type === 'image' ? ['nullable', 'image', 'max:8192'] : ['nullable', 'string', 'max:2000'];
            }
        }
        $rules['gtm_id'] = ['nullable', 'regex:/^GTM-[A-Z0-9]+$/'];
        $rules['notification_emails'] = ['nullable', 'string', 'max:1000', function ($attr, $value, $fail) {
            foreach (array_filter(array_map('trim', explode(',', (string) $value))) as $email) {
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $fail("\"$email\" is not a valid email address.");
                }
            }
        }];
        $request->validate($rules, ['gtm_id.regex' => 'GTM IDs look like GTM-ABC1234.']);

        foreach (self::GROUPS as $keys) {
            foreach ($keys as $key => [, $type]) {
                if ($type === 'image') {
                    if ($request->boolean($key.'_remove')) {
                        $images->delete(Setting::get($key));
                        Setting::put($key, null);
                    } elseif ($request->hasFile($key)) {
                        try {
                            $images->delete(Setting::get($key));
                            Setting::put($key, $images->store($request->file($key), 'brand')['path']);
                        } catch (InvalidArgumentException $e) {
                            return back()->withErrors([$key => $e->getMessage()]);
                        }
                    }
                } elseif ($type === 'password' && blank($request->input($key))) {
                    continue; // keep the stored secret when the field is left blank
                } else {
                    Setting::put($key, strip_tags((string) $request->input($key)));
                }
            }
        }

        return back()->with('status', 'Settings saved.');
    }
}
