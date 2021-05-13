<?php

namespace Core\Models;

use App\Models\User;
use Core\Database;
use Core\Helpers\Redirector;
use Core\Session;

/**
 * Class AbstractModel
 *
 * @package Core\Models
 */
abstract class AbstractUser extends AbstractModel
{

    /**
     * Wir definieren zunächst ein paar Konstanten für die User Session, die wir später verwenden können.
     */
    const LOGGED_IN_STATUS = 'is_logged_in';
    const LOGGED_IN_ID = 'logged_in_user';
    const LOGGED_IN_REMEMBER = 'remember_until';
    const LOGGED_IN_SESSION_LIFETIME = 7 * 25 * 60 * 60; // 7 Tage in Sekunden

    /**
     * @param string $emailOrUsername
     *
     * @return object|null
     */
    public static function findByEmailOrUsername (string $emailOrUsername): ?object
    {
        /**
         * Whitespace entfernen.
         */
        $emailOrUsername = trim($emailOrUsername);

        /**
         * Datenbankverbindung herstellen.
         */
        $database = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tablename = self::getTablenameFromClassname();

        /**
         * Query ausführen.
         *
         * Wir setzen hier LIMIT 1, weil wir nur eine*n User*in gleichzeitig einloggen können und daher nur eine*n
         * User*in zurückbekommen wollen aus der Datenbank. Nachdem sowohl email als auch username UNIQUE sind, gibt es
         * nur dann die Möglichkeit, dass wir mehr als ein Ergebnis erhalten, wenn jemand eine Fremde E-Mail Adresse als
         * Username verwendet hat.
         */
        $results = $database->query("SELECT * FROM $tablename WHERE email = ? OR username = ? LIMIT 1", [
            's:email' => $emailOrUsername,
            's:username' => $emailOrUsername
        ]);

        /**
         * Im AbstractModel haben wir diese Funktionalität aus der all()-Methode herausgezogen und in eine eigene
         * Methode verpackt, damit wir in allen anderen Methoden, die zukünftig irgendwelche Daten aus der Datenbank
         * abfragen, den selben Code verwenden können und nicht Code duplizieren müssen.
         */
        $result = self::handleUniqueResult($results);

        /**
         * Ergebnis zurückgeben.
         */
        return $result;
    }

    /**
     * Überprüfung, ob das übergebene $password auf den gespeicherten Hash zutrifft.
     *
     * Wir schreiben eine eigene Wrapper-Funktion, damit wir ohne Änderung an den Controllern einfach die Funktionsweise
     * der Passwort-Überprüfung ändern können.
     *
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword (string $password): bool
    {
        /**
         * Die folgende Funktion kann einen plaintext Passwort gegen einen bcrypt Hash prüfen und wird von PHP
         * mitgeliefert.
         */
        return password_verify($password, $this->password);
    }

    /**
     * Neues Passwort hashen und setzen.
     *
     * Der Return Type void definiert, dass die Funktion keinen Rückgabewert hat.
     *
     * @param string $password
     *
     * @return void
     */
    public function setPassword (string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Login durchführen.
     *
     * @param string $redirect
     * @param bool   $remember
     *
     * @return bool
     */
    public function login (string $redirect = '', bool $remember = false): bool
    {
        /**
         * Login-Status in die Session speichern.
         */
        Session::set(self::LOGGED_IN_STATUS, true);
        Session::set(self::LOGGED_IN_ID, $this->id);

        /**
         * Wurde die Remember-Checkbox angehakerlt, definieren wir hier die Session Lifetime - wann der Login-Status
         * abläuft.
         */
        if ($remember === true) {
            Session::set(self::LOGGED_IN_REMEMBER, time() + self::LOGGED_IN_SESSION_LIFETIME);
        }

        /**
         * Wurde eine Redirect-URL übergeben, leiten wir hier weiter.
         */

        /**
         * Die Funktionalität zum Redirecten haben wir in eine eigene Klasse ausgelagert, damit wir die auch wo anders
         * noch verwenden können.
         */
        Redirector::redirect($redirect);

        /**
         * Wurde keine Redirect-URL übergeben, geben wir true zurück.
         */
        return true;
    }

    /**
     * Logout durchführen.
     *
     * @param string $redirect
     *
     * @return bool
     */
    public static function logout (string $redirect = ''): bool
    {
        /**
         * Login Status in der Session aktualisieren.
         *
         * Hier könnten wir auch einfach die Session löschen, aber wir möchten Werte in der Session, die nichts mit dem
         * Login Status zu tun haben (bspw. Warenkorb, Dark/Lightmode Einstellungen), nicht löschen und legen daher
         * quasi nur den Schalter um.
         */
        Session::set(self::LOGGED_IN_STATUS, false);
        Session::forget(self::LOGGED_IN_ID);
        Session::forget(self::LOGGED_IN_REMEMBER);

        /**
         * Wurde eine Redirect-URL übergeben, leiten wir hier weiter.
         */
        Redirector::redirect($redirect);

        return true;
    }


    /**
     * Prüfen, ob aktuell ein*e User*in eingeloggt ist.
     *
     * Zu beachten ist hier, dass wir keinen Datenbank Query abschicken, weil in dieser Funktion nicht relevant ist,
     * welche*r User*in eingeloggt ist. Dadurch muss nur auf die Session zugegriffen werden und es entsteht kein
     * Bottleneck in der Datenbankverbindung, was insgesamt erheblich schneller sein dürfte.
     *
     * @return bool
     */
    public static function isLoggedIn (): bool
    {
        /**
         * Ist ein*e User*in eingeloggt, so geben wir true zurück ...
         */
        if (
            Session::get(self::LOGGED_IN_STATUS, false) === true
            && Session::get(self::LOGGED_IN_ID, null) !== null
        ) {
            return true;
        }

        /**
         * ... andernfalls false.
         */
        return false;
    }

    /**
     * Aktuell eingeloggten User mit Informationen aus der Session aus der Datenbank abfragen.
     *
     * @return ?User
     */
    public static function getLoggedIn (): ?User
    {
        /**
         * Ist ein*e User*in eingeloggt, ...
         */
        if (self::isLoggedIn()) {
            /**
             *  ... so holen wir uns hier die zugehörige ID mit dem default null.
             */
            $userId = Session::get(self::LOGGED_IN_ID, null);

            /**
             * Wurde also eine ID ind er Session gefunden, laden wir den/die User*in aus der Datenbank und geben das
             * Ergebnis zurück.
             */
            if ($userId !== null) {
                return User::find($userId);
            }
        }
        /**
         * Andernfalls geben wir null zurück.
         */
        return null;
    }

}
