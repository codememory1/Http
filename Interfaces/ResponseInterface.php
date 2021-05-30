<?php

namespace Codememory\HttpFoundation\Interfaces;

/**
 * Interface ResponseInterface
 * @package Codememory\HttpFoundation\Interfaces
 *
 * @author  Codememory
 */
interface ResponseInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Simplified method call to three methods setContent,
     * setResponseCode, setHeaders
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $content
     * @param int         $responseCode
     * @param array       $headers
     *
     * @return ResponseInterface
     */
    public function create(?string $content = null, int $responseCode = 200, array $headers = []): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set content that can be displayed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $content
     *
     * @return ResponseInterface
     */
    public function setContent(?string $content = null): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns content that was previously installed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Submit content - the installed content will be
     * displayed on the screen
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return ResponseInterface
     */
    public function sendContent(): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set headers to be sent after calling the
     * headers dispatch method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $headers
     *
     * @return ResponseInterface
     */
    public function setHeaders(array $headers): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set the response code that will be sent after calling
     * the headers dispatch method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int $code
     *
     * @return ResponseInterface
     */
    public function setResponseCode(int $code): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the current response code
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return int
     */
    public function getResponseCode(): int;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Send all headers and response code
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return ResponseInterface
     */
    public function sendHeaders(): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Sends both content and headers immediately, including the response code
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return ResponseInterface
     */
    public function sendContentAndHeaders(): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Send content with json data
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param mixed $data
     * @param int   $responseCode
     * @param array $headers
     *
     * @return ResponseInterface
     */
    public function json(mixed $data, int $responseCode = 200, array $headers = []): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Force download files, as the first argument, the path to the file
     * that can be downloaded is passed, the second argument, you can
     * rename the file name, by default null - which means default name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string      $file
     * @param string|null $rename
     * @param array       $headers
     *
     * @return ResponseInterface
     */
    public function giveForDownload(string $file, ?string $rename = null, array $headers = []): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Send header with page response type
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     *
     * @return ResponseInterface
     */
    public function setContentType(string $type): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Send header with new encoding for page
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $charset
     *
     * @return ResponseInterface
     */
    public function setCharset(string $charset): ResponseInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code for 100
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isContinue(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code for 200
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isOk(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code for 301  302
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isRedirect(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code for 403
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isForbidden(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code for 404
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isNotFound(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code from 100 to 200
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isInformational(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code from 200 to 300
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code from 300 to 400
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isRedirection(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code from 400 to 500
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isClientError(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check response code from 500 to >
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isServerError(): bool;

}