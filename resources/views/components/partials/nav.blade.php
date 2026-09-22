<nav
    x-data="{ scrolled: false, menuOpen: false }"
    x-init="scrolled = window.scrollY > 24; window.addEventListener('scroll', () => scrolled = window.scrollY > 24, { passive: true })"
    :class="scrolled ? 'site-nav site-nav--scrolled' : 'site-nav'"
    class="site-nav"
    aria-label="{{ app()->getLocale() === 'ar' ? 'التنقل الرئيسي' : 'Main navigation' }}"
>
    <div class="site-nav__inner">
        <a href="{{ route('home') }}" class="brand" aria-label="Media Link International">
            <span class="brand__mark">MLI</span>
            <span class="brand__name">Media Link<br><small>International</small></span>
        </a>

        <div class="site-nav__links">
            <a href="{{ route('home') }}#content" class="nav-link">
                {{ app()->getLocale() === 'ar' ? 'أعمالنا' : 'Our Work' }}
            </a>
            <a href="{{ route('shows.index') }}" class="nav-link">
                {{ app()->getLocale() === 'ar' ? 'البرامج' : 'Shows' }}
            </a>
            <a href="{{ route('home') }}#news" class="nav-link">
                {{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}
            </a>
            <a href="{{ route('home') }}#about" class="nav-link">
                {{ app()->getLocale() === 'ar' ? 'من نحن' : 'About' }}
            </a>
        </div>

        <div class="site-nav__actions">
            <a href="{{ route('lang.switch', ['locale' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}" class="language-switch">
                {{ app()->getLocale() === 'ar' ? 'EN' : 'العربية' }}
            </a>
            <a href="{{ route('home') }}#contact" class="nav-cta">
                {{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact' }}
            </a>
            <button type="button" class="menu-toggle" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen.toString()" aria-label="{{ app()->getLocale() === 'ar' ? 'فتح القائمة' : 'Open menu' }}">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <div x-cloak x-show="menuOpen" x-transition class="mobile-menu">
        <a href="{{ route('home') }}#content" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'أعمالنا' : 'Our Work' }}</a>
        <a href="{{ route('shows.index') }}" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'البرامج' : 'Shows' }}</a>
        <a href="{{ route('home') }}#news" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}</a>
        <a href="{{ route('home') }}#about" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'About' }}</a>
        <a href="{{ route('home') }}#contact" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact' }}</a>
        <a href="{{ route('lang.switch', ['locale' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}">{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</a>
    </div>
</nav>