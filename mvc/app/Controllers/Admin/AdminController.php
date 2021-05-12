<?php

namespace App\Controllers\Admin;

use Core\Middlewares\AuthMiddleware;
use Core\View;

/**
 * Class AdminController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class AdminController
{

    /**
     * [ ] Liste aller User (eigener Menüpunkt)
     * [ ] Liste aller Posts (eigener Menüpunkt)
     * [ ] Liste aller Kategorien (eigener Menüpunkt)
     * @todo: comment (named params)
     */
    public function dashboard ()
    {
        /**
         * @todo:comment
         */
        AuthMiddleware::isAdminOrFail();

        View::render('admin/dashboard', layout: 'sidebar');
    }

}
