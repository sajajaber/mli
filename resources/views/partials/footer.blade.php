@php
    $footer = \App\Models\FooterSetting::current();
@endphp

<footer class="mli-footer">
    <div class="mli-footer-container">

        <div class="mli-footer-main">

            <div class="mli-footer-brand">
                <a href="{{ route('home') }}" class="mli-footer-logo" aria-label="Media Link International">
                    <img src="{{ asset('images/mli-logo.jpeg') }}" alt="Media Link International" class="site-logo">
                </a>

                @if($footer?->tagline)
                    <p class="mli-footer-tagline">
                        {{ $footer->tagline }}
                    </p>
                @endif

                @if($footer?->description)
                    <p class="mli-footer-description">
                        {{ $footer->description }}
                    </p>
                @endif
            </div>

            <div class="mli-footer-contact">
                <span class="mli-footer-label">CONTACT</span>

                @if($footer?->phone_primary)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footer->phone_primary) }}" class="mli-footer-link">
                        {{ $footer->phone_primary }}
                    </a>
                @endif

                @if($footer?->phone_secondary)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footer->phone_secondary) }}" class="mli-footer-link">
                        {{ $footer->phone_secondary }}
                    </a>
                @endif

                @if($footer?->email)
                    <a href="mailto:{{ $footer->email }}" class="mli-footer-link">
                        {{ $footer->email }}
                    </a>
                @endif
            </div>

            <div class="mli-footer-office">
                <span class="mli-footer-label">OUR OFFICE</span>

                @if($footer?->office_address)
                    <address>
                        {!! nl2br(e($footer->office_address)) !!}
                    </address>
                @endif

                @if($footer?->map_url)
                    <a
                        href="{{ $footer->map_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mli-footer-map-link">
                        View on Google Maps
                        <span aria-hidden="true">↗</span>
                    </a>
                @endif
            </div>

            <div class="mli-footer-social">
                <span class="mli-footer-label">FOLLOW US</span>

                <div class="mli-social-links">
                    @if($footer?->facebook_url)
                        <a
                            href="{{ $footer->facebook_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Facebook"
                            class="mli-social-link">
                            <span>f</span>
                        </a>
                    @endif

                    @if($footer?->linkedin_url)
                        <a
                            href="{{ $footer->linkedin_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="LinkedIn"
                            class="mli-social-link">
                            <span>in</span>
                        </a>
                    @endif
                </div>
            </div>

        </div>

        <div class="mli-footer-bottom">

            <p class="mli-footer-copyright">
                © {{ date('Y') }} Media Link International. All rights reserved.
            </p>

            <div class="mli-footer-bottom-links">
                @if($footer?->privacy_policy_url)
                    <a href="{{ $footer->privacy_policy_url }}">
                        Privacy Policy
                    </a>
                @endif

                <a href="#top" class="mli-back-to-top">
                    Back to top ↑
                </a>
            </div>

        </div>

    </div>
</footer>
