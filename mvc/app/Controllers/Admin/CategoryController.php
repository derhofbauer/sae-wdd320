<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use Core\Helpers\Redirector;
use Core\Middlewares\AuthMiddleware;
use Core\Session;
use Core\Validator;
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
         * @todo:comment
         */
        AuthMiddleware::isAdminOrFail();

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
         * @todo:comment
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::findOrFail($id);

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
     * [x] Prüfen ob der User ein Admin ist
     * [x] zu ändernde Category aus DB laden
     * [x] Category aktualisieren
     * [x] Category speichern
     * [x] Erfolgsmeldung schreiben
     * [x] Redirect
     */
    public function update (int $id)
    {
        /**
         * @todo:comment
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Daten aus dem Formular validieren.
         *
         * @todo: comment (named params)
         */
        $validator = new Validator();
        $validator->textnum($_POST['title'], 'Title', true, max: 255);
        $validator->slug($_POST['slug'], 'Slug', true, 1, 255);
        $validator->textnum($_POST['description'], 'Beschreibung');
        $errors = $validator->getErrors();

        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::findOrFail($id);

        if (!empty($errors)) {
            Session::set('errors', $errors);
        } else {
            $category->title = trim($_POST['title']);
            $category->slug = trim($_POST['slug']);
            $category->description = trim($_POST['description']);

            $category->save();

            Session::set('success', ['Erfolgreich gespeichert.']);
        }

        Redirector::redirect(BASE_URL . "/admin/categories/{$category->id}/edit");
    }

    /**
     * @param int $id
     *
     * @todo: comment
     */
    public function deleteConfirm (int $id)
    {
        /**
         * @todo:comment
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::findOrFail($id);

        /**
         * View laden
         */
        View::render('admin/confirm', [
            'type' => 'Category',
            'title' => $category->title,
            'confirmUrl' => BASE_URL . "/admin/categories/{$category->id}/delete/confirm",
            'abortUrl' => BASE_URL . "/admin/categories"
        ], 'sidebar');
    }

    /**
     * @param int $id
     *
     * @todo: comment
     */
    public function delete (int $id)
    {
        /**
         * @todo:comment
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::findOrFail($id);
        $category->delete();

        Redirector::redirect(BASE_URL . '/admin/categories');
    }

}
