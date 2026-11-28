{{--
    Een review op papier met een stippelrand. Het citaat is verplicht;
    label (soort review), kop en naam zijn optioneel.

    groot: de brede variant, iets scheef. zegel: het monogram rechtsonder.
    De slots geven hun attributen door aan hun element, bijvoorbeeld
    data-cms-field.

    inklapbaar: a long quote folds in on a phone behind "Lees verder"
    (<x-read-more>).
--}}
@props([
    'groot' => false,
    'zegel' => false,
    'inklapbaar' => false,
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
    @if ($inklapbaar)
        <x-read-more><blockquote {{ $citaat->attributes }}>{{ $citaat }}</blockquote></x-read-more>
    @else
        <blockquote {{ $citaat->attributes }}>{{ $citaat }}</blockquote>
    @endif
    @if ($naam)
        <figcaption {{ $naam->attributes }}>{{ $naam }}</figcaption>
    @endif
    @if ($zegel)
        <x-brand-image name="monogram-groen" sizes="(max-width:720px) 58px, 76px" class="review__seal" alt="" />
    @endif
</figure>
