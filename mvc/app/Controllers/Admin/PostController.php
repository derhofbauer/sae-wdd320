<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Core\Helpers\Redirector;
use Core\Middlewares\AuthMiddleware;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class PostController
 *
 * @package app\Controllers\Admin
 * @todo: comment
 */
class PostController
{
    /**
     * Alle Posts listen.
     */
    public function index () {
        /**
         * @todo:comment
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Alle Posts über das Post-Model aus der Datenbank laden.
         */
        $posts = Post::all();

        /**
         * View laden
         */
        View::render('admin/posts/index', [
            'posts' => $posts
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
         * Gewünschten Post über das Post-Model aus der Datenbank laden.
         */
        $post = Post::findOrFail($id);
        $admins = User::findWhere('is_admin', '1');
        $categories = Category::all();

        /**
         * View laden
         */
        View::render('admin/posts/edit', [
            'post' => $post,
            'admins' => $admins,
            'categories' => $categories
        ], 'sidebar');
    }

    /**
     * @param int $id
     * @todo: comment
     */
    public function update (int $id) {
        /**
         * @todo:comment
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * @todo: handle categories from form
         */

        /**
         * Daten aus dem Formular validieren.
         * @todo: comment (named params)
         */
        $validator = new Validator();
        $validator->textnum($_POST['title'], 'Title', true, max: 255);
        $validator->slug($_POST['slug'], 'Slug', true, 1, 255);
        $validator->textnum($_POST['content'], 'Content');
        $validator->int((int)$_POST['author'], 'Autor', true);
        $errors = $validator->getErrors();
        if (empty(User::find($_POST['author']))) {
            $errors[] = 'Dieser User existiert nicht.';
        }

        /**
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $post = Post::findOrFail($id);

        if (!empty($errors)) {
            Session::set('errors', $errors);
        } else {
            $post->title = trim($_POST['title']);
            $post->slug = trim($_POST['slug']);
            $post->content = trim($_POST['content']);
            $post->author = trim($_POST['author']);

            $post->save();

            Session::set('success', ['Erfolgreich gespeichert.']);
        }

        Redirector::redirect(BASE_URL . "/admin/posts/{$post->id}/edit");
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
        $post = Post::findOrFail($id);

        /**
         * View laden
         */
        View::render('admin/confirm', [
            'type' => 'Post',
            'title' => $post->title,
            'confirmUrl' => BASE_URL . "/admin/posts/{$post->id}/delete/confirm",
            'abortUrl' => BASE_URL . "/admin/posts"
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
        $post = Post::findOrFail($id);
        $post->delete();

        Redirector::redirect(BASE_URL . '/admin/posts');
    }

}
