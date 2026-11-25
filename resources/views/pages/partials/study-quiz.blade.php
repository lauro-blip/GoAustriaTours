{{-- Hero quiz: steps come from Admin > Quiz, then a contact step posting to the real enquiry endpoint (consent + reCAPTCHA, same rules as the main site). --}}
@php
    $failed = old('form_name') === 'hero-quiz';
    $qe = fn ($f) => $failed ? $errors->first($f) : null;
    $siteKey = \App\Models\Setting::get('recaptcha_site_key', config('goaustria.recaptcha.site_key'));
    $nSteps = count($quizSteps);
    $answers = $failed ? (array) old('quiz_answers') : [];
    $promise = trim((string) ($site['response_promise'] ?? ''));
@endphp
<div class="survey" data-quiz data-start="{{ $failed ? $nSteps : 0 }}" data-site-key="{{ $siteKey }}">
    <form method="POST" action="{{ route('enquiry.store') }}" novalidate data-quiz-form>
        @csrf
        <input type="hidden" name="form_name" value="hero-quiz">
        <input type="hidden" name="page_url" value="{{ url()->full() }}">
        <div class="hp" aria-hidden="true"><label>Leave this field empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>


        @foreach($quizSteps as $i => $st)
            <fieldset class="q-step" data-step="{{ $i }}">
                <legend @class(['long' => mb_strlen($st['question']) > 70])>{{ $st['question'] }}</legend>
                @if($st['type'] === 'dropdown')
                    <select name="quiz_answers[{{ $st['key'] }}]" aria-label="{{ $st['question'] }}" data-q-select>
                        <option value="">Select…</option>
                        @foreach($st['options'] as $o)<option value="{{ $o['value'] }}" @selected(($answers[$st['key']] ?? null) === $o['value'])>{{ $o['label'] }}</option>@endforeach
                    </select>
                @else
                    <div class="opts">
                        @foreach($st['options'] as $o)
                            <label @class(['opt', 'txt' => empty($o['icon'])])><input type="radio" name="quiz_answers[{{ $st['key'] }}]" value="{{ $o['value'] }}" @checked(($answers[$st['key']] ?? null) === $o['value'])>
                                @if(! empty($o['icon']))<x-icon :name="$o['icon']" :stroke="0.85" class="qi" />@endif<span>{{ $o['label'] }}</span></label>
                        @endforeach
                    </div>
                @endif
            </fieldset>
        @endforeach

        <fieldset class="q-step q-contact" data-step="{{ $nSteps }}">
            <legend>Where should we send your personal quote?</legend>
            <div class="fields">
                <label>First name<input name="first_name" value="{{ $failed ? old('first_name') : '' }}" required autocomplete="given-name">@if($qe('first_name'))<em class="err">{{ $qe('first_name') }}</em>@endif</label>
                <label>Last name<input name="last_name" value="{{ $failed ? old('last_name') : '' }}" required autocomplete="family-name">@if($qe('last_name'))<em class="err">{{ $qe('last_name') }}</em>@endif</label>
                <label class="wide">Email<input type="email" name="email" value="{{ $failed ? old('email') : '' }}" required autocomplete="email">@if($qe('email'))<em class="err">{{ $qe('email') }}</em>@endif</label>
                <label>Country<select name="country" autocomplete="country">@foreach(\App\Support\Countries::options() as $iso => [$dial, $label])<option value="{{ $iso }}" @selected(($failed ? old('country', 'US') : 'US') === $iso)>{{ $iso }} {{ $dial }}</option>@endforeach</select></label>
                <label>Phone<input type="tel" name="phone" value="{{ $failed ? old('phone') : '' }}" required inputmode="tel" autocomplete="tel" data-phone>@if($qe('phone'))<em class="err">{{ $qe('phone') }}</em>@endif</label>
            </div>
            <label class="consent"><input type="checkbox" name="consent" value="1" required><span>{!! \App\Support\Consent::html() !!}</span></label>
            @if($qe('consent'))<em class="err">{{ $qe('consent') }}</em>@endif
            <div class="captcha" data-q-captcha></div>
            <p class="err" data-q-captcha-err hidden>Please tick “I’m not a robot”.</p>
            @if($qe('g-recaptcha-response'))<em class="err">{{ $qe('g-recaptcha-response') }}</em>@endif
            <button type="submit" class="pill btn q-submit">Request my quote</button>
        </fieldset>

        <button type="button" class="q-back" data-q-back hidden>&larr; Back</button>
    </form>
    <p class="q-note">Takes about a minute · No obligation @if($promise !== '')· {{ $promise }}@endif</p>
    <p class="q-private"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>Your details stay private. We never sell or share them.</p>
</div>
