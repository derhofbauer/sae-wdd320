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
}
