<footer class="site-footer">
    <div class="container site-footer__top">
        <a href="{{ route('home') }}" class="brand brand--footer">
            <span class="brand__mark">MLI</span>
            <span class="brand__name">Media Link<br><small>International</small></span>
        </a>
        <div class="site-footer__links">
            <a href="{{ route('home') }}#content">{{ app()->getLocale() === 'ar' ? 'أعمالنا' : 'Our Work' }}</a>
            <a href="{{ route('shows.index') }}">{{ app()->getLocale() === 'ar' ? 'البرامج' : 'Shows' }}</a>
            <a href="{{ route('home') }}#news">{{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}</a>
            <a href="{{ route('home') }}#about">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'About' }}</a>
        </div>
    </div>
    <div class="container site-footer__bottom">
        <span>&copy; {{ date('Y') }} Media Link International</span>
        <span>{{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة' : 'All rights reserved' }}</span>
    </div>
</footer>