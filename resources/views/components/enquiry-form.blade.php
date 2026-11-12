{{--
    THE enquiry form. Every form on the site (hero quiz, modal, package quote, ask a question,
    contact page) renders this component and posts to EnquiryController, so behaviour is identical.

    Props:
      formName  hero-quiz | header-modal | package-quote | ask-question | contact
      package   optional Package model (sends package_id + name)
      extended  show time of year / group size / nights
      slot      extra hidden inputs (the quiz puts its answers here)
--}}
@props(['formName', 'package' => null, 'extended' => false, 'button' => 'Get My Free Quote', 'uid' => null, 'dark' => false, 'nojs' => false])
@php
    $uid ??= $formName;
    $active = old('form_name') === $formName;
    $siteKey = \App\Models\Setting::get('recaptcha_site_key', config('goaustria.recaptcha.site_key'));
    $consentError = $active ? $errors->first('consent') : null;
    $captchaError = $active ? $errors->first('g-recaptcha-response') : null;
@endphp
<form method="POST" action="{{ route('enquiry.store') }}" novalidate
      x-data="enquiryForm({ formName: '{{ $formName }}', siteKey: @js($siteKey) })"
      @submit.prevent="submit($event)"
      class="space-y-4 text-left" data-enquiry-form="{{ $formName }}">
    @csrf
    <input type="hidden" name="form_name" value="{{ $formName }}">
    <input type="hidden" name="page_url" value="{{ url()->full() }}">
    @if($package)
        <input type="hidden" name="package_id" value="{{ $package->id }}">
    @else
        <input type="hidden" name="package_id" value="" x-bind:value="$store.modal.context.package_id ?? ''" data-modal-package>
    @endif

    {{-- Honeypot: humans never see or fill this. --}}
    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
        <label>Leave this field empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
    </div>

    {{ $slot }}

    @if($active && $errors->any() && ! $consentError && ! $captchaError && $errors->count())
        <p class="rounded-lg bg-red-50 border border-red-300 text-red-800 text-sm p-3" role="alert">Please check the highlighted fields and try again.</p>
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <x-form-field name="first_name" label="First name" required :id="$uid" :active="$active" autocomplete="given-name" />
        <x-form-field name="last_name" label="Last name" required :id="$uid" :active="$active" autocomplete="family-name" />
    </div>
    <x-form-field name="email" type="email" label="Email" required :id="$uid" :active="$active" autocomplete="email" />

    <div class="grid grid-cols-[minmax(0,9rem)_1fr] gap-3">
        <x-form-field name="country" type="select" label="Country code" required :id="$uid" :active="$active">
            @foreach(\App\Support\Countries::options() as $iso => [$dial, $label])
                <option value="{{ $iso }}" @selected(($active ? old('country') : null) === $iso || (! $active && $iso === 'US') || ($active && ! old('country') && $iso === 'US'))>{{ $iso }} {{ $dial }}</option>
            @endforeach
        </x-form-field>
        <x-form-field name="phone" type="tel" label="Phone" required :id="$uid" :active="$active" autocomplete="tel-national" />
    </div>

    @if($extended)
        <div class="grid gap-4 sm:grid-cols-3">
            <x-form-field name="season" type="select" label="Time of year" :id="$uid" :active="$active">
                <option value="">Select</option>
                @foreach(['Spring', 'Summer', 'Autumn', 'Winter'] as $s)<option @selected($active && old('season') === $s)>{{ $s }}</option>@endforeach
            </x-form-field>
            <x-form-field name="group_size" type="select" label="Group size" :id="$uid" :active="$active">
                <option value="">Select</option>
                @foreach(range(1, 10) as $n)<option value="{{ $n }}" @selected($active && old('group_size') == $n)>{{ $n }}{{ $n === 10 ? '+' : '' }}</option>@endforeach
            </x-form-field>
            <x-form-field name="nights" type="select" label="Nights" :id="$uid" :active="$active">
                <option value="">Select</option>
                @foreach(range(3, 15) as $n)<option value="{{ $n }}" @selected($active && old('nights') == $n)>{{ $n }}{{ $n === 15 ? '+' : '' }}</option>@endforeach
            </x-form-field>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <x-form-field name="travel_date" label="Preferred travel date (approx.)" placeholder="e.g. June 2027" :id="$uid" :active="$active" />
        <x-form-field name="second_quote" type="select" label="Would you like a second quote from a partner?" :id="$uid" :active="$active">
            <option value="">Select</option>
            <option value="Yes" @selected($active && old('second_quote') === 'Yes')>Yes</option>
            <option value="No" @selected($active && old('second_quote') === 'No')>No</option>
        </x-form-field>
    </div>

    <x-form-field name="special_request" type="textarea" label="Special request" :id="$uid" :active="$active" />

    {{-- Required consent. Unticked by default; wording is fixed (see App\Support\Consent). --}}
    <div>
        <div class="flex items-start gap-3">
            <input type="checkbox" id="{{ $uid }}-consent" name="consent" value="1" required class="mt-1 size-5 shrink-0 rounded border-line accent-brand" @if($consentError) aria-invalid="true" aria-describedby="{{ $uid }}-consent-err" @endif>
            <label for="{{ $uid }}-consent" class="text-xs leading-relaxed {{ $dark ? 'text-white/90' : 'text-muted' }}">{!! \App\Support\Consent::html() !!}</label>
        </div>
        @if($consentError)<p id="{{ $uid }}-consent-err" class="mt-1 text-sm text-red-700" role="alert">{{ $consentError }}</p>@endif
    </div>

    {{-- reCAPTCHA v2 checkbox, rendered explicitly by Alpine so several can share a page. --}}
    <div>
        <div x-ref="captcha" class="min-h-[78px]"></div>
        {{-- No-JS reCAPTCHA fallback. Must not be nested inside another <noscript> (HTML cannot nest them), hence $nojs. --}}
        @if($nojs)
            <iframe src="https://www.google.com/recaptcha/api/fallback?k={{ $siteKey }}" title="reCAPTCHA" class="w-full max-w-[302px] h-[422px] border-0"></iframe>
            <textarea name="g-recaptcha-response" class="w-full max-w-[302px] h-20" aria-label="reCAPTCHA response"></textarea>
        @else
            <noscript>
                <iframe src="https://www.google.com/recaptcha/api/fallback?k={{ $siteKey }}" title="reCAPTCHA" class="w-full max-w-[302px] h-[422px] border-0"></iframe>
                <textarea name="g-recaptcha-response" class="w-full max-w-[302px] h-20" aria-label="reCAPTCHA response"></textarea>
            </noscript>
        @endif
        <p x-show="captchaError" x-cloak class="mt-1 text-sm text-red-700" role="alert">Please tick "I'm not a robot".</p>
        @if($captchaError)<p class="mt-1 text-sm text-red-700" role="alert">{{ $captchaError }}</p>@endif
    </div>

    <x-button type="submit" class="w-full" x-bind:disabled="submitting" x-bind:class="submitting && 'opacity-60'">
        <span x-show="!submitting">{{ $button }}</span>
        <span x-show="submitting" x-cloak>Sending…</span>
    </x-button>
</form>
