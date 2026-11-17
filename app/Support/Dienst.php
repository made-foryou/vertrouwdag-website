<?php

namespace App\Support;

/**
 * Een van de drie diensten (dag, partial of full). In het CMS is het een
 * keuze in de strook pakketten op /diensten en in de strook diensten op
 * de homepage. Via die keuze vindt de homepage de prijs van een dienst in
 * de pakketten (App\Support\Aanbod).
 *
 * Hier staat wat de homepage en /diensten per dienst delen: de kleur, het
 * anker van de map op /diensten en de foto. Het anker ligt vast en volgt
 * de naam van de dienst niet, zodat bestaande links blijven werken als de
 * naam verandert (#17). De foto is die uit het ontwerp: de strook toont
 * hem zolang er in het CMS geen foto gekozen is (#21).
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
     * Het pad van de foto uit het ontwerp voor Vite::asset(), de terugval
     * van <x-foto>. De foto is sfeer: de tekst ernaast zegt al wat de
     * dienst is, dus de foto krijgt overal een lege alt (besluit Menno,
     * 2026-11-10, zie #12).
     */
    public function foto(): string
    {
        return match ($this) {
            self::Dag => 'resources/images/schoenen-boeket.jpg',
            self::Partial => 'resources/images/hand-in-hand-paar.jpg',
            self::Full => 'resources/images/sleep-jurk.jpg',
        };
    }
}
