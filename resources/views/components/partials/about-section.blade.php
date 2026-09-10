@props(['page'])

<section id="about" class="bg-navy-950 py-24 text-white">
    <div class="mx-auto grid max-w-6xl gap-16 px-6 lg:grid-cols-2">
        <div>
            <h2 class="text-3xl font-medium">
                {{ app()->getLocale() === 'ar' ? ($page->title_ar ?? 'من نحن') : ($page->title_en ?? 'About Us') }}
            </h2>
            <div class="prose prose-invert mt-6 max-w-none text-silver-200">
                {!! app()->getLocale() === 'ar' ? ($page->content_ar ?? '') : ($page->content_en ?? '') !!}
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 self-center">
            <div>
                <p class="text-4xl font-medium text-gold-500">20+</p>
                <p class="mt-1 text-sm text-silver-200">
                    {{ app()->getLocale() === 'ar' ? 'سنوات من الخبرة' : 'Years of experience' }}
                </p>
            </div>
            <div>
                <p class="text-4xl font-medium text-gold-500">{{ \App\Models\Show::published()->count() }}+</p>
                <p class="mt-1 text-sm text-silver-200">
                    {{ app()->getLocale() === 'ar' ? 'عمل موزع' : 'Titles distributed' }}
                </p>
            </div>
            <div>
                <p class="text-4xl font-medium text-gold-500">{{ \App\Models\Client::count() }}+</p>
                <p class="mt-1 text-sm text-silver-200">
                    {{ app()->getLocale() === 'ar' ? 'عملاء وشركاء' : 'Clients & partners' }}
                </p>
            </div>
            <div>
                <p class="text-4xl font-medium text-gold-500">3</p>
                <p class="mt-1 text-sm text-silver-200">
                    {{ app()->getLocale() === 'ar' ? 'مناطق التوزيع' : 'Distribution regions' }}
                </p>
            </div>
        </div>
    </div>
</section>