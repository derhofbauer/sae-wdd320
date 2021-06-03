<?php

namespace App\Controllers;

use App\Models\Favourite;
use App\Models\Share;
use App\Models\User;
use Core\Helpers\Redirector;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class FavouritesController
 *
 * @package App\Controllers
 */
class CheckoutController
{

    /**
     * Nur ein*e eingeloggt*e User*in kann einen Checkout Prozess starten.
     */
    public function __construct ()
    {
        if (!User::isLoggedIn()) {
            Redirector::redirect(BASE_URL);
        }
    }

    /**
     * Empfänger-Email Adresse erfassen
     */
    public function checkout ()
    {
        View::render('checkout/recipient');
    }

    /**
     * Daten aus dem Formular entgegennehmen und verarbeiten.
     */
    public function recipient ()
    {
        /**
         * Empfänger Name und E-Mail validieren.
         */
        $validator = new Validator();
        $validator->letters($_POST['name'], 'Name', true);
        $validator->email($_POST['email'], 'E-Mail', true);

        /**
         * Fehler aus dem Validator auslesen. Validator::getErrors() gibt uns dabei in jedem Fall ein Array zurück,
         * wenn keine Fehler aufgetreten sind, ist dieses Array allerdings leer.
         */
        $errors = $validator->getErrors();

        /**
         * Wenn der Fehler-Array nicht leer ist und es somit Fehler gibt ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session, damit sie im View ausgegeben werden können und leiten dann
             * zurück zum Formular.
             */
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . '/checkout');
        }

        /**
         * Aliases anlegen, damit wir weiter unten besser damit arbeiten können.
         */
        $name = $_POST['name'];
        $email = $_POST['email'];

        /**
         * Neuen Share anlegen und mit Daten befüllen.
         */
        $share = new Share();
        $share->user_id = User::getLoggedIn()->id;
        $share->recipient = "$name <{$email}>"; // Format: Arthur Dent <arthur.dent@galaxy.com>

        /**
         * Neuen Share in die Datenbank speichern.
         *
         * Die AbstractModel::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat.
         */
        if ($share->save()) {
            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zum nächsten Schritt.
             *
             * Um eine Erfolgsmeldung ausgeben zu können, verwenden wir die selbe Mechanik wie für die errors.
             *
             * Hier übergeben wir die Share ID in die URL, damit wir sie bis zum Ende des Checkouts immer weiter reichen
             * können und im jedem Schritt wissen, welchen Share wir grade bearbeiten.
             */
            Redirector::redirect(BASE_URL . "/checkout/2/{$share->id}");
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $errors[] = 'Die Empfängerinformationen konnten leider nicht gespeichert werden :(';
            Session::set('errors', $errors);

            /**
             * Redirect zurück zum Formular.
             */
            Redirector::redirect(BASE_URL . '/checkout');
        }
    }

    /**
     * Grußbotschaft für den Share eingeben.
     *
     * @param int $id
     */
    public function checkout2 (int $id)
    {
        /**
         * Share aus der Datenbank laden.
         */
        $share = Share::findOrFail($id);

        /**
         * View laden und Daten übergeben.
         */
        View::render('checkout/message', [
            'share' => $share
        ]);
    }

    /**
     * Daten aus Formular entgegennehmen und verarbeiten.
     *
     * @param int $id
     */
    public function message (int $id)
    {
        /**
         * Share aus der Datenbank laden.
         */
        $share = Share::findOrFail($id);

        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->textnum($_POST['message'], 'Message', true);

        /**
         * Fehler aus dem Validator auslesen. Validator::getErrors() gibt uns dabei in jedem Fall ein Array zurück,
         * wenn keine Fehler aufgetreten sind, ist dieses Array allerdings leer.
         */
        $errors = $validator->getErrors();

        /**
         * Wenn der Fehler-Array nicht leer ist und es somit Fehler gibt ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session, damit sie im View ausgegeben werden können und leiten dann
             * zurück zum Formular.
             */
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . "/checkout/2/{$share->id}");
        }

        /**
         * Share um die Formulardaten ergänzen.
         */
        $share->message = $_POST['message'];

        /**
         * Neuen Share in die Datenbank speichern.
         *
         * Die AbstractModel::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat.
         */
        if ($share->save()) {
            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zum Loginformular.
             *
             * Um eine Erfolgsmeldung ausgeben zu können, verwenden wir die selbe Mechanik wie für die errors.
             *
             * Auch hier reichen wir die Share ID wieder weiter.
             */
            Redirector::redirect(BASE_URL . "/checkout/summary/{$share->id}");
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $errors[] = 'Die Grußbotschaft konnten leider nicht gespeichert werden :(';
            Session::set('errors', $errors);

            /**
             * Redirect zurück zum Formular. Wir reichen die Share ID wieder zurück.
             */
            Redirector::redirect(BASE_URL . "/checkout/2/{$share->id}");
        }
    }

    /**
     * Übersicht anzeigen, bevor der Share abgeschlossen wird.
     *
     * @param int $id
     */
    public function summary (int $id)
    {
        /**
         * Alle benötigten Daten aus der Datenbank laden.
         */
        $share = Share::findOrFail($id);
        $user = User::getLoggedIn();
        $favourites = $user->favourites();

        /**
         * View laden und Daten übergeben.
         */
        View::render('checkout/summary', [
            'share' => $share,
            'user' => $user,
            'favourites' => $favourites
        ]);
    }

    /**
     * Share abschließen.
     *
     * @param int $id
     */
    public function finish (int $id)
    {
        /**
         * Alle benötigten Daten aus der Datenbank laden.
         */
        $share = Share::findOrFail($id);
        $user = User::getLoggedIn();
        $favourites = $user->favourites();

        /**
         * Array für die über die Favoriten verknüpften Posts vorbereiten.
         */
        $posts = [];

        /**
         * Posts aus allen Favoriten holen und in $posts speichern.
         */
        foreach ($favourites as $favourite) {
            $posts[] = $favourite->post();
        }

        /**
         * $posts in ein JSON umwandeln und in den Share hinzufügen, damit wir das ganze als Snapshot in die Datenbank
         * speichern können.
         */
        $json = json_encode($posts);
        $share->posts = $json;

        /**
         * Wenn der Speichervorgang funktioniert hat ...
         */
        if ($share->save()) {
            /**
             * ... löschen wir alle Favoriten dieses/r User*in, ...
             */
            Favourite::deleteWhereForeignKey('user_id', $user->id);

            /**
             * ... schreiben eine Erfolgsmeldung und leiten zur Share Übersicht.
             */
            Session::set('success', ['Posts erfolgreich geteilt!']);
            Redirector::redirect(BASE_URL . '/user/shares');
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $errors[] = 'Der Checkout konnten leider nicht abgeschlossen werden :(';
            Session::set('errors', $errors);

            /**
             * Redirect zurück zur Share Zusammenfassung.
             */
            Redirector::redirect(BASE_URL . "/checkout/summary/{$share->id}");
        }
    }
}
