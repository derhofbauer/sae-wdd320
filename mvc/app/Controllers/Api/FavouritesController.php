<?php

namespace App\Controllers\Api;

use App\Models\Favourite;
use App\Models\Post;
use App\Models\User;
use Core\ApiResponse;
use Core\Session;

/**
 * Class FavouriteController
 *
 * Hierbei handelt es sich um einen API Controller, wir laden also in den Actions keine Views, sondern geben JSON Daten
 * zurück, die dann von einem JavaScript im Browser verarbeitet werden können.
 *
 * @package App\Controllers\Api
 */
class FavouritesController
{

    /**
     * Favoriten können nur dann angelegt werden, wenn ein*e User*in eingeloggt ist.
     */
    public function __construct ()
    {
//        /**
//         * Ist kein*e User*in eingeloggt, so generieren wir einen Fehler.
           * @todo: comment
//         */
//        if (!User::isLoggedIn()) {
//            /**
//             * StdClass ist eine Klasse, die von PHP mitgeliefert wird und die verwendet werden kann, um "on the fly"
//             * Objekte zu erzeugen. Hier generieren wir einen Fehler ...
//             */
//            $error = new \StdClass();
//            $error->message = 'You need to be logged in';
//            $error->code = 401;
//            /**
//             * ... und geben ihn in der API Response mit dem Fehlercode 401 zurück.
//             */
//            ApiResponse::json($error, 401);
//        }
    }

    /**
     * @param int $id
     * @todo: comment
     */
    public function add (int $id)
    {
        if (User::isLoggedIn()) {
            $this->addLoggedIn($id);
        } else {
            $this->addGuest($id);
        }
    }

    /**
     * Post als Favorit hinzufügen.
     *
     * @param int $id
     * @todo: comment
     */
    public function addLoggedIn (int $id)
    {
        /**
         * Post und User*in aus der Datenbank laden.
         */
        $post = Post::findOrFail($id);
        $user = User::getLoggedIn();

        /**
         * Existiert der Favorit noch nicht für den/die User*in, so legen wir ihn an.
         */
        if (!$user->hasFavourite($post->id)) {
            $favourite = new Favourite();
            $favourite->user_id = $user->id;
            $favourite->post_id = $post->id;
            $favourite->save();
        }

        /**
         * Nun holen wir die neue Liste aller Favoriten aus der Datenbank ...
         */
        $allFavourites = $user->favourites();

        /**
         * ... und geben sie zurück, damit wir im JS immer als Antwort die aktuelle Liste der Favoriten bekommen.
         */
        return ApiResponse::json($allFavourites);
    }

    /**
     * Post als Favorit hinzufügen.
     *
     * @param int $id
     * @todo: comment
     */
    public function addGuest (int $id)
    {
        /**
         * Post und User*in aus der Datenbank laden.
         */
        $post = Post::findOrFail($id);

        /**
         * Existiert der Favorit noch nicht für den/die User*in, so legen wir ihn an.
         */
        $favourites = Session::get(Favourite::SESSION_KEY, []);
        if (!in_array($post->id, $favourites)) {
            $favourites[] = $post->id;
            Session::set(Favourite::SESSION_KEY, $favourites);
        }

        /**
         * Nun holen wir die neue Liste aller Favoriten aus der Datenbank ...
         */
        $allFavourites = Favourite::getFromSession();

        /**
         * ... und geben sie zurück, damit wir im JS immer als Antwort die aktuelle Liste der Favoriten bekommen.
         */
        return ApiResponse::json($allFavourites);
    }

    /**
     * @param int $id
     * @todo: comment
     */
    public function remove (int $id)
    {
        if (User::isLoggedIn()) {
            $this->removeLoggedIn($id);
        } else {
            $this->removeGuest($id);
        }
    }

    /**
     * Post als Favorit entfernen.
     *
     * @param int $id
     */
    public function removeLoggedIn (int $id)
    {
        /**
         * Post und User*in aus der Datenbank laden.
         */
        $post = Post::findOrFail($id);
        $user = User::getLoggedIn();

        /**
         * Existiert der Post als Favorit für den/die User*in, laden wir ihn aus der Datenbank und löschen ihn dann.
         */
        if ($user->hasFavourite($post->id)) {
            $favourite = $user->favourite($post->id);
            $favourite->delete();
        }

        /**
         * Nun holen wir die neue Liste aller Favoriten aus der Datenbank ...
         */
        $allFavourites = $user->favourites();

        /**
         * ... und geben sie zurück, damit wir im JS immer als Antwort die aktuelle Liste der Favoriten bekommen.
         */
        return ApiResponse::json($allFavourites);
    }

    public function removeGuest (int $id)
    {
        /**
         * Post und User*in aus der Datenbank laden.
         */
        $post = Post::findOrFail($id);

        /**
         * Existiert der Post als Favorit für den/die User*in, laden wir ihn aus der Datenbank und löschen ihn dann.
         */
        $favourites = Session::get(Favourite::SESSION_KEY, []);
        if (in_array($post->id, $favourites)) {
            $index = array_search($post->id, $favourites);
            unset($favourites[$index]);
            Session::set(Favourite::SESSION_KEY, $favourites);
        }

        /**
         * Nun holen wir die neue Liste aller Favoriten aus der Datenbank ...
         */
        $allFavourites = Favourite::getFromSession();

        /**
         * ... und geben sie zurück, damit wir im JS immer als Antwort die aktuelle Liste der Favoriten bekommen.
         */
        return ApiResponse::json($allFavourites);
    }
}
