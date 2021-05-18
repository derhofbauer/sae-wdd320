<?php

use App\Controllers\Admin\AdminController;
use App\Controllers\BlogController;
use App\Controllers\CategoryController;
use App\Controllers\AuthController;

/**
 * Werden mit dem use-Keyword mehrere Klassen mit dem selben Namen importiert, so können diese weiter unten nicht mehr
 * unterschieden werden, wenn der Namespace nicht angegeben wird. Daher kann man mit dem as-Keyword Aliases definieren.
 * Funktionell besteht also überhaupt kein Unterschied, aber die Admin\CategoryController Klasse ist weiter unten auch
 * unter dem Alias AdminCategoryController verfügbar.
 */
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\PostController as AdminPostController;
use App\Controllers\Admin\UserController as AdminUserController;

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

    /**
     * Blog Routes
     */
    '/blog' => [BlogController::class, 'index'], // Posts auflisten
    '/blog/{slug}' => [BlogController::class, 'show'], // einzelnen Post anzeigen

    /**
     * Category Routes
     */
    '/categories' => [CategoryController::class, 'index'], // Categories auflisten
    '/categories/{slug}' => [CategoryController::class, 'show'], // einzelne Category anzeigen (=> alle Posts einer Category auflisten)

    /**
     * Login & Sign-up Routes
     */
    '/login' => [AuthController::class, 'loginForm'], // Login Formular anzeigen
    '/login/do' => [AuthController::class, 'login'], // Login durchführen
    '/logout/do' => [AuthController::class, 'logout'], // Logout durchführen
    '/sign-up' => [AuthController::class, 'signupForm'], // Sign-up Formular anzeigen
    '/sign-up/do' => [AuthController::class, 'signup'], // Sign-up Formular anzeigen

    /**
     * Admin Routes
     */
    '/admin' => [AdminController::class, 'dashboard'], // Admin Dashboard anzeigen

    /**
     * Admin Category Routes
     */
    '/admin/categories' => [AdminCategoryController::class, 'index'], // Alle Kategorien listen
    '/admin/categories/{id}/edit' => [AdminCategoryController::class, 'edit'], // Bearbeitungsformular anzeigen
    '/admin/categories/{id}/update' => [AdminCategoryController::class, 'update'], // Bearbeitete Category speichern
    '/admin/categories/{id}/delete' => [AdminCategoryController::class, 'deleteConfirm'], // Löschen bestätigen
    '/admin/categories/{id}/delete/confirm' => [AdminCategoryController::class, 'delete'], // Category löschen

    /**
     * Admin Post Routes
     */
    '/admin/posts' => [AdminPostController::class, 'index'], // Alle Posts listen
    '/admin/posts/{id}/edit' => [AdminPostController::class, 'edit'], // Bearbeitungsformular anzeigen
    '/admin/posts/{id}/update' => [AdminPostController::class, 'update'], // Bearbeiteten Post speichern
    '/admin/posts/{id}/delete' => [AdminPostController::class, 'deleteConfirm'], // Löschen bestätigen
    '/admin/posts/{id}/delete/confirm' => [AdminPostController::class, 'delete'], // Post löschen

    /**
     * Admin User Routes
     */
    '/admin/users' => [AdminUserController::class, 'index'], // Alle User listen
    '/admin/users/{id}/edit' => [AdminUserController::class, 'edit'], // Bearbeitungsformular anzeigen
    '/admin/users/{id}/update' => [AdminUserController::class, 'update'], // Bearbeitete*n User*in speichern

];
