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
class Post extends AbstractModel
{
    /**
     * Wir innerhalb einer Klasse das use-Keyword verwendet, so wird damit ein Trait importiert. Das kann man sich
     * vorstellen wie einen Import mittels require, weil die Methoden, die im Trait definiert sind, einfach in die
     * Klasse, die den Trait verwendet, eingefügt werden, als ob sie in der Klasse selbst definiert worden wären.
     * Das hat den Vorteil, dass Methoden, die in mehreren Klassen vorkommen, zentral definiert und verwaltet werden
     * können in einem Trait, und dennoch überall dort eingebunden werden, wo sie gebraucht werden, ohne Probleme mit
     * komplexen und sehr verschachtelten Vererbungen zu kommen.
     */
    use HasSlug;

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $title;
    public string $slug;
    public string $content;
    public int $author;
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
        $this->content = $data['content'];
        $this->author = $data['author'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }

    /**
     * "computed property" teaser
     *
     * @param int $length
     *
     * @return string
     */
    public function teaser ($length = 240): string
    {
        return substr($this->content, 0, $length);
    }

    /**
     * "computed property" teaserSentence
     *
     * Hier wird eine $length angegeben, aber der Teaser wird bis zum nächsten Punkt zurückgegeben.
     *
     * @param int $length
     *
     * @return string
     */
    public function teaserSentence ($length = 240): string
    {
        /**
         * Index des ersten Punktes NACH $length suchen.
         */
        $indexOfNextPeriod = strpos($this->content, '.', $length);

        /**
         * String bis zu diesem gefundenen Punkt zurückgeben.
         *
         * +1 müssen wir rechnen, weil die strpos()-Funktion bei 0 anfängt und substr() bei 1.
         */
        return substr($this->content, 0, $indexOfNextPeriod + 1);
    }

    /**
     * Alle Posts zu einer bestimmten Category abfragen.
     *
     * Das ist sehr ähnlich wie die AbstractModel::all() Methode, nur ist der Query ein bisschen anders.
     *
     * @param int $categoryId
     *
     * @return array
     */
    public static function findByCategory (int $categoryId): array
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
                    ON `posts_categories_mm`.`post_id` = {$tablename}.`id`
            WHERE `posts_categories_mm`.`category_id` = ?
            ORDER BY crdate;
        ", [
            'i:category_id' => $categoryId
        ]);

        /**
         * im AbstractModel haben wir diese Funktionalität aus der all()-Methode herausgezogen und in eine eigene
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
     * Relation zu Categories
     *
     * @return array
     */
    public function categories (): array
    {
        /**
         * Über das Category Model alle zugehörigen Categories abrufen.
         */
        return Category::findByPost($this->id);
    }
}
