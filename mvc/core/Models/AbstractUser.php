<?php

namespace Core\Models;

use Core\Database;
use Core\Session;
use Core\View;

/**
 * Class AbstractModel
 *
 * @package Core\Models
 * @todo    : comment
 */
abstract class AbstractUser extends AbstractModel
{

    const LOGGED_IN_STATUS = 'is_logged_in';
    const LOGGED_IN_ID = 'logged_in_user';
    const LOGGED_IN_REMEMBER = 'remember_until';

    /**
     * @param string $emailOrUsername
     *
     * @return object|null
     */
    public static function findByEmailOrUsername (string $emailOrUsername): ?object
    {
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
         * Hier führen wir einen JOIN Query aus, weil wir Daten aus zwei Tabellen zusammenführen möchten.
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
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword (string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * @param string $redirect
     * @param bool   $remember
     *
     * @return bool
     */
    public function login (string $redirect = '', bool $remember = false): bool
    {
        Session::set(self::LOGGED_IN_STATUS, true);
        Session::set(self::LOGGED_IN_ID, $this->id);

        if ($remember === true) {
            Session::set(self::LOGGED_IN_REMEMBER, time() + 7 * 24 * 60 * 60);
        }

        if (!empty($redirect)) {
            header("Location: $redirect");
            exit;
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function isLoggedIn (): bool
    {
        if (
            Session::get(self::LOGGED_IN_STATUS, false) === true
            && Session::get(self::LOGGED_IN_ID, null) !== null
        ) {
            return true;
        }

        return false;
    }

}
