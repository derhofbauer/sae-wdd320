<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Post;
use Core\View;

/**
 * Class CategoryController
 *
 * @package App\Controllers
 */
class CategoryController
{

    /**
     * Alle Categories listen.
     */
    public function index ()
    {
        /**
         * Alle Categories über das Category-Model aus der Datenbank laden.
         */
        $categories = Category::all();

        /**
         * View laden.
         */
        View::render('categories/index', [
            'categories' => $categories
        ]);
    }

    /**
     * Einzelne Category anzeigen.
     *
     * [x] Alle Posts einer Kategorie gelistet bekommen
     * [x] Aus einem einzelnen Post auf alle Posts einer seiner Kategorien kommen
     *
     * @param string $slug
     */
    public function show (string $slug)
    {
        /**
         * Um alle Posts zu einer Kategorie abrufen zu können, müssen wir zuerst herausfinden, welche ID die Category
         * hat, die zu dem übergebenen $slug gehört. Das können wir über die findBySlug()-Methode machen, die über den
         * HasSlug-Trait ins Category Model importiert wurde.
         */
        $category = Category::findBySlug($slug);

        /**
         * Nun können wir mit der abgerufenen Category und ihrer ID, alle Posts in dieser Category abfragen.
         */
        $posts = Post::findByCategory($category->id);

        /**
         * View laden und Daten übergeben.
         */
        View::render('categories/show', [
            'category' => $category,
            'posts' => $posts
        ]);
    }

}
