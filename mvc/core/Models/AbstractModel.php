<?php

namespace Core\Models;

use Core\Database;
use Core\View;

/**
 * Class AbstractModel
 *
 * @package Core\Models
 */
abstract class AbstractModel
{

    /**
     * Der Konstruktor befüllt das Objekt, sofern Daten übergeben worden sind.
     *
     * @param array $data
     */
    public function __construct (array $data)
    {
        /**
         * Die Methode wird in AbstractModel nur abstrakt definiert, implementiert wird sie dann von jedem Model, das
         * diese Klasse erweitert.
         */
        $this->fill($data);
    }

    /**
     * Hier definieren wir, dass eine Klasse, die AbstractModel erweitert, eine fill() Methode haben MUSS.
     *
     * @param array $data
     */
    abstract public function fill (array $data);

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
         */
        if (empty($orderBy)) {
            $results = $database->query("SELECT * FROM {$tablename}");
        } else {
            $results = $database->query("SELECT * FROM {$tablename} ORDER BY $orderBy $direction");
        }

        /**
         * @todo: comment
         */
        return self::handleResult($results);
    }

    /**
     * @todo: comment
     */
    public static function find (int $id) {}

    /**
     * @todo: comment
     */
    public static function findOrFail (int $id) {}

    /**
     * @todo: comment
     */
    public static function returnOrFail (mixed $result) {
        if (empty($result)) {
            View::error404();
        }

        return $result;
    }

    /**
     * @param array $results
     *
     * @return array
     * @todo: comment
     */
    public static function handleResult (array $results): array
    {
        /**
         * Ergebnis-Array vorbereiten.
         */
        $objects = [];
        /**
         * Ergebnisse des Datenbank-Queries durchgehen und jeweils ein neues Objekt erzeugen.
         */
        foreach ($results as $result) {
            /**
             * Auslesen, welche Klasse aufgerufen wurde und ein Objekt dieser Klasse erstellen und in den Ergebnis-Array
             * speichern. Das ist nötig, weil wir bspw. Post Objekte haben wollen und nicht ein Array voller
             * AbstractModels.
             */
            $calledClass = get_called_class();
            $objects[] = new $calledClass($result);
        }

        /**
         * Ergebnisse zurückgeben.
         */
        return $objects;
    }

    /**
     * @todo: comment
     */
    public static function handleUniqueResult (array $results): object|null
    {
        $objects = self::handleResult($results);

        if (empty($objects)) {
            return null;
        }

        return $objects[0];
    }

    /**
     * Damit diese abstrakte Klasse für alle Models verwendet werden kann, ist es hilfreich, berechnen zu können, welche
     * Tabelle vermutlich zu dem erweiternden Model gehört.
     *
     * @return string
     */
    protected static function getTablenameFromClassname (): string
    {
        /**
         * Name der aufgerufenen Klasse abfragen.
         */
        $calledClass = get_called_class();

        /**
         * Hat die aufgerufene Klasse eine Konstante TABLENAME?
         */
        if (defined("$calledClass::TABLENAME")) {
            /**
             * Wenn ja, dann verwenden wir den Wert dieser Konstante als Tabellenname. Das ermöglicht uns einen Namen
             * für eine Tabelle anzugeben, wenn der Tabellenname nicht vom Klassennamen abgeleitet werden kann.
             */
            return $calledClass::TABLENAME;
        }

        /**
         * Wenn nein, dann holen wir uns den Namen der Klasse ohne Namespace, konvertieren ihn in Kleinbuchstaben und
         * fügen hinten ein s dran. So wird bspw. aus App\Models\Product --> products
         */
        $particles = explode('\\', $calledClass);
        $classname = array_pop($particles);
        $tablename = strtolower($classname) . 's';

        /**
         * Berechneten Tabellennamen zurückgeben.
         */
        return $tablename;
    }

}
