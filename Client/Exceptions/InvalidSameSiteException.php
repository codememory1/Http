<?php

namespace Codememory\Http\Client\Exceptions;


use JetBrains\PhpStorm\Pure;

/**
 * Class InvalidSameSiteException
 * @package System\Support\Exceptions\Cookie
 *
 * @author  Codememory
 */
class InvalidSameSiteException extends CookieException
{

    /**
     * InvalidSameSiteException constructor.
     *
     * @param string $sameSite
     * @param string $listSameSite
     */
    #[Pure] public function __construct(string $sameSite, string $listSameSite)
    {

        parent::__construct(sprintf(
            'SameSite%s is not reserved. Available list: %s',
            $sameSite,
            $listSameSite
        ));

    }

}