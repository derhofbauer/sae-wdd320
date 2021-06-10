<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Core\Helpers\Redirector;
use Core\Middlewares\AuthMiddleware;
use Core\Session;

/**
 * Class CommentController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class CommentController
{

    /**
     * @param int $id Post ID
     */
    public function create (int $id)
    {
        AuthMiddleware::isLoggedInOrFail();

        $post = Post::findOrFail($id);

        $errors = [];

        if (!isset($_POST['comment']) || empty($_POST['comment'])) {
            $errors[] = 'Der Kommentar-Text darf nicht leer sein.';
        }

        /**
         * ... Validierungen! (z.b. Profanity)
         */

        if (!empty($errors)) {
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . '/blog/' . $post->slug);
        }

        $comment = new Comment();
        $comment->author = User::getLoggedIn()->id;
        $comment->post_id = $post->id;
        $comment->content = $_POST['comment'];

        if (isset($_POST['parent-comment']) && is_numeric($_POST['parent-comment'])) {
            $comment->parent = (int)$_POST['parent-comment'];
        }

        $comment->save();

        Session::set('success', ['Kommentar erfolgreich gespeichert.']);
        Redirector::redirect(BASE_URL . "/blog/{$post->slug}#comment-{$comment->id}");
    }

}
