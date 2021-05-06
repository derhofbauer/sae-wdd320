<?php

namespace Core\Helpers;

/**
 * Class Redirector
 *
 * @package core\Helpers
 * @todo: comment
 */
class Redirector
{
    /**
     * @param string|null $redirect
     *
     * @todo: comment
     */
    public static function redirect (string $redirect = null)
    {
        /**
         * Wurde eine Redirect-URL übergeben, leiten wir hier weiter.
         */
        if (!empty($redirect)) {
            header("Location: $redirect");
            exit;
        }
    }
}
