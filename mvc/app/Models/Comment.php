<?php

namespace App\Models;

use Core\Database;
use Core\Models\AbstractModel;
use Core\Traits\HasSlug;
use Core\Traits\SoftDelete;

/**
 * Class Post
 *
 * @package App\Models
 */
class Comment extends AbstractModel
{
    /**
     * s. Post.php
     */
    use SoftDelete;

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public int $author;
    public string $content;
    public int $post_id;
    /**
     * Nachdem wir Ratings noch nicht implementiert haben und nicht jeder Kommentar einen Eltern-Kommentar hat,
     * definieren wir hier Default-Werte für beide Eigenschaften. ?int gibt dabei an, dass die Eigenschaft ein Integer
     * sein muss, aber auch null sein darf.
     */
    public ?int $rating = null;
    public ?int $parent = null;
    /**
     * @var string Nachdem wir hier den ISO-8601 Zeit verwenden in der Datenbank, handelt es sich um einen String.
     */
    public string $crdate;
    /**
     * @var string Nachdem wir hier den ISO-8601 Zeit verwenden in der Datenbank, handelt es sich um einen String.
     */
    public string $tstamp;
    /**
     * @var string Nachdem wir hier den ISO-8601 Zeit verwenden in der Datenbank, handelt es sich um einen String.
     */
    public mixed $deleted_at;

    /**
     * Diese Methode ermöglicht es uns, die Daten aus einem Datenbankergebnis in nur einer Zeile direkt in ein Objekt
     * zu füllen. Bei der Instanziierung kann über den Konstruktor auch diese Methode verwendet werden.
     *
     * @param array $data
     */
    public function fill (array $data)
    {
        $this->id = $data['id'];
        $this->author = $data['author'];
        $this->content = $data['content'];
        $this->post_id = $data['post_id'];
        $this->rating = $data['rating'];
        $this->parent = $data['parent'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }

    /**
     * Relation zum Post.
     *
     * @return array
     */
    public function post (): array
    {
        /**
         * Über das Post Model den zugehörigen Post abrufen.
         */
        return Post::find($this->post_id);
    }

    /**
     * Relation zum User.
     *
     * @return User
     */
    public function author (): User
    {
        return User::find($this->author);
    }

    /**
     * Relation zu den Kind-Kommentaren/Replies.
     *
     * @return array
     */
    public function replies (): array
    {
        return Comment::findWhere('parent', $this->id, 'crdate', 'DESC');
    }

    /**
     * crdate formatiert zurückgeben.
     *
     * Diese Hilfsfunktion dient uns dazu, $this->crdate hübsch zu formatieren und als String zurückzugeben. Das könnten
     * wir in den Views auch machen, aber hier haben wir es an einer zentralen Stelle.
     *
     * @return string
     * @throws \Exception
     */
    public function getCrdate (): string
    {
        /**
         * Die DateTime-Klasse wir von PHP mitgeliefert und bietet eine einfache Möglichkeit, wie diverse Datumsformate
         * einheitlich verarbeitet werden können.
         */
        $timestamp = new \DateTime($this->crdate);

        /**
         * DateTime-Objekt formatieren.
         */
        return $timestamp->format('d. M Y, H:i');
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
            return $database->query("UPDATE $tablename SET author = ?, content = ?, post_id = ?, rating = ?, parent = ? WHERE id = ?", [
                'i:author' => $this->author,
                's:content' => $this->content,
                'i:post_id' => $this->post_id,
                'i:rating' => $this->rating,
                'i:parent' => $this->parent,
                'i:id' => $this->id
            ]);
        } else {
            /**
             * Hat es keine ID, so müssen wir es neu anlegen.
             */
            $result = $database->query("INSERT INTO $tablename SET author = ?, content = ?, post_id = ?, rating = ?, parent = ?", [
                'i:author' => $this->author,
                's:content' => $this->content,
                'i:post_id' => $this->post_id,
                'i:rating' => $this->rating,
                'i:parent' => $this->parent
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
     * Toplevel Kommentare für einen bestimmten Post finden.
     *
     * Toplevel Kommentare sind solche, die keine Antwort sind, also keinen Eltern-Kommentar haben.
     *
     * @param int    $post_id
     * @param string $orderBy
     * @param string $direction
     *
     * @return array|bool
     */
    public static function findByPostTopLevel (int $post_id, string $orderBy = '', string $direction = 'ASC'): bool|array
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
            $results = $database->query("SELECT * FROM `{$tablename}` WHERE deleted_at IS NULL AND parent IS NULL AND post_id = ?", [
                's:post_id' => $post_id
            ]);
        } else {
            $results = $database->query("SELECT * FROM `{$tablename}` WHERE deleted_at IS NULL AND parent IS NULL AND post_id = ? ORDER BY $orderBy $direction", [
                's:post_id' => $post_id
            ]);
        }

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return self::handleResult($results);
    }

}
