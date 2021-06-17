<?php

namespace App\Models;

use Core\Database;
use Core\Models\AbstractModel;
use Core\Traits\SoftDelete;

/**
 * Class Post
 *
 * @package App\Models
 */
class PasswordReset extends AbstractModel
{
    const TABLENAME = 'password-resets';

    /**
     * s. Post.php
     */
    use SoftDelete;

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public int $user_id;
    public string $token;
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
        $this->user_id = $data['user_id'];
        $this->token = $data['token'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }

    /**
     * Neuen PasswordReset mit zufälligem Token generieren.
     *
     * @param int|null $user_id
     *
     * @return static
     * @throws \Exception
     */
    public static function make (int $user_id = null): self
    {
        /**
         * Neues PasswordReset Objekt erstellen.
         */
        $passwordReset = new self();
        /**
         * Zufälligen Byte-String erstellen.
         */
        $token = random_bytes(32);
        /**
         * Binärdaten in einen Hexadezimalen String umformatieren.
         */
        $passwordReset->token = bin2hex($token);

        /**
         * Wurde eine $user_id übergeben, so setzen wir sie zusätzlich direkt in das Objekt.
         */
        if (!empty($user_id)) {
            $passwordReset->user_id = $user_id;
        }

        /**
         * Final geben wir das erstellte Objekt zurück.
         */
        return $passwordReset;
    }

    /**
     * Relation zum User.
     *
     * @return User
     */
    public function user (): User
    {
        return User::find($this->user_id);
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
            return $database->query("UPDATE `$tablename` SET user_id = ?, token = ?  WHERE id = ?", [
                'i:user_id' => $this->user_id,
                's:token' => $this->token,
                'i:id' => $this->id
            ]);
        } else {
            /**
             * Hat es keine ID, so müssen wir es neu anlegen.
             */
            $result = $database->query("INSERT INTO `$tablename` SET user_id = ?, token = ?", [
                'i:user_id' => $this->user_id,
                's:token' => $this->token
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
}
