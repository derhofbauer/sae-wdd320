<?php

namespace Core\Middlewares;

use App\Models\User;
use Core\View;

/**
 * Class AuthMiddleware
 *
 * @package Core\Middlewares
 * @todo: comment
 */
class AuthMiddleware
{

    /**
     * @return bool|null
     * @todo: comment (nullsafe operator)
     */
    public static function isAdmin (): ?bool
    {
        return User::getLoggedIn()?->is_admin;
    }

    /**
     * @todo: comment
     */
    public static function isAdminOrFail ()
    {
        $isAdmin = self::isAdmin();

        if ($isAdmin !== true) {
            View::error403();
        }
    }

}
