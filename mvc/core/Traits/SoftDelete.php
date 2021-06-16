<?php

namespace Core\Traits;

use Core\Config;
use Core\Database;

/**
 * Trait SoftDelete
 *
 * Dieser Trait überschreibt einige Methoden des BaseModel, wenn Softdeletes verwendet werden sollen.
 *
 * @package Core\Traits
 */
trait SoftDelete
{

    /**
     * Den zum aktuellen Objekt gehörigen Datensatz in der Datenbank als gelöscht markieren.
     *
     * @return bool
     */
    public function delete (): bool
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
         *
         * CURRENT_TIMESTAMP() ist eine Funktion von MySQL, die den aktuellen Zeitstempel zurückgibt.
         */
        $results = $database->query("UPDATE `{$tablename}` SET deleted_at = CURRENT_TIMESTAMP() WHERE id = ?", [
            'i:id' => $this->id
        ]);

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return $results;
    }

    /**
     * Alle Datensätze aus der Datenbank abfragen.
     *
     * Die beiden Funktionsparameter bieten die Möglichkeit die Daten, die abgerufen werden, nach einer einzelnen Spalte
     * aufsteigend oder absteigend direkt über MySQL zu sortieren. Sortierungen sollten, sofern möglich, über die
     * Datenbank durchgeführt werden, weil das wesentlich performanter ist als über PHP.
     *
     * @param string $orderBy
     * @param string $direction
     *
     * @return array
     */
    public static function all (string $orderBy = '', string $direction = 'ASC'): array
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
         *
         * Wurde in den Funktionsparametern eine Sortierung definiert, so wenden wir sie hier an, andernfalls rufen wir
         * alles ohne sortierung ab.
         *
         * Hier nehmen wir auch Rücksicht auf die deleted_at Spalte und geben nur Einträge zurück, die nicht als
         * gelöscht markiert sind.
         */
        if (empty($orderBy)) {
            $results = $database->query("SELECT * FROM `{$tablename}` WHERE deleted_at IS NULL");
        } else {
            $results = $database->query("SELECT * FROM `{$tablename}` WHERE deleted_at IS NULL ORDER BY $orderBy $direction");
        }

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return self::handleResult($results);
    }

    /**
     * Anzahl der Einträge eines Models in der Datenbank zurückgeben.
     *
     * @return int
     */
    public static function count (): int
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
         *
         * Wurde in den Funktionsparametern eine Sortierung definiert, so wenden wir sie hier an, andernfalls rufen wir
         * alles ohne sortierung ab.
         */
        $results = $database->query("SELECT COUNT(*) as 'count' FROM `{$tablename}` WHERE deleted_at IS NULL");

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return (int)$results[0]['count'];
    }

    /**
     * Alle Datensätze aus der Datenbank abfragen und paginiert zurückgeben.
     *
     * @param int    $page
     * @param string $orderBy
     * @param string $direction
     *
     * @return array
     */
    public static function allPaginated (int $page = 1, string $orderBy = '', string $direction = 'ASC'): array
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
         * Pagination Konfiguration holen.
         */
        $limit = Config::get('app.items-per-page');

        /**
         * Berechnen, wie viele Elemente übersprungen werden sollen.
         */
        $offset = ($page - 1) * $limit;

        /**
         * Query ausführen.
         *
         * Wurde in den Funktionsparametern eine Sortierung definiert, so wenden wir sie hier an, andernfalls rufen wir
         * alles ohne sortierung ab.
         *
         * Zusätzlich arbeiten wir hier mit dem LIMIT Keyword und übergeben die beiden Parameter dynamisch.
         */
        if (empty($orderBy)) {
            $results = $database->query("SELECT * FROM `{$tablename}` WHERE deleted_at IS NULL LIMIT ?,?", [
                'i:offset' => $offset,
                'i:limit' => $limit
            ]);
        } else {
            $results = $database->query("SELECT * FROM `{$tablename}` WHERE deleted_at IS NULL LIMIT ?,? ORDER BY $orderBy $direction", [
                'i:offset' => $offset,
                'i:limit' => $limit
            ]);
        }

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return self::handleResult($results);
    }

    /**
     * Alle Datensätze aus der Datenbank abfragen.
     *
     * Die ersten beiden Funktionsparameter bieten die Möglichkeit eine ganz einfache WHERE-Abfrage zu machen.
     *
     * Die beiden letzten Funktionsparameter bieten die Möglichkeit die Daten, die abgerufen werden, nach einer
     * einzelnen Spalte aufsteigend oder absteigend direkt über MySQL zu sortieren. Sortierungen sollten, sofern
     * möglich, über die Datenbank durchgeführt werden, weil das wesentlich performanter ist als über PHP.
     *
     * @param string $field
     * @param mixed  $value
     * @param string $orderBy
     * @param string $direction
     *
     * @return array
     */
    public static function findWhere (string $field, mixed $value, string $orderBy = '', string $direction = 'ASC'): array
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
         *
         * Hier ist es wichtig zu bedenken, dass der $field-Parameter niemals die Benutzer*inneneingabe beinhalten darf,
         * weil sonst der Query für MySQL Injection anfällig ist.
         *
         * Wurde in den Funktionsparametern eine Sortierung definiert, so wenden wir sie hier an, andernfalls rufen wir
         * alles ohne sortierung ab.
         */
        if (empty($orderBy)) {
            $results = $database->query("SELECT * FROM `{$tablename}` WHERE deleted_at IS NULL AND {$field} = ?", [
                's:value' => $value
            ]);
        } else {
            $results = $database->query("SELECT * FROM `{$tablename}` WHERE deleted_at IS NULL AND {$field} = ? ORDER BY $orderBy $direction", [
                's:value' => $value
            ]);
        }

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return self::handleResult($results);
    }

}
