<?php

namespace App\Controllers\Admin;

use Core\Middlewares\AuthMiddleware;
use Core\View;

/**
 * Class AdminController
 *
 * @package App\Controllers
 */
class AdminController
{

    /**
     * Admin Dashboard ausgeben
     *
     * [ ] Liste aller User (eigener Menüpunkt)
     * [ ] Liste aller Posts (eigener Menüpunkt)
     * [ ] Liste aller Kategorien (eigener Menüpunkt)
     */
    public function dashboard ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * View laden und ein vom Standard abweichendes Layout wählen.
         *
         * Hier verwenden wir die in PHP 8 neuen "named params". Dadurch können wir optionale Parameter überspringen und
         * müssen nicht immer den Standardwert für alle optionalen Parameter vor dem gewünschten Parameter angeben. Der
         * optionale Parameter, den wir hier überspringen, ist der Parameter, mit dem wir Daten an den View übergeben
         * können.
         */
        View::render('admin/dashboard', layout: 'sidebar');
    }

}
