<?php

namespace App\Http\Controllers\Checklist;

use App\Checklist\Actions\RegisterSignup;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChecklistSignupRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Signs a visitor up for the checklist. A bot that filled the honeypot gets the same answer as a
 * visitor, so it cannot tell that nothing happened.
 */
final class StoreSignupController extends Controller
{
    public function __invoke(StoreChecklistSignupRequest $request): JsonResponse|RedirectResponse
    {
        if (! $request->isSpam()) {
            RegisterSignup::run($request->toData());
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => __('checklist.signup.thanks')], 201);
        }

        return redirect()->to(url()->previous().'#checklist-form')->with('checklist.signed_up', true);
    }
}
