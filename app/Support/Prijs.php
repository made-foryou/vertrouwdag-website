<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * De vanaf-prijs van een dienst, in hele euro's exclusief btw.
 *
 * Rekent het bedrag inclusief btw uit en schrijft beide bedragen in de
 * notatie van het ontwerp: het euroteken zonder spatie, een punt voor de
 * duizendtallen en alleen centen als het geen heel bedrag is (€850,
 * €1.028,50, €4.235).
 */
final readonly class Prijs
{
    public function __construct(
        public int $exclBtw,
        public int $btwPercentage,
    ) {}

    /**
     * De prijs van een dienst uit config/vertrouwdag.php.
     *
     * @throws InvalidArgumentException als de dienst geen prijs heeft
     */
    public static function voorDienst(string $dienst): self
    {
        $exclBtw = config("vertrouwdag.prijzen.{$dienst}");

        if (! is_int($exclBtw)) {
            throw new InvalidArgumentException("Er staat geen prijs voor de dienst [{$dienst}] in config/vertrouwdag.php.");
        }

        return new self($exclBtw, (int) config('vertrouwdag.btw_percentage'));
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
