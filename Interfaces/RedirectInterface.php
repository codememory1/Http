<?php

namespace Codememory\HttpFoundation\Interfaces;

/**
 * Interface RedirectInterface
 * @package Codememory\HttpFoundation\Interfaces
 *
 * @author  Codememory
 */
interface RedirectInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create redirection to specific url addresses, with
     * specific status and headers
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $url
     * @param int    $responseCode
     * @param array  $headers
     *
     * @return RedirectInterface
     */
    public function redirect(string $url, int $responseCode = 302, array $headers = []): RedirectInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Reload the current page with specific status and titles
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int   $responseCode
     * @param array $headers
     *
     * @return RedirectInterface
     */
    public function refresh(int $responseCode = 302, array $headers = []): RedirectInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Write the current url to the session as the previous one
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $url
     *
     * @return RedirectInterface
     */
    public function setPreviousUrl(string $url): RedirectInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Redirect to the previous url, if the url does not exist
     * in the session, the redirect will be to the current page
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int   $responseCode
     * @param array $headers
     *
     * @return RedirectInterface
     */
    public function previous(int $responseCode = 302, array $headers = []): RedirectInterface;

}