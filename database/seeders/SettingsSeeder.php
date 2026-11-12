<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'brand_name' => 'GoAustriaTours',
            'legal_company_name' => 'GoAustriaTours',
            'site_url' => 'https://goaustriatours.com',
            'phone' => '[CONFIRM: phone]',
            'phone_tel' => '',
            'email' => '[CONFIRM: email]',
            'address' => '[CONFIRM: address]',
            'social_facebook' => '',
            'social_instagram' => '',
            'social_youtube' => '',
            'notification_emails' => '',
            'gtm_id' => '',
            'recaptcha_site_key' => '',
            'recaptcha_secret_key' => '',
            'footer_text' => 'Tailor-made Austria tours for travellers from the United States.',
            'default_meta_description' => 'Self drive, escorted and coach tours of Austria, planned by local experts. Get a free, no-obligation quote.',
            'default_social_image' => '',
            'logo' => '',
            'favicon' => '',
            'modal_discount' => '[CONFIRM: discount %]',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
