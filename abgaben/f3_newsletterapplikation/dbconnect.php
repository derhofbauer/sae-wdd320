<?php
/**
 * Hier definieren wir die Zugangsdaten für die Datenbank. Die Angabe des Ports ist optional, wenn der MySQL Standard-
 * Port (3306) verwendet wird.
 *
 * mysqli_connect() ist die neue Implementierung der gesamten MySQL Funktionalitäten in PHP. Es das ganze auch ohne i,
 * dabei handelt es sich aber um die alte und nicht mehr sichere Implementierung, diese sollte nicht mehr verwendet
 * werden.
 */
$host = 'mariadb'; // bei euch localhost
$username = 'root';
$password = 'password';
$dbname = 'sae_newsletter';
// $port = 3306;
$link = mysqli_connect($host, $username, $password, $dbname);

/**
 * mysqli_connect() gibt false zurück, wenn ein Fehler aufgetreten ist bei der Verbindung (bspw. wenn die Zugangsdaten
 * nicht stimmen). In diesem Fall brechen wir die weitere Ausführung des Skripts ab und geben den Fehler aus.
 */
if (!$link) {
    die('Verbindungsfehler: (' . mysqli_connect_errno() . ') '
        . mysqli_connect_error());
}

/**
 * Hier definieren wir noch, welches Character Set für die Verbindung in $link verwendet werden soll.
 */
mysqli_set_charset($link, 'utf8');
