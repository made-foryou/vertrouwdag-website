<?php

namespace App\Support;

/**
 * Het onderwerp van een blog. De waarde staat in config/blogs.php, in
 * data-cat op de kaart en als anker achter /blog/archief (#planning).
 */
enum Blogcategorie: string
{
    case Planning = 'planning';
    case Locaties = 'locaties';
    case Budget = 'budget';
    case Inspiratie = 'inspiratie';
    case AchterDeSchermen = 'achter-de-schermen';

    public function label(): string
    {
        return match ($this) {
            self::Planning => 'Planning',
            self::Locaties => 'Locaties in Zuid-Holland',
            self::Budget => 'Budget',
            self::Inspiratie => 'Inspiratie & styling',
            self::AchterDeSchermen => 'Achter de schermen',
        };
    }
}
