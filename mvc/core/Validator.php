<?php

namespace Core;

/**
 * Class Validator
 *
 * @package Core
 */
class Validator
{
    /**
     * Definieren der string-basierten Datentypen, die validiert werden können.
     *
     * @var string[]
     */
    private array $types = [
        'letters' => '/^[a-zA-Z ]*$/',
        'text' => '/^[a-zA-Z .\-,;_]*&/',
        'textnum' => '/^[\w\s.,#\-!:;]*$/',
        'slug' => '/^[a-z0-9-]+$/',
        'email' => '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/',
        'url' => '/^http(s)?:\/\/([\w]{1,20}\.)?[a-z0-9-]{2,65}(\.[a-z]{2,10}){1,2}(\/)?$/',
        'tel' => '/^[\d +-/]*$',
        'password' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
        'checkbox' => '/^(on|true|checked|1)$/i'
    ];

    /**
     * Definieren der numerischen Datentypen, die validiert werden können. Hier wird auch definiert, mit welcher PHP
     * Funktion die Validierung durchgeführt werden soll.
     *
     * @var string[]
     */
    private array $numericTypes = [
        'numeric' => 'is_numeric',
        'int' => 'is_int',
        'float' => 'is_float'
    ];

    /**
     * Definieren der Fehlermeldungen für alle Datentypen oben. Hier wird immer ein %s-Platzhalter definiert, damit wir
     * später, wenn wir die Fehlermeldung verwenden, mit der sprintf()-Funktion das Label des Input Feldes einfügen
     * können.
     *
     * @var string[]
     */
    private array $errorMessages = [
        'letters' => '%s darf nur Buchstaben und Leerzeichen enthalten.',
        'text' => '%s darf nur Buchstaben und Sonderzeichen enthalten.',
        'textnum' => '%s darf nur aus alphanumerischen Zeichen bestehen.',
        'slug' => '%s darf nur Kleinbuchstaben, Zahlen und Bindestriche beinhalten.',
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

    /**
     * Definieren einer Property, in die alle aufgetretenen Fehler gespeichert werden.
     *
     * @var string[]
     */
    private array $errors = [];

    /**
     * Hier wird die gesamte Validierung der Daten durchgeführt.
     *
     * Die __call() Magic Method wird aufgerufen, wenn eine nicht zugreifbare Methode aufgerufen wird. Das betrifft
     * Methoden, die private oder protected sind, oder Methoden, die nicht existieren. Das führt also dazu, dass wir
     * $validator->email("something") aufrufen können, und in wirklichkeit wird $validator->__call("email",
     * ["something"]) aufgerufen. Dadurch müssen wir nicht für alle string-basierten Datentypen eine eigene Methode
     * schreiben, sondern können ein und die selbe Methode für alle Typen schreiben und haben trotzdem hübsch benannte
     * Methoden bei der Verwendung des Validators zur Verfügung.
     *
     * @throws \Exception
     */
    public function __call ($name, $arguments)
    {
        /**
         * Namen der aufgerufenen Funktion laden, der ident sein sollte mit einem der Types.
         */
        $type = $name;
        /**
         * Werte aus dem Arguments Array laden und mit Standardwerten auffüllen.
         */
        [$value, $label, $required, $min, $max] = $this->mergeDefaults($arguments);

        /**
         * Validierungen ausführen. Diese Methoden schreiben ihre Fehler in $this->errors.
         */
        $this->validateRequired($required, $value, $label);
        $this->validateMin($type, $min, $value, $label);
        $this->validateMax($type, $max, $value, $label);

        /**
         * Wenn es sich um einen numerischen Typ handelt, so prüfen wir nicht mit einer Regular Expression.
         */
        if ($this->isNumericType($type)) {
            $this->validateNumeric($type, $value, $label);
        } else {
            $this->validateRegex($type, $value, $label);
        }
    }

    /**
     * Hier prüfen wir, ob der aufgerufenen $type einer der oben definierten numericTypes ist.
     *
     * @param string $type
     *
     * @return bool
     */
    private function isNumericType (string $type): bool
    {
        return array_key_exists($type, $this->numericTypes);
    }

    /**
     * Hier vergleichen wir zwei Werte miteinander. Das ist für Passwort und Passwort wiederholen Felder sehr praktisch.
     *
     * @param array $valueAndLabel1 [$wert, $label]
     * @param array $valueAndLabel2 [$wert, $label]
     *
     * @return bool
     */
    public function compare (array $valueAndLabel1, array $valueAndLabel2): bool
    {
        /**
         * Werte aus den beiden Arrays extrahieren.
         */
        [$value1, $label1] = $valueAndLabel1;
        [$value2, $label2] = $valueAndLabel2;

        /**
         * Stimmen die Werte nicht überein, so schrieben wir einen Fehler und geben false zurück.
         */
        if ($value1 !== $value2) {
            $this->errors[] = sprintf($this->errorMessages['compare'], $label1, $label2);
            return false;
        }
        /**
         * Andernfalls geben wir true zurück.
         */
        return true;
    }

    /**
     * Diese Funktion hilft uns dabei, Standardwerte für alle Parameter in $arguments aus __call() zu setzen. Das ist
     * nötig, weil wir normalerweise Standardwerte für optionale Paramater direkt in der Funktion definieren können. Die
     * __call()-Methode erhält die Funktionsparameter aber als ein Array $arguments, wodurch wir die Funktionalität für
     * optionale Werte selbst bauen müssen.
     *
     * @param array $arguments
     *
     * @return array
     */
    public function mergeDefaults (array $arguments): array
    {
        /**
         * Standardwerte definieren.
         */
        $defaults = ['text', 'Label', false, 0, 0];

        /**
         * Finales Array vorbereiten.
         */
        $mergedArguments = [];

        /**
         * Nun gehen wir alle Standardwerte durch ...
         */
        foreach ($defaults as $index => $value) {
            /**
             * ... und prüfen, ob an der selben Stelle im $arguments Array ein Wert steht.
             */
            if (isset($arguments[$index])) {
                /**
                 * Wenn ja, dann verwenden wir diesen Wert aus $arguments für $mergedArguments.
                 */
                $mergedArguments[$index] = $arguments[$index];
            } else {
                /**
                 * Wenn nein, verwenden wir den Wert aus $defaults, der durch die Schleife in $value liegt.
                 */
                $mergedArguments[$index] = $value;
            }
        }
        /**
         * Nun geben wir das fertige Array zurück, das Werte aus $arguments enthält und überall dort, wo keine Werte
         * übergeben wurden, weil sie optional waren, enthält es die Werte aus $defaults.
         */
        return $mergedArguments;
    }

    /**
     * Prüfen, ob ein Pflichtfeld ausgefüllt wurd.
     *
     * @param bool   $required
     * @param mixed  $value
     * @param string $label
     *
     * @return bool
     */
    private function validateRequired (bool $required, mixed $value, string $label): bool
    {
        /**
         * Wenn ein Feld verpflichtend ist, aber empty, schreiben wir einen Fehler und geben false zurück.
         */
        if ($required === true && empty($value)) {
            $this->errors[] = "$label ist ein Pflichtfeld.";
            return false;
        }
        /**
         * Andernfalls geben wir true zurück.
         */
        return true;
    }

    /**
     * Prüfen, ob der Mindestwert unterschritten wurde.
     *
     * @param string $type
     * @param mixed  $min
     * @param mixed  $value
     * @param string $label
     *
     * @return bool
     */
    private function validateMin (string $type, int $min, mixed $value, string $label): bool
    {
        /**
         * Wurde $min definiert ...
         */
        if ($min > 0) {
            /**
             * ... so prüfen wir für numerische Typen direkt ...
             */
            if ($this->isNumericType($type) && $value < $min) {
                $this->errors[] = "$label muss mindestens $min sein.";
                return false;
            }
            /**
             * ... und für string-basierte Typen die Länge des Strings.
             *
             * In beiden Fällen schreiben wir einen Fehler und geben false zurück im Fehlerfall.
             */
            if (!$this->isNumericType($type) && strlen($value) < $min) {
                $this->errors[] = "$label muss mindestens $min Zeichen haben.";
                return false;
            }
        }
        /**
         * Im Erfolgsfall geben wir true zurück.
         */
        return true;
    }

    /**
     * S. this->validateMin() nur umgekehrt.
     *
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
     * Prüfen, ob der Wert auf die oben definierte Regex zutrifft.
     *
     * @param mixed  $type
     * @param mixed  $value
     * @param string $label
     *
     * @return bool
     * @throws \Exception
     */
    private function validateRegex (string $type, mixed $value, string $label): bool
    {
        /**
         * Ist der gewünschte $type nicht in $this->types definiert, so liegt ein schwerer Fehler in der Programmierung
         * vor und wir werfen einen Exception (s. https://www.php.net/manual/de/class.exception.php).
         */
        if (!array_key_exists($type, $this->types)) {
            throw new \Exception("Type $type does not exist.");
        }

        /**
         * Nun holen wir uns die Regular Expression und prüfen mit der preg_match()-Funktion.
         */
        $typeRegex = $this->types[$type];
        if (preg_match($typeRegex, $value) !== 1) {
            /**
             * Findet preg_match() keinen Treffer oder tritt ein Fehler auf, so schreiben wir einen Fehler und geben
             * false zurück.
             */
            $this->errors[] = sprintf($this->errorMessages[$type], $label);
            return false;
        }
        /**
         * Im Erfolgsfall geben wir true zurück.
         */
        return true;
    }

    /**
     * Numerische Typen werden nicht über Regular Expression validiert sondern mit den passenden PHP Funktionen.
     *
     * @param mixed $type
     * @param mixed $value
     * @param mixed $label
     *
     * @return bool
     */
    private function validateNumeric (mixed $type, mixed $value, mixed $label): bool
    {
        /**
         * Hier holen wir uns den Namen der Funktion, mit der validiert werden soll.
         */
        $typeFunction = $this->numericTypes[$type];
        /**
         * Dann rufen wir diese Variable als Funktion auf und schreiben im Fehlerfall einen Fehler und geben false zurück.
         */
        if (!$typeFunction($value)) {
            $this->errors[] = sprintf($this->errorMessages[$type], $label);
            return false;
        }
        /**
         * Im Erfolgsfall geben wir true zurück.
         */
        return true;
    }

    /**
     * Hierbei handelt es sich um eine praktische Hilfsfunktion, mit der wir ganz einfach prüfen können, ob im Zuge der
     * Validierung Fehler aufgetreten sind.
     *
     * @return bool
     */
    public function hasErrors (): bool
    {
        if (empty($this->errors)) {
            return false;
        }
        return true;
    }

    /**
     * Nachdem $this->errors private ist, damit von außerhalb des Validators nicht darauf zugegriffen werden kann,
     * müssen wir irgendwie die Möglichkeit schaffen, die Fehler doch außerhalb zu bekommen. Daher definieren wir hier
     * einen einfachen Getter.
     *
     * @return string[]
     */
    public function getErrors (): array
    {
        return $this->errors;
    }
}
