<?php

namespace App\Support;

/**
 * De kleur van een dienstkaart of een map op /diensten, en wat daarmee
 * per kaart verschilt: de eyebrow, een gedempte prijsregel, de kleur van
 * de hartjes en de knop. De waarde is ook het achtervoegsel van de
 * klassen in de stylesheet (f-lila, tf--lila).
 */
enum Dienstkleur: string
{
    case Lila = 'lila';
    case Cream = 'cream';
    case Groen = 'groen';

    /**
     * De extra klasse van de eyebrow, naast eb en eb--sm.
     */
    public function eyebrowKlasse(): ?string
    {
        return match ($this) {
            self::Lila => null,
            self::Cream => 'eb--grijs',
            self::Groen => 'eb--lila',
        };
    }

    /**
     * Of de kleine regels van de prijs grijs (crème) of lila (groen) zijn.
     */
    public function isGedempt(): bool
    {
        return $this !== self::Lila;
    }

    public function hartKleur(): string
    {
        return match ($this) {
            self::Lila => '#401838',
            self::Cream => '#C8AEC6',
            self::Groen => '#E0D0E0',
        };
    }

    /**
     * De variant van <x-knop> naar de kennismaking.
     */
    public function knopVariant(): string
    {
        return match ($this) {
            self::Lila => 'aubergine',
            self::Cream => 'primair',
            self::Groen => 'lila',
        };
    }
}
