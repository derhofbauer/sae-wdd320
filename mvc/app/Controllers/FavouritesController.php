<?php

namespace App\Controllers;

use App\Models\User;
use Core\Helpers\Redirector;
use Core\View;

/**
 * Class FavouritesController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class FavouritesController
{

    public function __construct ()
    {
        if (!User::isLoggedIn()) {
            Redirector::redirect(BASE_URL);
        }
    }

    public function index ()
    {
        $user = User::getLoggedIn();
        $favourites = $user->favourites();

        View::render('favourites/index', [
            'favourites' => $favourites
        ]);

    }
}
