@props(['shows'])

<section id="content" class="mx-auto max-w-6xl px-6 py-24">
    <div class="mb-12 flex items-end justify-between">
        <h2 class="text-3xl font-medium text-navy-950">
            {{ app()->getLocale() === 'ar' ? 'أعمالنا' : 'Our Content' }}
        </h2>

        <a href="{{ route('shows.index') }}" class="text-sm font-medium text-blue-600 hover:text-navy-950 transition">
            {{ app()->getLocale() === 'ar' ? 'عرض جميع الأعمال ←' : 'Browse all shows →' }}
        </a>
    </div>

    <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ($shows->take(8) as $show)
        <a href="{{ route('shows.index') }}" class="group">
            <div class="aspect-[2/3] overflow-hidden rounded-xl bg-silver-100 shadow-sm">
                @if ($show->cover_image_url)
                <img
                    src="{{ $show->cover_image_url }}"
                    alt="{{ $show->cover_image_alt ?? '' }}"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                    loading="lazy">
                @endif
            </div>
            <h3 class="mt-3 text-sm font-medium text-navy-950">
                {{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}
            </h3>
            @if ($show->category)
            <p class="text-xs text-navy-950/50">
                {{ app()->getLocale() === 'ar' ? $show->category->name_ar : $show->category->name_en }}
            </p>
            @endif
        </a>
        @endforeach
    </div>
</section>