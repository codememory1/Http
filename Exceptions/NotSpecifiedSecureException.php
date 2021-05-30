<?php

namespace Codememory\HttpFoundation\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class NotSpecifiedSecureException
 * @package System\Support\Exceptions\Cookie
 *
 * @author  Codememory
 */
class NotSpecifiedSecureException extends CookieException
{

    /**
     * NotSpecifiedSecureException constructor.
     *
     * @param string $sameSite
     */
    #[Pure] public function __construct(string $sameSite)
    {

        parent::__construct(sprintf('When choosing SameSite %s, you need to add Secure', $sameSite));

    }

}