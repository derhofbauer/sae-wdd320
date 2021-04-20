<?php

namespace Core;

/**
 * Class View
 *
 * @package Core
 * @todo: comment
 */
class View
{

    public static function error (int $httpCode = 500, string $message = 'An error occurred.')
    {
        http_response_code($httpCode);
        echo "$httpCode: $message";
    }

    public static function error404 ()
    {
        self::error(404, 'Page not found.');
    }
}
