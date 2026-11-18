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
            'address' => ['Address', 'textarea'],
            'social_facebook' => ['Facebook URL', 'text'], 'social_instagram' => ['Instagram URL', 'text'], 'social_youtube' => ['YouTube URL', 'text'],
        ],
        'Enquiries' => [
            'notification_emails' => ['Staff notification emails (comma separated)', 'text'],
            'modal_discount' => ['Header modal discount figure (shown as "save up to X")', 'text'],
        ],
        'Security (reCAPTCHA)' => [
            'recaptcha_site_key' => ['reCAPTCHA v2 site key (blank = use .env value)', 'text'],
            'recaptcha_secret_key' => ['reCAPTCHA v2 secret key (blank = use .env value)', 'password'],
        ],
        'SEO' => [
            'default_meta_description' => ['Default meta description', 'textarea'], 'default_social_image' => ['Default social sharing image', 'image'],
        ],
    ];

    /** The field groups this page edits. Overridden by TrustController. */
    protected function groups(): array
    {
        return self::GROUPS;
    }

    /** Page chrome: title, heading, form action and whether to show the GoHighLevel status box. */
    protected function page(): array
    {
        return ['title' => 'Settings', 'heading' => 'Site settings', 'action' => route('admin.settings.update'), 'ghl' => true];
    }

    public function edit()
    {
        return view('admin.settings', [
            'groups' => $this->groups(), 'values' => Setting::all_(), 'ghlOn' => SyncEnquiryToGhl::enabled(), 'page' => $this->page(),
        ]);
    }

    public function update(Request $request, ImageService $images)
    {
        $rules = [];
        foreach ($this->groups() as $keys) {
            foreach ($keys as $key => [, $type]) {
                $rules[$key] = $type === 'image' ? ['nullable', 'image', 'max:8192'] : ['nullable', 'string', 'max:2000'];
            }
        }
        foreach (array_keys($rules) as $key) {
            if (str_ends_with($key, '_url')) {
                $rules[$key] = ['nullable', 'url:http,https', 'max:500'];
            }
        }
        if (array_key_exists('gtm_id', $rules)) {
            $rules['gtm_id'] = ['nullable', 'regex:/^GTM-[A-Z0-9]+$/'];
        }
        foreach ([
            'clarity_id' => '/^[a-z0-9]{6,20}$/', 'google_ads_id' => '/^AW-[0-9]{6,14}$/',
            'google_ads_conversion_label' => '/^[A-Za-z0-9_-]{6,40}$/', 'meta_pixel_id' => '/^[0-9]{8,20}$/',
        ] as $key => $regex) {
            if (array_key_exists($key, $rules)) {
                $rules[$key] = ['nullable', 'regex:'.$regex];
            }
        }
        if (array_key_exists('email', $rules)) {
            $rules['email'] = ['nullable', 'email', 'max:190'];
        }
        if (array_key_exists('phone_tel', $rules)) {
            $rules['phone_tel'] = ['nullable', 'regex:/^\+?[0-9]{6,16}$/'];
        }
        if (array_key_exists('notification_emails', $rules)) {
        $rules['notification_emails'] = ['nullable', 'string', 'max:1000', function ($attr, $value, $fail) {
            foreach (array_filter(array_map('trim', explode(',', (string) $value))) as $email) {
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $fail("\"$email\" is not a valid email address.");
                }
            }
        }];
        }
        $request->validate($rules, [
            'gtm_id.regex' => 'GTM IDs look like GTM-ABC1234.',
            'clarity_id.regex' => 'The Clarity project ID is lowercase letters and numbers only (6 to 20 characters).',
            'google_ads_id.regex' => 'The Google Ads tag ID looks like AW-123456789.',
            'google_ads_conversion_label.regex' => 'Paste only the label, for example AbC-D_efG (without the AW id and slash).',
            'meta_pixel_id.regex' => 'The Meta Pixel ID is digits only.',
            'phone_tel.regex' => 'Use digits and an optional leading +, with no spaces (for example +18005550100).',
        ]);

        foreach ($this->groups() as $keys) {
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
