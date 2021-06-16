<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Core\Helpers\Redirector;
use Core\Middlewares\AuthMiddleware;
use Core\Session;
use Core\Validator;

/**
 * Class CommentController
 *
 * @package App\Controllers
 */
class CommentController
{

    /**
     * Daten aus Kommentarformular entgegennehmen und speichern.
     *
     * @param int $id Post ID
     */
    public function create (int $id)
    {
        /**
         * Prüfen, ob eine Person eingeloggt ist und Fehler ausgeben, wenn nein.
         */
        AuthMiddleware::isLoggedInOrFail();

        /**
         * @todo: comment
         */
        $validator = new Validator();
        $validator->int((int)$_POST['rating'], 'Rating', false, 1, 5);

        /**
         * Fehler Array vorbereiten.
         */
        $errors = $validator->getErrors();

        /**
         * Wenn kein oder ein leerer Kommentar-Text übergeben wurden, schrieben wir einen Fehler.
         */
        if (!isset($_POST['comment']) || empty($_POST['comment'])) {
            $errors[] = 'Der Kommentar-Text darf nicht leer sein.';
        }

        /**
         * Hier würden noch weitere Validierungen durchgeführt werden, wie beispielsweise Profanity Checks.
         */

        /**
         * Post anhand des Route-Parameters aus der Datenbank laden.
         */
        $post = Post::findOrFail($id);

        /**
         * Sind Fehler aufgetreten ...
         */
        if (!empty($errors)) {
            /**
             * ... so speichern wir sie in die Session und leiten zurück zum Post. Hier ist zu beachten, dass wir den Slug brauchen, um einen einzelnen Post anzeigen zu können.
             */
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . '/blog/' . $post->slug);
        }

        /**
         * Kommen wir im Programmablauf bis hier her, so ist die Validierung erfolgreich durchgelaufen und es sind keine Fehler aufgetreten.
         *
         * Wir erstellen ein neues Kommentar Objekt und weisen die ID des/der aktuell eingeloggten User*in als Autor und die ID des Posts, der in der Route definiert wurde, zu.
         */
        $comment = new Comment();
        $comment->author = User::getLoggedIn()->id;
        $comment->post_id = $post->id;
        $comment->content = $_POST['comment'];
        $comment->rating = $_POST['rating'];

        /**
         * Um nur eine einzelne Action für die Erstellung von Top Level Kommentaren und Antworten auf Kommentare zu
         * benötigen, haben wir im Formular für Kommentare auf Antworten ein Input Feld vom Typ "hidden", damit wir die
         * ID vom "Parent Post" im Formular übergeben können. Ist dieses "hidden" Feld gesetzt und enthält einen
         * numerischen Wert (ID des Eltern-Kommentars), so setzen wir diese ID in den gerade erstellen Reply-Kommentar.
         */
        if (isset($_POST['parent-comment']) && is_numeric($_POST['parent-comment'])) {
            $comment->parent = (int)$_POST['parent-comment'];
        }

        /**
         * Neu erstelles Kommentar-Objekt in die Datenbank speichern.
         */
        $comment->save();

        /**
         * Erfolgsmeldung in die Session speichern und zum Post zurückleiten, wo der Kommentar angezeigt werden wird.
         * Beachte, dass wir zum HTML-Element mit der ID comment-{id} redirecten, damit der Browser direkt zum neu
         * erstellen Kommentar scrollt.
         */
        Session::set('success', ['Kommentar erfolgreich gespeichert.']);
        Redirector::redirect(BASE_URL . "/blog/{$post->slug}#comment-{$comment->id}");
    }

}
