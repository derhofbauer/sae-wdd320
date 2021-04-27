<?php

use App\Controllers\BlogController;

/**
 * Die Dateien im /routes Ordner beinhalten ein Mapping von einer URL auf eine eindeutige Controller & Action
 * kombination. Als Konvention definieren wir, dass URL-Parameter mit {xyz} definiert werden müssen, damit das Routing
 * korrekt funktioniert.
 *
 * + /blog/{slug} --> BlogController->show($slug)
 * + /shop/{id} --> ProductController->show($id)
 */

return [
    /**
     * Home Routes
     */
    '/' => [BlogController::class, 'index'],

    '/blog' => [BlogController::class, 'index'], // Posts auflisten
    '/blog/{slug}' => [BlogController::class, 'show'], // einzelnen Post anzeigen
    // ...

    // '/login' => [AuthController, 'showLogin'], // Login Formular anzeigen
    // '/login/do' => [AuthController, 'login'], // Login durchführen
    // ...

];
