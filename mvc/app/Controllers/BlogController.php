<?php

namespace App\Controllers;

use App\Models\Post;
use Core\Models\AbstractModel;
use Core\View;

class BlogController
{

    /**
     * Alle Posts listen.
     */
    public function index ()
    {
        /**
         * Alle Posts über das Post-Model aus der Datenbank laden.
         */
        $posts = Post::all();

        /**
         * Um nicht in jeder Action den Header und den Footer und dann den View laden zu müssen, haben wir uns eine View
         * Klasse gebaut. Als ersten Parameter übergeben wir den Namen des Tempalte Files inkl. Pfad und als zweiten
         * Parameter alle Werte aus dem Controller, die im Template als eigene Variablen verfügbar sein sollen.
         */
        View::render('blog/index', [
            'posts' => $posts
        ]);
    }

    /**
     * Einzelnen Post anzeigen.
     *
     * [x] Post aus DB abrufen
     * [x] Post an View übergeben
     *
     * @param string $slug
     */
    public function show (string $slug)
    {
        $post = Post::findBySlug($slug);

        View::render('blog/show', [
            'post' => AbstractModel::returnOrFail($post)
        ]);
    }

}
