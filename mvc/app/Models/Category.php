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
     * @todo: comment
     */
    const TABLENAME = 'categories';

    /**
     * @todo: comment
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
     * @param int $postId
     *
     * @return array
     * @todo: comment
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
         */
        $results = $database->query("SELECT {$tablename}.* FROM {$tablename} JOIN posts_categories_mm ON posts_categories_mm.category_id = categories.id WHERE posts_categories_mm.post_id = ?", [
            'i:post_id' => $postId
        ]);

        /**
         * @todo: comment
         */
        $result = self::handleResult($results);

        return $result;
    }
}
