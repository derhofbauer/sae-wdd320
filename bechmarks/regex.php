<?php

/**
 * Benchmark Regex vs. natives PHP
 *
 * Hier soll verglichen werden ob eine Regular Expression oder ausprogrammierter PHP Code für eine mehr oder weniger
 * komplexe Prüfung schneller sind.
 */

/**
 * Beispiel Passwort definieren, das geprüft werden soll
 *
 * Die Zeile 15 ist dabei so optimiert, dass das Passwort von vorne und hinten gleich aufgebaut ist.
 */
//$sampleInput = 'P@ss0wr!D';
$sampleInput = 'P@ssw0rd!';

/**
 * Regular Expression und Zeichen-Listen definieren, die dann später verwendet werden können.
 */
$regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
$specialChars = "./\\()\"':,.;<>~!@#$%^&*|+=[]{}`~?-";
$digits = '0123456789';
/**
 * Definieren, wie oft die Prüfung durchgeführt werden soll.
 */
$rounds = 10000;

/**
 * Um einen möglichst aussagekräftigen Benchmark implementieren zu können, muss mitgestoppt werden, wie lange eine
 * Operation dauert. Dazu verwenden wir die microtime()-Funktion, die einen Mikrosekunden-Zeitstempel ausgibt.
 * Normalerweise wäre das ein formatierter String, man kann aber den ersten Parameter auf true setzen und erhält dann
 * einen Float, mit dem gut gerechnet werden kann. Rechnen müssen wir insofern, als wir die Differenz der beiden
 * Zeitstempel von vor und nach dem Code Block berechnen müssen.
 */
$startTimeRegex = microtime(true);
/**
 * So oft, wie in $rounds angegeben, die selbe Operation durchführen.
 */
for ($j = 1; $j <= $rounds; $j++) {
    /**
     * Trifft die Regular Expression nicht auf das Passwort zu, geben wir einen Fehler aus. Das echo-Statement ist hier
     * nur proforma, weil wir ein Passwort definiert haben, dass auf die Regular Expression zutrifft, damit alle
     * Kriterien geprüft werden müssen und die Expression nicht beim ersten Fehler abbricht.
     */
    if (preg_match($regex, $sampleInput) !== 1) {
        echo "Das passwort muss folgende Kriterien erfüllen: Groß- & Kleinbuchstaben, Sonderzeichen, Ziffern, min. 8 Zeichen.";
    }
}
$endTimeRegex = microtime(true);


/**
 * Hier haben wir die selbe Logik wie in der Regular Expression ausprogrammiert.
 *
 * Wir holen uns wieder einen Zeitstempel vor und nach der Schleife und führen die selbe Operation so oft durch, wie in
 * $rounds definiert.
 */
$startTimeLogic = microtime(true);
for ($j = 1; $j <= $rounds; $j++) {
    /**
     * Wenn ein String mit einer Repräsentation seiner selbst in Kleinbuchstaben übereinstimmt, dann folgt daraus, dass
     * zuvor keine Großbuchstaben in dem String vorhanden waren.
     */
    if ($sampleInput === strtolower($sampleInput)) {
        echo "Das Passwort muss Großbuchstaben beinhalten.";

        /**
         * Analog zur vorhergehenden Bedingung.
         */
    } elseif ($sampleInput === strtoupper($sampleInput)) {
        echo "Das Passwort muss Kleinbuchstaben beinhalten.";

        /**
         * Ist die Länge des Strings kleiner als 8?
         */
    } elseif (strlen($sampleInput) < 8) {
        echo "Das Passwort muss mindestens 8 Zeichen lang sein.";
    } else {
        /**
         * Andernfalls definieren wir eine Switch-Variable - quasi ein Schalter, der umgelegt wird, wenn eine Bedingung
         * erfüllt ist.
         */
        $hasSpecialChar = false;
        /**
         * Nun rufen wir eine Schleife so oft auf, wie $specialChars lang ist, damit wir jedes einzelne Zeichen aus
         * dieser Variable durchgehen (s. PHP Zeichenindizierung) und prüfen können, ob es in dem Passwort vorkommt.
         */
        for ($i = 0; $i < strlen($specialChars); $i++) {
            if (str_contains($sampleInput, $specialChars[$i])) {
                /**
                 * Beim ersten Treffer eines Sonderzeichens im Passwort legen wir den Schalter um und unterbrechen die
                 * Schleife mit dem break-Keyword. Das führt dazu, dass keine weiteren Schleifendurchläufe mehr
                 * stattfinden, weil wir ohnehin schon das Kriterium erfüllt haben, dass mindestens ein Sonderzeichen im
                 * Passwort vorkommen muss.
                 */
                $hasSpecialChar = true;
                break;
            }
        }

        /**
         * Gibt es Sonderzeichen im Passwort, prüfen wir auch noch auf Ziffern. Diese Prüfung entfällt, wenn keine
         * Sonderzeichen vorhanden sind, weil damit das Passwort schon nicht mehr gültig ist.
         */
        if ($hasSpecialChar) {
            /**
             * Hier gehen wir ident vor, wie bei der Prüfung auf Sonderzeichen.
             */
            $hasDigits = false;
            for ($i = 0; $i < strlen($digits); $i++) {
                if (str_contains($sampleInput, $digits[$i])) {
                    $hasDigits = true;
                    break;
                }
            }

            /**
             * Wenn keine Ziffern gefunden wurden, geben wir einen Fehler aus.
             */
            if (!$hasDigits) {
                echo "Das Passwort muss Ziffern beinhalten.";
            }

        } else {
            /**
             * Wenn keine Sonderzeichen gefunden wurden, geben wir einen Fehler aus.
             */
            echo "Das Passwort muss Sonderzeichen beinhalten.";
        }
    }
}
$endTimeLogic = microtime(true);

/**
 * Abschließend berechnen wir für beide Implementierungen die Differenz der Zeitstempel, damit wir wissen, wie lang die
 * Operationen jeweils gedauert haben.
 * Weiters berechnen wir das Verhältnis der beiden Werte, damit wir eine zusätzliche Metrik haben, die plakativ
 * darstellt, welche der beiden Implementierungen um wie viel schneller ist.
 */
$durationRegex = $endTimeRegex - $startTimeRegex;
$durationLogic = $endTimeLogic - $startTimeLogic;
$ratio = $durationRegex / $durationLogic;
echo "Regex: {$durationRegex}s<br>";
echo "Logic: {$durationLogic}s<br>";
echo "Ratio Regex:Logic = $ratio:1";
