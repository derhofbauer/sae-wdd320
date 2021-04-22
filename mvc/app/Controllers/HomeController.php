<?php

namespace App\Controllers;

use App\Models\Post;
use Core\View;

class HomeController
{

    public function index () {
        $posts = Post::all();

        View::render('home', [
            'posts' => $posts
        ]);
    }

}
