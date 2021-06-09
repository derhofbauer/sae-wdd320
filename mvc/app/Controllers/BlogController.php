<?php

namespace App\Controllers;

use App\Models\Post;
use Core\Config;
use Core\Models\AbstractModel;
use Core\View;

class BlogController
{

    /**
     * Alle Posts listen.
     *
     * @param int $page Pagination-Seite, die angezeigt werden soll.
     */
    public function index (int $page = 1)
    {
        /**
         * Alle Posts über das Post-Model aus der Datenbank laden.
         *
         * Hier laden wir aber eine bestimmte Seite. Wenn wir also bspw. 5 Elemente pro Seite anzeigen möchten, laden
         * wir hier nur 5 Elemente und überspringen davor ($page - 1) * 5 Elemente.
         */
        $posts = Post::allPaginated($page);

        /**
         * Damit wir die Links für die Paginierung generieren können, müssen wir wissen, wie viele Elemente es gesamt
         * gibt und wie viele pro Seite angezeigt werden soll. Die Anzahl der Seiten ergibt sich durch Division. Nachdem
         * es keine halben Seiten geben kann, werden wir mit der ceil()-Funktion auf die nächste ganze Zahl aufrunden.
         */
        $count = Post::count();
        $itemsPerPage = Config::get('app.items-per-page');
        $numerOfPages = ceil($count / $itemsPerPage);

        /**
         * Um nicht in jeder Action den Header und den Footer und dann den View laden zu müssen, haben wir uns eine View
         * Klasse gebaut. Als ersten Parameter übergeben wir den Namen des Template Files inkl. Pfad und als zweiten
         * Parameter alle Werte aus dem Controller, die im Template als eigene Variablen verfügbar sein sollen.
         */
        View::render('blog/index', [
            'posts' => $posts,
            'page' => $page,
            'numberOfPages' => $numerOfPages
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
