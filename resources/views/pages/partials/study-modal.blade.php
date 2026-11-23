{{-- Quote popup for the design studies: opened by any [data-quote] link (they keep a real href as the no-JS fallback).
     Posts to the ONE enquiry endpoint as form_name "header-modal" (same consent, honeypot, reCAPTCHA and server rules as every other form). --}}
@php
    $mFailed = old('form_name') === 'header-modal';
    $mErr = fn ($f) => $mFailed ? $errors->first($f) : null;
    $mKey = \App\Models\Setting::get('recaptcha_site_key', config('goaustria.recaptcha.site_key'));
    $mPackage = $mFailed ? \App\Models\Package::find(old('package_id')) : null;
@endphp
<dialog class="qm" data-qm data-site-key="{{ $mKey }}" @if($mFailed) data-open @endif aria-labelledby="qm-title">
    <form method="POST" action="{{ route('enquiry.store') }}" novalidate data-qm-form>
        @csrf
        <input type="hidden" name="form_name" value="header-modal">
        <input type="hidden" name="page_url" value="{{ url()->full() }}">
        <input type="hidden" name="package_id" value="{{ $mPackage?->id }}" data-qm-package-id>
        <div class="hp" aria-hidden="true"><label>Leave this field empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <button type="button" class="qm-x" data-qm-close aria-label="Close">&times;</button>
        <span class="qtag">Complimentary &middot; No obligation</span>
        <h2 id="qm-title">Tell us about your trip</h2>
        <p class="qm-for" data-qm-for @if(! $mPackage) hidden @endif>@if($mPackage)Enquiring about: <b>{{ $mPackage->title }}</b>@endif</p>
        <p class="qm-sub">A personal quote from a local Austria expert.</p>
        <div class="fields">
            <label>First name<input name="first_name" value="{{ $mFailed ? old('first_name') : '' }}" required autocomplete="given-name">@if($mErr('first_name'))<em class="err">{{ $mErr('first_name') }}</em>@endif</label>
            <label>Last name<input name="last_name" value="{{ $mFailed ? old('last_name') : '' }}" required autocomplete="family-name">@if($mErr('last_name'))<em class="err">{{ $mErr('last_name') }}</em>@endif</label>
            <label class="wide">Email<input type="email" name="email" value="{{ $mFailed ? old('email') : '' }}" required autocomplete="email">@if($mErr('email'))<em class="err">{{ $mErr('email') }}</em>@endif</label>
            <label>Country<select name="country" autocomplete="country">@foreach(\App\Support\Countries::options() as $iso => [$dial, $label])<option value="{{ $iso }}" @selected(($mFailed ? old('country', 'US') : 'US') === $iso)>{{ $iso }} {{ $dial }}</option>@endforeach</select></label>
            <label>Phone (optional)<input type="tel" name="phone" value="{{ $mFailed ? old('phone') : '' }}" autocomplete="tel-national">@if($mErr('phone'))<em class="err">{{ $mErr('phone') }}</em>@endif</label>
            <label>When would you like to travel?<input name="travel_date" value="{{ $mFailed ? old('travel_date') : '' }}" placeholder="e.g. December 2026"></label>
            <label class="wide">Anything we should know? (optional)<textarea name="special_request" rows="3">{{ $mFailed ? old('special_request') : '' }}</textarea></label>
        </div>
        <label class="consent"><input type="checkbox" name="consent" value="1" required><span>{!! \App\Support\Consent::html() !!}</span></label>
        @if($mErr('consent'))<em class="err">{{ $mErr('consent') }}</em>@endif
        <div class="captcha" data-qm-captcha></div>
        <p class="err" data-qm-captcha-err hidden>Please tick &ldquo;I&rsquo;m not a robot&rdquo;.</p>
        @if($mErr('g-recaptcha-response'))<em class="err">{{ $mErr('g-recaptcha-response') }}</em>@endif
        <button type="submit" class="btn qm-submit">Request my quote <i aria-hidden="true">&rarr;</i></button>
    </form>
</dialog>
