<?php

namespace App\Controllers;

use App\Models\User;
use Core\Helpers\Redirector;
use Core\View;

/**
 * Class FavouritesController
 *
 * @package App\Controllers
 */
class FavouritesController
{

    /**
     * Favoriten können nur dann angelegt werden, wenn ein*e User*in eingeloggt ist.
     */
    public function __construct ()
    {
        if (!User::isLoggedIn()) {
            Redirector::redirect(BASE_URL);
        }
    }

    /**
     * Übersicht der Favoriten anzeigen.
     */
    public function index ()
    {
        /**
         * User*in und zugehörige Favoriten aus der Datenbank holen.
         */
        $user = User::getLoggedIn();
        $favourites = $user->favourites();

        /**
         * View laden und Daten übergeben.
         */
        View::render('favourites/index', [
            'favourites' => $favourites
        ]);

    }
}
