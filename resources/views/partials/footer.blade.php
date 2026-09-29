@php
    $footer = \App\Models\FooterSetting::current();

    $tagline = app()->getLocale() === 'ar'
        ? ($footer?->tagline_ar ?: $footer?->tagline)
        : $footer?->tagline;

    $description = app()->getLocale() === 'ar'
        ? ($footer?->description_ar ?: $footer?->description)
        : $footer?->description;

    $officeAddress = app()->getLocale() === 'ar'
        ? ($footer?->office_address_ar ?: $footer?->office_address)
        : $footer?->office_address;
@endphp

<footer id="contact" class="mli-footer">
    <div class="mli-footer-container">

        <div class="mli-footer-top mli-footer-top--rebalanced">
            <div class="mli-footer-intro">
                <div class="mli-footer-brand-block">
                    <a href="{{ route('home') }}" class="mli-footer-brandmark" aria-label="Media Link International">
                        <img src="{{ asset('images/mli-logo.jpeg') }}" alt="Media Link International">
                    </a>

                    <div class="mli-footer-brand-copy">
                        @if($tagline)
                            <h2>{{ $tagline }}</h2>
                        @endif

                        @if($description)
                            <p>{{ $description }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mli-footer-cta">
                <span class="mli-footer-kicker">
                    {{ app()->getLocale() === 'ar' ? 'تعاون مع MLI' : 'Partner with MLI' }}
                </span>

                @if($footer?->email)
                    <a href="mailto:{{ $footer->email }}" class="mli-footer-email">
                        {{ $footer->email }}
                        <span aria-hidden="true">↗</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="mli-footer-grid mli-footer-grid--compact">
            <div class="mli-footer-column">
                <span class="mli-footer-label">
                    {{ app()->getLocale() === 'ar' ? 'تواصل' : 'Contact' }}
                </span>

                @if($footer?->phone_primary)
                    <a dir="ltr" href="tel:{{ preg_replace('/[^0-9+]/', '', $footer->phone_primary) }}" class="mli-footer-text-link mli-footer-phone">
                        {{ $footer->phone_primary }}
                    </a>
                @endif

                @if($footer?->phone_secondary)
                    <a dir="ltr" href="tel:{{ preg_replace('/[^0-9+]/', '', $footer->phone_secondary) }}" class="mli-footer-text-link mli-footer-phone">
                        {{ $footer->phone_secondary }}
                    </a>
                @endif
            </div>

            <div class="mli-footer-column">
                <span class="mli-footer-label">
                    {{ app()->getLocale() === 'ar' ? 'المكتب' : 'Office' }}
                </span>

                @if($officeAddress)
                    <address dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="mli-footer-address">
                        {!! nl2br(e($officeAddress)) !!}
                    </address>
                @endif

                @if($footer?->map_url)
                    <a href="{{ $footer->map_url }}" target="_blank" rel="noopener noreferrer" class="mli-footer-text-link mli-footer-map">
                        {{ app()->getLocale() === 'ar' ? 'عرض الموقع' : 'View location' }}
                        <span aria-hidden="true">↗</span>
                    </a>
                @endif
            </div>

            <div class="mli-footer-column">
                <span class="mli-footer-label">
                    {{ app()->getLocale() === 'ar' ? 'تابعنا' : 'Follow' }}
                </span>

                <div class="mli-footer-social">
                    @if($footer?->facebook_url)
                        <a href="{{ $footer->facebook_url }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">f</a>
                    @endif

                    @if($footer?->linkedin_url)
                        <a href="{{ $footer->linkedin_url }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mli-footer-bottom">
            <span>© {{ date('Y') }} Media Link International</span>

            <div class="mli-footer-bottom__links">
                @if($footer?->privacy_policy_url)
                    <a href="{{ $footer->privacy_policy_url }}">
                        {{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy' }}
                    </a>
                @endif

                <a href="#top" class="mli-footer-back">
                    {{ app()->getLocale() === 'ar' ? 'العودة للأعلى' : 'Back to top' }}
                    <span aria-hidden="true">↑</span>
                </a>
            </div>
        </div>
    </div>
</footer>