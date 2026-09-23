@props(['clients'])

<section id="clients" class="mli-clients">
    <div class="container">
        <div class="mli-clients__heading" data-reveal="up">
            <div>
                <span class="mli-clients__index">06 / CLIENTS &amp; PARTNERS</span>
                <h2>{{ app()->getLocale() === 'ar' ? 'شركاؤنا' : 'Clients & Partners' }}</h2>
            </div>
            <p>
                {{ app()->getLocale() === 'ar'
                    ? 'علامات وشركاء نعمل معهم لإيصال المحتوى إلى جمهور أوسع.'
                    : 'Brands and partners we work with to move content to wider audiences.' }}
            </p>
        </div>

        @if($clients->isNotEmpty())
            <div class="mli-clients__marquee" aria-label="{{ app()->getLocale() === 'ar' ? 'العملاء والشركاء' : 'Clients and partners' }}">
                <div class="mli-clients__track">
                    @foreach($clients as $client)
                        <div class="mli-client-logo" title="{{ $client->name }}">
                            @if($client->logo_url)
                                <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" loading="lazy">
                            @else
                                <span>{{ $client->name }}</span>
                            @endif
                        </div>
                    @endforeach

                    @foreach($clients as $client)
                        <div class="mli-client-logo" aria-hidden="true">
                            @if($client->logo_url)
                                <img src="{{ $client->logo_url }}" alt="" loading="lazy">
                            @else
                                <span>{{ $client->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="mli-content-empty" data-reveal="up">
                {{ app()->getLocale() === 'ar' ? 'لا يوجد عملاء أو شركاء مضافون حاليًا.' : 'No clients or partners added yet.' }}
            </p>
        @endif
    </div>
</section>
