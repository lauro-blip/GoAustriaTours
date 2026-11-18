{{-- Sticky mobile call-to-action bar. Phones and small tablets only (lg:hidden); appears once the visitor has scrolled past the hero,
     so it never covers the hero quiz. Pages with their own bottom bar (package page) or a form-only page set $noStickyBar. --}}
@unless($noStickyBar ?? false)
    @php
        $stickyPhone = trim((string) ($site['phone'] ?? ''));
        $stickyTel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $stickyPhone));
    @endphp
    <div class="h-20 lg:hidden" aria-hidden="true"></div>{{-- spacer so the bar never hides the footer --}}
    <div x-data="{ show: false }" @scroll.window.passive="show = window.scrollY > window.innerHeight * 0.7" x-show="show" x-cloak
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         class="fixed inset-x-0 bottom-0 z-30 border-t border-accent/50 bg-brand-dark/95 px-3 pt-3 shadow-[0_-12px_30px_-12px_rgb(0_0_0/0.5)] pb-[calc(0.75rem+env(safe-area-inset-bottom))] lg:hidden">
        <div class="mx-auto flex max-w-xl items-center gap-3">
            @if($stickyPhone !== '' && $stickyTel !== '')
                <a href="tel:{{ $stickyTel }}" class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-full border border-accent/70 px-5 text-base font-semibold text-white" aria-label="Call us on {{ $stickyPhone }}"><x-icon name="phone" class="size-5 text-accent" />Call</a>
            @endif
            <button type="button" @click="$store.modal.show({ form_name: 'header-modal' })" class="inline-flex h-12 flex-1 items-center justify-center rounded-full bg-cta px-6 text-base font-semibold text-brand transition hover:bg-cta-dark">Plan My Journey</button>
        </div>
    </div>
@endunless
