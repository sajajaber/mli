@props(['shows'])

<section class="relative overflow-hidden bg-navy-950 text-white">
    <div class="mx-auto flex min-h-[90vh] max-w-6xl flex-col items-center justify-center px-6 text-center">
        <h1 class="max-w-3xl text-4xl font-medium leading-tight sm:text-6xl">
            @if(app()->getLocale() === 'ar')
            محتوى عربي أصيل لعالم يشاهد
            @else
            Arabic content, distributed to the world
            @endif
        </h1>

        <p class="mt-6 max-w-xl text-lg text-silver-200">
            @if(app()->getLocale() === 'ar')
            ميديا لينك إنترناشونال — أكثر من 20 عامًا في إنتاج وتوزيع المحتوى العربي عبر الشرق الأوسط وشمال أفريقيا وجنوب شرق آسيا.
            @else
            Media Link International — over 20 years producing and distributing Arabic content across the Middle East, North Africa, and Southeast Asia.
            @endif
        </p>

        <a href="{{ route('shows.index') }}"
            class="mt-10 inline-flex items-center gap-2 rounded-full bg-gold-500 px-8 py-3 font-medium text-navy-950 transition hover:bg-gold-400">
            @if(app()->getLocale() === 'ar')
            استكشف أعمالنا
            @else
            Explore our Shows
            @endif
        </a>
    </div>

    {{-- Film-reel marquee: real show covers, looping horizontally --}}
    <div class="relative border-y border-white/10 bg-navy-800 py-6">
        <div class="absolute inset-x-0 top-0 flex justify-between px-4">
            @for ($i = 0; $i < 40; $i++)
                <span class="h-2 w-2 rounded-full bg-silver-200/20"></span>
                @endfor
        </div>

        <div class="flex w-max reel-track">
            @foreach ($shows->concat($shows) as $show)
            <div class="mx-3 h-32 w-24 flex-shrink-0 overflow-hidden rounded-md border border-white/10 shadow-lg sm:h-40 sm:w-28">
                @if ($show->cover_image_url)
                <img
                    src="{{ $show->cover_image_url }}"
                    alt="{{ $show->cover_image_alt ?? ($locale === 'ar' ? $show->title_ar : $show->title_en) }}"
                    class="h-full w-full object-cover"
                    loading="lazy">
                @else
                <div class="flex h-full w-full items-center justify-center bg-navy-950 text-xs text-silver-200/50">
                    {{ $show->title_en }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="absolute inset-x-0 bottom-0 flex justify-between px-4">
            @for ($i = 0; $i < 40; $i++)
                <span class="h-2 w-2 rounded-full bg-silver-200/20"></span>
                @endfor
        </div>
    </div>
</section>