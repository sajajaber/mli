<x-layouts.public
    :title="(app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact MLI') . ' — Media Link International'"
    :meta-description="app()->getLocale() === 'ar' ? 'تواصل مع Media Link International للاستفسار عن المحتوى والتوزيع والإنتاج.' : 'Contact Media Link International about content, licensing, distribution, and production.'">

    <section class="min-h-[70vh] bg-white px-4 pb-20 pt-32 text-[#2f4156] sm:px-6">
        <div class="mx-auto w-full max-w-3xl">
            <a href="{{ $show ? route('shows.show', ['slug' => $show->slug]) : route('shows.index') }}"
                class="mb-8 inline-flex items-center gap-2 text-sm text-[#567c8d] transition hover:text-[#2f4156]">
                <span aria-hidden="true">{{ app()->getLocale() === 'ar' ? '→' : '←' }}</span>
                {{ app()->getLocale() === 'ar' ? 'العودة' : 'Back' }}
            </a>

            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-[#c5a46d]">
                {{ app()->getLocale() === 'ar' ? 'ابقَ على تواصل' : 'GET IN TOUCH' }}
            </p>

            <h1 class="text-4xl font-medium tracking-tight sm:text-5xl">
                {{ app()->getLocale() === 'ar' ? 'كيف يمكننا مساعدتك؟' : 'How can we help?' }}
            </h1>

            <p class="mt-4 max-w-2xl text-sm leading-7 text-[#567c8d] sm:text-base">
                {{ app()->getLocale() === 'ar'
                    ? 'أرسل استفسارك إلى فريق MLI وسنتواصل معك.'
                    : 'Send your inquiry to the MLI team and we’ll be in touch.' }}
            </p>

            @if($show)
                <div class="mt-7 flex items-start gap-3 border-s-2 border-[#c5a46d] bg-[#f5f2eb] px-4 py-4">
                    <span class="mt-0.5 text-[#c5a46d]" aria-hidden="true">✉</span>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-[#567c8d]">
                            {{ app()->getLocale() === 'ar' ? 'استفسار حول البرنامج' : 'INQUIRY ABOUT' }}
                        </p>
                        <p class="mt-1 font-medium">
                            {{ app()->getLocale() === 'ar' ? $show->title_ar : $show->title_en }}
                        </p>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div role="status" class="mt-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->has('form'))
                <div role="alert" class="mt-6 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first('form') }}
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <input type="hidden" name="show_slug" value="{{ old('show_slug', $show?->slug) }}">

                <div aria-hidden="true" class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden">
                    <label for="website">Leave this field empty</label>
                    <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium">
                            {{ app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full name' }} *
                        </label>
                        <input id="name" name="name" type="text" required maxlength="120" autocomplete="name"
                            value="{{ old('name') }}"
                            class="w-full border border-[#c8d9e6] bg-white px-4 py-3 text-sm outline-none transition placeholder:text-[#8a9aa8] focus:border-[#c5a46d]"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'اسمك' : 'Your name' }}">
                        @error('name') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium">
                            {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Business email' }} *
                        </label>
                        <input id="email" name="email" type="email" required maxlength="254" autocomplete="email"
                            value="{{ old('email') }}"
                            class="w-full border border-[#c8d9e6] bg-white px-4 py-3 text-sm outline-none transition placeholder:text-[#8a9aa8] focus:border-[#c5a46d]"
                            placeholder="you@company.com">
                        @error('email') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="company" class="mb-2 block text-sm font-medium">
                            {{ app()->getLocale() === 'ar' ? 'الشركة (اختياري)' : 'Company (optional)' }}
                        </label>
                        <input id="company" name="company" type="text" maxlength="160" autocomplete="organization"
                            value="{{ old('company') }}"
                            class="w-full border border-[#c8d9e6] bg-white px-4 py-3 text-sm outline-none transition placeholder:text-[#8a9aa8] focus:border-[#c5a46d]"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'اسم الشركة' : 'Your company' }}">
                        @error('company') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="inquiry_type" class="mb-2 block text-sm font-medium">
                            {{ app()->getLocale() === 'ar' ? 'نوع الاستفسار' : 'Inquiry type' }} *
                        </label>
                        <select id="inquiry_type" name="inquiry_type" required
                            class="w-full border border-[#c8d9e6] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#c5a46d]">
                            @foreach([
                                'General Inquiry' => 'General Inquiry',
                                'Content Licensing & Distribution' => 'Content Licensing & Distribution',
                                'Production & Collaboration' => 'Production & Collaboration',
                                'Media Services' => 'Media Services',
                                'Press & Media' => 'Press & Media',
                                'Careers' => 'Careers',
                                'Other' => 'Other',
                            ] as $value => $label)
                                <option value="{{ $value }}" @selected(old('inquiry_type', $show ? 'Content Licensing & Distribution' : 'General Inquiry') === $value)>
                                    {{ app()->getLocale() === 'ar' ? match ($value) {
                                        'General Inquiry' => 'استفسار عام',
                                        'Content Licensing & Distribution' => 'ترخيص المحتوى والتوزيع',
                                        'Production & Collaboration' => 'الإنتاج والتعاون',
                                        'Media Services' => 'الخدمات الإعلامية',
                                        'Press & Media' => 'الصحافة والإعلام',
                                        'Careers' => 'الوظائف',
                                        default => 'أخرى',
                                    } : $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('inquiry_type') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="message" class="mb-2 block text-sm font-medium">
                        {{ app()->getLocale() === 'ar' ? 'رسالتك' : 'Message' }} *
                    </label>
                    <textarea id="message" name="message" rows="5" required maxlength="5000"
                        class="w-full resize-y border border-[#c8d9e6] bg-white px-4 py-3 text-sm outline-none transition placeholder:text-[#8a9aa8] focus:border-[#c5a46d]"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب رسالتك هنا...' : 'Tell us how we can help...' }}">{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>

                @error('show_slug') <p class="text-xs text-red-700">{{ $message }}</p> @enderror

                <button type="submit"
                    class="inline-flex min-h-12 items-center justify-center gap-3 bg-[#2f4156] px-6 py-3 text-xs font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#c5a46d] hover:text-[#2f4156] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#c5a46d]">
                    {{ app()->getLocale() === 'ar' ? 'إرسال الرسالة' : 'Send message' }}
                    <span aria-hidden="true">↗</span>
                </button>
            </form>
        </div>
    </section>
</x-layouts.public>
