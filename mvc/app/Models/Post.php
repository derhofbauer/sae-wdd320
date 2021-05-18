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
class Post extends AbstractModel
{
    /**
     * @todo: comment
     */
    const TABLENAME_CATEGORIES_MM = 'posts_categories_mm';

    /**
     * Wir innerhalb einer Klasse das use-Keyword verwendet, so wird damit ein Trait importiert. Das kann man sich
     * vorstellen wie einen Import mittels require, weil die Methoden, die im Trait definiert sind, einfach in die
     * Klasse, die den Trait verwendet, eingefügt werden, als ob sie in der Klasse selbst definiert worden wären.
     * Das hat den Vorteil, dass Methoden, die in mehreren Klassen vorkommen, zentral definiert und verwaltet werden
     * können in einem Trait, und dennoch überall dort eingebunden werden, wo sie gebraucht werden, ohne Probleme mit
     * komplexen und sehr verschachtelten Vererbungen zu kommen.
     */
    use HasSlug, SoftDelete;

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
    public function teaser (int $length = 240): string
    {
        /**
         * Hier müssen wir prüfen, ob der Content kürzer ist als die gewünschte Länge, weil sonst gibt uns der Aufruf
         * der substr()-Funktion unten einen Fehler zurück, weil ein Substring nicht länger sein kann, als der Original-
         * String. Ist die gewünschte Länge also großer als der vorhandene Content, so geben wir den Content direkt
         * zurück, weil er nicht gekürzt werden muss.
         */
        if ($length > strlen($this->content)) {
            return $this->content;
        }

        /**
         * Andernfalls geben wir den Content gekürzt auf die gewünschte Länge zurück.
         */
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
    public function teaserSentence (int $length = 240): string
    {
        /**
         * Hier müssen wir prüfen, ob der Content kürzer ist als die gewünschte Länge, weil sonst gibt uns der Aufruf
         * der substr()-Funktion unten einen Fehler zurück, weil ein Substring nicht länger sein kann, als der Original-
         * String. Ist die gewünschte Länge also großer als der vorhandene Content, so geben wir den Content direkt
         * zurück, weil er nicht gekürzt werden muss.
         */
        if ($length > strlen($this->content)) {
            return $this->content;
        }

        /**
         * Index des ersten Punktes NACH $length suchen.
         */
        $indexOfNextPeriod = strpos($this->content, '.', $length);

        /**
         * Gibt es einen Punkt nach der gewünschten Länge?
         */
        if ($indexOfNextPeriod !== false) {
            /**
             * String bis zu diesem gefundenen Punkt zurückgeben.
             *
             * +1 müssen wir rechnen, weil die strpos()-Funktion bei 0 anfängt und substr() bei 1.
             */
            return substr($this->content, 0, $indexOfNextPeriod + 1);
        } else {
            /**
             * Wurde kein Punkt gefunden, so verwenden wir einfach die normale teaser()-Methode, die den Content einfach
             * rücksichtslos kürzt.
             */
            return self::teaser($length);
        }
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
                AND `posts`.`deleted_at` IS NULL
            ORDER BY crdate;
        ", [
            'i:category_id' => $categoryId
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

    /**
     * @todo: comment
     * @param array<int> $categoryIds
     *
     * @return array
     */
    public function setCategories (array $categoryIds): array
    {
        $oldCategories = $this->categories();
        $categoriesToDelete = [];
        $categoriesToNotBeTouched = [];

        foreach ($oldCategories as $oldCategory) {
            if (!in_array($oldCategory->id, $categoryIds)) {
                $categoriesToDelete[] = $oldCategory->id;
            } else {
                $categoriesToNotBeTouched[] = $oldCategory->id;
            }
        }

        $categoriesToAdd = array_diff($categoryIds, $categoriesToDelete, $categoriesToNotBeTouched);

        foreach ($categoriesToDelete as $categoryToDelete) {
            $this->detachCategory($categoryToDelete);
        }
        foreach ($categoriesToAdd as $categoryToAdd) {
            $this->attachCategory($categoryToAdd);
        }

        return $this->categories();
    }

    /**
     * @param int $categoryId
     *
     * @return bool
     * @todo: comment
     */
    public function detachCategory (int $categoryId): bool
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $database = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tablename = self::TABLENAME_CATEGORIES_MM;

        /**
         * Query ausführen.
         */
        $results = $database->query("DELETE FROM {$tablename} WHERE post_id = ? AND category_id = ?", [
            'i:post_id' => $this->id,
            'i:category_id' => $categoryId
        ]);

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return $results;
    }

    /**
     * @param int $categoryId
     *
     * @return bool
     * @todo: comment
     */
    public function attachCategory (int $categoryId): bool
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $database = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tablename = self::TABLENAME_CATEGORIES_MM;

        /**
         * Query ausführen.
         */
        $results = $database->query("INSERT INTO {$tablename} SET post_id = ?, category_id = ?", [
            'i:post_id' => $this->id,
            'i:category_id' => $categoryId
        ]);


        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return $results;
    }

    /**
     * Relation zu Users.
     *
     * @return User
     */
    public function author (): User
    {
        /**
         * Nachdem ein Post nur einen Author haben kann, haben wir die User ID des Authors bereits abgefragt und können
         * daher ein einfaches find() machen.
         */
        return User::find($this->author);
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
            return $database->query("UPDATE $tablename SET title = ?, slug = ?, content = ?, author = ? WHERE id = ?", [
                's:title' => $this->title,
                's:slug' => $this->slug,
                's:content' => $this->content,
                'i:author' => $this->author,
                'i:id' => $this->id
            ]);
        } else {
            /**
             * Hat es keine ID, so müssen wir es neu anlegen.
             */
            $result = $database->query("INSERT INTO $tablename SET title = ?, slug = ?, content = ?, author = ?", [
                's:title' => $this->title,
                's:slug' => $this->slug,
                's:content' => $this->content,
                'i:author' => $this->author
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
