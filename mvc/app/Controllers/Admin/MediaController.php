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
     * @todo: comment
     */
    public function new ()
    {
        /**
         * View laden.
         */
        View::render('admin/media/new', layout: 'sidebar');
    }

    /**
     * @todo: comment
     */
    public function create ()
    {
        $uploadedFiles = File::createFromUpload('files');

        $errors = [];

        foreach ($uploadedFiles as $file) {
            if ($file->hasUploadError() || !$file->validateImage()) {
                $errors[] = "Der Dateiupload für die Datei $file->name ist fehlgeschlagen :(";

                /**
                 * Wir holen die Validierungsfehler aus dem File Objekt und fügen sie in unseren $errors Array ein.
                 */
                $errors = array_merge($errors, $file->getErrors());
            } else {
                $file->put();
            }
        }

        if (!empty($errors)) {
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . '/admin/media/new');
        }

        Session::set('success', ['Die Dateien wurden erfolgreich hochgeladen.']);
        Redirector::redirect(BASE_URL . '/admin/media');
    }

    /**
     * @todo: comment
     */
    public function deleteMultipleConfirm ()
    {
        $titles = [];
        $ids = [];
        foreach ($_POST['delete-file'] as $id => $on) {
            $file = File::findOrFail($id);
            $titles[] = $file->name;
            $ids[] = $file->id;
        }

        $title = implode(', ', $titles);
        $id = implode(',', $ids);

        View::render('admin/confirm', [
            'type' => 'Medien',
            'title' => $title,
            'confirmUrl' => BASE_URL . "/admin/media/$id/delete-multiple/confirm",
            'abortUrl' => BASE_URL . "/admin/media"
        ], 'sidebar');
    }

    /**
     * @todo: comment
     */
    public function deleteMultiple (string $ids)
    {
        $ids = explode(',', $ids);
        $ids = array_map(function ($id) {
            return (int)$id;
        }, $ids);

        foreach ($ids as $id) {
            $file = File::findOrFail($id);
            $file->deleteFile();
        }

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
        $validator->textnum($_POST['caption'], 'Caption');

        /**
         * Fehler aus dem Validator holen und direkt zurückgeben.
         */
        return $validator->getErrors();
    }
}
