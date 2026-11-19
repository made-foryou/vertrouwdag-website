<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\IpUtils;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');

        $this->limitChecklistSignups();
    }

    /**
     * Every signup sends a mail, so the form is limited per network and per address: nobody can flood
     * the mailbox of someone else with checklist mails.
     */
    private function limitChecklistSignups(): void
    {
        RateLimiter::for('checklist-signup', function (Request $request): array {
            $network = $this->network($request);
            $address = hash('sha256', Str::lower(trim((string) $request->input('email'))));
            $throttled = fn (Request $request, array $headers): JsonResponse|RedirectResponse => $this->checklistThrottled($request, $headers);

            return [
                Limit::perMinute(5)->by('checklist-network-minute|'.$network)->response($throttled),
                Limit::perHour(20)->by('checklist-network-hour|'.$network)->response($throttled),
                Limit::perHour(3)->by('checklist-address|'.$address)->response($throttled),
            ];
        });
    }

    /**
     * @param  array<string, string>  $headers
     */
    private function checklistThrottled(Request $request, array $headers): JsonResponse|RedirectResponse
    {
        $message = __('checklist.signup.throttled');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 429, $headers);
        }

        return redirect()->to(url()->previous().'#checklist-form')
            ->withInput($request->except('website'))
            ->withErrors(['email' => $message]);
    }

    /**
     * The network a request comes from: an IPv4 address as it is, an IPv6 address as its /64, because one
     * connection usually gets a whole /64 to pick addresses from. The same rule as made-cms-package.
     */
    private function network(Request $request): string
    {
        $ip = $request->ip();

        if ($ip === null || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return 'unknown';
        }

        return IpUtils::anonymize($ip, 0, 8);
    }
}
