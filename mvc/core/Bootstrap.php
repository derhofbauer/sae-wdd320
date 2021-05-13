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
     * [x] Session starten
     * [x] Routing laden
     */
    public function __construct ()
    {
        /**
         * Session starten
         */
        Session::init();
        /**
         * Daten aus den $_GET und $_POST Superglobals in die Session speichern. Das wird benötigt, damit die old()-
         * Methode der Session Core Klasse funktioniert.
         */
        Session::initSuperglobals();

        /**
         * Damit wir nicht bei jedem Redirect die baseurl aus der Config laden müssen, erstellen wir hier eine Hilfs-Konstante.
         */
        define('BASE_URL', Config::get('app.baseurl'));

        /**
         * Hier erstellen wir einen neuen Router und starten dann das Routing.
         */
        $router = new Router();
        $router->route();
    }

    /**
     * Je nach Umgebung, welche Umgebung (dev/prod) gerade konfiguriert ist, schalten wir das error reporting ein oder
     * aus.
     */
    public static function setErrorDisplay ()
    {
        /**
         * Config aus dem app.php Config File auslesen
         */
        $environment = Config::get('app.environment', 'prod');

        /**
         * Wenn grade die dev Environment konfiguriert ist ...
         */
        if ($environment === 'dev') {
            /**
             * ... zeigen wir alle Fehler an.
             *
             * Hier werden zwei PHP Einstellungen überschrieben, die in der php.ini Datei konfiguriert sind.
             */
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            /**
             * E_ALL ist eine von PHP mitgelieferte Konstante zur Konfiguration. Hier wird definiert, dass wir ALLE
             * Fehler angezeigt bekommen möchten.
             */
            error_reporting(E_ALL);
        }
    }

}
