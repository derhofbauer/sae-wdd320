<?php

namespace Core\Traits;

use Core\Database;

trait HasSlug
{

    /**
     * @param string $slug
     *
     * @return object|null
     * @todo: comment
     */
    public static function findBySlug (string $slug): object|null
    {
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
         */
        $results = $database->query("SELECT * FROM {$tablename} WHERE `slug` = ?", [
            's:slug' => $slug
        ]);

        /**
         * @todo: comment
         */
        $result = self::handleUniqueResult($results);

        return $result;
    }

}
