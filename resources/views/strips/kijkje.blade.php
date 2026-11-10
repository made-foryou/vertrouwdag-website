{{--
    Strook: "Een kijkje achter de planner", met links een korte tekst en
    rechts twee persoonlijke polaroids die over elkaar heen liggen.

    Velden: label, kop (met accent), tekst, bijschrift_links en
    bijschrift_rechts (onder de polaroids). Het zijn er precies twee,
    want de stylesheet legt ze met nth-child neer. De foto's liggen vast
    tot #21.
--}}
<section class="wrap persoonlijk" data-cms-strip="{{ $strip->id }}">
    <div class="persoonlijk__txt" data-reveal>
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <h2 data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
        <p class="lead" data-cms-field="tekst">{{ $strip->tekst }}</p>
    </div>
    <div class="persoonlijk__fotos" data-reveal-group>
        <figure class="polaroid" data-reveal><img src="{{ Vite::asset('resources/images/romy-klein.jpg') }}" alt="Romy als klein meisje verkleed als bruidje, met sluier, bloemenkrans en boeket" width="800" height="1038" loading="lazy"><span data-cms-field="bijschrift_links">{{ $strip->bijschrift_links }}</span></figure>
        <figure class="polaroid" data-reveal><img src="{{ Vite::asset('resources/images/romy-samen.jpg') }}" alt="Romy samen met haar partner aan zee" width="900" height="842" loading="lazy"><span data-cms-field="bijschrift_rechts">{{ $strip->bijschrift_rechts }}</span></figure>
    </div>
</section>
