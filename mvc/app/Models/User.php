<?php

namespace app\Models;

use Core\Models\AbstractUser;

/**
 * Class User
 *
 * @package app\Models
 * @todo    : comment
 */
class User extends AbstractUser
{

    public int $id;
    public string $email;
    public string $password;
    public ?string $avatar;
    public bool $is_admin;
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
     * @param array $data
     */
    public function fill (array $data)
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->avatar = $data['avatar'];
        $this->is_admin = (bool)$data['is_admin'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }
}
