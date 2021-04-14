<?php

/*
 * SQL-Query um Newsletter-Themen auszulesen (query schreiben & ausführen)
 */
$query = "SELECT * FROM newsletter_categories";
$result = mysqli_query($link, $query);

/*
 * Resultat in Variable legen (MYSQLI_ASSOC)
 */
$newsletter_categories = mysqli_fetch_all($result, MYSQLI_ASSOC);


// Wenn Formular abgeschickt wurde



if(isset($_POST['email'])) {

    /*
     * Inhalte von Formular sowie den aktuellen Timestamp auf Variablen legen
     */
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $newsletter_category = $_POST['newsletter_category'];
    $created_at = time();


    /*
     * SQL Query schreiben um eingegebene Daten in die DB zu schreiben
     */
    $sql = "INSERT INTO recipients SET email = '$email', fullname = '$fullname', newsletter_category_id = '$newsletter_category', created_at = '$created_at'";

    // Query ausführen, wenn es fehlschlägt Error anzeigen
    mysqli_query($link, $sql) or die(mysqli_error($link));


    /*
     * Session-Variable setzen und Benutzer als "eingetragen" markieren
     */
    $_SESSION['has_abo'] = true;
}
