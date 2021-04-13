<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Zufällige ganze Zahl zwischen [2 und 5]
 */
$randomNumber = rand(2, 5);
echo "<p>$randomNumber</p>";

/**
 * Aufgabe:
 * [ ] Schreib folgenden Code in EIN switch-Statement um
 */
if ($randomNumber === 2) {
    echo "<p>Die Zahl ist eine ZWEI</p>";
} elseif ($randomNumber === 3) {
    echo "<p>Die Zahl ist eine DREI</p>";
} else {
    if ($randomNumber === 4) {
        echo "<p>Die Zahl ist eine VIER</p>";
    } elseif ($randomNumber === 5) {
        echo "<p>Die Zahl ist eine FÜNF</p>";
    }
}

?>

</body>
</html>
