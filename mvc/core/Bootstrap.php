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
        // Session::init();

        /**
         * Damit wir nicht bei jedem Redirect die baseurl aus der Config laden müssen, erstellen wir hier eine Hilfs-Konstante.
         */
        define('BASE_URL', Config::get('app.baseurl'));

        // $router = new Router();
        // $router->route();
    }

}
