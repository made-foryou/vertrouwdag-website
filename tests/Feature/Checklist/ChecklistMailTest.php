<?php

use App\Checklist\Mail\ChecklistMail;
use Illuminate\Contracts\Queue\ShouldQueue;

test('the checklist mail greets the visitor and links to the checklist with a signed link', function () {
    $mail = new ChecklistMail('Anna', '01JD0000000000000000000000');
    $link = $mail->checklistUrl();

    expect($link)->toStartWith(route('checklist.open', ['signup' => '01JD0000000000000000000000']).'?signature=')
        ->and($mail)->toBeInstanceOf(ShouldQueue::class);

    $mail->assertHasSubject('Jullie trouwplanning-checklist');
    $mail->assertSeeInHtml('Hoi Anna,');
    $mail->assertSeeInHtml('Open de checklist');
    $mail->assertSeeInHtml($link);
    $mail->assertSeeInHtml('data-pm-no-track', false);
    $mail->assertSeeInText('Hoi Anna,');
    $mail->assertSeeInText($link, false);
});

test('the checklist mail says why the visitor gets it and that unsubscribing is always possible', function () {
    $mail = new ChecklistMail('Anna', '01JD0000000000000000000000');

    $mail->assertSeeInOrderInHtml([
        'Jullie krijgen deze mail omdat dit e-mailadres de checklist aanvroeg op vertrouwdag.nl.',
        'Met de knop bevestigen jullie ook dat ik af en toe een mail mag sturen.',
        'Afmelden kan altijd, met de link onderaan elke mail.',
        'Niet zelf aangevraagd? Dan hoeven jullie niets te doen: zonder klik komt dit adres niet op de lijst.',
    ]);
    $mail->assertSeeInText('Afmelden kan altijd, met de link onderaan elke mail.');
});
