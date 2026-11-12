<?php

namespace App\Support;

/**
 * De veelgestelde vragen als FAQPage voor zoekmachines (schema.org).
 *
 * De strook faq bouwt dit uit dezelfde vragen die hij op de pagina zet
 * (with() in App\Cms\Strips\Faq), zodat de gestructureerde gegevens
 * nooit van de zichtbare vragen afwijken. Een antwoord is opgemaakte tekst, zoals RichText van het
 * package hem levert; zoekmachines krijgen hem als platte tekst.
 */
final class FaqSchema
{
    /**
     * @param  list<array{vraag: string, antwoord: string}>  $vragen
     * @return array{'@context': string, '@type': string, mainEntity: list<array{'@type': string, name: string, acceptedAnswer: array{'@type': string, text: string}}>}
     */
    public static function vanVragen(array $vragen): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(static fn (array $vraag): array => [
                '@type' => 'Question',
                'name' => $vraag['vraag'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => self::platteTekst($vraag['antwoord']),
                ],
            ], array_values($vragen)),
        ];
    }

    /**
     * De tekst zonder opmaak: blokken en regelovergangen worden een spatie,
     * entiteiten weer tekens, en witruimte één spatie.
     */
    private static function platteTekst(string $html): string
    {
        $tekst = strip_tags((string) preg_replace('/<(?:\/?(?:p|li|ul|ol|h[1-6])|br)\b[^>]*>/i', ' ', $html));
        $tekst = html_entity_decode($tekst, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $tekst));
    }
}
