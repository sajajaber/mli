@props(['services'])

<section id="media-services" class="mli-services section section--navy">
    <div class="container">
        <div class="mli-services__heading" data-reveal="up">
            <div>
                <span class="mli-services__index">03 / MEDIA</span>
                <h2>{{ app()->getLocale() === 'ar' ? 'خدماتنا الإعلامية' : 'Media Services' }}</h2>
            </div>
            <p>{{ app()->getLocale() === 'ar' ? 'الخدمات التي يضيفها المسؤول تظهر هنا تلقائيًا.' : 'Services added by the administrator appear here automatically.' }}</p>
        </div>

        <div class="mli-services__grid">
            @forelse($services as $index => $service)
                <article class="mli-service-card" data-reveal="up" style="--reveal-delay: {{ min($index, 3) * 80 }}ms">
                    <span class="mli-service-card__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <h3>{{ app()->getLocale() === 'ar' ? $service->title_ar : $service->title_en }}</h3>
                        <div class="mli-service-card__content">
                            {!! app()->getLocale() === 'ar' ? $service->content_ar : $service->content_en !!}
                        </div>
                    </div>
                    <span class="mli-service-card__arrow">↗</span>
                </article>
            @empty
                <div class="mli-content-empty mli-content-empty--dark">
                    {{ app()->getLocale() === 'ar' ? 'لا توجد خدمات منشورة حاليًا.' : 'No media services published yet.' }}
                </div>
            @endforelse
        </div>
    </div>
</section>
