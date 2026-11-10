<?php

namespace App\Support;

/**
 * Een van de drie diensten. De waarde is de sleutel van de prijs in
 * config/vertrouwdag.php (dag, partial of full).
 *
 * Hier staat wat de homepage en /diensten per dienst delen: de kleur, het
 * anker van de map op /diensten en de foto. Het anker ligt vast en volgt
 * de naam van de dienst niet, zodat bestaande links blijven werken als de
 * naam verandert (#17). De foto's liggen vast tot #21.
 */
enum Dienst: string
{
    case Dag = 'dag';
    case Partial = 'partial';
    case Full = 'full';

    public function kleur(): Dienstkleur
    {
        return match ($this) {
            self::Dag => Dienstkleur::Lila,
            self::Partial => Dienstkleur::Cream,
            self::Full => Dienstkleur::Groen,
        };
    }

    /**
     * Het id van de map op /diensten, zonder #.
     */
    public function anker(): string
    {
        return match ($this) {
            self::Dag => 'dagcoordinatie',
            self::Partial => 'partial-planning',
            self::Full => 'full-service',
        };
    }

    /**
     * Het pad van de foto voor Vite::asset().
     */
    public function foto(): string
    {
        return match ($this) {
            self::Dag => 'resources/images/schoenen-boeket.jpg',
            self::Partial => 'resources/images/hand-in-hand-paar.jpg',
            self::Full => 'resources/images/sleep-jurk.jpg',
        };
    }

    public function alt(): string
    {
        return match ($this) {
            self::Dag => 'Bruidsschoenen en boeket op de vensterbank',
            self::Partial => 'Bruid en bruidegom hand in hand',
            self::Full => 'Bruidegom draagt de sleep van de trouwjurk',
        };
    }
}
