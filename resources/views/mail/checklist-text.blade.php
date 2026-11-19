{{--
    The checklist mail (App\Checklist\Mail\ChecklistMail), plain text version. Nothing is escaped:
    plain text has no html, and an escaped & would break the signature of the link.
--}}
{!! __('checklist.mail.greeting', ['name' => $firstName]) !!}

{!! __('checklist.mail.intro') !!}

{!! __('checklist.mail.button') !!}:
{!! $checklistUrl !!}

{!! __('checklist.mail.closing') !!}
{!! __('checklist.mail.signature') !!}

--
@foreach (__('checklist.mail.why') as $sentence)
{!! $sentence !!}
@endforeach
