<?php
/**
 * Hier müssen wir die Session erneut starten, obwohl wir das im index.php-File schon gemacht haben. Das liegt daran,
 * dass das redirector.php-File nicht über das index.php geladen wird, sondern direkt über den Link aus der Linkliste.
 */
session_start();

/**
 * Wenn der url-Parameter übergeben wurde ...
 */
if (isset($_GET['url'])) {
    /**
     * ... erstellen wir ein Alias und verwenden die urldecode()-Funktion um das urlencoding aus dem links.php-File
     * wieder umzukehren.
     */
    $encodedUrl = $_GET['url'];
    $decodedUrl = urldecode($encodedUrl);

    /**
     * Wenn in der Session noch kein Element mit dem Key 'visited_urls' ist UND/ODER dieses Element kein Array ist, so
     * definieren wir es hier als leeren Array. Das würde zutreffen, wenn der redirector noch nie aufgerufen wurde.
     */
    if (!isset($_SESSION['visited_urls']) || !is_array($_SESSION['visited_urls'])) {
        $_SESSION['visited_urls'] = [];
    }

    /**
     * Befindet sich die aufgerufene URL nicht bereits in der Session ...
     */
    if (!in_array($decodedUrl, $_SESSION['visited_urls'])) {
        /**
         * ... pushen wir sie einfach hinein.
         */
        $_SESSION['visited_urls'][] = $decodedUrl;
    }

    /**
     * Als letzten Schritt führen wir den Redirect durch.
     */
    header("Location: $decodedUrl");

    /**
     * Sehr häufig macht man nach einem Redirect ein exit-Statement, damit der nachfolgende Code nicht mehr aufgerufen
     * wird, weil dieser ohnehin nicht mehr an den Client geschickt werden könnte.
     */
    exit;
}


?>
