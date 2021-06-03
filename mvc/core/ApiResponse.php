<?php

namespace Core;

/**
 * Class ApiResponse
 *
 * Hierbei handelt es sich um eine Hilfsklasse, die es ermöglicht JSON aus der API zurückzugeben.
 *
 * @package Core
 */
class ApiResponse {

    /**
     * Daten im JSON Format als Api Response zurückgeben.
     *
     * @param mixed $data
     * @param int   $httpStatusCode
     */
    public static function json (mixed $data, int $httpStatusCode = 200) {
        /**
         * HTTP Status Code setzen, damit wir auch saubere Fehlercodes setzen könnten.
         */
        http_response_code($httpStatusCode);
        /**
         * Content Type Header setzen.
         */
        header('Content-Type: application/json');
        /**
         * Übergebene Daten in JSON konvertieren und mit echo in den Response Body schreiben.
         */
        echo json_encode($data);
        /**
         * Abbrechen, damit kein weiterer Code mehr ausgeführt wird.
         */
        exit;
    }

}
