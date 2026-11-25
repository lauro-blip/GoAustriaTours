{{-- Inline enquiry form for inner pages (contact, package page, tour type page). Posts to the ONE enquiry endpoint (same consent, honeypot,
     reCAPTCHA and server rules as every other form). Vars: $formName, $package (optional Package), $uid, $button, $qfShort, $qfTrip (adds group size + nights). --}}
@php
    $qfName = $formName ?? 'contact';
    $qfUid = $uid ?? $qfName;
    $qfFailed = old('form_name') === $qfName && (($package ?? null) === null || (int) old('package_id') === (int) ($package->id ?? 0));
    $qfErr = fn ($f) => $qfFailed ? $errors->first($f) : null;
    $qfKey = \App\Models\Setting::get('recaptcha_site_key', config('goaustria.recaptcha.site_key'));
    $qfShort = $qfShort ?? false;
@endphp
<form class="qf" method="POST" action="{{ route('enquiry.store') }}" novalidate data-qform data-site-key="{{ $qfKey }}" data-form-name="{{ $qfName }}" id="qf-{{ $qfUid }}">
    @csrf
    <input type="hidden" name="form_name" value="{{ $qfName }}">
    <input type="hidden" name="page_url" value="{{ url()->full() }}">
    @if(($package ?? null))<input type="hidden" name="package_id" value="{{ $package->id }}">@endif
    <div class="hp" aria-hidden="true"><label>Leave this field empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
    <div class="fields">
        <label>First name<input name="first_name" value="{{ $qfFailed ? old('first_name') : '' }}" required autocomplete="given-name">@if($qfErr('first_name'))<em class="err">{{ $qfErr('first_name') }}</em>@endif</label>
        <label>Last name<input name="last_name" value="{{ $qfFailed ? old('last_name') : '' }}" required autocomplete="family-name">@if($qfErr('last_name'))<em class="err">{{ $qfErr('last_name') }}</em>@endif</label>
        <label class="wide">Email<input type="email" name="email" value="{{ $qfFailed ? old('email') : '' }}" required autocomplete="email">@if($qfErr('email'))<em class="err">{{ $qfErr('email') }}</em>@endif</label>
        <label>Country<select name="country" autocomplete="country">@foreach(\App\Support\Countries::options() as $iso => [$dial, $label])<option value="{{ $iso }}" @selected(($qfFailed ? old('country', 'US') : 'US') === $iso)>{{ $iso }} {{ $dial }}</option>@endforeach</select></label>
        <label>Phone<input type="tel" name="phone" value="{{ $qfFailed ? old('phone') : '' }}" required inputmode="tel" autocomplete="tel" data-phone>@if($qfErr('phone'))<em class="err">{{ $qfErr('phone') }}</em>@endif</label>
        @if($qfTrip ?? false)
        {{-- Optional trip details (package page): stored like the popup's group size / nights. Values stay within the 10-char server limit. --}}
        <label>Group size<select name="group_size"><option value="">How many people?</option>@foreach(range(1, 20) as $n)<option value="{{ $n }} {{ $n === 1 ? 'person' : 'people' }}" @selected($qfFailed && old('group_size') === $n.' '.($n === 1 ? 'person' : 'people'))>{{ $n }} {{ $n === 1 ? 'person' : 'people' }}</option>@endforeach<option value="21+ people" @selected($qfFailed && old('group_size') === '21+ people')>21 or more</option></select></label>
        <label>Number of nights<select name="nights"><option value="">How many nights?</option>@foreach(range(1, 20) as $n)<option value="{{ $n }} {{ $n === 1 ? 'night' : 'nights' }}" @selected($qfFailed && old('nights') === $n.' '.($n === 1 ? 'night' : 'nights'))>{{ $n }} {{ $n === 1 ? 'night' : 'nights' }}</option>@endforeach<option value="21+ nights" @selected($qfFailed && old('nights') === '21+ nights')>21 or more</option></select></label>
        @endif
        @unless($qfShort)
        <label class="wide">Preferred travel date<input type="date" name="travel_date" value="{{ $qfFailed ? old('travel_date') : '' }}" min="{{ now()->format('Y-m-d') }}" max="{{ now()->addYears(3)->format('Y-m-d') }}" data-date>@if($qfErr('travel_date'))<em class="err">{{ $qfErr('travel_date') }}</em>@else<small class="hint">An approximate date is fine.</small>@endif</label>
        @endunless
        <label class="wide">{{ $qfShort ? 'Your question' : 'Anything we should know? (optional)' }}<textarea name="special_request" rows="{{ $qfShort ? 3 : 4 }}" @if($qfShort) required @endif>{{ $qfFailed ? old('special_request') : '' }}</textarea></label>
    </div>
    <label class="consent"><input type="checkbox" name="consent" value="1" required><span>{!! \App\Support\Consent::html() !!}</span></label>
    @if($qfErr('consent'))<em class="err">{{ $qfErr('consent') }}</em>@endif
    <div class="captcha" data-qf-captcha></div>
    <p class="err" data-qf-captcha-err hidden>Please tick &ldquo;I&rsquo;m not a robot&rdquo;.</p>
    @if($qfErr('g-recaptcha-response'))<em class="err">{{ $qfErr('g-recaptcha-response') }}</em>@endif
    <button type="submit" class="btn qf-submit">{{ $button ?? 'Request my quote' }} <i aria-hidden="true">&rarr;</i></button>
</form>
