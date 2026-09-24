<footer id="contact" class="mli-footer">
    <div class="mli-footer-container">

        <div class="mli-footer-top">
            <div class="mli-footer-brand">
                <a href="{{ route('home') }}" class="mli-footer-brandmark" aria-label="Media Link International">
                    <span>MLI</span>
                    <small>MEDIA LINK<br>INTERNATIONAL</small>
                </a>
            </div>

            <div class="mli-footer-cta">
                <span class="mli-footer-kicker">
                    {{ app()->getLocale() === 'ar' ? 'لنتحدث' : 'Let’s work together' }}
                </span>

                @if($footer?->email)
                    <a href="mailto:{{ $footer->email }}" class="mli-footer-email">
                        {{ $footer->email }}
                        <span aria-hidden="true">↗</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="mli-footer-grid">
            <div class="mli-footer-column mli-footer-column--intro">
                @php
                    $tagline = app()->getLocale() === 'ar'
                        ? ($footer?->tagline_ar ?: $footer?->tagline)
                        : $footer?->tagline;

                    $description = app()->getLocale() === 'ar'
                        ? ($footer?->description_ar ?: $footer?->description)
                        : $footer?->description;
                @endphp

                @if($tagline)
                    <p class="mli-footer-tagline">{{ $tagline }}</p>
                @endif

                @if($description)
                    <p class="mli-footer-description">{{ $description }}</p>
                @endif
            </div>

            <div class="mli-footer-column">
                <span class="mli-footer-label">
                    {{ app()->getLocale() === 'ar' ? 'تواصل' : 'Contact' }}
                </span>

                @if($footer?->phone_primary)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footer->phone_primary) }}" class="mli-footer-text-link">
                        {{ $footer->phone_primary }}
                    </a>
                @endif

                @if($footer?->phone_secondary)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footer->phone_secondary) }}" class="mli-footer-text-link">
                        {{ $footer->phone_secondary }}
                    </a>
                @endif
            </div>

            <div class="mli-footer-column">
                <span class="mli-footer-label">
                    {{ app()->getLocale() === 'ar' ? 'المكتب' : 'Office' }}
                </span>

                @if($footer?->office_address)
                    <address class="mli-footer-address">
                        {!! nl2br(e($footer->office_address)) !!}
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