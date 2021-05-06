<?php

namespace App\Controllers;

use app\Models\User;
use Core\Helpers\Redirector;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class AuthController
 *
 * @package App\Controllers
 */
class AuthController
{

    /**
     * Loin Formular anzeigen
     */
    public function loginForm ()
    {
        /**
         * Wenn bereits ein User eingeloggt ist, zeigen wir das Login Formular nicht an, sondern leiten auf die
         * Startseite weiter.
         */
        if (User::isLoggedIn()) {
            header('Location:' . BASE_URL);
            exit;
        }

        /**
         * Andernfalls laden wir das Login Formular.
         */
        View::render('login');
    }

    /**
     * Login durchführen.
     */
    public function login ()
    {
        /**
         * 1) Username & Passwort ins Login Formular eingeben
         * 2) Remember Me Checkbox anhakerln (optional)
         * 3) Formular absenden
         * ---
         * 4) Gibts den User schon? ja: weiter, nein: Fehlermeldung
         * 5) Passwort aus DB abrufen (Salted Hashes)
         * 6) Passwort aus Eingabe und DB ident? ja: weiter, nein: Fehlermeldung
         * 7) "Remember Me" angehakerlt? ja: $exp=7, nein: $exp=0 (für die aktuelle Browser Session, bis der Tab
         * geschlossen wird)
         * 8) Session schreiben: logged_in=>true, expiration_date=$exp
         * 9) Redirect zu bspw. Dashboard/Home Seite/whatever
         */

        /**
         * User anhand einer Email-Adresse oder eines Usernames aus der Datenbank laden.
         * Diese Funktionalität kommt aus der erweiterten Klasse AbstractUser.
         */
        $user = User::findByEmailOrUsername($_POST['email-or-username']);

        /**
         * Fehler-Array vorbereiten
         */
        $errors = [];

        /**
         * Wurde ein*e User*in in der Datenbank gefunden und stimmt das eingegebene Passwort mit dem Passwort Hash
         * des/der User*in überein?
         *
         * Hier ist wichtig zu bedenken, dass wir nicht zwei unterschiedliche Fehlermeldungen ausgeben, damit wir nicht
         * einem Angreifer verraten, dass der Username richtig ist und nur das Passwort noch nicht. Dadurch wäre es
         * nämlich erheblich einfacher, das Passwort zu brute-forcen.
         */
        if (empty($user) || !$user->checkPassword($_POST['password'])) {
            /**
             * Wenn nein: Fehler!
             */
            $errors[] = 'Username/E-Mail oder Passwort sind falsch.';
        } else {
            /**
             * Wenn ja: weiter.
             */

            /**
             * Remember Status vorbereiten.
             */
            $remember = false;

            /**
             * Wenn die Rmember-Checkbox angehakerlt worden ist, ändern wir den Status.
             */
            if (isset($_POST['remember-me']) && $_POST['remember-me'] === 'on') {
                $remember = true;
            }

            /**
             * Ist die/der User*in, der sich einloggen möchte ein Admin, so redirecten wir in den Admin-Bereich, sonst
             * auf die home-Seite.
             */
            if ($user->is_admin) {
                $user->login(BASE_URL . '/admin', $remember);
            } else {
                $user->login(BASE_URL . '/home', $remember);
            }
        }

        /**
         * Fehler in die Session schreiben und zum Login zurück leiten. In die Session speichern wir deshalb, weil wir
         * im Login Formular nicht mehr auf die Variable $errors zugreifen können und daher eine Möglichkeit brauchen
         * über einen Request hinweg Daten zu speichern. Im Login Form laden wir die Fehler aus der Session, zeigen sie
         * an und löschen sie in der Session wieder.
         */
        Session::set('errors', $errors);
        header('Location:' . BASE_URL . '/login');
        exit;
    }

    /**
     * Logout und redirect auf die Home-Seite durchführen.
     */
    public function logout ()
    {
        User::logout(BASE_URL . '/home');
    }

    /**
     * Registrierungsformular anzeigen
     */
    public function signupForm ()
    {
        /**
         * Wenn bereits ein User eingeloggt ist, zeigen wir das Sign-up Formular nicht an, sondern leiten auf die
         * Startseite weiter.
         */
        if (User::isLoggedIn()) {
            header('Location:' . BASE_URL);
            exit;
        }

        /**
         * Andernfalls laden wir das Sign-up Formular.
         */
        View::render('sign-up');
    }

    /**
     * Daten aus dem Registrierungsformular entgegennehmen und verarbeiten.
     */
    public function signup ()
    {
        /**
         * [x] Daten validieren
         * [x] erfolgreich: weiter, nicht erfolgreich: Fehler
         * [x] Gibts E-Mail oder Username schon in der DB?
         * [x] ja: Fehler, nein: weiter
         * [ ] User Object aus den Daten erstellen & in DB speichern
         * [ ] Weiterleiten zum Login
         */

        $validator = new Validator();
        $validator->email($_POST['email'], 'E-Mail', true);
        $validator->textnum($_POST['username'], 'Username', false);
        $validator->password($_POST['password'], 'Password', true, 8, 255);
        $validator->compare([
            $_POST['password'],
            'Password'
        ], [
            $_POST['password_repeat'],
            'Password wiederholen'
        ]);

        $errors = [];
        if ($validator->hasErrors()) {
            $errors = $validator->getErrors();
        }

        if (!empty(User::findByEmailOrUsername($_POST['email']))) {
            $errors[] = 'Diese E-Mail Adresse wird bereits verwendet.';
        }

        if (!empty($_POST['username']) && !empty(User::findByEmailOrUsername($_POST['username']))) {
            $errors[] = 'Dieser Username wird bereits verwendet.';
        }

        if (!empty($errors)) {
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . '/sign-up');
        }

        /**
         * @todo: zuvor eingegeben Werte müssen bei Fehlern wieder im Formular stehen.
         * @todo: neuen User anlegen
         */

        $user = new User();
        $user->email = trim($_POST['email']);
        $user->username = trim($_POST['username']);
        $user->setPassword($_POST['password']);
        var_dump($user);
        $user->save(); // @todo: gibts noch nicht
    }

}
