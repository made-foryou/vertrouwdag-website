{{--
    A text that folds in on a phone behind a "Lees verder" button (feedback from Romy, 2026-11-28).

    The slot is the text. The stylesheet sets after how many lines it folds in (--read-more-lines, per place and
    only for narrow screens); resources/js/modules/read-more.js shows the button only when the text is longer than
    that. Without JavaScript the whole text stands there and the button stays hidden. The attributes go to the
    element that folds in.
--}}
<div {{ $attributes->class(['read-more']) }} data-read-more>{{ $slot }}</div>
<button type="button" class="read-more__toggle" aria-expanded="false" hidden>
    <span class="read-more__expand">{{ __('read-more.expand') }}</span>
    <span class="read-more__collapse">{{ __('read-more.collapse') }}</span>
</button>
