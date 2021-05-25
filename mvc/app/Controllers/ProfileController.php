<?php

namespace App\Controllers;

use App\Controllers\Admin\UserController;
use App\Models\User;
use Core\Helpers\Redirector;
use Core\Session;
use Core\View;

/**
 * Class ProfileController
 *
 * @package App\Controllers
 */
class ProfileController
{

    /**
     * Profil Bearbeitungsformular anzeigen.
     */
    public function edit ()
    {
        /**
         * Aktuell eingeloggte*n User*in aus der Datenbank abfragen. Wird der Eintrag nicht gefunden, wird ein Fehler
         * 404 zurückgegeben.
         */
        $user = User::getLoggedIn();

        /**
         * View laden und Daten übergeben.
         */
        View::render('profile', [
            'user' => $user
        ]);
    }

    /**
     * Aktuell eingeloggte*n User*in mit den Daten aus dem Bearbeitungsformular aktualisieren.
     *
     * @throws \Exception
     */
    public function update ()
    {
        /**
         * Nachdem wir exakt die selben Validierungen durchführen für update und create, können wir sie in eine eigene
         * Methode auslagern und überall dort verwenden, wo wir sie brauchen.
         */
        $errors = UserController::validateFormData();

        /**
         * Aktuell eingeloggte*n User*in aus der Datenbank abfragen. Wird der Eintrag nicht gefunden, wird ein Fehler
         * 404 zurückgegeben.
         */
        $user = User::getLoggedIn();

        /**
         * Nachdem die Schritte zur Verarbeitung und Speicherung von Avatar Bildern immer völlig ident sind, können wir
         * sie in eine eigene Methode auslagern und überall dort verwenden, wo wir sie brauchen.
         */
        $errors = UserController::handleAvatarUpload($user, $errors);

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
            $user->email = trim($_POST['email']);
            /**
             * Wurde ein Username über das Formular geschickt, trimmen wir ihn und überschreiben den aktuellen Wert.
             */
            if (!empty($_POST['username'])) {
                $user->username = trim($_POST['username']);
            }
            /**
             * Wurde ein Passwort geschickt, so setzen wir es neu. Dadurch wird ein Hash erstellt und dieser dann in die
             * Datenbank gespeichert.
             */
            if (!empty($_POST['password'])) {
                $user->setPassword($_POST['password']);
            }
            /**
             * User*in aktualisieren.
             */
            $user->save();

            /**
             * Nun speichern wir eine Erfolgsmeldung in die Session ...
             */
            Session::set('success', ['Erfolgreich gespeichert.']);
        }

        /**
         * ... und leiten in jedem Fall zurück zum Bearbeitungsformular - entweder mit Fehlern oder mit Erfolgsmeldung.
         */
        Redirector::redirect(BASE_URL . "/profile");
    }
}
