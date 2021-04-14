<?php
/*
 *  "dbconnect.php" einbinden
 */
require_once '../dbconnect.php';

/*
 * Query schreiben mit dem der Newsletter-Empfänger sowie die zugehörige ID ausgelesen wird ("Select aus 2 Tabellen")
 *
 * Hier ist es nötig Daten aus zwei verschiedenen Tabellen zu selektieren, wir benötigen also einen JOIN-Query. Dabei
 * wird im SELECT schon angegeben, dass wir aus mehreren Tabellen Daten abfragen wollen. Das FROM Statement kann als
 * Einheit zweiter Tabellen betrachtet werden (recipients JOIN newsletter_categories). Das ON Statement gibt schließlich
 * noch an, wie die Zeilen beider Tabellen zusammengeführt werden sollen. Hier wird also definiert, welche Spalten in
 * beiden Tabellen den selben Wert haben müssen, damit die beiden Zeilen im JOIN zusammengeführt werden.
 */
$sql = '
    SELECT recipients.fullname, recipients.email, recipients.created_at, newsletter_categories.title
    FROM recipients
        JOIN newsletter_categories
            ON recipients.newsletter_category_id = newsletter_categories.id
';
/**
 * Hier wird der Query abgeschickt ODER, sofern ein Fehler auftritt wird mit der die()-Funktion die weitere Ausführung
 * des Skripts abgebrochen und der Fehler ausgegeben.
 */
$result = mysqli_query($link, $sql) or die(mysqli_error($link));

/*
 * Ergebnis des Queries auf Variable legen (MYSQLI_ASSOC)
 */
$recipients = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container col-md-8 col-md-offset-2">
    <header class="page-header">
        <ul class="nav nav-pills pull-right">
            <li class=""><a href="../index.php">Home</a></li>
            <li class="active"><a href="#">Administration</a></li>
        </ul>
        <h3 class="text-muted"> Super Newsletter <small>Administration</small></h3>
    </header>

    <table class="table table-hover">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>gewähltes Thema</th>
            <th>eingetragen am</th>
        </tr>
        </thead>
        <tbody>

        <?php

        /*
         * Newsletter Empfänger mittels Schleife durchlaufen und Tabelle befüllen
         */

        //Schleife-Beginn
        foreach ($recipients as $recipient) {
            /*
             * Variablen mit Inhalt aus DB befüllen
             */

            $fullname = $recipient['fullname'];
            $email = $recipient['email'];
            $title = $recipient['title'];
            $created_at = $recipient['created_at'];
            ?>

            <tr>
                <td>
                    <?= $fullname; ?>
                </td>
                <td>
                    <?= $email; ?>
                </td>
                <td>
                    <?= $title; ?>
                </td>
                <td>
                    <?= date('d.m.Y', $created_at); ?>
                </td>
            </tr>

            <?php
        //Schleife-Ende
        }
        ?>

        </tbody>
    </table>

    <footer class="footer">
        <p>&copy; SAE Wien 2014</p>
    </footer>

</div>
<!-- /container -->


<script src="../js/jquery.js"></script>
<script src="../js/bootstrap.min.js"></script>
</body>
</html>
