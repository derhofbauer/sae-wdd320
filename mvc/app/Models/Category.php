<?php

namespace App\Models;

use Core\Database;
use Core\Models\AbstractModel;
use Core\Traits\HasSlug;

/**
 * Class Post
 *
 * @package App\Models
 */
class Category extends AbstractModel
{
    /**
     * Nachdem aus "Category" über unsere normale getTablenameFromClassname() Methode "categorys" würde und nicht
     * "categories", wie die Tabelle wirklich heißt, können wir hier die TABLENAME Klassenkonstante nutzen, die wir in
     * in der genannten Funktion abfragen um die Berechnung zu überschreiben.
     */
    const TABLENAME = 'categories';

    /**
     * s. Post.php
     */
    use HasSlug;

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $title;
    public string $slug;
    public string $description;
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
        $this->title = $data['title'];
        $this->slug = $data['slug'];
        $this->description = (string)$data['description'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }

    /**
     * Alle Categories zu einem bestimmten Post abfragen.
     *
     * Das ist sehr ähnlich wie die AbstractModel::all() Methode, nur ist der Query ein bisschen anders.
     *
     * @param int $postId
     *
     * @return array
     */
    public static function findByPost (int $postId): array
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
         * Hier führen wir einen JOIN Query aus, weil wir Daten aus zwei Tabellen zusammenführen möchten.
         */
        $results = $database->query("
            SELECT {$tablename}.* FROM {$tablename}
                JOIN `posts_categories_mm`
                    ON `posts_categories_mm`.`category_id` = {$tablename}.`id`
            WHERE `posts_categories_mm`.`post_id` = ?
        ", [
            'i:post_id' => $postId
        ]);

        /**
         * Im AbstractModel haben wir diese Funktionalität aus der all()-Methode herausgezogen und in eine eigene
         * Methode verpackt, damit wir in allen anderen Methoden, die zukünftig irgendwelche Daten aus der Datenbank
         * abfragen, den selben Code verwenden können und nicht Code duplizieren müssen.
         */
        $result = self::handleResult($results);

        /**
         * Ergebnis zurückgeben.
         */
        return $result;
    }

    /**
     * Relation zu Posts
     *
     * @return array
     */
    public function posts (): array
    {
        /**
         * Über das Post Model alle zugehörigen Posts abrufen.
         */
        return Post::findByCategory($this->id);
    }

    /**
     * @return bool
     * @todo: comment
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

        if (!empty($this->id)) {
            // Objekt existiert in der DB bereits
            return $database->query("UPDATE $tablename SET title = ?, slug = ?, description = ? WHERE id = ?", [
                's:title' => $this->title,
                's:slug' => $this->slug,
                's:description' => $this->description,
                'i:id' => $this->id
            ]);
        } else {
            // Objekt existiert in der DB noch nicht
            $result = $database->query("INSERT INTO $tablename SET title = ?, slug = ?, description = ?", [
                's:title' => $this->title,
                's:slug' => $this->slug,
                's:description' => $this->description
            ]);

            $this->handleInsertResult($database);

            return $result;
        }
    }

}
