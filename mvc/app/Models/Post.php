<?php

namespace App\Models;

use Core\Database;

/**
 * Class Post
 *
 * @package App\Models
 * @todo: comment
 */
class Post
{

    public static function all () {
        $database = new Database();

        $results = $database->query('SELECT * FROM posts');
    }

}
