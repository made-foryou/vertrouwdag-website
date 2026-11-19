<?php

namespace App\Checklist\Jobs;

use App\Checklist\Actions\SubscribeToNewsletter;
use App\Checklist\Models\Signup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;

/**
 * Puts a confirmed signup in the newsletter list from the queue, so an outage at the newsletter service
 * never reaches the visitor. An outage is tried again with a growing pause; a rate limit waits as long as
 * the service asks; a contact the service refuses fails at once and stays in failed_jobs.
 */
#[Tries(5)]
#[Backoff([60, 300, 900, 3600])]
final class SubscribeToNewsletterJob implements ShouldQueue
{
    use Queueable;

    private const int DEFAULT_RETRY_AFTER_SECONDS = 60;

    public function __construct(public readonly string $signupId) {}

    public function handle(): void
    {
        $signup = Signup::query()->confirmed()->find($this->signupId);

        if ($signup === null) {
            return;
        }

        try {
            SubscribeToNewsletter::run($signup);
        } catch (RequestException $exception) {
            $this->handleRefusal($exception);
        }
    }

    private function handleRefusal(RequestException $exception): void
    {
        $response = $exception->response;

        if ($response->tooManyRequests()) {
            $this->release((int) $response->header('Retry-After') ?: self::DEFAULT_RETRY_AFTER_SECONDS);

            return;
        }

        if ($response->clientError()) {
            $this->fail($exception);

            return;
        }

        throw $exception;
    }
}
