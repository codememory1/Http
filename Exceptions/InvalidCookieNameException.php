<?php

namespace Codememory\HttpFoundation\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class InvalidCookieNameException
 * @package System\Support\Exceptions\Cookie
 *
 * @author  Codememory
 */
class InvalidCookieNameException extends CookieException
{

    /**
     * InvalidCookieNameException constructor.
     *
     * @param string $chars
     */
    #[Pure] public function __construct(string $chars)
    {

        parent::__construct(sprintf('The cookie name must not contain the following characters: %s or perhaps the cookie name has not been set', $chars));

    }

}