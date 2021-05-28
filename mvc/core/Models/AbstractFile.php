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
    protected array $errors = [];

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
     * @param array    $data
     * @param int|null $key
     */
    public function fillUploadedData (array $data, int $key = null)
    {
        if ($key !== null) {
            $this->name = $data['name'][$key];
            $this->type = $data['type'][$key];
            $this->tmp_name = $data['tmp_name'][$key];
            $this->error = $data['error'][$key];
            $this->size = $data['size'][$key];
        } else {
            $this->name = $data['name'];
            $this->type = $data['type'];
            $this->tmp_name = $data['tmp_name'];
            $this->error = $data['error'];
            $this->size = $data['size'];
        }
    }

    /**
     * File Objekte aus den Daten aus der $_FILES Superglobal erstellen.
     *
     * @param string $keyInFilesSuperglobal Name des Upload Feldes im Formular
     * @param string $fileClass             Klasse, auf Basis derer Objekte erstellt werden sollen.
     *
     * @return array
     */
    public static function createFromUpload (string $keyInFilesSuperglobal, string $fileClass = File::class): array
    {
        /**
         * Daten zu einem bestimmten Upload Feld aus $_FILES holen.
         */
        $files = $_FILES[$keyInFilesSuperglobal];
        /**
         * Liste vorbereiten.
         */
        $filesObjects = [];

        /**
         * Alle Dateinamen durchgehen und über den zugehörigen $key alle Daten in ein File füllen.
         */
        foreach ($files['name'] as $key => $name) {
            $file = new $fileClass();
            $file->fillUploadedData($files, $key);
            $filesObjects[] = $file;
        }

        /**
         * Liste der generierten File Objekte zurückgeben.
         */
        return $filesObjects;
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
     * Hilfsfunktion zur Prüfung ob die Datei alle die Größenbeschränkungen erfüllt und auch wirklich ein Bild ist.
     *
     * @return bool
     */
    public function validateImage (): bool
    {
        /**
         * Upload Limit aus der Config laden.
         */
        $uploadLimit = Config::get('app.upload-limit');

        /**
         * Ist die Dateigröße größer als das Upload Limit?
         */
        if ($this->size > $uploadLimit) {
            /**
             * Wenn ja, formatieren wir das Upload Limit in eine MB Angabe um ...
             */
            $uploadLimitNice = $uploadLimit / 1024 / 1024; // Upload Limit in MB
            /**
             * Und schreiben eine Fehlermeldung.
             */
            $this->errors[] = "Upload Limit überschritten ({$uploadLimitNice}).";
            /**
             * Wir geben false zurück, damit wir diese Methode in einem if-Statement verwenden können.
             */
            return false;
        }

        /**
         * Nun prüfen wir, ob es sich um eine Bild-Datei handelt. Das geht am einfachsten, indem wir den MimeType der
         * Datei analysieren. Bilder haben MimeTypes wie image/jpg oder imag/png - kommt also "image/" im MimeType vor,
         * so handelt es sich um ein Bild. Andernfalls schreiben wir einen Fehler und geben wieder false zurück.
         */
        if (!str_contains($this->type, 'image/')) {
            $this->errors[] = 'Die Datei ist kein Bild!';
            return false;
        }

        /**
         * Waren alle Prüfungen erfolgreich, geben wir true zurück.
         */
        return true;
    }

    /**
     * Datei an einen Pfad relativ zum Storage Pfad speichern.
     *
     * @param string|null $relativeStoragePath
     * @param string|null $filename
     *
     * @return string|false|File Filepath, an den das File gespeichert wurde
     *
     * @throws \Exception
     */
    public function putTo (string $relativeStoragePath = null, string $filename = null): string|false|File
    {
        /**
         * Wir berechnen uns den Storage Pfad absolut zum Server Wurzelverzeichnis (Root).
         */
        $absoluteStoragePath = self::getStoragePath();

        /**
         * Nun berechnen wir uns den Zielpfad der Datei aus dem absoluten Storage Pfad und dem Pfad, der relativ zum
         * Storage angeben wurde.
         */
        $destinationPath = $absoluteStoragePath . '/' . $relativeStoragePath; // /uploads

        /**
         * Existiert an diesem Pfad bereits ein Element, handelt es sich aber nicht um einen Ordner, dann werfen wir
         * eine Exception. Dadurch generieren wir einen Fatal Error. Das macht nur dann Sinn, wenn ein Fehler nicht im
         * Programm abgefangen werden kann.
         * Diese Situation kann beispielsweise passieren, wenn jemand eine Datei mit dem Namen "avatars" in den
         * storage/uploads Ordner hochlädt.
         */
        if (file_exists($destinationPath) && !is_dir($destinationPath)) {
            throw new \Exception('Uploads folder already exists as file.');
        }

        /**
         * Existiert der Pfad noch nicht, kann aber auch nicht angelegt werden, werfen wir auch einen Fehler. Das kann
         * dann passieren, wenn die Berechtigungen des Webservers nicht ausreichend sind, um einen Ordner zu erstellen.
         * Der "recursive" Parameter gibt dabei an, dass für einen Pfad wie "uploads/avatars" zuerst der Ordner uploads
         * und dann darin der Ordner avatars angelegt werden soll. Andernfalls würde mkdir() versuchen avatars anzulegen
         * und einen Fehler ausgeben, weil der Ordner uploads nicht existiert.
         */
        if (!file_exists($destinationPath) && !mkdir($destinationPath, recursive: true)) {
            throw new \Exception('Uploads folder could not be created.');
        }

        /**
         * Gibt es also den Zielordner, generieren wir den Dateinamen. Hier werden wir den originalen Dateinamen
         * verwenden und um einen Unix Timestamp erweitern. Das hat den Grund, dass 2 Dateien mit dem selben Namen sich
         * gegenseitig dadurch nicht überschreiben - das würde nur passieren, wenn sie in der selben Sekunde hochgeladen
         * werden, was für unseren Anwendungsfall unwahrscheinlich genug ist.
         *
         * Hier prüfen wir, ob ein $filename als Funktionsparameter übergeben wurde. Wenn ja, dann verwenden wir diesen
         * als $destinationName, wenn nein, generieren wir den $destinationName selbst.
         */
        if (!empty($filename)) {
            $destinationName = $filename;
        } else {
            $destinationName = time() . "_$this->name";
        }
        /**
         * Dann generieren wir aus Pfad und Dateiname den vollständigen Zielpfad.
         */
        $destinationPath = $destinationPath . '/' . $destinationName;
        /**
         * Und entfernen noch doppelte Slashes aus dem Pfad.
         */
        $destinationPath = str_replace('//', '/', $destinationPath);

        /**
         * Die move_uploaded_file()-Funktion ist dazu gedacht, eine hochgeladene Datei von ihrem Temporären Ort an ihren
         * Zielort zu verschieben.
         */
        if (move_uploaded_file($this->tmp_name, $destinationPath)) {
            /**
             * Hat das funktioniert, geben wir den Zielpfad zurück, damit wir damit weiter arbeiten können.
             */
            return $destinationPath;
        } else {
            /**
             * Ist das Verschieben fehlgeschlagen, bspw. wegen fehlender Schreibrechte im Zielordner, geben wir false
             * zurück.
             */
            return false;
        }
    }

    /**
     * Hilfsfunktion zur Berechnung des Storage Path absolut zum Server Wurzelverzeichnis (Root).
     *
     * @return string
     */
    public static function getStoragePath (): string
    {
        /**
         * Wir definieren unseren Pfad ausgehend von dem Ordner, in dem diese Datei liegt, "relative".
         */
        $absoluteStoragePath = __DIR__ . '/../../storage';
        /**
         * Die realpath()-Methode löst bspw. ".." und "~" in Pfaden auf und erstellt einen absoluten Pfad daraus.
         */
        $absoluteStoragePath = realpath($absoluteStoragePath);
        /**
         * Diesen Pfad geben wir zurück.
         */
        return $absoluteStoragePath;
    }

    /**
     * Datei in den Standard Uploads Ordner speichern. Das ist eine Hilfsfunktion für die putTo()-Methode.
     *
     * @param string|null $relativeStoragePath
     * @param string|null $filename
     *
     * @return string
     * @throws \Exception
     */
    public function put (string $relativeStoragePath = null, string $filename = null): string|File
    {
        /**
         * Wurde kein Pfad übergeben, so verwenden wir den Standard Upload Ordner aus der Config.
         */
        if ($relativeStoragePath === null) {
            $relativeStoragePath = Config::get('app.uploads-folder');
        }
        /**
         * Die Speicherung selber passiert wieder über die putTo()-Methode, damit wir so wenig Duplikate im Code haben
         * wie möglich.
         */
        return $this->putTo($relativeStoragePath, $filename);
    }

    /**
     * Datei physisch löschen.
     *
     * @param string $filepathRelativeToStorage
     *
     * @return bool|int
     */
    public function deleteFile (string $filepathRelativeToStorage): bool|int
    {
        /**
         * Existiert eine Datei an dem Pfad, der übergeben wurde ...
         */
        if (file_exists($filepathRelativeToStorage)) {
            /**
             * ... so löschen wir die Datei.
             */
            return unlink($filepathRelativeToStorage);
        }
        /**
         * Andernfalls geben wir -1 zurück. Dadurch können wir zwischen Erfolg (true) und Fehler (false) der unlink()-
         * Methode unterscheiden und dem Status, dass die Datei nicht existiert.
         */
        return -1;
    }

    /**
     * Datei verschieben.
     *
     * In dieser Funktion ist die App, die auf dem MVC Framework aufbaut, und das Framework selbst ein wenig verwoben,
     * weil $this->name nicht im Core existiert, sondern nur in der App. Das ist ein wenig unsauber, muss ich zugeben.
     *
     * @param string      $relativeStoragePath
     * @param string|null $newFilename
     *
     * @return bool|int|string
     */
    public function moveTo (string $relativeStoragePath, string $newFilename = null): bool|int|string
    {
        /**
         * $destinationFilename aus generieren aus dem übergeben $newFilename oder dem Namen der Datei oder dem Wert
         * aus der Datenbank.
         */
        $destinationFilename = $newFilename;
        if ($newFilename === null) {
            if (property_exists($this, 'name')) {
                $destinationFilename = $this->name;
            } else {
                $destinationFilename = basename($relativeStoragePath);
            }
        }

        /**
         * Wir berechnen uns den Storage Pfad absolut zum Server Wurzelverzeichnis (Root).
         */
        $absoluteStoragePath = self::getStoragePath();

        /**
         * Nun berechnen wir uns den Zielpfad der Datei aus dem absoluten Storage Pfad und dem Pfad, der relativ zum
         * Storage angeben wurde.
         */
        $destinationPath = $absoluteStoragePath . '/' . $relativeStoragePath;

        /**
         * Existiert der $destinationPath nicht und kann nicht angelegt werden, geben wir -1 zurück.
         */
        if (!file_exists($destinationPath) && !mkdir($destinationPath, recursive: true)) {
            return -1;
        }

        /**
         * Alten Dateipfad berechnen.
         *
         * Hier liegt wieder eine Verwebung von Framework Core und App vor. Das ist unsauber und wir sollten eine
         * hübschere Lösung finden.
         */
        $oldPath = $absoluteStoragePath . '/' . $this->path . '/' . $this->name;
        /**
         * Fertigen Zielpfad generieren.
         */
        $destinationPathAndFilename = $destinationPath . '/' . $destinationFilename;

        /**
         * Kann die Datei erfolgreich verschoben werden, geben wir den neuen Pfad zurück.
         */
        if (rename($oldPath, $destinationPathAndFilename)) {
            return $destinationPath;
        }
        /**
         * Andernfalls geben wir false zurück.
         */
        return false;
    }

    /**
     * Getter für die $errors, damit wir sie außerhalb abrufen, aber $this->errors (protected) nicht bearbeiten können
     * von außerhalb der Klasse.
     *
     * @return array
     */
    public function getErrors (): array
    {
        return $this->errors;
    }
}
