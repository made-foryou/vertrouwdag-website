{{--
    Een review op papier met een stippelrand. Het citaat is verplicht;
    label (soort review), kop en naam zijn optioneel.

    groot: de brede variant, iets scheef. zegel: het monogram rechtsonder.
    De slots geven hun attributen door aan hun element, bijvoorbeeld
    data-cms-field.
--}}
@props([
    'groot' => false,
    'zegel' => false,
    'citaat',
    'label' => null,
    'kop' => null,
    'naam' => null,
])
<figure {{ $groot ? $attributes->class(['review', 'review--groot'])->merge(['style' => 'transform:rotate(-1.5deg)']) : $attributes->class(['review']) }}>
    @if ($label)
        <span {{ $label->attributes->class(['review__label', 'eb', 'eb--sm']) }}>{{ $label }}</span>
    @endif
    @if ($kop)
        <p {{ $kop->attributes->class(['eb']) }}>{{ $kop }}</p>
    @endif
    <blockquote {{ $citaat->attributes }}>{{ $citaat }}</blockquote>
    @if ($naam)
        <figcaption {{ $naam->attributes }}>{{ $naam }}</figcaption>
    @endif
    @if ($zegel)
        <img class="review__seal" src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt="" width="460" height="473">
    @endif
</figure>
