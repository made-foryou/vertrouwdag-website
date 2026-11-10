<?php

namespace App\Support;

/**
 * De links waarmee bezoekers Romy bereiken, met het nummer en het adres
 * uit config/vertrouwdag.php.
 *
 * <x-whatsapp-link> en <x-mail-link> gebruiken deze links, en ook een
 * strook die een ander element dan een gewone link nodig heeft, zoals de
 * contactbriefjes op /kennismaken.
 */
final class Contact
{
    /**
     * WhatsApp met een bericht dat alvast klaarstaat. Zonder tekst is dat
     * het bericht uit de config.
     */
    public static function whatsappUrl(?string $tekst = null): string
    {
        $tekst ??= (string) config('vertrouwdag.whatsapp.tekst');

        return 'https://wa.me/'.config('vertrouwdag.whatsapp.nummer').'?text='.rawurlencode($tekst);
    }

    /**
     * Een mail met een onderwerp dat alvast is ingevuld. Zonder onderwerp
     * is dat het onderwerp uit de config; een leeg onderwerp geeft een
     * mail zonder onderwerp.
     */
    public static function mailUrl(?string $onderwerp = null): string
    {
        $onderwerp ??= config('vertrouwdag.email_onderwerp');
        $url = 'mailto:'.config('vertrouwdag.email');

        if (filled($onderwerp)) {
            $url .= '?subject='.rawurlencode($onderwerp);
        }

        return $url;
    }
}
