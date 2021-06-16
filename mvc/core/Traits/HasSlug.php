<?php

namespace Core\Traits;

use Core\Database;

/**
 * Trait HasSlug
 *
 * Dieser Trait bietet uns die Möglichkeit, die findBySlug()-Methode in alle Models hinzuzufügen, in denen wir sie
 * brauchen. Wir könnten das selbe Ergebnis erreichen, wenn wir diese Methode in das AbstractModel einbauen würden. In
 * diesem Fall hätten dann aber Models, die keinen Slug in der Datenbank haben, auch diese Methode und sie würde dann
 * einen Fehler produzieren, wenn sie aufgerufen werden würde. Daher ist es eleganter, wenn wir einen Trait dafür
 * bauen.
 *
 * @package Core\Traits
 */
trait HasSlug
{

    /**
     * Objekte anhand der Spalte "slug" finden.
     *
     * @param string $slug
     *
     * @return object|null
     */
    public static function findBySlug (string $slug): object|null
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
        $results = $database->query("SELECT * FROM `{$tablename}` WHERE `slug` = ?", [
            's:slug' => $slug
        ]);

        /**
         * Datenbankergebnis verarbeiten.
         *
         * Nachdem wir hier aber nur maximal ein Ergebnis erwarten, weil die slug-Spalte UNIQUE ist, verwenden wir aus
         * Gründen der Gemütlichkeit nicht die handleRequest()-Methode, sondern die handleUniqueRequest()-Methode.
         */
        $result = self::handleUniqueResult($results);

        /**
         * Ergebnis zurückgeben.
         */
        return $result;
    }

    /**
     * @param string|null $title
     *
     * @return string
     * @todo: comment
     *
     *      1) Funktionsparameter
     *      2) Klassenkonstante
     *      3) $title property
     */
    public function createSlug (string $title = null): string
    {
        if (!empty($title)) {
            $titleValue = $title;
        } elseif (defined(self::class . "::TITLE_PROPERTY")) {
            $titlePropertyName = self::TITLE_PROPERTY;
            $titleValue = $this->$titlePropertyName;
        } elseif (property_exists($this, 'title')) {
            $titleValue = $this->title;
        } else {
            throw new \Exception('Property for slug not found.');
        }

        $titleValue = strtolower($titleValue);
        $titleValue = preg_replace('/[^a-z0-9-]/', '-', $titleValue );
        $titleValue = preg_replace('/-{2,}/', '-', $titleValue);
        $slug = trim($titleValue);

        if (defined(self::class . "::SLUG_PROPERTY")) {
            $slugPropertyName = self::SLUG_PROPERTY;
            $this->$slugPropertyName = $slug;
        }

        return $slug;
    }

}
