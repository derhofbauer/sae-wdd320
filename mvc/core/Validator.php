<?php

namespace Core;

/**
 * Class Validator
 *
 * @package Core
 * @todo    : comment
 */
class Validator
{
    private array $types = [
        'letters' => '/^[a-zA-Z ]*$/',
        'text' => '/^[a-zA-Z .\-,;_]*&/',
        'textnum' => '/^[\w\s.,#\-!:;]*$/',
        'email' => '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/',
        'url' => '/^http(s)?:\/\/([\w]{1,20}\.)?[a-z0-9-]{2,65}(\.[a-z]{2,10}){1,2}(\/)?$/',
        'tel' => '/^[\d +-/]*$',
        'password' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
        'checkbox' => '/^(on|true|checked|1)$/i'
    ];
    private array $numericTypes = [
        'numeric' => 'is_numeric',
        'int' => 'is_int',
        'float' => 'is_float'
    ];
    private array $errorMessages = [
        'letters' => '%s darf nur Buchstaben und Leerzeichen enthalten.',
        'text' => '%s darf nur Buchstaben und Sonderzeichen enthalten.',
        'textnum' => '%s darf nur aus alphanumerischen Zeichen bestehen.',
        'email' => '%s muss eine E-Mail Adresse sein.',
        'url' => '%s muss eine valide URL sein.',
        'tel' => '%s muss eine valide Telefonnummer sein.',
        'password' => '%s muss aus GROSS- und Kleinbuchstaben, Ziffern und Sonderzeichen bestehen.',
        'checkbox' => '%s muss aktiviert sein.',
        'numeric' => '%s muss numerisch sein.',
        'int' => '%s muss ganzzahlig sein.',
        'float' => '%s muss eine Fließkommazahl sein.',
        'compare' => '%s muss ident sein mit %s.'
    ];
    private array $errors = [];

    /**
     * @throws \Exception
     */
    public function __call ($name, $arguments)
    {
        $type = $name;
        [$value, $label, $required, $min, $max] = $this->mergeDefaults($arguments);

        $this->validateRequired($required, $value, $label);
        $this->validateMin($type, $min, $value, $label);
        $this->validateMax($type, $max, $value, $label);

        if ($this->isNumericType($type)) {
            $this->validateNumeric($type, $value, $label);
        } else {
            $this->validateRegex($type, $value, $label);
        }
    }

    private function isNumericType (string $type): bool
    {
        return array_key_exists($type, $this->numericTypes);
    }

    public function compare (array $valueAndLabel1, array $valueAndLabel2): bool
    {
        [$value1, $label1] = $valueAndLabel1;
        [$value2, $label2] = $valueAndLabel2;

        if ($value1 !== $value2) {
            $this->errors[] = sprintf($this->errorMessages['compare'], $label1, $label2);
            return false;
        }
        return true;
    }

    public function mergeDefaults (array $arguments): array
    {
        $defaults = ['text', 'Label', false, 0, 0];
        $mergedArguments = [];
        foreach ($defaults as $index => $value) {
            if (isset($arguments[$index])) {
                $mergedArguments[$index] = $arguments[$index];
            } else {
                $mergedArguments[$index] = $value;
            }
        }
        return $mergedArguments;
    }

    /**
     * @param bool   $required
     * @param mixed  $value
     * @param string $label
     *
     * @return bool
     */
    private function validateRequired (bool $required, mixed $value, string $label): bool
    {
        if ($required === true && empty($value)) {
            $this->errors[] = "$label ist ein Pflichtfeld.";
            return false;
        }
        return true;
    }

    /**
     * @param string $type
     * @param mixed  $min
     * @param mixed  $value
     * @param string $label
     *
     * @return bool
     */
    private function validateMin (string $type, int $min, mixed $value, string $label): bool
    {
        if ($min > 0) {
            if ($this->isNumericType($type) && $value < $min) {
                $this->errors[] = "$label muss mindestens $min sein.";
                return false;
            }
            if (!$this->isNumericType($type) && strlen($value) < $min) {
                $this->errors[] = "$label muss mindestens $min Zeichen haben.";
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed  $type
     * @param mixed  $max
     * @param mixed  $value
     * @param string $label
     *
     * @return bool
     */
    private function validateMax (mixed $type, mixed $max, mixed $value, string $label): bool
    {
        if ($max > 0) {
            if ($this->isNumericType($type) && $value > $max) {
                $this->errors[] = "$label muss maximal $max sein.";
                return false;
            }
            if (!$this->isNumericType($type) && strlen($value) > $max) {
                $this->errors[] = "$label muss maximal $max Zeichen haben.";
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed  $type
     * @param mixed  $value
     * @param string $label
     *
     * @return bool
     * @throws \Exception
     */
    private function validateRegex (string $type, mixed $value, string $label): bool
    {
        if (!array_key_exists($type, $this->types)) {
            throw new \Exception("Type $type does not exist.");
        }

        $typeRegex = $this->types[$type];
        if (preg_match($typeRegex, $value) !== 1) {
            $this->errors[] = sprintf($this->errorMessages[$type], $label);
            return false;
        }
        return true;
    }

    /**
     * @param mixed $type
     * @param mixed $value
     * @param mixed $label
     *
     * @return bool
     */
    private function validateNumeric (mixed $type, mixed $value, mixed $label): bool
    {
        $typeFunction = $this->numericTypes[$type];
        if (!$typeFunction($value)) {
            $this->errors[] = sprintf($this->errorMessages[$type], $label);
            return false;
        }
        return true;
    }

    public function hasErrors (): bool
    {
        if (empty($this->errors)) {
           return false;
        }
        return true;
    }

    public function getErrors (): array {
        return $this->errors;
    }
}
