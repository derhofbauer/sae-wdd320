<?php

namespace App\Models;

use Core\Database;
use Core\Models\AbstractModel;

/**
 * Class Share
 *
 * @package App\Models
 */
class Share extends AbstractModel
{
    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public int $user_id;
    public ?string $recipient = null;
    public ?string $posts = null;
    public ?string $message = null;
    public string $status = 'open';
    /**
     * @var string Nachdem wir hier den ISO-8601 Zeit verwenden in der Datenbank, handelt es sich um einen String.
     */
    public string $crdate;
    /**
     * @var string Nachdem wir hier den ISO-8601 Zeit verwenden in der Datenbank, handelt es sich um einen String.
     */
    public string $tstamp;

    /**
     * Damit wir die möglichen Stati eines Shares an einem zentralen Ort definiert haben, gibt es für die Share Klasse
     * eine Klassenkonstante, die sowohl für die Generierung des Dropdowns in den Views als auch bei der Validierung in
     * den Controllern verwendet werden kann.
     */
    const STATI = [
        'open' => 'Open',
        'progress' => 'In Progress',
        'storno' => 'Storno',
        'delivered' => 'Delivered! :D'
    ];

    /**
     * Diese Methode ermöglicht es uns, die Daten aus einem Datenbankergebnis in nur einer Zeile direkt in ein Objekt
     * zu füllen. Bei der Instanziierung kann über den Konstruktor auch diese Methode verwendet werden.
     *
     * @param array $data
     */
    public function fill (array $data)
    {
        $this->id = $data['id'];
        $this->user_id = $data['user_id'];
        $this->recipient = $data['recipient'];
        $this->posts = $data['posts'];
        $this->message = $data['message'];
        $this->status = $data['status'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
    }

    /**
     * Objekt speichern.
     *
     * Wenn das Objekt bereits existiert hat, so wird es aktualisiert, andernfalls neu angelegt. Dadurch können wir eine
     * einzige Funktion verwenden und müssen uns nicht darum kümmern ob das Objekt angelegt oder aktualisiert werden
     * muss.
     *
     * @return bool
     */
    public function save (): bool
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
         * Hat das Objekt bereits eine ID, so existiert in der Datenbank auch schon ein Eintrag dazu und wir können es
         * aktualisieren.
         */
        if (!empty($this->id)) {
            /**
             * Query ausführen und Ergebnis direkt zurückgeben. Das kann entweder true oder false sein, je nachdem ob
             * der Query funktioniert hat oder nicht.
             */
            return $database->query("UPDATE $tablename SET user_id = ?, recipient = ?, posts = ?, message = ?, status = ? WHERE id = ?", [
                'i:user_id' => $this->user_id,
                's:recipient' => $this->recipient,
                's:posts' => $this->posts,
                's:message' => $this->message,
                's:status' => $this->status,
                'i:id' => $this->id
            ]);
        } else {
            /**
             * Hat es keine ID, so müssen wir es neu anlegen.
             */
            $result = $database->query("INSERT INTO $tablename SET user_id = ?, recipient = ?, posts = ?, message = ?, status = ?", [
                'i:user_id' => $this->user_id,
                's:recipient' => $this->recipient,
                's:posts' => $this->posts,
                's:message' => $this->message,
                's:status' => $this->status
            ]);

            /**
             * Ein INSERT Query generiert eine neue ID, diese müssen wir daher extra abfragen und verwenden daher die
             * von uns geschrieben handletInsertResult()-Methode, die über das AbstractModel verfügbar ist.
             */
            $this->handleInsertResult($database);

            /**
             * Ergebnis zurückgeben. Das kann entweder true oder false sein, je nachdem ob der Query funktioniert hat
             * oder nicht.
             */
            return $result;
        }
    }

    /**
     * Alle Datensätze aus der Datenbank abfragen, die nicht abgeschlossen oder storniert sind.
     *
     * Die beiden Funktionsparameter bieten die Möglichkeit die Daten, die abgerufen werden, nach einer einzelnen
     * Spalte aufsteigend oder absteigend direkt über MySQL zu sortieren. Sortierungen sollten, sofern möglich, über die
     * Datenbank durchgeführt werden, weil das wesentlich performanter ist als über PHP.
     *
     * @param string $orderBy
     * @param string $direction
     *
     * @return array
     */
    public static function allOpen (string $orderBy = '', string $direction = 'ASC'): array
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
            $results = $database->query("SELECT * FROM {$tablename} WHERE status = 'open' OR status = 'progress'");
        } else {
            $results = $database->query("SELECT * FROM {$tablename} WHERE status = 'open' OR status = 'progress' ORDER BY $orderBy $direction");
        }

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return self::handleResult($results);
    }

    /**
     * Relation zur User-Klasse herstellen.
     */
    public function user ()
    {
        return User::find($this->user_id);
    }

    /**
     * Posts, die im Share als JSON gespeichert sind, als Array an Objekten zurückgeben.
     *
     * Anders als bspw. die self::user()-Methode und andere Relation-Methoden, bezieht sich diese Methode nicht auf
     * andere Post Objekte, sondern auf die Daten, die in $this->posts als JSON gespeichert sind.
     */
    public function posts ()
    {
        return json_decode($this->posts);
    }
}
