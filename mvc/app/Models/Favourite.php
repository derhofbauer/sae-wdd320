<?php

namespace App\Models;

use Core\Models\AbstractModel;
use Core\Database;

/**
 * Class Favourite
 *
 * @package App\Models
 */
class Favourite extends AbstractModel
{
    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public int $user_id;
    public int $post_id;
    /**
     * @var object Das ist eine Art Cache. Wenn wir den Post zum ersten mal über die post()-Methode der Favourite-Klasse
     *                   abfragen, speichern wir das Ergebnis hier in $post und greifen zukünftig nur noch darauf zu.
     */
    private object $post;

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
        $this->post_id = $data['post_id'];
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

    /**
     * Relation zu Posts.
     *
     * @return mixed|object
     */
    public function post ()
    {
        /**
         * Ist $this->post leer, so holen wir den zugehörigen Post aus der Datenbank und speichern ihn in $this->>post.
         * Wurde der Post bereits einmal aus der Datenbank geladen, ist $this->post nicht mehr leer und entsprechend
         * würde einfach $this->post zurückgegeben werden, ohne einen neuen Datenbank Query abzuschicken.
         */
        if (empty($this->post)) {
            $this->post = Post::find($this->post_id);
        }
        /**
         * Post zurückgeben.
         */
        return $this->post;
    }
}
