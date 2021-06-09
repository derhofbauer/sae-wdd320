<?php

namespace App\Controllers\Admin;

use App\Models\File;
use Core\Helpers\Redirector;
use Core\Middlewares\AuthMiddleware;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class MediaController
 *
 * @package App\Controllers\Admin
 */
class MediaController
{

    /**
     * Wir haben die Möglichkeit die Prüfung, ob ein*e User*in eingeloggt ist und die richtigen Berechtigungen hat, auch
     * hier im Konstruktor anzugeben. Nachdem der Konstruktur ausgeführt wird, sobald die Klasse instanziiert wird, wird
     * er auch vor allen anderen Methoden ausgeführt, wodurch wir alle Methoden in dem Controller "schützen" können,
     * ohne in jeder einzelnen Methode wieder die Middleware aufrufen zu müssen.
     */
    public function __construct ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();
    }

    /**
     * Alle Bilder listen.
     */
    public function index ()
    {
        /**
         * Alle Bilder, die kein Avatar Bild sind, finden.
         */
        $files = File::findWhere('is_avatar', 0);

        /**
         * View laden und Daten übergeben
         */
        View::render('admin/media/index', [
            'files' => $files
        ], 'sidebar');
    }

    /**
     * Bearbeitungsformular anzeigen.
     *
     * @param int $id
     */
    public function edit (int $id)
    {
        /**
         * Gewünschtes File über das File-Model aus der Datenbank laden.
         */
        $file = File::find($id);

        /**
         * View laden.
         */
        View::render('admin/media/edit', [
            'file' => $file
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
         * Nachdem wir exakt die selben Validierungen durchführen für update und create, können wir sie in eine eigene
         * Methode auslagern und überall dort verwenden, wo wir sie brauchen.
         */
        $errors = $this->validateFormData();

        /**
         * Gewünschtes File über das File-Model aus der Datenbank laden.
         */
        $file = File::findOrFail($id);

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
            $file->title = trim($_POST['title']);
            $file->alttext = trim($_POST['alttext']);
            $file->caption = trim($_POST['caption']);

            /**
             * ... und speichern das aktualisierte Objekt in die Datenbank zurück.
             */
            $file->save();

            /**
             * Nun speichern wir eine Erfolgsmeldung in die Session ...
             */
            Session::set('success', ['Erfolgreich gespeichert.']);
        }

        /**
         * ... und leiten in jedem Fall zurück zum Bearbeitungsformular - entweder mit Fehlern oder mit Erfolgsmeldung.
         */
        Redirector::redirect(BASE_URL . "/admin/media/{$file->id}/edit");
    }

    /**
     * Formular zum Upload von Dateien anzeigen.
     */
    public function new ()
    {
        /**
         * View laden.
         */
        View::render('admin/media/new', layout: 'sidebar');
    }

    /**
     * Daten aus dem Formular für neue Dateien verarbeiten.
     */
    public function create ()
    {
        /**
         * Datei Objekte aus der $_FILES Superglobal generieren, damit wir einfach damit arbeiten können.
         */
        $uploadedFiles = File::createFromUpload('files');

        /**
         * Fehler Array vorbereiten.
         */
        $errors = [];

        /**
         * Alle hochgeladenen Dateien einzelne verarbeiten.
         */
        foreach ($uploadedFiles as $file) {
            /**
             * Ist ein Fehler aufgetreten UND/ODER die Validierung auf ein Bild fehlgeschlagen ...
             */
            if ($file->hasUploadError() || !$file->validateImage()) {
                /**
                 * ... so schreiben wir eine Fehlermeldung.
                 */
                $errors[] = "Der Dateiupload für die Datei $file->name ist fehlgeschlagen :(";

                /**
                 * Wir holen außerdem die Validierungsfehler aus dem File Objekt und fügen sie in unseren $errors Array
                 * ein.
                 */
                $errors = array_merge($errors, $file->getErrors());
            } else {
                /**
                 * Sind keine Fehler aufgetreten, so speichern wir die Datei in den Standard Upload Ordner, der in der
                 * config/app.php definiert ist.
                 */
                $file->put();
            }
        }

        /**
         * Sind irgendwo Fehler aufgetreten ...
         */
        if (!empty($errors)) {
            /**
             * ... speichern wir sie in die Session und leiten zurück zum Formular.
             */
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . '/admin/media/new');
        }

        /**
         * Im Erfolgsfall schreiben wir eine Erfolgsmeldung in die Session und leiten zur Übersicht der Medien.
         */
        Session::set('success', ['Die Dateien wurden erfolgreich hochgeladen.']);
        Redirector::redirect(BASE_URL . '/admin/media');
    }

    /**
     * Confirm-Seite für mehrere ausgewählte Bilder generieren.
     */
    public function deleteMultipleConfirm ()
    {
        /**
         * Variablen, die wir brauchen werden, vorbereiten.
         */
        $titles = [];
        $ids = [];

        /**
         * Alle Dateien, die gelöscht werden sollen (s. Checkboxen in der Medienübersicht), durchgehen ...
         */
        foreach ($_POST['delete-file'] as $id => $on) {
            /**
             * ... zugehörige Daten aus der Datenbank holen und $name und $id für später speichern.
             */
            $file = File::findOrFail($id);
            $titles[] = $file->name;
            $ids[] = $file->id;
        }

        /**
         * Alle $titles zusammenfügen, damit wir sie im Text der Confirmation Page ausgeben können.
         */
        $title = implode(', ', $titles);
        /**
         * Alle $ids zusammenfügen, damit wir sie als Route-Parameter für den Löschen Button übergeben können. Hier ist
         * zu beachten, dass wir alle zu löschenden IDs als ein einziger kommaseparierter String übergeben, weil wir
         * in Routen keine dynamische Anzahl von Parametern haben können und aber eine dynamische Anzahl an zu löschenden
         * Dateien behandeln müssen.
         */
        $id = implode(',', $ids);

        /**
         * Confirmation View laden und Daten übergeben.
         */
        View::render('admin/confirm', [
            'type' => 'Medien',
            'title' => $title,
            'confirmUrl' => BASE_URL . "/admin/media/$id/delete-multiple/confirm",
            'abortUrl' => BASE_URL . "/admin/media"
        ], 'sidebar');
    }

    /**
     * Dateien löschen.
     */
    public function deleteMultiple (string $ids)
    {
        /**
         * Wir holen uns zunächst die kommaseparierten IDs aus dem Route Parameter und generieren ein Array daraus.
         */
        $ids = explode(',', $ids);
        /**
         * Dann konvertieren wir alle Elemente aus dem Array, die aktuell numerische Strings sind, in Integers um.
         */
        $ids = array_map(function ($id) {
            return (int)$id;
        }, $ids);

        /**
         * Nun gehen wir alle $ids durch, suchen die Daten in der Datenbank und softdeleten sie.
         */
        foreach ($ids as $id) {
            $file = File::findOrFail($id);
            /**
             * Die File::deleteFile()-Methode führt einen Softdelete auf die Daten in der Datenbank durch und verschiebt
             * die Datei physisch in einen _trash_ Ordner.
             */
            $file->deleteFile();
        }

        /**
         * Nun leiten wir zurück zur Medien Übersicht.
         */
        Redirector::redirect(BASE_URL . '/admin/media');
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
        $validator->textnum($_POST['title'], 'Title', max: 255);
        $validator->textnum($_POST['alttext'], 'Alternative Text');
        /**
         * Hier müssten wir eigentlich die textarea validieren, wir haben aber den CKEditor eingebaut, damit wir einen
         * Rich Text Editor statt einer normalen Textarea verwenden können und dadurch müssten wir eine Validierung auf
         * valides HTML durchführen, was tricky ist. Daher verzichten wir hier mal auf die Validierung - mit dem Hinweis,
         * dass das nicht schön ist und eigentlich gelöst werden müsste.
         */
//        $validator->textnum($_POST['caption'], 'Caption');

        /**
         * Fehler aus dem Validator holen und direkt zurückgeben.
         */
        return $validator->getErrors();
    }
}
