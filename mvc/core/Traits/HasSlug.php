<?php

namespace Core\Traits;

use Core\Database;

/**
 * Trait HasSlug
 *
 * Dieser Trait bietet uns die Möglichkeit, die findBySlug()-Methode in alle Models hinzuzufügen, in denen wir sie
 * brauchen. Wir könnten das selbe Ergebnis erreichen, wenn wir diese Methode in das AbstractModel einbauen würden. In
 * diesem Fall hätten dann aber Models, die keinen Slug in der Datenbank haben, auch diese Methode und sie würde dann
 * einen Fehler produzieren, wenn sie aufgerufen werden würde. Daher ist es eleganter, wenn wir einen Trait dafür
 * bauen.
 *
 * @package Core\Traits
 */
trait HasSlug
{

    /**
     * Objekte anhand der Spalte "slug" finden.
     *
     * @param string $slug
     *
     * @return object|null
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
         * Datenbankergebnis verarbeiten.
         *
         * Nachdem wir hier aber nur maximal ein Ergebnis erwarten, weil die slug-Spalte UNIQUE ist, verwenden wir aus
         * Gründen der Gemütlichkeit nicht die handleRequest()-Methode, sondern die handleUniqueRequest()-Methode.
         */
        $result = self::handleUniqueResult($results);

        /**
         * Ergebnis zurückgeben.
         */
        return $result;
    }

}
