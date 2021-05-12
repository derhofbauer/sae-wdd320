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
     * Diese Methode erlaubt es uns innerhalb der Controller der App (s. HomeController), einen View in nur einer
     * einzigen Zeile zu laden und auch Parameter an den View zu übergeben. Die View Parameter dienen dazu, dass Werte,
     * die in den Controllern berechnet wurden, an den View zur Darstellung übergeben werden können.
     *
     * Aufruf: View::load('ProductSingle', $productValues)
     *
     * @param string      $template
     * @param array       $params
     * @param string|null $layout
     */
    public static function render (string $template, array $params = [], string $layout = null)
    {
        /**
         * Standard-Layout laden, wenn kein $layout angegeben wurde.
         */
        if ($layout === null) {
            $layout = Config::get('app.default-layout', 'default');
        }

        /**
         * extract() erstellt aus jedem Wert in einem Array eine eigene Variable. Das brauchen wir aber nur zu tun, wenn
         * überhaupt $params vorhanden sind.
         */
        if (!empty($params)) {
            extract($params);
        }

        /**
         * View Base Path vorbereiten, damit ihn später verwenden können.
         */
        $viewBasePath = __DIR__ . '/../resources/views';

        /**
         * View Path vorbereiten, damit im Layout file der View geladen werden kann
         */
        $renderTemplate = "{$viewBasePath}/templates/{$template}.php";

        /**
         * Hier laden wir das Layout-File anhand des $layout Funktionsparameters. Das Layout lädt dann den $view.
         */
        require_once "{$viewBasePath}/layouts/{$layout}.php";
    }

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
        self::render('error', [
            'httpCode' => $httpCode,
            'message' => $message
        ]);
        exit;
    }

    /**
     * Das ist eine einfache Hilfsfunktion die den HTTP Status Code 404 an den Browser schickt und danach abbricht.
     */
    public static function error404 ()
    {
        self::error(404, 'Page not found.');
    }

    /**
     * Das ist eine einfache Hilfsfunktion die den HTTP Status Code 403 an den Browser schickt und danach abbricht.
     */
    public static function error403 ()
    {
        self::error(403, 'Forbidden.');
    }
}
