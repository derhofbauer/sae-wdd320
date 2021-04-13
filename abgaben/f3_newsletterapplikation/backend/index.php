<?php
/*
 * TODO:
 *  "dbconnect.php" einbinden
 */
require_once '../dbconnect.php';

/*
 * TODO:
 * Query schreiben mit dem der Newsletter-Empfänger sowie die zugehörige ID ausgelesen wird ("Select aus 2 Tabellen")
 *
 * @todo: comment
 */
$sql = '
    SELECT recipients.fullname, recipients.email, recipients.created_at, newsletter_categories.title
    FROM recipients
        JOIN newsletter_categories
            ON recipients.newsletter_category_id = newsletter_categories.id
';
/**
 * @todo: comment
 */
$result = mysqli_query($link, $sql) or die(mysqli_error($link));

/*
 * TODO:
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
         * TODO:
         * Newsletter Empfänger mittels Schleife durchlaufen und Tabelle befüllen
         */

        //Schleife-Beginn
        foreach ($recipients as $recipient) {
            /*
             * TODO:
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
