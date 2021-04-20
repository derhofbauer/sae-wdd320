<?php

namespace Core;

/**
 * Class Bootstrap
 *
 * Diese Klasse hat nichts mit dem CSS Framework Bootstrap zutun, nur der Name ist gleich.
 *
 * @package Core
 */
class Bootstrap
{

    /**
     * [ ] Session starten
     * [ ] Routing laden
     */
    public function __construct ()
    {
        /**
         * Session starten
         */
        Session::init();

        /**
         * Damit wir nicht bei jedem Redirect die baseurl aus der Config laden müssen, erstellen wir hier eine Hilfs-Konstante.
         */
        define('BASE_URL', Config::get('app.baseurl'));

        /**
         * @todo: comment
         */
        $router = new Router();
        $router->route();
    }

    /**
     * @todo: comment
     */
    public static function setErrorDisplay ()
    {
        $environment = Config::get('app.environment', 'prod');

        if ($environment === 'dev') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
    }

}
