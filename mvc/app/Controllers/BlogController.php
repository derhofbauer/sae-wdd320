<?php

namespace App\Controllers;

use App\Models\Post;
use Core\Models\AbstractModel;
use Core\View;

class BlogController
{

    /**
     * @todo: comment
     */
    public function index ()
    {
        $posts = Post::all();

        View::render('blog/index', [
            'posts' => $posts
        ]);
    }

    /**
     * @todo: comment
     */
    public function show (string $slug)
    {
        /**
         * [x] Post aus DB abrufen
         * [x] Post an View übergeben
         */
        $post = Post::findBySlug($slug);

        View::render('blog/show', [
            'post' => AbstractModel::returnOrFail($post)
        ]);
    }

}
