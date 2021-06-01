<?php

namespace Core;

/**
 * Class ApiResponse
 *
 * @package Core
 * @todo: comment
 */
class ApiResponse {

    public static function json (mixed $data, int $httpStatusCode = 200) {
        http_response_code($httpStatusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

}
