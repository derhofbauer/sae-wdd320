<?php

namespace App\Models;

use Core\Database;
use Core\Models\AbstractUser;
use Core\Traits\SoftDelete;

/**
 * Class User
 *
 * @package app\Models
 */
class User extends AbstractUser
{
    /**
     * Hier laden wir den SoftDelete Trait, der die delete()- und find()-Methoden überschreibt, damit Objekte nicht
     * komplett gelöscht werden, sondern nur auf deleted gesetzt werden und damit die find()-Methode auch nur Objekte
     * findet, die nicht gelöscht sind.
     */
    use SoftDelete;

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen und für einige Properties auch schon
     * Default-Werte.
     */
    public int $id;
    public string $email;
    public string $username;
    public string $password;
    public ?string $avatar = null;
    public bool $is_admin = false;
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
        $this->email = $data['email'];
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->avatar = $data['avatar'];
        $this->is_admin = (bool)$data['is_admin'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }

    /**
     * Objekt speichern.
     *
     * Wenn das Objekt bereits existiert hat, so wird es aktualisiert, andernfalls neu angelegt. Dadurch können wir eine
     * einzige Funktion verwenden und müssen uns nicht darum kümmern ob das Objekt angelegt oder aktualisiert werden
     * muss.
     *
     * [x] DB Verbindung herstellen & Tablename holen
     * [x] Ist das Objekt schon in der DB vorhanden?
     * [x] wenn ja: UPDATE Query
     * [x] wenn nein: INSERT Query & neue ID abfragen
     * [x] Ergebnis zurückgeben
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
            return $database->query("UPDATE $tablename SET email = ?, username = ?, password = ?, avatar = ?, is_admin = ? WHERE id = ?", [
                's:email' => $this->email,
                's:username' => $this->username,
                's:password' => $this->password,
                'i:avatar' => $this->avatar,
                'i:is_admin' => $this->is_admin,
                'i:id' => $this->id
            ]);
        } else {
            /**
             * Hat es keine ID, so müssen wir es neu anlegen.
             */
            $result = $database->query("INSERT INTO $tablename SET email = ?, username = ?, password = ?, avatar = ?, is_admin = ?", [
                's:email' => $this->email,
                's:username' => $this->username,
                's:password' => $this->password,
                'i:avatar' => $this->avatar,
                'i:is_admin' => $this->is_admin
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
     * Relation zu Files, aber nur für das Avatar Bild.
     *
     * @return File|null
     */
    public function avatar (): File|null
    {
        /**
         * Nachdem man keinen Avatar haben muss, müssen wir auch prüfen, ob einer gesetzt ist, wenn ja, laden wir den
         * File Eintrag aus der Datenbank und geben ihn zurück.
         */
        if (!empty($this->avatar)) {
            return File::find($this->avatar);
        }
        /**
         * Wurde kein Avatar gesetzt, können wir auch nichts laden.
         */
        return null;
    }

    /**
     * Relation zu Favourites.
     *
     * @return array
     */
    public function favourites ()
    {
        return Favourite::findWhere('user_id', $this->id);
    }

    /**
     * Relation zu Shares.
     *
     * @return array
     */
    public function shares ()
    {
        return Share::findWhere('user_id', $this->id);
    }

    /**
     * Prüfen ob ein Post für den/die aktuelle User*in als Favorit verknüpft ist oder nicht.
     *
     * @param int $post_id
     *
     * @return bool
     */
    public function hasFavourite (int $post_id): bool
    {
        /**
         * Wir holen alle Favoriten und gehen sie durch.
         */
        foreach ($this->favourites() as $favourite) {
            /**
             * Ist die Post ID in einem Favoriten gleich wie die übergeben Post ID, geben wir true zurück und brechen
             * den Schleifendurchlauf damit ab.
             */
            if ($favourite->post_id === $post_id) {
                return true;
            }
        }
        /**
         * Andernfalls geben wir false zurück.
         */
        return false;
    }

    /**
     * Einen Favoriten anhand einer Post ID für den/die aktuelle User*in finden.
     *
     * @param int $post_id
     *
     * @return Favourite|null
     */
    public function favourite (int $post_id): ?Favourite
    {
        /**
         * Alle Favoriten durchgehen.
         */
        foreach ($this->favourites() as $favourite) {
            /**
             * Ist die Post ID in einem Favoriten gleich wie die übergeben Post ID, geben wir den Favoriten zurück und
             * brechen den Schleifendurchlauf damit ab.
             */
            if ($favourite->post_id === $post_id) {
                return $favourite;
            }
        }
        /**
         * Andernfalls geben wir null zurück.
         */
        return null;
    }

    /**
     * Diese magische Methode wird aufgerufen, wenn ein Objekt in eine String konvertiert werden soll. Das passiert
     * beispielsweise dann, wenn man ein echo von einem Objekt macht: echo $object.
     *
     * @return string
     */
    public function __toString (): string
    {
        return "$this->username &lt;{$this->email}&gt;";
    }
}
