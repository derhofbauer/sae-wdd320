<?php

namespace App\Models;

use Core\Config;
use Core\Database;
use Core\Models\AbstractFile;
use Core\Traits\SoftDelete;

/**
 * Class File
 *
 * Diese Klasse abstrahiert den Datei-Zugriff, sodass wir überall in unserem System diese Klasse verwenden können. Der
 * Core liefert eine AbstractFile Basisklasse, die alle Funktionalitäten bereits implementiert, wir haben in unserer App
 * aber speziellere Anforderungen an Dateien, weshalb wir eine eigene Klasse dafür gebaut haben und die AbstractFile
 * Klasse erweitern und einzelne Methoden überschreiben oder erweitern.
 *
 * @package App\Models
 */
class File extends AbstractFile
{
    use SoftDelete;

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen. Die AbstractFile Klasse beinhaltet
     * ihrerseits die selben Properties wie die $_FILES Superglobal, wodurch diese auch hier in File verfügbar sind.
     */
    public int $id;
    public string $path;
    public string $name;
    public ?string $title = null;
    public ?string $alttext = null;
    public ?string $caption = null;
    public bool $is_avatar = false;
    public int $author;
    public string $path_deleted;
    public string $crdate;
    public string $tstamp;
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
        $this->path = $data['path'];
        $this->name = $data['name'];
        $this->title = $data['title'];
        $this->alttext = $data['alttext'];
        $this->caption = $data['caption'];
        $this->is_avatar = $data['is_avatar'];
        $this->author = $data['author'];
        $this->path_deleted = (string)$data['path_deleted'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }

    /**
     * Eine Funktion, die die vom AbstractFile definierte Funktion validateImage() ergänzt.
     *
     * @return bool
     */
    public function validateAvatar (): bool
    {
        /**
         * Wenn die validateImage()-Methode aus dem AbstractFile nicht erfolgreich validiert, geben wir direkt false
         * zurück.
         */
        if (!$this->validateImage()) {
            return false;
        }

        /**
         * Andernfalls definieren wir, dass es sich bei dem aktuellen File um ein Avatarbild handelt.
         */
        $this->is_avatar = true;

        /**
         * Nun holen wir uns die maximalen Dimensionen für Avatarbilder aus der Config.
         */
        [$maxWidth, $maxHeight] = Config::get('app.avatar-max-dimensions');
        /**
         * Dann holen wir die tatsächlichen Dimensionen des aktuellen Bildes ...
         */
        [$width, $height] = getimagesize($this->tmp_name);
        /**
         * ... und vergleichen sie mit den Maximalwerten.
         */
        if ($width > $maxWidth || $height > $maxHeight) {
            /**
             * Überschreitet eine der Dimensionen die Maximalwerte, schreiben wir einen Fehler.
             */
            $this->errors[] = "Die Dimensionen des Bildes überschreiten die Maximalgrößen ({$maxWidth}x{$maxHeight}).";
            return false;
        }

        /**
         * Ist auch die Dimensionsprüfung erfolgreich, wurde das Avatarbild erfolgreich validiert und wir geben true
         * zurück.
         */
        return true;
    }

    /**
     * Hier erweitern wir die putTo()-Methode aus dem AbstractFile um die Erstellung eines Datenbankeintrags.
     *
     * @param string|null $relativeStoragePath
     * @param string|null $filename
     *
     * @return $this
     * @throws \Exception
     */
    public function putTo (string $relativeStoragePath = null, string $filename = null): File
    {
        /**
         * Das parent-Keyword ruft die putTo()-Methode der Elternklasse auf. Die Elternklasse ist die Klasse, die
         * extended wird - in diesem Fall also AbstractFile.
         */
        $filepath = parent::putTo($relativeStoragePath, $filename);
        /**
         * Nun verarbeiten wir das Ergebnis der Speicherung anhand des Dateipfades.
         */
        return $this->handlePut($filepath);
    }

    /**
     * Hier erweitern wir die put()-Methode aus dem AbstractFile um die Erstellung eines Datenbankeintrags.
     *
     * @param string|null $relativeStoragePath
     * @param string|null $filename
     *
     * @return $this
     * @throws \Exception
     */
    public function put (string $relativeStoragePath = null, string $filename = null): File
    {
        /**
         * Das parent-Keyword ruft die putTo()-Methode der Elternklasse auf. Die Elternklasse ist die Klasse, die
         * extended wird - in diesem Fall also AbstractFile.
         */
        $filepath = parent::put($relativeStoragePath, $filename);
        /**
         * Nun verarbeiten wir das Ergebnis der Speicherung anhand des Dateipfades.
         */
        return $this->handlePut($filepath);
    }

    /**
     * Um einen ordentlichen Media Manager bauen zu können, brauchen wir einen Index aller Dateien in der Datenbank.
     * Diese Methode erstellt einen solchen Eintrag.
     *
     * @param string $filepath
     *
     * @return $this
     */
    private function handlePut (string $filepath): File
    {
        /**
         * Wir wandeln zu allererst den $filepath in einen Pfad um, der relativ zu unserem Storage Path ist.
         */
        $this->path = self::convertToRelativePath($filepath);

        /**
         * Die basename()-Funktion gibt aus einem Pfad den Dateinamen zurück.
         *
         * Bsp.: basename('/some/path/foobar.txt') --> 'foobar.txt'
         */
        $this->name = basename($filepath);
        /**
         * Autor der Datei ist die gerade eingeloggte Person.
         */
        $this->author = User::getLoggedIn()->id;

        /**
         * Eintrag in die Datenbank speichern.
         */
        $this->save();

        /**
         * Damit wir einfach damit arbeiten können, geben wir das aktuelle Objekt zurück, das nun auch eine ID haben
         * sollte durch die Datenbank.
         */
        return $this;
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
            return $database->query("UPDATE $tablename SET path = ?, name = ?, title = ?, alttext = ?, caption = ?, is_avatar = ?, author = ?, path_deleted = ? WHERE id = ?", [
                's:path' => $this->path,
                's:name' => $this->name,
                's:title' => $this->title,
                's:alttext' => $this->alttext,
                's:caption' => $this->caption,
                'i:is_avatar' => $this->is_avatar,
                'i:author' => $this->author,
                's:path_deleted' => $this->path_deleted,
                'i:id' => $this->id
            ]);
        } else {
            /**
             * Hat es keine ID, so müssen wir es neu anlegen.
             */
            $result = $database->query("INSERT INTO $tablename SET path = ?, name = ?, title = ?, alttext = ?, caption = ?, is_avatar = ?, author = ?, path_deleted = ?", [
                's:path' => $this->path,
                's:name' => $this->name,
                's:title' => $this->title,
                's:alttext' => $this->alttext,
                's:caption' => $this->caption,
                'i:is_avatar' => $this->is_avatar,
                'i:author' => $this->author,
                's:path_deleted' => $this->path_deleted
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
     * Eine Hilfsfunktion, die 3 Mögliche Dateipfade berechnen kann:
     *
     * + ein Dateipfad relativ zum Storage Ordner
     * + ein Dateipfad absolut zum Server Root ('/')
     * + ein absoluter Dateipfad als URL für die Verwendung in <img>-Tags
     *
     * @param bool $absolute
     * @param bool $http
     *
     * @return string
     */
    public function getFilePath (bool $absolute = false, bool $http = false): string
    {
        /**
         * Wir fügen path und name zusammen und haben somit einen Pfad, der relativ zum Storage Folder ist.
         */
        $path = $this->path . '/' . $this->name;
        /**
         * Wenn ein absoluter Pfad generiert werden soll ...
         */
        if ($absolute === true) {
            /**
             * ... unterscheiden wir, ob wir den Pfad als URL oder als Dateisystem-Pfad erhalten möchten.
             */
            if ($http === true) {
                /**
                 * Unter Zuhilfenahme der BASE_URL erstellen wir eine Datei-URL, die wir einfach in <img>-Tags verwenden
                 * können.
                 */
                return BASE_URL . '/storage/' . $path;
            } else {
                /**
                 * Soll ein Dateisystempfad generiert werden, so nutzen wir dazu die Methode für den Storage Pfad aus
                 * dem AbstractFile.
                 */
                return AbstractFile::getStoragePath() . '/' . $path;
            }
        }
        /**
         * In jedem Fall geben wir den berechneten Pfad am Ende zurück. Zu beachten ist, dass wir nicht prüfen, ob der
         * Pfad auch wirklich korrekt ist und eine Datei an diesem Pfad zu finden ist.
         */
        return $path;
    }

    /**
     * Hilfsfunktion zur berechnung der Abmessungen einer Rastergrafik.
     *
     * @return array
     */
    public function getImageDimensions (): array
    {
        /**
         * Zuerst generieren wir den Dateipfad als absoluten Filesystem Pfad und berechnen danach die Dimensionen des
         * Bildes.
         */
        return getimagesize($this->getFilePath(true));
    }

    /**
     * Hilfsfunktion zur Generierung eines <img>-Tag aus der aktuellen Datei.
     *
     * @return string
     */
    public function getImgTag (): string
    {
        /**
         * Dimensionen des Bildes berechnen.
         */
        [$height, $width] = $this->getImageDimensions();
        /**
         * <img>-Tag befüllen.
         */
        return sprintf('<img src="%s" title="%s" alt="%s" width="%s" height="%s">', $this->getFilePath(true, true), $this->title, $this->alttext, $height, $width);
    }

    /**
     * Datei und zugehörigen Datensatz softdeleten.
     *
     * @param string $filepathRelativeToStorage
     *
     * @return bool|int
     */
    public function deleteFile (string $filepathRelativeToStorage = '_trash_'): bool|int
    {
        /**
         * Datei in den Papierkorb schieben.
         */
        $trashedPath = $this->moveTo($filepathRelativeToStorage);

        /**
         * Wurde nicht false zurückgegeben und hat der Move somit funktioniert, dann speichern wir diesen Pfad in die
         * Datenbank zum zugehörigen Eintrag.
         */
        if ($trashedPath !== false) {
            $this->path_deleted = self::convertToRelativePath($trashedPath);
        }
        /**
         * File in der Datenbank aktualisieren.
         */
        $this->save();
        /**
         * File in der Datenbank löschen. Hier wird durch den SoftDelete-Trait, den wir oben eingebunden haben, ein
         * ein Softdelete durchgeführt.
         */
        $this->delete();

        /**
         * Nun geben wir true zurück, weil wir nichts besseres haben, was wir zurückgeben könnten.
         */
        return true;
    }

    /**
     * Pfad in einen Pfad umwandeln, der relativ zu unserem Storage Path ist.
     *
     * @param string $absolutePath
     *
     * @return string
     */
    private static function convertToRelativePath (string $absolutePath): string
    {
        /**
         * StoragePath aus dem AbstractFile holen. Dieser Pfas ist absolut zum Server Root.
         */
        $storagePath = AbstractFile::getStoragePath();
        /**
         * Jetzt wandeln wir den absoluten $filepath der gespeicherten Datei in einen relativen Pfad um, indem wir den
         * $storagePath einfach entfernen. Was übrig bleibt, ist der Teil, der über den $storagePath hinausgeht - ein
         * relativer Pfad.
         */
        $relativePath = str_replace($storagePath, '', $absolutePath);
        /**
         * Wir entfernen alle / auf der linken Seite des Pfades.
         */
        $relativePath = ltrim($relativePath, '/');
        /**
         * Die dirname()-Funktion gibt aus einem Pfad alles zurück, was nicht der Dateiname ist.
         *
         * Bsp.: dirname('/some/path/foobar.txt') --> '/some/path/'
         */
        $dirname = dirname($relativePath);

        /**
         * Ist der $dirname nur '.' (Punkt), dann war $absolutePath ein Ordner und keine Datei. In diesem Fall brauchen
         * wir den dirname nicht generieren und geben direkt den $relativPath zurück. Andernfalls, wenn der Pfad zu
         * einer Datei übergeben wurde, geben wir den dirname der Datei zurück.
         */
        if ($dirname === '.') {
            return $relativePath;
        }
        return $dirname;
    }

    /**
     * Alle Files zu einem bestimmten Post abfragen.
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
                JOIN `posts_files_mm`
                    ON `posts_files_mm`.`file_id` = {$tablename}.`id`
            WHERE `posts_files_mm`.`post_id` = ?
                AND `files`.`deleted_at` IS NULL
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
}
