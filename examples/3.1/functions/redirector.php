<?php
/**
 * @todo: comment
 */
session_start();

/**
 * redirector.php?url=http://something
 */

if (isset($_GET['url'])) {
    /**
     * [x] Url in Session speichern
     * [x] Redirect
     */
    $encodedUrl = $_GET['url'];
    $decodedUrl = urldecode($encodedUrl);

    if (!isset($_SESSION['visited_urls']) || !is_array($_SESSION['visited_urls'])) {
        $_SESSION['visited_urls'] = [];
    }

    if (!in_array($decodedUrl, $_SESSION['visited_urls'])) {
        $_SESSION['visited_urls'][] = $decodedUrl;
    }

    var_dump($_SESSION);
    header("Location: $decodedUrl");
    exit;
}


?>
