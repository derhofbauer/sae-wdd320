<?php

namespace Core\Models;

use App\Models\File;
use Core\Config;

/**
 * Class AbstractFile
 *
 * Damit wir eine Abstraktionsebene über das Dateisystem legen können, bauen wir eine eigene Klasse, die die Arbeit mit
 * einzelnen Dateien vereinfachen soll.
 *
 * @package Core\Models
 */
abstract class AbstractFile extends AbstractModel
{
    /**
     * Properties definieren.
     */
    public string $name;
    public string $type;
    public string $tmp_name;
    public int $error;
    public int $size;
    /**
     * Fehler Array vorbereiten.
     */
    protected array $errors;

    /**
     * Der Konstruktor befüllt das Objekt, sofern Daten übergeben worden sind.
     *
     * @param array $data
     */
    public function __construct (array $data = [])
    {
        /**
         * Die Methode wird in AbstractModel nur abstrakt definiert und kann in jedem Model, dass die Klasse erweitert,
         * überschrieben werden.
         */

        /**
         * Wenn Daten übergeben wurden, so füllen wir das aktuelle Objekt damit.
         */
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    /**
     * Hier definieren wir, dass eine Klasse, die AbstractModel erweitert, eine fill() Methode haben MUSS.
     *
     * @param array $data
     */
    public function fillUploadedData (array $data)
    {
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->tmp_name = $data['tmp_name'];
        $this->error = $data['error'];
        $this->size = $data['size'];
    }

    /**
     * Hilfsfunktion zur einfachen Prüfung ob ein Upload Fehler aufgetreten ist.
     *
     * @return bool
     */
    public function hasUploadError (): bool
    {
        return $this->error !== UPLOAD_ERR_OK;
    }

    /**
     * Hilfsfunktion zur Prüfung ob die Datei alle Größen- und Dimensionsbeschränkungen erfüllt.
     *
     * @return bool
     * @todo: comment
     */
    public function validateImage (): bool
    {
        $uploadLimit = Config::get('app.upload-limit');

        if ($this->size > $uploadLimit) {
            $uploadLimitNice = $uploadLimit / 1024 / 1024; // Upload Limit in MB
            $this->errors[] = "Upload Limit überschritten ({$uploadLimitNice}).";
            return false;
        }

        if (!str_contains($this->type, 'image/')) {
            $this->errors[] = 'Die Datei ist kein Bild!';
            return false;
        }

        return true;
    }

    /**
     * @param string|null $relativeStoragePath
     * @param string|null $filename
     *
     * @return string|false|File Filepath, an den das File gespeichert wurde
     *
     * @throws \Exception
     * @todo: comment
     */
    public function putTo (string $relativeStoragePath = null, string $filename = null): string|false|File
    {
        $absoluteStoragePath = self::getStoragePath();

        $destinationPath = $absoluteStoragePath . '/' . $relativeStoragePath; // /uploads

        if (file_exists($destinationPath) && !is_dir($destinationPath)) {
            throw new \Exception('Uploads folder already exists as file.');
        }

        if (!file_exists($destinationPath) && !mkdir($destinationPath, recursive: true)) {
            throw new \Exception('Uploads folder could not be created.');
        }

        $destinationName = time() . "_$this->name";
        $destinationPath = $destinationPath . '/' . $destinationName;
        $destinationPath = str_replace('//', '/', $destinationPath);

        if (move_uploaded_file($this->tmp_name, $destinationPath)) {
            return $destinationPath;
        } else {
            return false;
        }
    }

    /**
     * @return string
     * @todo: comment
     */
    public static function getStoragePath (): string
    {
        $absoluteStoragePath = __DIR__ . '/../../storage';
        $absoluteStoragePath = realpath($absoluteStoragePath);
        return $absoluteStoragePath;
    }

    /**
     * @param string|null $relativeStoragePath
     * @param string|null $filename
     *
     * @return string
     * @throws \Exception
     * @todo: comment
     */
    public function put (string $relativeStoragePath = null, string $filename = null): string|File
    {
        if ($relativeStoragePath === null) {
            $relativeStoragePath = Config::get('app.uploads-folder');
        }
        return $this->putTo($relativeStoragePath, $filename);
    }

    /**
     * @return array
     * @todo: comment
     */
    public function getErrors (): array
    {
        return $this->errors;
    }
}
