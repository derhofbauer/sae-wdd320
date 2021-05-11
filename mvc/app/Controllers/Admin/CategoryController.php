<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use Core\Helpers\Redirector;
use Core\Session;
use Core\View;

/**
 * Class CategoryController
 *
 * @package App\Controllers\Admin
 * @todo    : comment
 */
class CategoryController
{

    /**
     * Alle Kategorien listen.
     */
    public function index ()
    {
        /**
         * Alle Categories über das Category-Model aus der Datenbank laden.
         */
        $categories = Category::all();

        /**
         * View laden
         */
        View::render('admin/categories/index', [
            'categories' => $categories
        ], 'sidebar');
    }

    /**
     * @todo: comment
     */
    public function edit (int $id)
    {
        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::find($id);

        /**
         * View laden
         */
        View::render('admin/categories/edit', [
            'category' => $category
        ], 'sidebar');
    }

    /**
     * @todo: comment
     *
     * @todo: [ ] Prüfen ob der User ein Admin ist
     * [x] zu ändernde Category aus DB laden
     * [x] Category aktualisieren
     * [x] Category speichern
     * [x] Erfolgsmeldung schreiben
     * [x] Redirect
     */
    public function update (int $id) {
        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::find($id);

        $category->title = trim($_POST['title']);
        $category->slug = trim($_POST['slug']);
        $category->description = trim($_POST['description']);

        $category->save();

        Session::set('success', ['Erfolgreich gespeichert.']);
        Redirector::redirect(BASE_URL . "/admin/categories/{$category->id}/edit");
    }

}
