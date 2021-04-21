<?php

namespace Core;

/**
 * Class View
 *
 * @package Core
 */
class View
{

    /**
     * HTTP Fehler Code an den Browser schicken und dann abbrechen.
     *
     * @param int    $httpCode
     * @param string $message
     */
    public static function error (int $httpCode = 500, string $message = 'An error occurred.')
    {
        /**
         * HTTP Fehler Code an den Browser schicken.
         */
        http_response_code($httpCode);

        /**
         * Error Code und Fehlermeldung ausgeben, damit man auch irgendwas sieht.
         *
         * Das werden wir vielleicht noch anpassen und eine hübsche Fehlermeldung bauen, die wir an dieser Stelle dann
         * ausgeben.
         */
        echo "$httpCode: $message";
    }

    /**
     * Das ist eine einfache Hilfsfunktion die den HTTP Status Code 404 an den Browser schickt und danach abbricht.
     */
    public static function error404 ()
    {
        self::error(404, 'Page not found.');
    }
}
