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

    if (!isset($_SESSION['counted_urls']) || !is_array($_SESSION['counted_urls'])) {
        $_SESSION['counted_urls'] = [];
    }

    if (!array_key_exists($decodedUrl, $_SESSION['counted_urls'])) {
        $_SESSION['counted_urls'][$decodedUrl] = 1;
    } else {
        $_SESSION['counted_urls'][$decodedUrl] += 1;
    }

//    var_dump($_SESSION);
    header("Location: $decodedUrl");
    exit;
}


?>
