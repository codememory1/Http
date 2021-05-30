<?php

namespace Codememory\HttpFoundation\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class HeaderNotFoundInSubException
 * @package Codememory\Http\Client\Exceptions
 *
 * @author  Codememory
 */
class HeaderNotFoundInSubException extends HeaderException
{

    /**
     * HeaderNotFoundInSubException constructor.
     *
     * @param string $header
     */
    #[Pure] public function __construct(string $header)
    {

        parent::__construct(sprintf(
            'Unable to add value to dispatched header %1$s. First you need to create a %1$s header',
            $header
        ));

    }

}