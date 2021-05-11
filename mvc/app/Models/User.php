<?php

namespace App\Models;

use Core\Database;
use Core\Models\AbstractUser;

/**
 * Class User
 *
 * @package app\Models
 */
class User extends AbstractUser
{
    /**
     * @todo: Soft Deletes!
     */

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
     * @return bool
     * @todo: comment
     *
     * [x] DB Verbindung herstellen & Tablename holen
     * [x] Ist das Objekt schon in der DB vorhanden?
     * [x] wenn ja: UPDATE Query
     * [x] wenn nein: INSERT Query & neue ID abfragen
     * [x] Ergebnis zurückgeben
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

        if (!empty($this->id)) {
            // Objekt existiert in der DB bereits
            return $database->query("UPDATE $tablename SET email = ?, username = ?, password = ?, avatar = ?, is_admin = ? WHERE id = ?", [
                's:email' => $this->email,
                's:username' => $this->username,
                's:password' => $this->password,
                'i:avatar' => $this->avatar,
                'i:is_admin' => $this->is_admin,
                'i:id' => $this->id
            ]);
        } else {
            // Objekt existiert in der DB noch nicht
            $result = $database->query("INSERT INTO $tablename SET email = ?, username = ?, password = ?, avatar = ?, is_admin = ?", [
                's:email' => $this->email,
                's:username' => $this->username,
                's:password' => $this->password,
                'i:avatar' => $this->avatar,
                'i:is_admin' => $this->is_admin
            ]);

            $this->handleInsertResult($database);

            return $result;
        }
    }
}
