<?php

use App\Checklist\Mail\ChecklistMail;
use App\Checklist\Models\Signup;
use Database\Seeders\ChecklistpaginaSeeder;
use Dom\HTMLDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Mail::fake();
});

/**
 * The fields the checklist form sends, as a visitor fills them in on /checklist.
 *
 * @param  array<string, string|null>  $overrides
 * @return array<string, string|null>
 */
function checklistSignupForm(array $overrides = []): array
{
    return array_merge([
        'voornaam' => 'Anna',
        'email' => 'anna@example.com',
        'trouwdatum' => '2027-06-12',
        'avg' => 'on',
        'bron' => 'checklist',
        'website' => '',
    ], $overrides);
}

test('a visitor who signs up is saved and receives the checklist mail with a signed link', function () {
    $this->postJson(route('checklist.signup'), checklistSignupForm())
        ->assertCreated()
        ->assertJson(['message' => 'Dankjewel! De checklist is onderweg naar jullie mailbox.']);

    $signup = Signup::query()->sole();

    expect($signup->first_name)->toBe('Anna')
        ->and($signup->email)->toBe('anna@example.com')
        ->and($signup->wedding_date?->format('Y-m-d'))->toBe('2027-06-12')
        ->and($signup->source)->toBe('checklist')
        ->and($signup->signed_up_at)->not->toBeNull()
        ->and($signup->confirmed_at)->toBeNull();

    Mail::assertQueued(ChecklistMail::class, function (ChecklistMail $mail) use ($signup) {
        return $mail->hasTo('anna@example.com')
            && $mail->signupId === $signup->id
            && URL::hasValidSignature(Request::create($mail->checklistUrl()));
    });
});

test('the email address is stored trimmed and in lower case', function () {
    $this->postJson(route('checklist.signup'), checklistSignupForm(['email' => '  Anna@Example.COM ']))->assertCreated();

    expect(Signup::query()->sole()->email)->toBe('anna@example.com');
});

test('a signup without a wedding date or source is fine', function () {
    $this->postJson(route('checklist.signup'), checklistSignupForm(['trouwdatum' => null, 'bron' => null]))->assertCreated();

    $signup = Signup::query()->sole();

    expect($signup->wedding_date)->toBeNull()
        ->and($signup->source)->toBeNull();
});

test('invalid input is refused with a Dutch message and nothing is sent', function (array $overrides, string $field, string $message) {
    $this->postJson(route('checklist.signup'), checklistSignupForm($overrides))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field => $message]);

    expect(Signup::query()->count())->toBe(0);
    Mail::assertNothingQueued();
})->with([
    'no first name' => [['voornaam' => ''], 'voornaam', 'Vul je voornaam in.'],
    'no email address' => [['email' => ''], 'email', 'Vul je e-mailadres in.'],
    'an invalid email address' => [['email' => 'anna@'], 'email', 'Vul een geldig e-mailadres in.'],
    'an invalid wedding date' => [['trouwdatum' => '12-06-2027'], 'trouwdatum', 'Vul een geldige trouwdatum in.'],
    'no consent' => [['avg' => null], 'avg', 'Vink aan dat jullie de checklist en af en toe een mail willen ontvangen.'],
]);

test('without javascript invalid input sends the visitor back to the form with the input kept', function () {
    $this->from('/checklist')
        ->post(route('checklist.signup'), checklistSignupForm(['email' => 'anna@']))
        ->assertRedirect(url('/checklist').'#checklist-form')
        ->assertSessionHasErrors(['email' => 'Vul een geldig e-mailadres in.'])
        ->assertSessionHasInput('voornaam', 'Anna');

    Mail::assertNothingQueued();
});

test('a filled honeypot looks like a normal signup but saves and sends nothing', function () {
    $this->postJson(route('checklist.signup'), checklistSignupForm(['website' => 'https://spam.example']))
        ->assertCreated()
        ->assertJson(['message' => 'Dankjewel! De checklist is onderweg naar jullie mailbox.']);

    expect(Signup::query()->count())->toBe(0);
    Mail::assertNothingQueued();
});

test('signing up again with the same address updates the one signup and sends the same link again', function () {
    $this->postJson(route('checklist.signup'), checklistSignupForm())->assertCreated();
    $this->postJson(route('checklist.signup'), checklistSignupForm(['voornaam' => 'Anne', 'email' => 'ANNA@example.com']))->assertCreated();

    $signup = Signup::query()->sole();

    expect($signup->first_name)->toBe('Anne');
    Mail::assertQueuedCount(2);
    Mail::assertQueued(ChecklistMail::class, fn (ChecklistMail $mail) => $mail->signupId === $signup->id);
});

test('one address can ask for the checklist three times an hour, so nobody can flood a mailbox', function () {
    foreach (range(1, 3) as $network) {
        $this->withServerVariables(['REMOTE_ADDR' => "10.0.{$network}.1"])
            ->postJson(route('checklist.signup'), checklistSignupForm())
            ->assertCreated();
    }

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.4.1'])
        ->postJson(route('checklist.signup'), checklistSignupForm())
        ->assertTooManyRequests()
        ->assertJson(['message' => 'Dat waren veel aanvragen achter elkaar. Probeer het over een paar minuten opnieuw.']);

    Mail::assertQueuedCount(3);
});

test('one network can send the form five times a minute', function () {
    foreach (range(1, 5) as $visitor) {
        $this->postJson(route('checklist.signup'), checklistSignupForm(['email' => "gast{$visitor}@example.com"]))->assertCreated();
    }

    $this->postJson(route('checklist.signup'), checklistSignupForm(['email' => 'gast6@example.com']))->assertTooManyRequests();

    expect(Signup::query()->count())->toBe(5);
});

test('without javascript the rate limit sends the visitor back to the form with a message', function () {
    foreach (range(1, 3) as $network) {
        $this->withServerVariables(['REMOTE_ADDR' => "10.0.{$network}.1"])->post(route('checklist.signup'), checklistSignupForm());
    }

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.4.1'])
        ->from('/checklist')
        ->post(route('checklist.signup'), checklistSignupForm())
        ->assertRedirect(url('/checklist').'#checklist-form')
        ->assertSessionHasErrors(['email' => 'Dat waren veel aanvragen achter elkaar. Probeer het over een paar minuten opnieuw.']);
});

test('without javascript a signup sends the visitor back to the form with a flash', function () {
    $this->from('/checklist')
        ->post(route('checklist.signup'), checklistSignupForm())
        ->assertRedirect(url('/checklist').'#checklist-form')
        ->assertSessionHas('checklist.signed_up', true);

    Mail::assertQueuedCount(1);
});

test('after a signup without javascript the page shows the thank-you block and the message', function () {
    $this->withoutVite();
    $this->seed(ChecklistpaginaSeeder::class);

    $response = $this->withSession(['checklist' => ['signed_up' => true]])->get('/checklist');
    $document = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);
    $message = $document->querySelector('#checklist-form .form-msg');

    expect($document->getElementById('bedankt')?->hasAttribute('hidden'))->toBeFalse()
        ->and($message?->hasAttribute('hidden'))->toBeFalse()
        ->and($message?->textContent)->toBe('Dankjewel! De checklist is onderweg naar jullie mailbox.');
});

test('after invalid input without javascript the form shows the error and keeps what was filled in', function () {
    $this->withoutVite();
    $this->seed(ChecklistpaginaSeeder::class);

    // The session keeps the error bags as arrays and turns them back into a ViewErrorBag when it starts.
    $response = $this->withSession([
        'errors' => ['default' => ['messages' => ['email' => ['Vul een geldig e-mailadres in.']], 'format' => ':message']],
        '_old_input' => ['voornaam' => 'Anna', 'email' => 'anna@', 'trouwdatum' => '2027-06-12', 'avg' => 'on'],
    ])->get('/checklist');

    $form = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR)->getElementById('checklist-form');

    expect($form?->querySelector('.form-msg')?->textContent)->toBe('Vul een geldig e-mailadres in.')
        ->and($form?->querySelector('.form-msg')?->hasAttribute('hidden'))->toBeFalse()
        ->and($form?->querySelector('input[name="voornaam"]')?->getAttribute('value'))->toBe('Anna')
        ->and($form?->querySelector('input[name="email"]')?->getAttribute('aria-invalid'))->toBe('true')
        ->and($form?->querySelector('input[name="trouwdatum"]')?->getAttribute('value'))->toBe('2027-06-12')
        ->and($form?->querySelector('input[name="avg"]')?->hasAttribute('checked'))->toBeTrue();
});
