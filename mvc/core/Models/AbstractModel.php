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
    public function __construct (array $data = [])
    {
        /**
         * Die Methode wird in AbstractModel nur abstrakt definiert und kann in jedem Model, dass die Klasse erweitert,
         * überschrieben werden.
         */

        /**
         * Wenn Daten übergeben wurden, so füllen wir das aktuelle Objekt damit.
         */
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    /**
     * Hier definieren wir, dass eine Klasse, die AbstractModel erweitert, eine fill() Methode haben MUSS.
     *
     * @param array $data
     */
    abstract public function fill (array $data);

    /**
     * Hier definieren wir, dass jede Class, die das AbstractModel erweitert, auch eine save()-Methode definieren muss.
     *
     * @return bool
     */
    abstract public function save (): bool;

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
            $results = $database->query("SELECT * FROM {$tablename} WHERE {$field} = ?", [
                's:value' => $value
            ]);
        } else {
            $results = $database->query("SELECT * FROM {$tablename} WHERE {$field} = ? ORDER BY $orderBy $direction", [
                's:value' => $value
            ]);
        }

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return self::handleResult($results);
    }

    /**
     * Ein einzelnes Objekt anhand seiner ID finden.
     *
     * @param int $id
     *
     * @return mixed
     */
    public static function find (int $id): mixed
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
        $results = $database->query("SELECT * FROM {$tablename} WHERE id = ?", [
            'i:id' => $id
        ]);

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return self::handleUniqueResult($results);
    }

    /**
     * find()-Methode aufrufen oder einen Fehler 404 Not Found zurück geben, wenn kein Ergebnis aus der Datenbank zurück
     * gekommen ist.
     *
     * @param int $id
     *
     * @return mixed
     */
    public static function findOrFail (int $id): mixed
    {
        /**
         * find()-Methode aufrufen.
         */
        $result = self::find($id);
        /**
         * Mittels der bereits existierenden returnOrFail()-Methode das Ergebnis verarbeiten.
         */
        return self::returnOrFail($result);
    }

    /**
     * Objekt löschen.
     *
     * @return array|bool
     */
    public function delete (): array|bool
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
        $results = $database->query("DELETE FROM {$tablename} WHERE id = ?", [
            'i:id' => $this->id
        ]);

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return $results;
    }

    /**
     * Datenbankeinträge löschen, wo ein Fremdschlüssel einen gewissen Wert hat.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function deleteWhereForeignKey (string $field, int $value): mixed
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
        $results = $database->query("DELETE FROM {$tablename} WHERE {$field} = ?", [
            'i:field' => $value
        ]);

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return $results;
    }

    /**
     * Wert zurückgeben oder Fehler 404 werfen, wenn der Wert leer ist.
     *
     * Das macht dann Sinn, wenn in einer Action innerhalb eines Controllers die Funktionalität auf der Existenz eines
     * einzelnen Objekts aufbaut. Beispielsweise wenn ein einzelner Post angezeigt werden soll, dann macht es Sinn einen
     * Fehler auszugeben, wenn der angefragt Post nicht existiert.
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public static function returnOrFail (mixed $result): mixed
    {
        if (empty($result)) {
            View::error404();
        }

        return $result;
    }

    /**
     * Resultat aus der Datenbank verarbeiten.
     *
     * Wir haben das aus der self::all()-Methode ausgelagert, weil die all()-Methode nicht die einzige Methode sein
     * wird, in der wir Datenbankergebnisse verarbeiten werden müssen. Damit wir den Code nicht immer kopieren müssen,
     * was als Bad Practice gilt, haben wir eine eigene Methode gebaut.
     *
     * @param array $results
     *
     * @return array
     */
    public static function handleResult (array $results): array|bool
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
     * Hier erweitern wir die self::handleResult()-Methode für den Fall, dass wir von einem Query kein oder maximal ein
     * Ergebnis erwarten. Bei einem Query mit einer WHERE-Abfrage auf eine UNIQUE-Spalte, würden wir maximal ein
     * Ergebnis zurück bekommen. Diese Funktion ist also mehr eine Convenience Funktion, weil sie entweder null zurück
     * gibt, wenn kein Ergebnis zurückgekommen ist (statt eines leeren Arrays in self::handleResult()) oder ein
     * einzelnes Objekt (statt eines Arrays mit einem einzigen Objekt darin).
     *
     * @param array $results
     *
     * @return ?object
     */
    public static function handleUniqueResult (array $results): ?object
    {
        /**
         * Datenbankergebnis ganz normal verarbeiten.
         */
        $objects = self::handleResult($results);

        /**
         * ist das Ergebnis aus der Datenbank leer, geben wir null zurück.
         */
        if (empty($objects)) {
            return null;
        }

        /**
         * Andernfalls geben wir das Objekt an Stelle 0 zurück, das in diesem Fall das einzige Objekt sein sollte.
         */
        return $objects[0];
    }

    /**
     * Wird ein INSERT-Query ausgeführt, so wird in den allermeisten Fällen auch eine neue ID generiert. Diese ist über
     * die Datenbankverbindung abrufbar. Hier holen wir diese ID und aktualisieren das aktuelle Objekt mit der neuen ID.
     *
     * @param Database $database
     */
    public function handleInsertResult (Database $database)
    {
        /**
         * Neu generierte ID holen.
         */
        $newId = $database->getInsertId();

        /**
         * Handelt es sich um einen Integer und wurde somit eine neue ID vergeben ...
         */
        if (is_int($newId)) {
            /**
             * ... aktualisieren wir das aktuelle Objekt mit diesem Wert.
             */
            $this->id = $newId;
        }
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
