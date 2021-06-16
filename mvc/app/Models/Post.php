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
     * Damit wir zukünftig an zentraler Stelle den Namen für die Mappingtabelle mit den Categories verwalten können,
     * legen wir eine Klassenkonstante an.
     */
    const TABLENAME_CATEGORIES_MM = 'posts_categories_mm';
    const TABLENAME_FILES_MM = 'posts_files_mm';
    const TITLE_PROPERTY = 'title';
    const SLUG_PROPERTY = 'slug';

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
     * @var array
     * @todo: comment
     */
    private array $_buffer;

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
            SELECT `{$tablename}`.* FROM `{$tablename}`
                JOIN `posts_categories_mm`
                    ON `posts_categories_mm`.`post_id` = `{$tablename}`.`id`
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
     * Relation zu Files
     *
     * @return array
     */
    public function files (): array
    {
        /**
         * Über das File Model alle zugehörigen Files abrufen.
         */
        return File::findByPost($this->id);
    }

    /**
     * Relation zu Comments.
     *
     * @return array
     */
    public function comments (): array
    {
        /**
         * Hier laden wir nur alle Toplevel Kommentare, die Antworten auf diese Kommentare laden wir später über die
         * jeweiligen Toplevel Kommentare selbst.
         */
        return Comment::findByPostTopLevel($this->id, 'crdate', 'DESC');
    }

    /**
     * Neue Liste an verknüpften Kategorien zuweisen.
     *
     * @param array<int> $categoryIds
     *
     * @return array
     */
    public function setCategories (array $categoryIds): array
    {
        /**
         * Zunächst holen wir uns die aktuell zugewiesenen Kategorien aus der Datenbank.
         */
        $oldCategories = $this->categories();

        /**
         * Dann bereiten wir uns zwei Arrays vor, damit wir die zu löschenden Zuweisungen und jene, die unverändert
         * bleiben sollen, speichern können. Daraus ergibt sich, dass alle weiteren, die in $categoryIds vorhanden sind,
         * neu angelegt werden müssen.
         */
        $categoriesToDelete = [];
        $categoriesToNotBeTouched = [];

        /**
         * Nun gehen wir alle alten Zuweisungen durch ...
         */
        foreach ($oldCategories as $oldCategory) {
            /**
             * ... und prüfen, ob sie auch in den neuen Kategorien vorkommen sollen.
             */
            if (!in_array($oldCategory->id, $categoryIds)) {
                /**
                 * Wenn nein, soll die Zuweisung gelöscht werden.
                 */
                $categoriesToDelete[] = $oldCategory->id;
            } else {
                /**
                 * Wenn ja, soll sie weiterhin bestehen bleiben.
                 */
                $categoriesToNotBeTouched[] = $oldCategory->id;
            }
        }

        /**
         * Nun berechnen wir uns die Differenz der drei Arrays, wobei alle Werte aus dem ersten Array das Ergebnis
         * bilden, die in keinem der weiteren Arrays vorhanden sind. Diese Kategorien müssen neu zugewiesen werden.
         */
        $categoriesToAdd = array_diff($categoryIds, $categoriesToDelete, $categoriesToNotBeTouched);

        /**
         * Nun gehen wir alle zu löschenden und neu anzulegenden Kategorieverbindungen durch und führen die Aktion aus.
         */
        foreach ($categoriesToDelete as $categoryToDelete) {
            $this->detachCategory($categoryToDelete);
        }
        foreach ($categoriesToAdd as $categoryToAdd) {
            $this->attachCategory($categoryToAdd);
        }

        /**
         * Neue Liste aller Kategorien für den Post zurückgeben.
         */
        return $this->categories();
    }

    /**
     * Verknüpfung zu einer Kategorie aufheben.
     *
     * @param int $categoryId
     *
     * @return bool
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
        $results = $database->query("DELETE FROM `{$tablename}` WHERE post_id = ? AND category_id = ?", [
            'i:post_id' => $this->id,
            'i:category_id' => $categoryId
        ]);

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return $results;
    }

    /**
     * Verknüpfung zu einer Kategorie herstellen.
     *
     * @param int $categoryId
     *
     * @return bool
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
        $results = $database->query("INSERT INTO `{$tablename}` SET post_id = ?, category_id = ?", [
            'i:post_id' => $this->id,
            'i:category_id' => $categoryId
        ]);


        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return $results;
    }

    /**
     * Neue Liste an verknüpften Files zuweisen.
     *
     * @param array<int> $fileIds
     *
     * @return array
     */
    public function setFiles (array $fileIds): array
    {
        /**
         * Zunächst holen wir uns die aktuell zugewiesenen Kategorien aus der Datenbank.
         */
        $oldFiles = $this->files();

        /**
         * Dann bereiten wir uns zwei Arrays vor, damit wir die zu löschenden Zuweisungen und jene, die unverändert
         * bleiben sollen, speichern können. Daraus ergibt sich, dass alle weiteren, die in $categoryIds vorhanden sind,
         * neu angelegt werden müssen.
         */
        $filesToDelete = [];
        $filesNotToBeTouched = [];

        /**
         * Nun gehen wir alle alten Zuweisungen durch ...
         */
        foreach ($oldFiles as $oldFile) {
            /**
             * ... und prüfen, ob sie auch in den neuen Files vorkommen sollen.
             */
            if (!in_array($oldFile->id, $fileIds)) {
                /**
                 * Wenn nein, soll die Zuweisung gelöscht werden.
                 */
                $filesToDelete[] = $oldFile->id;
            } else {
                /**
                 * Wenn ja, soll sie weiterhin bestehen bleiben.
                 */
                $filesNotToBeTouched[] = $oldFile->id;
            }
        }

        /**
         * Nun berechnen wir uns die Differenz der drei Arrays, wobei alle Werte aus dem ersten Array das Ergebnis
         * bilden, die in keinem der weiteren Arrays vorhanden sind. Diese Files müssen neu zugewiesen werden.
         */
        $filesToAdd = array_diff($fileIds, $filesToDelete, $filesNotToBeTouched);

        /**
         * Nun gehen wir alle zu löschenden und neu anzulegenden Fileverbindung durch und führen die Aktion aus.
         */
        foreach ($filesToDelete as $fileToDelete) {
            $this->detachFile($fileToDelete);
        }
        foreach ($filesToAdd as $fileToAdd) {
            $this->attachFile($fileToAdd);
        }

        /**
         * Neue Liste aller Files für den Post zurückgeben.
         */
        return $this->files();
    }

    /**
     * Verknüpfung zu einem File aufheben.
     *
     * @param int $fileId
     *
     * @return bool
     */
    public function detachFile (int $fileId): bool
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $database = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tablename = self::TABLENAME_FILES_MM;

        /**
         * Query ausführen.
         */
        $results = $database->query("DELETE FROM `{$tablename}` WHERE post_id = ? AND file_id = ?", [
            'i:post_id' => $this->id,
            'i:file_id' => $fileId
        ]);

        /**
         * Datenbankergebnis verarbeiten und zurückgeben.
         */
        return $results;
    }

    /**
     * Verknüpfung zu einem File herstellen.
     *
     * @param int $fileId
     *
     * @return bool
     */
    public function attachFile (int $fileId): bool
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $database = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tablename = self::TABLENAME_FILES_MM;

        /**
         * Query ausführen.
         */
        $results = $database->query("INSERT INTO `{$tablename}` SET post_id = ?, file_id = ?", [
            'i:post_id' => $this->id,
            'i:file_id' => $fileId
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

    /**
     * @param int $user_id
     *
     * @todo: comment
     */
    public function hasBeenRatedByUser (int $user_id): bool
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $database = new Database();

        $result = $database->query("SELECT COUNT(*) as count FROM comments WHERE author = ? AND post_id = ? AND rating IS NOT NULL", [
            'i:author' => $user_id,
            'i:post_id' => $this->id
        ]);

        $numerOfRatings = $result[0]['count'];
        $hasBeenRated = $numerOfRatings > 0;

        return $hasBeenRated;
    }

    /**
     * @return array
     * @todo: comment
     */
    public function getAverageAndNumberRatings (): array
    {
        if (empty($this->_buffer)) {

            /**
             * Datenbankverbindung herstellen.
             */
            $database = new Database();

            $result = $database->query("SELECT AVG(rating) as average, COUNT(*) as count FROM comments WHERE post_id = ? AND rating IS NOT NULL", [
                'i:post_id' => $this->id
            ]);

            $numberOfRatings = (int)$result[0]['count'];
            $average = (float)$result[0]['average'];

            $this->_buffer = [
                'average' => $average,
                'numberOfRatings' => $numberOfRatings
            ];

        }

        return $this->_buffer;
    }
}
