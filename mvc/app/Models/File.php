<?php

namespace App\Models;

use Core\Config;
use Core\Database;
use Core\Models\AbstractFile;

/**
 * Class File
 *
 * @package App\Models
 * @todo    : comment
 */
class File extends AbstractFile
{
    public int $id;
    public string $path;
    public string $name;
    public ?string $title = null;
    public ?string $alttext = null;
    public ?string $caption = null;
    public bool $is_avatar = false;
    public int $author;
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
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }

    public function validateAvatar (): bool
    {
        if (!$this->validateImage()) {
            return false;
        }

        $this->is_avatar = true;

        [$maxWidth, $maxHeight] = Config::get('app.avatar-max-dimensions');
        [$width, $height] = getimagesize($this->tmp_name);
        if ($width > $maxWidth || $height > $maxHeight) {
            $this->errors[] = "Die Dimensionen des Bildes überschreiten die Maximalgrößen ({$maxWidth}x{$maxHeight}).";
            return false;
        }

        return true;
    }

    public function putTo (string $relativeStoragePath = null, string $filename = null): File
    {
        $filepath = parent::putTo($relativeStoragePath, $filename);
        return $this->handlePut($filepath);
    }

    public function put (string $relativeStoragePath = null, string $filename = null): File
    {
        $filepath = parent::put($relativeStoragePath, $filename);
        return $this->handlePut($filepath);
    }

    /**
     * @param string $filepath
     *
     * @return $this
     */
    private function handlePut (string $filepath): File
    {
        $storagePath = AbstractFile::getStoragePath();
        $relativePath = str_replace($storagePath, '', $filepath);
        $relativePath = ltrim($relativePath, '/');
        $this->path = dirname($relativePath);
        $this->name = basename($filepath);
        $this->author = User::getLoggedIn()->id;

        $this->save();

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
            return $database->query("UPDATE $tablename SET path = ?, name = ?, title = ?, alttext = ?, caption = ?, is_avatar = ?, author = ? WHERE id = ?", [
                's:path' => $this->path,
                's:name' => $this->name,
                's:title' => $this->title,
                's:alttext' => $this->alttext,
                's:caption' => $this->caption,
                'i:is_avatar' => $this->is_avatar,
                'i:author' => $this->author,
                'i:id' => $this->id
            ]);
        } else {
            /**
             * Hat es keine ID, so müssen wir es neu anlegen.
             */
            $result = $database->query("INSERT INTO $tablename SET path = ?, name = ?, title = ?, alttext = ?, caption = ?, is_avatar = ?, author = ?", [
                's:path' => $this->path,
                's:name' => $this->name,
                's:title' => $this->title,
                's:alttext' => $this->alttext,
                's:caption' => $this->caption,
                'i:is_avatar' => $this->is_avatar,
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
     * @param bool $absolute
     *
     * @return string
     * @todo: comment
     */
    public function getFilePath (bool $absolute = false, bool $http = false): string
    {
        $path = $this->path . '/' . $this->name;
        if ($absolute === true) {
            if ($http === true) {
                return BASE_URL . '/storage/' . $path;
            } else {
                return AbstractFile::getStoragePath() . '/' . $path;
            }
        }
        return $path;
    }

    public function getImageDimensions (): array
    {
        return getimagesize($this->getFilePath(true));
    }

    /**
     * @param int $maxWidth
     * @param int $maxHeight
     *
     * @return string
     * @todo: comment
     */
    public function getImgTag (): string
    {
        [$height, $width] = $this->getImageDimensions();
        return sprintf('<img src="%s" title="%s" alt="%s" width="%s" height="%s">', $this->getFilePath(true, true), $this->title, $this->alttext, $height, $width);
    }

}
