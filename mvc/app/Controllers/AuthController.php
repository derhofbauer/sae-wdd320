<?php

namespace App\Controllers;

use app\Models\User;
use Core\Session;
use Core\View;

/**
 * Class AuthController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class AuthController
{

    public function loginForm ()
    {
        if (User::isLoggedIn()) {
            header('Location:' . BASE_URL);
            exit;
        }
        View::render('login');
    }

    /**
     * Login
     *
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
    public function login ()
    {
        $user = User::findByEmailOrUsername($_POST['email-or-username']);

        $errors = [];

        if (empty($user) || !$user->checkPassword($_POST['password'])) {
            $errors[] = 'Username/E-Mail oder Passwort sind falsch.';
        } else {
            $remember = false;

            if (isset($_POST['remember-me']) && $_POST['remember-me'] === 'on') {
                $remember = true;
            }

            if ($user->is_admin) {
                $user->login(BASE_URL . '/admin', $remember);
            } else {
                $user->login(BASE_URL . '/home', $remember);
            }
        }

        Session::set('errors', $errors);
        header('Location:' . BASE_URL . '/login');
        exit;
    }

}
