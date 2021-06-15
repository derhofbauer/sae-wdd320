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
         * Wenn bereits ein*e User*in eingeloggt ist, zeigen wir das Login Formular nicht an, sondern leiten auf die
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
         * 4) Gibts den/die User*in schon? ja: weiter, nein: Fehlermeldung
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
         * Wenn bereits ein*e User*in eingeloggt ist, zeigen wir das Sign-up Formular nicht an, sondern leiten auf die
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
     *
     * [x] Daten validieren
     * [x] erfolgreich: weiter, nicht erfolgreich: Fehler
     * [x] Gibts E-Mail oder Username schon in der DB?
     * [x] ja: Fehler, nein: weiter
     * [x] User Object aus den Daten erstellen & in DB speichern
     * [x] Weiterleiten zum Login
     */
    public function signup ()
    {
        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->email($_POST['email'], 'E-Mail', true);
        $validator->textnum($_POST['username'], 'Username', false);
        $validator->password($_POST['password'], 'Password', true, 8, 255);
        /**
         * Das Feld 'password_repeat' braucht nicht validiert werden, weil wenn 'password' ein valides Passwort ist und
         * alle Kriterien erfüllt, und wir hier nun prüfen, ob 'password' und 'password_repeat' ident sind, dann ergibt
         * sich daraus, dass auch 'password_repeat' ein valides Passwort ist.
         */
        $validator->compare([
            $_POST['password'],
            'Password'
        ], [
            $_POST['password_repeat'],
            'Password wiederholen'
        ]);

        /**
         * Fehler aus dem Validator auslesen. Validator::getErrors() gibt uns dabei in jedem Fall ein Array zurück,
         * wenn keine Fehler aufgetreten sind, ist dieses Array allerdings leer.
         */
        $errors = $validator->getErrors();

        /**
         * Gibt es schon einen Account zur eingegebenen Email-Adresse?
         */
        if (!empty(User::findByEmailOrUsername($_POST['email']))) {
            $errors[] = 'Diese E-Mail Adresse wird bereits verwendet.';
        }

        /**
         * Gibt es schon einen Account zum eingegebenen Username?
         */
        if (!empty($_POST['username']) && !empty(User::findByEmailOrUsername($_POST['username']))) {
            $errors[] = 'Dieser Username wird bereits verwendet.';
        }

        /**
         * Wenn der Fehler-Array nicht leer ist und es somit Fehler gibt ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session, damit sie im View ausgegeben werden können und leiten dann
             * zurück zum Formular.
             */
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . '/sign-up');
        }

        /**
         * Kommen wir an diesen Punkt, können wir sicher sein, dass die E-Mail Adresse und der Username noch nicht
         * verwendet werden und alle eingegebenen Daten korrekt validiert werden konnten.
         */
        $user = new User();
        $user->email = trim($_POST['email']);
        $user->username = trim($_POST['username']);
        $user->setPassword($_POST['password']);

        /**
         * Neue*n User*in in die Datenbank speichern.
         *
         * Die User::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat.
         */
        if ($user->save()) {
            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zum Loginformular.
             *
             * Um eine Erfolgsmeldung ausgeben zu können, verwenden wir die selbe Mechanik wie für die errors.
             */
            $success = ['Herzlich wilkommen!'];
            Session::set('success', $success);
            Redirector::redirect(BASE_URL . '/login');
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $errors[] = 'Die Registrierung konnte nicht durchgeführt werden. :(';
            Session::set('errors', $errors);

            /**
             * Redirect zurück zum Registrierungsformular.
             */
            Redirector::redirect(BASE_URL . '/sign-up');
        }
    }

    /**
     * @todo: comment
     */
    public function resetPasswordForm ()
    {
        View::render('reset-password');
    }

    /**
     * @todo: comment
     */
    public function sendResetMail ()
    {
        var_dump($_POST);
        /**
         * [ ] Gibts den User?
         * [ ] Wenn nein: neutrale Meldung, wenn ja: weiter
         * [ ] Reset-Token generieren und in die DB speichern
         * [ ] Email generieren mit Reset-Token in einem Link
         * [ ] Email verschicken
         * [ ] neutrale Meldung
         *
         * ----
         * Klick auf Link in Email:
         * [ ] Token aus GET Paramatern auslesen
         * [ ] Gibts den Token in der DB?
         * [ ] Wenn nein: Fehler, wenn ja: weiter
         * [ ] Formular für neues passwort anzeigen
         */
    }

}
