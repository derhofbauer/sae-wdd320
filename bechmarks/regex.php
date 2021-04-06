<?php

/**
 * Benchmark Regex vs. natives PHP
 *
 * @todo: comment
 *
 * [x] Regeln für ein Passwort: Groß- & Kleinbuchstaben, Sonderzeichen, Ziffern, min. 8 Zeichen
 * [x] Regex
 * [x] Logik
 * [x] beides timen
 */

//$sampleInput = 'P@ss0wr!D';
$sampleInput = 'P@ssw0rd!';
$regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
$specialChars = "./\\()\"':,.;<>~!@#$%^&*|+=[]{}`~?-";
$digits = '0123456789';
$rounds = 10000;

$startTimeRegex = microtime(true);
for ($j = 1; $j <= $rounds; $j++) {
    if (preg_match($regex, $sampleInput) !== 1) {
        echo "Das passwort muss folgende Kriterien erfüllen: Groß- & Kleinbuchstaben, Sonderzeichen, Ziffern, min. 8 Zeichen.";
    }
}
$endTimeRegex = microtime(true);


/**
 * Logik
 */
$startTimeLogic = microtime(true);
for ($j = 1; $j <= $rounds; $j++) {
    if ($sampleInput === strtolower($sampleInput)) {
        echo "Das Passwort muss Großbuchstaben beinhalten.";
    } elseif ($sampleInput === strtoupper($sampleInput)) {
        echo "Das Passwort muss Kleinbuchstaben beinhalten.";
    } elseif (strlen($sampleInput) < 8) {
        echo "Das Passwort muss mindestens 8 Zeichen lang sein.";
    } else {
        $hasSpecialChar = false;
        for ($i = 0; $i < strlen($specialChars); $i++) {
            if (str_contains($sampleInput, $specialChars[$i])) {
                $hasSpecialChar = true;
                break;
            }
        }

        if ($hasSpecialChar) {
            $hasDigits = false;
            for ($i = 0; $i < strlen($digits); $i++) {
                if (str_contains($sampleInput, $digits[$i])) {
                    $hasDigits = true;
                    break;
                }
            }

            if (!$hasDigits) {
                echo "Das Passwort muss Ziffern beinhalten.";
            }

        } else {
            echo "Das Passwort muss Sonderzeichen beinhalten.";
        }
    }
}
$endTimeLogic = microtime(true);

/**
 * Differenz
 */
$durationRegex = $endTimeRegex - $startTimeRegex;
$durationLogic = $endTimeLogic - $startTimeLogic;
$ratio = $durationRegex / $durationLogic;
echo "Regex: {$durationRegex}s<br>";
echo "Logic: {$durationLogic}s<br>";
echo "Ratio Regex:Logic = $ratio";
