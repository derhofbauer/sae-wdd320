<?php

/**
 * Routen
 *
 * @todo: comment
 *
 * + /blog/{slug} --> BlogController->show($slug)
 * + /shop/{id} --> ProductController->show($id)
 */

use App\Controllers\HomeController;

return [
    '/' => [HomeController::class, 'index'],

    // '/blog' => [BlogController, 'index'], // Posts auflisten
    // '/blog/{slug}' => ['BlogController', 'show'], // einzelnen Post anzeigen
    // ...

    // '/login' => [AuthController, 'showLogin'], // Login Formular anzeigen
    // '/login/do' => [AuthController, 'login'], // Login durchführen
    // ...

];
