<?php


namespace App\Controllers;

use Core\Config;

/**
 * Class MailtestController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class MailtestController
{

    public function test (string $email)
    {
        $result = mail(
            $email,
            'Test Mail',
            "This is a testing mail!",
            [
                'From' => 'noreply@' . Config::get('app.app-slug')
            ]
        );

        var_dump($result);
    }

}
