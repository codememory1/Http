<?php

namespace Codememory\HttpFoundation\Response;

use Codememory\HttpFoundation\Client\Header\Header;
use Codememory\HttpFoundation\Interfaces\ResponseInterface;

/**
 * Class SingletonResponse
 * @package Codememory\HttpFoundation\Response
 *
 * @author  Codememory
 */
class SingletonResponse
{

    /**
     * @var ResponseInterface|null
     */
    private static ?ResponseInterface $response = null;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a response object with a singleton pattern implementation
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return ResponseInterface
     */
    public static function getResponse(): ResponseInterface
    {

        if (!self::$response instanceof ResponseInterface) {
            self::$response = new Response(new Header());
        }

        return self::$response;

    }

}