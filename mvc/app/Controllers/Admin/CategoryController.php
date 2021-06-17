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
 */
class CategoryController
{

    /**
     * Alle Kategorien listen.
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
         * Alle Categories über das Category-Model aus der Datenbank laden.
         */
        $categories = Category::all();

        /**
         * View laden.
         */
        View::render('admin/categories/index', [
            'categories' => $categories
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
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::findOrFail($id);

        /**
         * View laden.
         */
        View::render('admin/categories/edit', [
            'category' => $category
        ], 'sidebar');
    }

    /**
     * Formulardaten aus dem Bearbeitungsformular entgegennehmen und verarbeiten.
     *
     * [x] Prüfen ob der/die User*in ein Admin ist
     * [x] zu ändernde Category aus DB laden
     * [x] Category aktualisieren
     * [x] Category speichern
     * [x] Erfolgsmeldung schreiben
     * [x] Redirect
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
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::findOrFail($id);

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
             * Sind keine Fehler aufgetreten aktualisieren wir die Werte des vorher geladenen Objekts ...
             */
            $category->title = trim($_POST['title']);
            /**
             * Wurde ein Slug im Formular angegeben ...
             */
            if (isset($_POST['slug']) && !empty($_POST['slug'])) {
                /**
                 * ... so verwenden wir diesen, ...
                 */
                $category->slug = trim($_POST['slug']);
            } else {
                /**
                 * ... andernfalls generieren wir einen neuen.
                 */
                $category->createSlug();
            }
            $category->description = trim($_POST['description']);

            /**
             * ... und speichern das aktualisierte Objekt in die Datenbank zurück.
             */
            $category->save();

            /**
             * Nun speichern wir eine Erfolgsmeldung in die Session ...
             */
            Session::set('success', ['Erfolgreich gespeichert.']);
        }

        /**
         * ... und leiten in jedem Fall zurück zum Bearbeitungsformular - entweder mit Fehlern oder mit Erfolgsmeldung.
         */
        Redirector::redirect(BASE_URL . "/admin/categories/{$category->id}/edit");
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
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::findOrFail($id);

        /**
         * View laden und relativ viele Daten übergeben. Die große Anzahl an Daten entsteht dadurch, dass der
         * admin/confirm-View so dynamisch wie möglich sein soll, damit wir ihn für jede Delete Confirmation Seite
         * verwenden können, unabhängig vom Objekt, das gelöscht werden soll. Wir übergeben daher einen Typ und einen
         * Titel, die für den Text der Confirmation verwendet werden, und zwei URLs, eine für den Bestätigungsbutton und
         * eine für den Abbrechen-Button.
         */
        View::render('admin/confirm', [
            'type' => 'Category',
            'title' => $category->title,
            'confirmUrl' => BASE_URL . "/admin/categories/{$category->id}/delete/confirm",
            'abortUrl' => BASE_URL . "/admin/categories"
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
         * Gewünschte Category über das Category-Model aus der Datenbank laden.
         */
        $category = Category::findOrFail($id);

        /**
         * Category löschen. Je nachdem ob das Objekt, das gelöscht wird, den SoftDelete-Trait verwendet oder nicht,
         * wird das Objekt wirklich komplett aus der Datenbank gelöscht oder eben nur auf deleted gesetzt.
         */
        $category->delete();

        /**
         * Zur Kategorie-Liste zurück leiten.
         */
        Redirector::redirect(BASE_URL . '/admin/categories');
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
         * View laden.
         */
        View::render('admin/categories/new', layout: 'sidebar');
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
            Redirector::redirect(BASE_URL . "/admin/categories/new");
        } else {
            /**
             * Sind keine Fehler aufgetreten legen wir ein neues Objekt an ...
             */
            $category = new Category();
            $category->title = trim($_POST['title']);
            /**
             * Wurde ein Slug im Formular angegeben ...
             */
            if (isset($_POST['slug']) && !empty($_POST['slug'])) {
                /**
                 * ... so verwenden wir diesen, ...
                 */
                $category->slug = trim($_POST['slug']);
            } else {
                /**
                 * ... andernfalls generieren wir einen neuen.
                 */
                $category->createSlug();
            }
            $category->description = trim($_POST['description']);

            /**
             * ... und speichern das aktualisierte Objekt in die Datenbank zurück.
             */
            $category->save();

            /**
             * Nun speichern wir eine Erfolgsmeldung in die Session ...
             */
            Session::set('success', ['Erfolgreich gespeichert.']);

            /**
             * ... und leiten im Erfolgsfall zurück zum Bearbeitungsformular - entweder mit Fehlern oder mit
             * Erfolgsmeldung.
             */
            Redirector::redirect(BASE_URL . "/admin / categories /{
                $category->id}/edit");
        }
    }

    /**
     * Validierungen kapseln, damit wir sie überall dort, wo wir derartige Objekte validieren müssen, verwenden können.
     *
     * @return array
     */
    public function validateFormData (): array
    {
        /**
         * Daten aus dem Formular validieren.
         *
         * Auch hier verwenden wir wieder die PHP 8 "named params", damit wir für "title" eine Maximum definieren
         * können, ohne ein Minimum definieren zu müssen.
         */
        $validator = new Validator();
        $validator->textnum($_POST['title'], 'Title', true, max: 255);
        $validator->slug($_POST['slug'], 'Slug', false, max: 255);
        /**
         * Hier müssten wir eigentlich die textarea validieren, wir haben aber den CKEditor eingebaut, damit wir einen
         * Rich Text Editor statt einer normalen Textarea verwenden können und dadurch müssten wir eine Validierung auf
         * valides HTML durchführen, was tricky ist. Daher verzichten wir hier mal auf die Validierung - mit dem Hinweis,
         * dass das nicht schön ist und eigentlich gelöst werden müsste.
         */
//        $validator->textnum($_POST['description'], 'Beschreibung');

        /**
         * Fehler aus dem Validator holen und direkt zurückgeben.
         */
        return $validator->getErrors();
    }

}
