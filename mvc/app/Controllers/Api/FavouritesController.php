<?php

namespace App\Controllers\Api;

use App\Models\Favourite;
use App\Models\Post;
use App\Models\User;
use Core\ApiResponse;

/**
 * Class FavouriteController
 *
 * @package App\Controllers\Api
 * @todo    : comment
 */
class FavouritesController
{

    public function __construct ()
    {
        if (!User::isLoggedIn()) {
            $error = new \StdClass();
            $error->message = 'You need to be logged in';
            $error->code = 1;
            ApiResponse::json($error, 401);
        }
    }

    public function add (int $id)
    {
        $post = Post::findOrFail($id);
        $user = User::getLoggedIn();

        if (!$user->hasFavourite($post->id)) {
            $favourite = new Favourite();
            $favourite->user_id = $user->id;
            $favourite->post_id = $post->id;
            $favourite->save();
        }

        $allFavourites = $user->favourites();

        return ApiResponse::json($allFavourites);
    }

    public function remove (int $id)
    {
        $post = Post::findOrFail($id);
        $user = User::getLoggedIn();

        if ($user->hasFavourite($post->id)) {
            $favourite = $user->favourite($post->id);
            $favourite->delete();
        }

        $allFavourites = $user->favourites();

        return ApiResponse::json($allFavourites);
    }

}
