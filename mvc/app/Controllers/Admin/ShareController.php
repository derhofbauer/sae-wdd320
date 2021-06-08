<?php

namespace App\Controllers\Admin;

use App\Models\Share;
use Core\Helpers\Redirector;
use Core\Middlewares\AuthMiddleware;
use Core\Session;
use Core\View;

/**
 * Class ShareController
 *
 * @package App\Controllers\Admin
 * @todo    : comment
 */
class ShareController
{

    public function __constructor ()
    {
        AuthMiddleware::isAdminOrFail();
    }

    public function index ()
    {
        $shares = Share::allOpen();

        View::render('admin/shares/index', [
            'shares' => $shares
        ], 'sidebar');
    }

    public function edit (int $id)
    {
        $share = Share::findOrFail($id);

        View::render('admin/shares/edit', [
            'share' => $share
        ]);
    }

    public function update (int $id)
    {
        $possibleStati = array_keys(Share::STATI);
        $errors = [];

        if (!isset($_POST['status']) || !in_array($_POST['status'], $possibleStati)) {
            $errors[] = 'Possible value for status are open, progress, storno and delivered.';
            Session::set('errors', $errors);
            Redirector::redirect(BASE_URL . "/admin/shares/{$id}/edit");
        }

        $share = Share::findOrFail($id);
        $share->status = $_POST['status'];
        $share->save();

        Session::set('success', ['Share erfolgreich aktualisiert.']);
        Redirector::redirect(BASE_URL . '/admin/shares');
    }

}
