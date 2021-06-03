<?php

use Codememory\HttpFoundation\Interfaces\ResponseInterface;
use Codememory\HttpFoundation\Response\SingletonResponse;

if (!function_exists('responseCode')) {
    /**
     * @param int $code
     *
     * @return ResponseInterface
     */
    function responseCode(int $code): ResponseInterface
    {

        return SingletonResponse::getResponse()
            ->setResponseCode($code)
            ->sendHeaders();

    }
}

if (!function_exists('sendHeaders')) {
    /**
     * @param array $headers
     *
     * @return ResponseInterface
     */
    function sendHeaders(array $headers): ResponseInterface
    {

        return SingletonResponse::getResponse()
            ->setHeaders($headers)
            ->sendHeaders();

    }
}

if (!function_exists('sendContent')) {
    /**
     * @param string $content
     * @param int    $responseCode
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    function sendContent(string $content, int $responseCode = 200, array $headers = []): ResponseInterface
    {

        return SingletonResponse::getResponse()
            ->create($content, $responseCode, $headers)
            ->sendContentAndHeaders();

    }
}

if (!function_exists('jsonResponse')) {
    /**
     * @param mixed $data
     * @param int   $responseCode
     * @param array $headers
     *
     * @return ResponseInterface
     */
    function jsonResponse(mixed $data, int $responseCode = 200, array $headers = []): ResponseInterface
    {

        return SingletonResponse::getResponse()
            ->json($data, $responseCode, $headers);

    }
}