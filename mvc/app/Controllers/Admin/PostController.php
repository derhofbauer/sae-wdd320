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
 */
class PostController
{
    /**
     * Alle Posts listen.
     */
    public function index ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Alle Posts über das Post-Model aus der Datenbank laden.
         */
        $posts = Post::all();

        /**
         * View laden und Daten übergeben.
         */
        View::render('admin/posts/index', [
            'posts' => $posts
        ], 'sidebar');
    }

    /**
     * Bearbeitungsformular anzeigen.
     */
    public function edit (int $id)
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Gewünschten Post über das Post-Model aus der Datenbank laden.
         */
        $post = Post::findOrFail($id);
        /**
         * Alle Admins aus der Datenbank laden, damit wir das Author-Dropdown befüllen können.
         */
        $admins = User::findWhere('is_admin', '1');
        /**
         * Alle Categories laden, damit wir die Category-Checkboxes generieren können.
         */
        $categories = Category::all();

        /**
         * View laden und Daten übergeben.
         */
        View::render('admin/posts/edit', [
            'post' => $post,
            'admins' => $admins,
            'categories' => $categories
        ], 'sidebar');
    }

    /**
     * Formulardaten aus dem Bearbeitungsformular entgegennehmen und verarbeiten.
     *
     * @param int $id
     */
    public function update (int $id)
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Nachdem wir exakt die selben Validierungen durchführen für update und create, können wir sie in eine eigene
         * Methode auslagern und überall dort verwenden, wo wir sie brauchen.
         */
        $errors = $this->validateFormData();

        /**
         * Gewünschten Post über das Post-Model aus der Datenbank laden. Hier verwenden wir die findOrFail()-Methode aus
         * dem AbstractModel, die einen 404 Fehler ausgibt, wenn das Objekt nicht gefunden wird. Dadurch sparen wir uns
         * hier zu prüfen ob ein Post gefunden wurde oder nicht.
         */
        $post = Post::findOrFail($id);

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session um sie in den Views dann ausgeben zu können.
             */
            Session::set('errors', $errors);
        } else {
            /**
             * Sind keine Fehler aufgetreten legen aktualisieren wir die Werte des vorher geladenen Objekts ...
             */
            $post->title = trim($_POST['title']);
            $post->slug = trim($_POST['slug']);
            $post->content = trim($_POST['content']);
            $post->author = trim($_POST['author']);
            /**
             * ... und speichern das aktualisierte Objekt in die Datenbank zurück.
             */
            $post->save();

            /**
             * Category Selections speichern.
             *
             * Wurden Kategorien im Formular ausgewählt, so holen wir hier die gewählten IDs und überschreiben die
             * aktuell verknüpften Kategorien. Andernfalls löschen wir alle Kategoriezuweisungen, weil alle Checkboxen
             * abgewählt wurden.
             */
            if (isset($_POST['categories'])) {
                $idsOfSelectedCategories = array_keys($_POST['categories']);
                $post->setCategories($idsOfSelectedCategories);
            } else {
                $post->setCategories([]);
            }

            /**
             * Nun speichern wir eine Erfolgsmeldung in die Session ...
             */
            Session::set('success', ['Erfolgreich gespeichert.']);
        }

        /**
         * ... und leiten in jedem Fall zurück zum Bearbeitungsformular - entweder mit Fehlern oder mit Erfolgsmeldung.
         */
        Redirector::redirect(BASE_URL . "/admin/posts/{$post->id}/edit");
    }

    /**
     * Abfrage, ob das Objekt wirklich gelöscht werden soll.
     *
     * @param int $id
     */
    public function deleteConfirm (int $id)
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Gewünschten Post über das Post-Model aus der Datenbank laden. Hier verwenden wir die findOrFail()-Methode aus
         * dem AbstractModel, die einen 404 Fehler ausgibt, wenn das Objekt nicht gefunden wird. Dadurch sparen wir uns
         * hier zu prüfen ob ein Post gefunden wurde oder nicht.
         */
        $post = Post::findOrFail($id);

        /**
         * View laden und relativ viele Daten übergeben. Die große Anzahl an Daten entsteht dadurch, dass der
         * admin/confirm-View so dynamisch wie möglich sein soll, damit wir ihn für jede Delete Confirmation Seite
         * verwenden können, unabhängig vom Objekt, das gelöscht werden soll. Wir übergeben daher einen Typ und einen
         * Titel, die für den Text der Confirmation verwendet werden, und zwei URLs, eine für den Bestätigungsbutton und
         * eine für den Abbrechen-Button.
         */
        View::render('admin/confirm', [
            'type' => 'Post',
            'title' => $post->title,
            'confirmUrl' => BASE_URL . "/admin/posts/{$post->id}/delete/confirm",
            'abortUrl' => BASE_URL . "/admin/posts"
        ], 'sidebar');
    }

    /**
     * Objekt wirklich löschen.
     *
     * @param int $id
     */
    public function delete (int $id)
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Gewünschten Post über das Post-Model aus der Datenbank laden. Hier verwenden wir die findOrFail()-Methode aus
         * dem AbstractModel, die einen 404 Fehler ausgibt, wenn das Objekt nicht gefunden wird. Dadurch sparen wir uns
         * hier zu prüfen ob ein Post gefunden wurde oder nicht.
         */
        $post = Post::findOrFail($id);

        /**
         * Post löschen. Je nachdem ob das Objekt, das gelöscht wird, den SoftDelete-Trait verwendet oder nicht, wird
         * das Objekt wirklich komplett aus der Datenbank gelöscht oder eben nur auf deleted gesetzt.
         */
        $post->delete();

        /**
         * Zur Post-Liste zurück leiten.
         */
        Redirector::redirect(BASE_URL . '/admin/posts');
    }

    /**
     * Formular für neues Element anzeigen.
     */
    public function new ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Alle Admins aus der Datenbank laden, damit wir das Author-Dropdown befüllen können.
         */
        $admins = User::findWhere('is_admin', '1');
        /**
         * Alle Categories laden, damit wir die Category-Checkboxes generieren können.
         */
        $categories = Category::all();

        /**
         * View laden.
         */
        View::render('admin/posts/new', [
            'admins' => $admins,
            'categories' => $categories
        ], 'sidebar');
    }

    /**
     * Daten aus dem Formular für neue Elemente entgegennehmen.
     */
    public function create ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();

        /**
         * Nachdem wir exakt die selben Validierungen durchführen für update und create, können wir sie in eine eigene
         * Methode auslagern und überall dort verwenden, wo wir sie brauchen.
         */
        $errors = $this->validateFormData();

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session um sie in den Views dann ausgeben zu können.
             */
            Session::set('errors', $errors);

            /**
             * Im Fehlerfall leiten wir zurück zum Formular und geben die Fehlermeldungen aus.
             */
            Redirector::redirect(BASE_URL . "/admin/posts/new");
        } else {
            /**
             * Sind keine Fehler aufgetreten legen aktualisieren wir die Werte des vorher geladenen Objekts ...
             */
            $post = new Post();
            $post->title = trim($_POST['title']);
            $post->slug = trim($_POST['slug']);
            $post->content = trim($_POST['content']);
            $post->author = trim($_POST['author']);
            /**
             * ... und speichern das aktualisierte Objekt in die Datenbank zurück.
             */
            $post->save();

            /**
             * Category Selections speichern.
             *
             * Wurden Kategorien im Formular ausgewählt, so holen wir hier die gewählten IDs und überschreiben die
             * aktuell verknüpften Kategorien. Andernfalls löschen wir alle Kategoriezuweisungen, weil alle Checkboxen
             * abgewählt wurden.
             */
            if (isset($_POST['categories'])) {
                $idsOfSelectedCategories = array_keys($_POST['categories']);
                $post->setCategories($idsOfSelectedCategories);
            } else {
                $post->setCategories([]);
            }

            /**
             * Nun speichern wir eine Erfolgsmeldung in die Session ...
             */
            Session::set('success', ['Erfolgreich gespeichert.']);

            /**
             * ... und leiten im Erfolgsfall zurück zum Bearbeitungsformular - entweder mit Fehlern oder mit
             * Erfolgsmeldung.
             */
            Redirector::redirect(BASE_URL . "/admin/posts/{$post->id}/edit");
        }
    }

    /**
     * Validierungen kapseln, damit wir sie überall dort, wo wir derartige Objekte validieren müssen, verwenden können.
     *
     * @return array
     */
    private function validateFormData (): array
    {
        /**
         * Daten aus dem Formular validieren.
         *
         * Auch hier verwenden wir wieder die PHP 8 "named params", damit wir für "title" eine Maximum definieren
         * können, ohne ein Minimum definieren zu müssen.
         */
        $validator = new Validator();
        $validator->textnum($_POST['title'], 'Title', true, max: 255);
        $validator->slug($_POST['slug'], 'Slug', true, 1, 255);
        $validator->textnum($_POST['content'], 'Content');
        $validator->int((int)$_POST['author'], 'Autor', true);

        /**
         * Categories validieren.
         *
         * Es kann sein, dass keine Kategorien im Formular angewählt werden und dadurch kein Wert übergeben wird. In
         * diesem Fall wollen wir auch nicht validieren, weil es nichts gibt, was wir validieren könnten.
         */
        if (isset($_POST['categories'])) {
            foreach ($_POST['categories'] as $categoryId => $on) {
                $validator->checkbox($on, "Category $categoryId");
            }
        }

        /**
         * Fehler aus dem Validator holen.
         */
        $errors = $validator->getErrors();

        /**
         * Hier suchen wir den User, der als Author übergeben wurde. Wird ein leeres Ergebnis zurückgegeben und der User
         * somit nicht gefunden, schreiben wir einen Fehler.
         */
        if (empty(User::find($_POST['author']))) {
            $errors[] = 'Dieser User existiert nicht.';
        }

        /**
         * Fehler zurückgeben.
         */
        return $errors;
    }

}
