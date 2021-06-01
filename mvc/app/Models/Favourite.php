<?php

namespace App\Models;

use Core\Models\AbstractModel;
use Core\Database;

/**
 * Class Favourite
 *
 * @package App\Models
 * @todo: comment
 */
class Favourite extends AbstractModel
{
    public int $id;
    public int $user_id;
    public int $post_id;
    private object $post;


    public function fill (array $data)
    {
        $this->id = $data['id'];
        $this->user_id = $data['user_id'];
        $this->post_id = $data['post_id'];
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
            return $database->query("UPDATE $tablename SET user_id = ?, post_id = ? WHERE id = ?", [
                'i:user_id' => $this->user_id,
                'i:post_id' => $this->post_id,
                'i:id' => $this->id
            ]);
        } else {
            /**
             * Hat es keine ID, so müssen wir es neu anlegen.
             */
            $result = $database->query("INSERT INTO $tablename SET user_id = ?, post_id = ?", [
                'i:user_id' => $this->user_id,
                'i:post_id' => $this->post_id
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

    public function post ()
    {
        if (empty($this->post)) {
            $this->post = Post::find($this->post_id);
        }
        return $this->post;
    }
}
