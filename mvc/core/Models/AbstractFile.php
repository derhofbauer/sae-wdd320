<?php

namespace Core\Models;

use Core\Config;

/**
 * Class AbstractFile
 *
 * Damit wir eine Abstraktionsebene über das Dateisystem legen können, bauen wir eine eigene Klasse, die die Arbeit mit
 * einzelnen Dateien vereinfachen soll.
 *
 * @package Core\Models
 */
abstract class AbstractFile
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
    public function fill (array $data)
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
     * @return array
     */
    public function validateImage (): array
    {
        $uploadLimit = Config::get('app.upload-limit');
        /**
         * @todo: Avatar Dimensionen sollten in File.php geprüft werden und über das parent-Keyword diese Methode erweitern.
         */
    }
}
