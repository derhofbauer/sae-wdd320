<?php

/**
 * Damit wir überall die $_SESSION Superglobal verwenden können, müssen wir die Session erst starten. Das bietet sich so
 * gut wie immer relativ weit am Beginn des Programms an.
 */
session_start();

require_once "partials/header.php";

/**
 * Alias definiert, damit ich nicht immer die Superglobal mit den lästigen Array Klammern schreiben muss.
 */
$get = isset($_GET['page']) ? $_GET['page'] : null;

if ($get === 'home') {
    require_once 'content/home.php';
} elseif ($get === 'contact') {
    require_once 'content/contact.php';
} elseif ($get === 'links') {
    require_once 'content/links.php';
} elseif ($get === 'agb') {
    require_once 'content/agb.php';
} else {
    require_once 'content/home.php';
}

/**
 * ODER:
 */

/*switch ($get) {
    case 'contact':
        require_once 'content/contact.php';
        break;
    case 'agb':
        require_once 'content/agb.php';
        break;
    case 'home':
    default:
        require_once 'content/home.php';
        break;
}*/

require_once "partials/footer.php";
?>
