<?php

namespace App\Support;

/**
 * Een lijnicoon van 24 bij 24 dat een redacteur in het CMS kiest, zoals
 * bij een citaat in de strook herkenning. <x-icoon> tekent hem in de
 * lijnkleur van zijn plek.
 *
 * De klok, het hart, het lijstje, de telefoon, het vergrootglas en de
 * locatie komen uit het ontwerp; de andere zijn er in dezelfde stijl bij
 * getekend. De waarde staat in de database, dus een icoon hernoemen of
 * weghalen breekt de briefjes die hem gekozen hebben.
 */
enum Icoon: string
{
    case Agenda = 'agenda';
    case Bloem = 'bloem';
    case Envelop = 'envelop';
    case Hart = 'hart';
    case Klok = 'klok';
    case Lijstje = 'lijstje';
    case Locatie = 'locatie';
    case Mensen = 'mensen';
    case Ringen = 'ringen';
    case Ster = 'ster';
    case Telefoon = 'telefoon';
    case Vergrootglas = 'vergrootglas';

    /**
     * De naam in de keuzelijst van het CMS.
     */
    public function label(): string
    {
        return match ($this) {
            self::Agenda => 'Agenda',
            self::Bloem => 'Bloem',
            self::Envelop => 'Envelop',
            self::Hart => 'Hart',
            self::Klok => 'Klok',
            self::Lijstje => 'Lijstje',
            self::Locatie => 'Locatie',
            self::Mensen => 'Mensen',
            self::Ringen => 'Ringen',
            self::Ster => 'Ster',
            self::Telefoon => 'Telefoon',
            self::Vergrootglas => 'Vergrootglas',
        };
    }

    /**
     * De lijnen van het icoon: de inhoud van een svg met viewBox 0 0 24 24.
     */
    public function tekening(): string
    {
        return match ($this) {
            self::Agenda => '<rect x="4" y="5" width="16" height="16" rx="2"/><path d="M4 10h16M8 3v4M16 3v4"/>',
            self::Bloem => '<path d="M12 12v9"/><path d="M12 12c-2.8 0-4.5-2.2-4.5-5.5V4l2.25 1.8L12 3.5l2.25 2.3L16.5 4v2.5c0 3.3-1.7 5.5-4.5 5.5z"/><path d="M12 19.5c-.4-2.6-2.4-4.3-5-4.5.4 2.6 2.4 4.3 5 4.5zM12 17.5c.4-2.6 2.4-4.3 5-4.5-.4 2.6-2.4 4.3-5 4.5z"/>',
            self::Envelop => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6.5l8.5 6.5 8.5-6.5"/>',
            self::Hart => '<path d="M12 20s-7-4.4-7-9.6C5 7.4 7.2 5 9.6 5c1.1 0 1.9.6 2.4 1.4.5-.8 1.3-1.4 2.4-1.4C16.8 5 19 7.4 19 10.4 19 15.6 12 20 12 20z"/>',
            self::Klok => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            self::Lijstje => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h3"/>',
            self::Locatie => '<path d="M12 21s-6-5.3-6-10a6 6 0 0 1 12 0c0 4.7-6 10-6 10z"/><circle cx="12" cy="11" r="2"/>',
            self::Mensen => '<circle cx="9" cy="8" r="3"/><path d="M3.5 19.5c.5-3.3 2.6-5.5 5.5-5.5s5 2.2 5.5 5.5"/><circle cx="16.5" cy="9" r="2.5"/><path d="M16 14c2.6 0 4.2 1.9 4.6 4.8"/>',
            self::Ringen => '<circle cx="9" cy="14" r="5"/><circle cx="15" cy="14" r="5"/><path d="M15 3.5l1.5 2-1.5 2-1.5-2z"/>',
            self::Ster => '<path d="M12 3.5l2.6 5.3 5.9.9-4.25 4.1 1 5.8L12 16.85 6.75 19.6l1-5.8L3.5 9.7l5.9-.9z"/>',
            self::Telefoon => '<rect x="7" y="3" width="10" height="18" rx="2"/><path d="M11 18h2"/>',
            self::Vergrootglas => '<circle cx="11" cy="11" r="6"/><path d="M16 16l4 4"/>',
        };
    }
}
