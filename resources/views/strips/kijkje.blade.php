{{--
    Strook: "Een kijkje achter de planner", met links een korte tekst en
    rechts twee persoonlijke polaroids die over elkaar heen liggen.

    Velden: label, kop (Kop met nadruk), tekst, foto_links en foto_rechts (op
    de polaroids), bijschrift_links en bijschrift_rechts (onder de
    polaroids). Het zijn er precies twee, want de stylesheet legt ze met
    nth-child neer. Zonder foto staat de foto uit het ontwerp (<x-foto>).
--}}
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap persoonlijk">
        <div class="persoonlijk__txt" data-reveal="text">
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
            <p class="lead" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
        </div>
        <div class="persoonlijk__fotos" data-reveal-group="notes">
            <figure class="polaroid"><x-foto :foto="$strip->foto_links" terugval="resources/images/romy-klein.jpg" alt="Romy als klein meisje verkleed als bruidje, met sluier, bloemenkrans en boeket" :breedte="800" :hoogte="1038" sizes="(max-width:860px) 44vw, (max-width:1250px) 23vw, 280px" veld="foto_links" loading="lazy" /><span data-cms-field="bijschrift_links">{{ $strip->bijschrift_links }}</span></figure>
            <figure class="polaroid"><x-foto :foto="$strip->foto_rechts" terugval="resources/images/romy-samen.jpg" alt="Romy samen met haar partner aan zee" :breedte="900" :hoogte="842" sizes="(max-width:860px) 51vw, (max-width:1250px) 26vw, 320px" veld="foto_rechts" loading="lazy" /><span data-cms-field="bijschrift_rechts">{{ $strip->bijschrift_rechts }}</span></figure>
        </div>
    </div>
</section>
