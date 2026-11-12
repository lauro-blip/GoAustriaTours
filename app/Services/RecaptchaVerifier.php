<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Throwable;

/** Server-side reCAPTCHA v2 verification. Fails CLOSED on any problem, including Google being unreachable. */
class RecaptchaVerifier
{
    public function passes(?string $response, ?string $ip = null): bool
    {
        if (blank($response)) {
            return false;
        }

        $secret = Setting::get('recaptcha_secret_key', config('goaustria.recaptcha.secret_key'));
        if (blank($secret)) {
            return false;
        }

        try {
            $result = Http::asForm()->timeout(8)->post(config('goaustria.recaptcha.verify_url'), [
                'secret' => $secret,
                'response' => $response,
                'remoteip' => $ip,
            ]);

            return $result->successful() && $result->json('success') === true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
