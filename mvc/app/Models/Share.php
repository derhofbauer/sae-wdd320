<?php

namespace App\Models;

use Core\Database;
use Core\Models\AbstractModel;

/**
 * Class Share
 *
 * @package App\Models
 * @todo    : comment
 */
class Share extends AbstractModel
{
    public int $id;
    public int $user_id;
    public ?string $recipient = null;
    public ?string $posts = null;
    public ?string $message = null;
    public string $status = 'open';
    public string $crdate;
    public string $tstamp;

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
