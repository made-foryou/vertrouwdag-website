<?php

namespace App\Support;

/**
 * De vanaf-prijs van een dienst, in hele euro's exclusief btw.
 *
 * Rekent het bedrag inclusief btw uit en schrijft beide bedragen in de
 * notatie van het ontwerp: het euroteken zonder spatie, een punt voor de
 * duizendtallen en alleen centen als het geen heel bedrag is (€850,
 * €1.028,50, €4.235).
 *
 * Romy typt de prijs in het CMS, in de strook pakketten op /diensten
 * (App\Support\Aanbod). Het package kent nog geen veld voor een getal,
 * dus de prijs is tekst en uitTekst() leest hem.
 */
final readonly class Prijs
{
    public function __construct(
        public int $exclBtw,
        public int $btwPercentage,
    ) {}

    /**
     * De prijs uit de tekst van een redacteur, met het btw-percentage uit
     * config/vertrouwdag.php. Null als de tekst geen heel bedrag is.
     */
    public static function uitTekst(?string $tekst): ?self
    {
        $euros = self::eurosUitTekst($tekst);

        return $euros === null ? null : new self($euros, (int) config('vertrouwdag.btw_percentage'));
    }

    /**
     * Een heel bedrag in euro's zoals een redacteur het typt: 850, 1.850,
     * € 1850, 1850,- of 1.850,00. Null bij lege tekst, centen, nul of iets
     * anders dan een bedrag.
     */
    public static function eurosUitTekst(?string $tekst): ?int
    {
        $bedrag = preg_replace(['/\s+/u', '/^€/u', '/,(?:-|00)$/'], '', (string) $tekst);

        if (! is_string($bedrag) || preg_match('/^(?:\d{1,3}(?:\.\d{3}){1,2}|\d{1,7})$/', $bedrag) !== 1) {
            return null;
        }

        $euros = (int) str_replace('.', '', $bedrag);

        return $euros > 0 ? $euros : null;
    }

    public function exclBtw(): string
    {
        return self::formatteer($this->exclBtw * 100);
    }

    public function inclBtw(): string
    {
        return self::formatteer($this->inclBtwInCenten());
    }

    /**
     * Met hele euro's en een heel percentage is dit exact, zonder afronden.
     */
    public function inclBtwInCenten(): int
    {
        return $this->exclBtw * (100 + $this->btwPercentage);
    }

    private static function formatteer(int $centen): string
    {
        $decimalen = $centen % 100 === 0 ? 0 : 2;

        return '€'.number_format($centen / 100, $decimalen, ',', '.');
    }
}
