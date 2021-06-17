<?php

namespace App\Controllers;

use App\Models\Favourite;
use App\Models\User;
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
        /**
         * Nachdem mittlerweile auch nicht eingeloggt Personen Favoriten speichern können, benötigen wir den
         * untenstehenden Code nicht mehr.
         */

//        if (!User::isLoggedIn()) {
//            Redirector::redirect(BASE_URL);
//        }
    }

    /**
     * Favoriten listen.
     */
    public function index ()
    {
        /**
         * Je nachdem ob eine Person eingeloggt ist oder nicht, passieren rufen wir eine andere Funktion auf.
         */
        if (User::isLoggedIn()) {
            $this->indexLoggedIn();
        } else {
            $this->indexGuest();
        }
    }

    /**
     * Übersicht der Favoriten anzeigen.
     */
    public function indexLoggedIn ()
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

    /**
     * Übersicht der Favoriten anzeigen.
     */
    public function indexGuest ()
    {
        /**
         * User*in und zugehörige Favoriten aus der Datenbank holen.
         */
        $favourites = Favourite::getFromSession();

        /**
         * View laden und Daten übergeben.
         */
        View::render('favourites/index', [
            'favourites' => $favourites
        ]);
    }
}
