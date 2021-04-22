<?php

namespace Core;

use mysqli;
use mysqli_result;

/**
 * Class Database
 *
 * @package Core
 * @todo    : comment
 */
class Database
{
    private object $link;
    private object $stmt;
    private mixed $lastResult;
    private array $data;

    public function __construct ()
    {
        $this->link = new mysqli(
            Config::get('database.host'),
            Config::get('database.user'),
            Config::get('database.password'),
            Config::get('database.dbname'),
            Config::get('database.port'),
            Config::get('database.socket'),
        );
    }

    /**
     * @param string $query
     * @param array  $params
     *
     * @return mixed
     * @todo: comment
     */
    public function query (string $query, array $params = []): mixed
    {
        if (empty($params)) {
            $this->lastResult = $this->executeQuery($query);
        } else {
            $this->lastResult = $this->prepareStatementAndExecute($query, $params);
        }

        if ($this->lastResult === false) {
            if ($this->stmt->errno === 0) {
                $this->lastResult = true;
            } else
                $this->lastResult = false;
        }

        if (is_bool($this->lastResult)) {
            return $this->lastResult;
        }

        $this->data = $this->lastResult->fetch_all(MYSQLI_ASSOC);

        return $this->data;
    }

    /**
     * @param string $query
     *
     * @return mysqli_result|bool
     * @todo: comment
     */
    private function executeQuery (string $query): mysqli_result|bool
    {
        return $this->link->query($query);
    }

    /**
     * @param string $queryWithPlaceholders
     * @param array  $params
     *
     * $database->query('SELECT * FROM users WHERE id = ? AND email = ?', ['i:id' => $id, 's:email' => $email]);
     *
     * @return bool|mysqli_result
     * @todo: comment
     */
    private function prepareStatementAndExecute (string $queryWithPlaceholders, array $params): bool|mysqli_result
    {
        $this->stmt = $this->link->prepare($queryWithPlaceholders);

        $paramTypes = [];
        $paramValues = [];

        foreach ($params as $typeAndName => $value) {
            $paramTypes[] = explode(':', $typeAndName)[0];

            $_value = $value;
            $paramValues[] = &$_value;
            unset($_value);
        }

        $paramString = implode('', $paramTypes);

        $this->stmt->bind_param($paramString, ...$paramValues);

        $this->stmt->execute();

        return $this->stmt->get_result();
    }

    /**
     * @return object
     * @todo: comment
     */
    public function getLink (): object
    {
        return $this->link;
    }

    /**
     * @return array|bool
     * @todo: comment
     */
    public function getLastResult (): array|bool
    {
        return $this->lastResult;
    }

    /**
     * @return array
     * @todo: comment
     */
    public function getData (): array
    {
        return $this->data;
    }

    /**
     * @return int|string
     * @todo: comment
     */
    public function getInsertId (): int|string
    {
        return $this->link->insert_id;
    }

    /**
     * @todo: comment
     */
    public function __destruct ()
    {
        $this->link->close();
    }

}
