<?php

/*
 * TODO:
 *  Session starten
 *  "dbconnect.php" einbinden
 *  "logic.php" einbinden
 */
session_start();

require_once 'dbconnect.php';
require_once 'logic.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Newsletter</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container col-md-8 col-md-offset-2">
    <header class="page-header">
        <ul class="nav nav-pills pull-right">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="backend">Administration</a></li>
        </ul>
        <h3 class="text-muted">Super Newsletter</h3>
    </header>

    <section class="jumbotron">
        <h1>Super Newsletter</h1>

        <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Fusce dapibus, tellus ac cursus
                        commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>

        <p><a class="btn btn-lg btn-success" href="#form" role="button">Für Newsletter anmelden</a></p>
    </section>

    <section class="row marketing">
        <div class="col-lg-6">
            <h3>Unsere Themen</h3>
            <?php

            /*
             * TODO:
             * Newsletter-Themen mittels Schleife durchlaufen und Titel + Beschreibung anzeigen
             *
             * @todo: comment
             */
            $query = 'SELECT title, description FROM newsletter_categories';
            $result = mysqli_query($link, $query);
            /*$categories = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }*/

            // ODER
            $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

            //Schleife-Beginn
            /*
             * Markup:
             *  h4 mit Titel
             *  p mit Beschreibung
             */
            //Schleife-Ende
            foreach ($categories as $category): ?>
                <h4><?php echo $category['title'];?></h4>
                <p><?php echo $category['description'];?></p>
            <?php endforeach; ?>
        </div>

        <div class="col-lg-6" id="form">
            <?php

            /*
             * TODO:
             * Überprüfen ob Benutzer sich bereits eingetragen hat (Session-Variable die beim Eintragen gesetzt wird)
             *  wenn ja "content/thank_you.php" anzeigen
             *  wenn nein "content/newsletter_form.php" anzeigen
             */
            if (isset($_SESSION['has_abo']) && $_SESSION['has_abo'] === true) {
                require_once 'content/thank_you.php';
            } else {
                require_once 'content/newsletter_form.php';
            }

            ?>
        </div>
    </section>


    <footer class="footer">
        <p>&copy; SAE Wien 2014</p>
    </footer>

</div>
<!-- /container -->


<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
