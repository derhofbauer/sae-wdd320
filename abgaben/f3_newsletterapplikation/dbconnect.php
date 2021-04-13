<?php
// ToDo: SQL Zugangsdaten an eure DB anpassen
/**
 * @todo: comment
 */
$host = 'mariadb'; // bei euch localhost
$username = 'root';
$password = 'password';
$dbname = 'sae_newsletter';
// $port = 3306;
$link = mysqli_connect($host, $username, $password, $dbname);

if (!$link) {
    die('Verbindungsfehler: (' . mysqli_connect_errno() . ') '
        . mysqli_connect_error());
}

mysqli_set_charset($link, 'utf8');
