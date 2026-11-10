<header class="site-header">
    <div class="site-header__in">
        <a class="site-header__logo" href="{{ route('home') }}" aria-label="vertrouwdag, naar de homepage"><img src="{{ Vite::asset('resources/images/logo-groen.png') }}" alt="vertrouwdag" width="941" height="473"></a>
        {{-- De knop staat vóór het menu, zodat Tab na het openen in het menu verdergaat. In het ontwerp staat hij erna. Op het scherm verandert er niets. --}}
        <button type="button" class="nav-toggle" aria-label="Menu openen" aria-expanded="false" aria-controls="site-nav">
            <svg width="20" height="14" viewBox="0 0 20 14" aria-hidden="true" fill="none" stroke="#401838" stroke-width="1.6" stroke-linecap="round"><path class="l1" d="M1 1h18"/><path class="l2" d="M1 7h18"/><path class="l3" d="M1 13h12"/></svg>
        </button>
        <nav id="site-nav" class="site-nav" aria-label="Hoofdmenu">
            @foreach (config('vertrouwdag.menu') as $route => $label)
                <a href="{{ route($route) }}" @if (request()->routeIs($route, $route.'.*')) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
            <a class="btn" href="{{ route(config('vertrouwdag.cta.route')) }}">{{ config('vertrouwdag.cta.label') }}</a>
        </nav>
        <a class="btn btn--sm site-header__cta" href="{{ route(config('vertrouwdag.cta.route')) }}">{{ config('vertrouwdag.cta.label') }}</a>
    </div>
</header>
