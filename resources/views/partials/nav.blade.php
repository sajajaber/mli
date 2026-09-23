<nav x-data="{ scrolled: false, menuOpen: false }" x-init="scrolled = window.scrollY > 30; window.addEventListener('scroll', () => scrolled = window.scrollY > 30, { passive: true })" :class="scrolled ? 'mli-nav mli-nav--scrolled' : 'mli-nav'" aria-label="{{ app()->getLocale() === 'ar' ? 'التنقل الرئيسي' : 'Main navigation' }}">
    <div class="mli-nav__inner">
        <a href="{{ route('home') }}" class="brand-lockup" aria-label="Media Link International">
            <img
                src="{{ Vite::asset('resources/assets/images/mli-logo.jpeg') }}"
                alt="Media Link International"
                class="site-logo">
        </a>

        <div class="mli-nav__center">
            <a href="{{ route('home') }}#content">{{ app()->getLocale() === 'ar' ? 'أعمالنا' : 'Work' }}</a>
            <a href="{{ route('shows.index') }}">{{ app()->getLocale() === 'ar' ? 'البرامج' : 'Shows' }}</a>
            <a href="{{ route('home') }}#news">{{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}</a>
            <a href="{{ route('home') }}#about">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'About' }}</a>
        </div>

        <div class="mli-nav__right">
            <span class="mli-nav__status"><i></i> ON AIR</span>
            <a href="{{ route('lang.switch', ['locale' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}" class="mli-nav__language">{{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}</a>
            <a href="{{ route('home') }}#contact" class="mli-nav__contact">{{ app()->getLocale() === 'ar' ? 'تواصل' : 'Contact' }} ↗</a>
            <button type="button" class="mli-nav__menu" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen.toString()" aria-label="{{ app()->getLocale() === 'ar' ? 'فتح القائمة' : 'Open menu' }}">
                <span></span><span></span>
            </button>
        </div>
    </div>

    <div x-cloak x-show="menuOpen" x-transition class="mli-mobile-menu">
        <a href="{{ route('home') }}#content" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'أعمالنا' : 'Work' }}</a>
        <a href="{{ route('shows.index') }}" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'البرامج' : 'Shows' }}</a>
        <a href="{{ route('home') }}#news" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}</a>
        <a href="{{ route('home') }}#about" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'About' }}</a>
        <a href="{{ route('home') }}#contact" @click="menuOpen = false">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact' }}</a>
        <a href="{{ route('lang.switch', ['locale' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}">{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</a>
    </div>
</nav>