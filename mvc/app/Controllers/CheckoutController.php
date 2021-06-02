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
 * @todo    : comment
 */
class CheckoutController
{

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

    public function recipient ()
    {
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

        $name = $_POST['name'];
        $email = $_POST['email'];
        $share = new Share();
        $share->user_id = User::getLoggedIn()->id;
        $share->recipient = "$name <{$email}>";

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
             */
            Redirector::redirect(BASE_URL . "/checkout/2/{$share->id}");
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $errors[] = 'Die Empfängerinformationen konnten leider nicht gespeichert werden :(';
            Session::set('errors', $errors);

            /**
             * Redirect zurück zum Registrierungsformular.
             */
            Redirector::redirect(BASE_URL . '/checkout');
        }
    }

    public function checkout2 (int $id)
    {
        $share = Share::findOrFail($id);
        View::render('checkout/message', [
            'share' => $share
        ]);
    }

    public function message (int $id)
    {
        $share = Share::findOrFail($id);

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
             */
            Redirector::redirect(BASE_URL . "/checkout/summary/{$share->id}");
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $errors[] = 'Die Grußbotschaft konnten leider nicht gespeichert werden :(';
            Session::set('errors', $errors);

            /**
             * Redirect zurück zum Registrierungsformular.
             */
            Redirector::redirect(BASE_URL . "/checkout/2/{$share->id}");
        }
    }

    public function summary (int $id)
    {
        $share = Share::findOrFail($id);
        $user = User::getLoggedIn();
        $favourites = $user->favourites();

        View::render('checkout/summary', [
            'share' => $share,
            'user' => $user,
            'favourites' => $favourites
        ]);
    }

    public function finish (int $id)
    {
        $share = Share::findOrFail($id);
        $user = User::getLoggedIn();
        $favourites = $user->favourites();
        $posts = [];

        foreach ($favourites as $favourite) {
            $posts[] = $favourite->post();
        }

        $json = json_encode($posts);
        $share->posts = $json;

        if ($share->save()) {
            Favourite::deleteWhereForeignKey('user_id', $user->id);

            Session::set('success', ['Posts erfolgreich geteilt!']);
            Redirector::redirect(BASE_URL . '/user/shares');
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $errors[] = 'Der Checkout konnten leider nicht abgeschlossen werden :(';
            Session::set('errors', $errors);

            /**
             * Redirect zurück zum Registrierungsformular.
             */
            Redirector::redirect(BASE_URL . "/checkout/summary/{$share->id}");
        }
    }
}
