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
     * Slug berechnen.
     *
     * @param string|null $title
     *
     * @return string
     * @throws \Exception
     */
    public function createSlug (string $title = null): string
    {
        /**
         * Wenn ein $title übergeben wurde, generieren wir den Slug davon.
         */
        if (!empty($title)) {
            $titleValue = $title;
        } elseif (defined(self::class . "::TITLE_PROPERTY")) {
            /**
             * Wenn nicht, dann suchen wir nach der Klassenkonstante TITLE_PROPERTY und verwenden deren Wert um die
             * Eigenschaft zu kriegen, die den Titel enthält.
             */
            $titlePropertyName = self::TITLE_PROPERTY;
            $titleValue = $this->$titlePropertyName;
        } elseif (property_exists($this, 'title')) {
            /**
             * Existiert diese auch nicht, suchen wir nach der Eigenschaft $title.
             */
            $titleValue = $this->title;
        } else {
            /**
             * Andernfalls können wir keinen weiteren Fallback anbieten und generieren einen Fatal Error.
             */
            throw new \Exception('Property for slug not found.');
        }

        /**
         * Zuerst konvertieren wir den String in Kleinbuchstaben.
         */
        $titleValue = strtolower($titleValue);
        /**
         * Ersetzen dann alles, was nicht a-z0-9 ist mit einem Bindestrich ...
         */
        $titleValue = preg_replace('/[^a-z0-9-]/', '-', $titleValue);
        /**
         * ... und alle Vorkommen von mehr als einem Bindestrich hintereinander mit nur einem Bindestrich.
         */
        $titleValue = preg_replace('/-{2,}/', '-', $titleValue);
        /**
         * Final trimmen wir das ganze nochmal sicherheitshalber.
         */
        $slug = trim($titleValue);

        /**
         * Existiert die Klassenkonstante SLUG_PROPERTY, dann speichern wir den generierten Slug auf die darin
         * definierte Eigenschaft.
         */
        if (defined(self::class . "::SLUG_PROPERTY")) {
            $slugPropertyName = self::SLUG_PROPERTY;
            $this->$slugPropertyName = $slug;
        }

        /**
         * Generierten Slug zurückgeben.
         */
        return $slug;
    }

}
