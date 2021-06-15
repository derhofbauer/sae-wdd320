<?php

namespace App\Controllers;

use App\Models\Favourite;
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
        // @todo: comment
//        if (!User::isLoggedIn()) {
//            Redirector::redirect(BASE_URL);
//        }
    }

    /**
     * @todo: comment
     */
    public function index ()
    {
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
     * @todo: comment
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
