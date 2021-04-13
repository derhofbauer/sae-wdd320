<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Zufällige ganze Zahl zwischen [0 und 10]
 */
$randomNumber = rand(0, 10);
echo "<p>$randomNumber</p>";

/**
 * Aufgabe:
 * [ ] Schreib folgenden Code in EIN switch-Statement um
 */
if ($randomNumber === 2) {
    echo "<p>Die Zahl ist eine ZWEI</p>";
} else if ($randomNumber === 3) {
    echo "<p>Die Zahl ist eine DREI</p>";
} else {
    if ($randomNumber === 4 || $randomNumber === 5) {
        echo "<p>Die Zahl ist eine VIER oder eine FÜNF</p>";
    } else {
        echo "<p>Die Zahl ist keine 2,3,4 oder 5</p>";
    }
}

?>

</body>
</html>
