<footer class="site-footer">
    <div class="wrap site-footer__grid">
        <div class="site-footer__col" style="gap:18px">
            <x-brand-image name="logo-cream" sizes="(max-width:720px) 104px, 152px" class="site-footer__logo" alt="vertrouwdag" />
            <p style="font-size:22px;font-style:italic">Jullie dag in vertrouwde handen</p>
        </div>
        <div class="site-footer__col">
            <p class="eb">Contact</p>
            <x-mail-link>{{ config('vertrouwdag.email') }}</x-mail-link>
            <x-whatsapp-link>WhatsApp</x-whatsapp-link>
            <a href="{{ config('vertrouwdag.instagram') }}" target="_blank" rel="noopener">Instagram</a>
            <span>Werkgebied: Zuid-Holland en omgeving</span>
        </div>
        <nav class="site-footer__col" aria-label="Footer">
            <p class="eb">Pagina's</p>
            @foreach (config('vertrouwdag.footer.paginas') as $pad => $label)
                <a href="{{ url($pad) }}">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="site-footer__col">
            <p class="eb">Goed om te weten</p>
            @foreach (config('vertrouwdag.footer.juridisch') as $pad => $label)
                <a href="{{ url($pad) }}">{{ $label }}</a>
            @endforeach
            <button type="button" class="linkbtn" data-cookie-open>Cookie-instellingen</button>
        </div>
    </div>
    <div class="wrap site-footer__bottom">
        <span>© {{ now()->year }} vertrouwdag</span>
        @if (filled(config('vertrouwdag.kvk')))
            <span>KvK {{ config('vertrouwdag.kvk') }}</span>
        @endif
    </div>
</footer>
