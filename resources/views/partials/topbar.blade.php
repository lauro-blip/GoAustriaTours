<div class="bg-brand-dark text-white text-sm">
    <div class="container-page flex flex-wrap items-center justify-between gap-x-6 gap-y-1 py-2">
        <div class="flex items-center gap-5">
            <a href="mailto:{{ $site['email'] ?? '' }}" class="hidden sm:inline-flex items-center gap-2 hover:underline"><x-icon name="mail" class="size-4" />{{ $site['email'] ?? '' }}</a>
            <a href="tel:{{ $site['phone_tel'] ?? '' }}" class="inline-flex items-center gap-2 font-semibold hover:underline"><x-icon name="phone" class="size-4" />{{ $site['phone'] ?? '' }}</a>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('contact') }}" class="hidden sm:inline hover:underline">Get Instant Quote</a>
            <button type="button" @click="$store.modal.show({ form_name: 'header-modal' })" class="bg-cta hover:bg-cta-dark text-white font-semibold rounded-full px-4 py-1.5">Start Your Adventure</button>
        </div>
    </div>
</div>
