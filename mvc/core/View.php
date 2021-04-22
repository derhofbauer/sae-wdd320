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
     * @param string      $template
     * @param array       $params
     * @param string|null $layout
     *
     * @todo: comment
     */
    public static function render (string $template, array $params = [], string $layout = null)
    {
        if ($layout === null) {
            $layout = Config::get('app.default-layout', 'default');
        }

        $viewBasePath = __DIR__ . '/../resources/views';
        $renderTemplate = "{$viewBasePath}/templates/{$template}.php";

        if (!empty($params)) {
            extract($params);
        }

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
        echo "$httpCode: $message";
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
