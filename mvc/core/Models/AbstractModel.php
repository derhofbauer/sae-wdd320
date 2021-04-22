<?php

namespace Core\Models;

use Core\Database;

/**
 * Class AbstractModel
 *
 * @package Core\Models
 * @todo: comment
 */
abstract class AbstractModel {

    public function __construct (array $data) {
        $this->fill($data);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    abstract public function fill (array $data);

    /**
     * @param string $orderBy
     * @param string $direction
     *
     * @return array<object>
     */
    public static function all (string $orderBy = '', string $direction = 'ASC'): array
    {
        $database = new Database();

        $tablename = self::getTablenameFromClassname();

        if (empty($orderBy)) {
            $results = $database->query("SELECT * FROM {$tablename}");
        } else {
            $results = $database->query("SELECT * FROM {$tablename} ORDER BY $orderBy $direction");
        }

        $objects = [];
        foreach ($results as $result) {
            $calledClass = get_called_class();
            $objects[] = new $calledClass($result);
        }

        return $objects;
    }

    /**
     * @return string
     * @todo: comment
     */
    private static function getTablenameFromClassname (): string
    {
        $calledClass = get_called_class();

        if (defined("$calledClass::TABLENAME")) {
            return $calledClass::TABLENAME;
        }

        $particles = explode('\\', $calledClass);
        $classname = array_pop($particles);
        $tablename = strtolower($classname) . 's';

        return $tablename;
    }

}
