<?php

namespace App\Http\Controllers\Checklist;

use App\Checklist\Actions\ConfirmSignup;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * The link in the checklist mail. The signed route proves the visitor got the mail, so opening it
 * confirms the address. The checklist opens every time, also once the signup is gone.
 */
final class OpenChecklistController extends Controller
{
    public function __invoke(string $signup): BinaryFileResponse
    {
        ConfirmSignup::run($signup);

        return response()
            ->file(config()->string('vertrouwdag.checklist.pdf'), [
                'Content-Type' => 'application/pdf',
                'X-Robots-Tag' => 'noindex',
            ])
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, config()->string('vertrouwdag.checklist.filename'))
            ->setPrivate();
    }
}
