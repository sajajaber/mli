@props(['content', 'stats'])

<section id="about" class="section section--navy mli-about">
    <div class="container mli-about__inner">
        <div class="mli-about__copy" data-reveal="left">
            <div class="mli-about__kicker">
                <span>{{ app()->getLocale() === 'ar' ? 'منذ عام ٢٠٠٥' : 'Since 2005' }}</span>
                <i aria-hidden="true"></i>
            </div>

            <h2 class="mli-about__title">
                @if(app()->getLocale() === 'ar')
                    بُنيت لتناسب اتساع<br>التلفزيون العربي
                @else
                    Built for the reach of<br>Arabic television
                @endif
            </h2>

            <div class="mli-about__description">
                @if(app()->getLocale() === 'ar')
                    <p>على مدى أكثر من عشرين عامًا، عملت ميديا لينك إنترناشونال على اقتناء الأعمال العربية ودبلجتها وترجمتها وتوزيعها على محطات ومنصات خارج المنطقة، حاملةً القصص من استوديو في بيروت إلى شاشات في ثلاث قارات.</p>
                @else
                    <p>For over twenty years, MLI has acquired, dubbed, subtitled and placed Arabic-language titles with broadcasters and platforms well beyond the region — carrying scripts, casts and stories from a Beirut studio onto screens across three continents.</p>
                @endif
            </div>

            <div class="mli-about__stats">
                <div class="mli-about__stat">
                    <strong>20<span>+</span></strong>
                    <small>{{ app()->getLocale() === 'ar' ? 'سنوات من الخبرة' : 'Years of experience' }}</small>
                </div>
                <div class="mli-about__stat">
                    <strong>{{ $stats['shows'] }}<span>+</span></strong>
                    <small>{{ app()->getLocale() === 'ar' ? 'عمل موزع' : 'Titles distributed' }}</small>
                </div>
                <div class="mli-about__stat">
                    <strong>{{ $stats['clients'] }}<span>+</span></strong>
                    <small>{{ app()->getLocale() === 'ar' ? 'عملاء وشركاء' : 'Clients & partners' }}</small>
                </div>
                <div class="mli-about__stat">
                    <strong>3</strong>
                    <small>{{ app()->getLocale() === 'ar' ? 'مناطق التوزيع' : 'Distribution regions' }}</small>
                </div>
            </div>

            <div class="mli-about__scroll" aria-hidden="true">
                <span>SCROLL</span>
                <b>↓</b>
            </div>
        </div>

        <div class="mli-about__map-wrap" data-reveal="right" aria-hidden="true">
            <div class="mli-about__map-glow"></div>
            <svg class="mli-about__map" viewBox="0 0 520 440" role="img">
                <defs>
                    <radialGradient id="mliMapGlow" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#9ddfd3" stop-opacity=".22"/>
                        <stop offset="100%" stop-color="#9ddfd3" stop-opacity="0"/>
                    </radialGradient>
                    <filter id="mliMapBlur">
                        <feGaussianBlur stdDeviation="7"/>
                    </filter>
                </defs>

                <g class="mli-about__world">
                    <path d="M73 112l22-27 41 5 24 27 35 2 28 25-11 28-34 6-18 31-35-8-8-28-28-18-16-23z"/>
                    <path d="M205 78l42-21 55 8 22 29-20 18-28-3-25 19-27-13-19-18z"/>
                    <path d="M279 139l39-16 34 18 12 34-18 24-11 35-27 2-16-30-29-13-4-29z"/>
                    <path d="M348 101l53-10 55 27 22 34-31 17-28-12-34 5-25-26z"/>
                    <path d="M349 222l44 8 25 30-8 47-34 26-33-19-9-39 15-26z"/>
                    <path d="M222 235l35 9 19 32-16 34-28 15-17-27 8-29-16-21z"/>
                </g>

                <g class="mli-about__routes">
                    <path d="M267 228 C235 160 167 116 101 99"/>
                    <path d="M267 228 C323 159 384 124 449 112"/>
                    <path d="M267 228 C318 279 374 320 432 347"/>
                </g>

                <g class="mli-about__route-dashes">
                    <path d="M267 228 C220 181 163 145 112 121"/>
                    <path d="M267 228 C320 185 376 152 432 135"/>
                    <path d="M267 228 C315 269 366 302 417 329"/>
                </g>

                <circle cx="267" cy="228" r="70" fill="url(#mliMapGlow)" filter="url(#mliMapBlur)"/>
                <circle class="mli-about__hub-ring" cx="267" cy="228" r="31"/>
                <circle class="mli-about__hub-ring mli-about__hub-ring--inner" cx="267" cy="228" r="17"/>
                <circle class="mli-about__hub" cx="267" cy="228" r="9"/>

                <g class="mli-about__nodes">
                    <circle cx="101" cy="99" r="5"/>
                    <circle cx="449" cy="112" r="5"/>
                    <circle cx="432" cy="347" r="5"/>
                </g>

                <g class="mli-about__labels">
                    <text x="101" y="77" text-anchor="middle">North Africa</text>
                    <text x="101" y="94" text-anchor="middle" class="arabic">شمال أفريقيا</text>

                    <text x="449" y="90" text-anchor="middle">Southeast Asia</text>
                    <text x="449" y="107" text-anchor="middle" class="arabic">جنوب شرق آسيا</text>

                    <text x="432" y="378" text-anchor="middle">GCC &amp; Levant</text>
                    <text x="432" y="397" text-anchor="middle" class="arabic">الخليج والمشرق</text>

                    <text x="267" y="266" text-anchor="middle" class="hub-label">Beirut</text>
                </g>
            </svg>

            <div class="mli-about__map-caption">
                <span>{{ app()->getLocale() === 'ar' ? 'من بيروت إلى العالم' : 'FROM BEIRUT / TO THE WORLD' }}</span>
                <i aria-hidden="true"></i>
            </div>
        </div>
    </div>
</section>