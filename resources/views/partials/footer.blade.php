<footer class="mli-footer">
    <div class="mli-footer-container">
        <div class="mli-footer-main">
            <div class="mli-footer-brand">
                <a href="{{ route('home') }}" class="mli-footer-logo" aria-label="Media Link International">
                    <img
                        src="{{ Vite::asset('resources/assets/images/mli-logo.png') }}"
                        alt="Media Link International">
                </a>

                <p class="mli-footer-tagline">
                    Media Link International
                </p>

                <p class="mli-footer-description">
                    Connecting stories, media and people through meaningful
                    communication and creative experiences.
                </p>
            </div>

            <div class="mli-footer-contact">
                <span class="mli-footer-label">CONTACT</span>

                <a href="tel:+9611395901" class="mli-footer-link">
                    +961 1 395 901
                </a>

                <a href="tel:+9611395952" class="mli-footer-link">
                    +961 1 395 952
                </a>

                <a href="mailto:info@mli-lb.com" class="mli-footer-link">
                    info@mli-lb.com
                </a>
            </div>

            <div class="mli-footer-office">
                <span class="mli-footer-label">OUR OFFICE</span>

                <address>
                    Tayouneh, Omar Bayham St.<br>
                    Al-Nakhil Bldg, Second Floor<br>
                    Beirut, Lebanon
                </address>

                <a
                    href="https://www.google.com/maps/search/?api=1&query=Media+Link+International+Beirut"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mli-footer-map-link">
                    View on Google Maps
                    <span aria-hidden="true">↗</span>
                </a>
            </div>

            <div class="mli-footer-social">
                <span class="mli-footer-label">FOLLOW US</span>

                <div class="mli-social-links">
                    <a
                        href="#"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Facebook"
                        class="mli-social-link">
                        <span>f</span>
                    </a>

                    <a
                        href="#"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="LinkedIn"
                        class="mli-social-link">
                        <span>in</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="mli-footer-bottom">
            <p class="mli-footer-copyright">
                © {{ date('Y') }} Media Link International. All rights reserved.
            </p>

            <div class="mli-footer-bottom-links">
                <a href="#">
                    Privacy Policy
                </a>

                <a href="#top" class="mli-back-to-top">
                    Back to top ↑
                </a>
            </div>
        </div>
    </div>
</footer>