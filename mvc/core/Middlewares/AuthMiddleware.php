<?php

namespace Core\Middlewares;

use App\Models\User;
use Core\View;

/**
 * Class AuthMiddleware
 *
 * @package Core\Middlewares
 */
class AuthMiddleware
{

    /**
     * Prüfen, ob der/die eingeloggte User*in ein Admin ist.
     *
     * @return bool|null
     */
    public static function isAdmin (): ?bool
    {
        /**
         * Hier verwenden wir den Nullsafe Operator (?). Dadurch wird kein Fehler auftreten, wenn kein*e User*in
         * eingeloggt und getLoggedIn() somit keine*n User*in zurückgibt und somit dieser leere Rückgabewert auch keine
         * Property is_admin hat. Der Nullsafe Operator wird einfach den Wert des gesamten Ausdrucks auf null setzen.
         */
        return User::getLoggedIn()?->is_admin;
    }

    /**
     * Prüfen, ob der/die eingeloggte User*in ein Admin ist und andernfalls Fehler 403 Forbidden zurückgeben.
     */
    public static function isAdminOrFail ()
    {
        /**
         * Prüfen, ob der/die aktuell eingeloggt User*in Admin ist.
         */
        $isAdmin = self::isAdmin();

        /**
         * Wenn nein, geben wir einen Fehler 403 Forbidden zurück und brechen somit die weitere Ausführung ab.
         */
        if ($isAdmin !== true) {
            View::error403();
        }
    }

}
