<?php


namespace App\Controllers;

use Core\Config;

/**
 * Class MailtestController
 *
 * @package App\Controllers
 */
class MailtestController
{

    /**
     * Testmail verschicken.
     *
     * @param string $email
     */
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
