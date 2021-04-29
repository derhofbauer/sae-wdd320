<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Post;
use Core\View;

/**
 * Class CategoryController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class CategoryController
{

    public function index ()
    {
        $categories = Category::all();

        View::render('categories/index', [
            'categories' => $categories
        ]);
    }

    /**
     * [ ] Alle Posts einer Kategorie gelistet bekommen
     * [ ] Aus einem einzelnen Post auf alle Posts einer seiner Kategorien kommen
     */
    public function show (string $slug)
    {
        $category = Category::findBySlug($slug);
        $posts = Post::findByCategory($category->id);

        // sortieren

        View::render('categories/show', [
            'category' => $category,
            'posts' => $posts
        ]);
    }

}
