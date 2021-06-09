<?php

namespace App\Controllers\Admin;

use App\Models\Share;
use Core\Helpers\Redirector;
use Core\Middlewares\AuthMiddleware;
use Core\Session;
use Core\View;

/**
 * Class ShareController
 *
 * @package App\Controllers\Admin
 */
class ShareController
{

    /**
     * Wir haben die Möglichkeit die Prüfung, ob ein*e User*in eingeloggt ist und die richtigen Berechtigungen hat, auch
     * hier im Konstruktor anzugeben. Nachdem der Konstruktur ausgeführt wird, sobald die Klasse instanziiert wird, wird
     * er auch vor allen anderen Methoden ausgeführt, wodurch wir alle Methoden in dem Controller "schützen" können,
     * ohne in jeder einzelnen Methode wieder die Middleware aufrufen zu müssen.
     */
    public function __constructor ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und ob diese*r eingeloggte User*in Admin ist. Wenn nicht, geben wir
         * einen Fehler 403 Forbidden zurück. Dazu haben wir eine Art Middleware geschrieben, damit wir nicht immer das
         * selbe if-Statement kopieren müssen, sondern einfach diese Funktion aufrufen können.
         */
        AuthMiddleware::isAdminOrFail();
    }

    /**
     * Shares listen.
     */
    public function index ()
    {
        /**
         * Alle offenen Shares aus der Datenbank holen.
         */
        $shares = Share::allOpen();

        /**
         * View laden und Daten übergeben.
         */
        View::render('admin/shares/index', [
            'shares' => $shares
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
         * Share laden.
         */
        $share = Share::findOrFail($id);

        /**
         * View laden und Daten übergeben.
         */
        View::render('admin/shares/edit', [
            'share' => $share
        ]);
    }

    /**
     * Daten aus Bearbeitungsformular entgegennehmen.
     *
     * @param int $id
     */
    public function update (int $id)
    {
        /**
         * Alle Werte, die für das Status Feld möglich sind, aus dem Share Model holen.
         */
        $possibleStati = array_keys(Share::STATI);
        /**
         * Fehler Array vorbereiten.
         */
        $errors = [];

        /**
         * Wurde kein Status übergeben aus dem Formular oder findet sich der übergebene Wert nicht im Array der
         * möglichen Werte, schreiben wir einen Fehler in die Session und leiten zurück zum Bearbeitungsformular.
         */
        if (!isset($_POST['status']) || !in_array($_POST['status'], $possibleStati)) {
            $errors[] = 'Possible value for status are open, progress, storno and delivered.';
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . "/admin/shares/{$id}/edit");
        }

        /**
         * Ist kein Fehler aufgetreten laden wir den bearbeiteten Share aus der Datenbank, aktualisieren den Status und
         * speichern die Änderungen zurück.
         */
        $share = Share::findOrFail($id);
        $share->status = $_POST['status'];
        $share->save();

        /**
         * Abschließend schreiben wir eine Erfolgsmeldung in die Session und leiten zurück zur Übersichtsseite.
         */
        Session::set('success', ['Share erfolgreich aktualisiert.']);
        Redirector::redirect(BASE_URL . '/admin/shares');
    }

}
