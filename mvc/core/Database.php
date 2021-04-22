<?php

namespace Core;

/**
 * Class Database
 *
 * @package Core
 * @todo    : comment
 */
class Database
{
    private object $link;
    private object $stmt;



    {
        $this->link = new \mysqli(
            Config::get('database.host'),
            Config::get('database.user'),
            Config::get('database.password'),
            Config::get('database.dbname'),
            Confi:
        );
    }

    public function query (string $query, array $params)
    {

    }

}
