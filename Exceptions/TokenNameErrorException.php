<?php

namespace Codememory\HttpFoundation\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class TokenNameErrorException
 * @package System\Http\Request\Exceptions
 *
 * @author  Codememory
 */
class TokenNameErrorException extends TokenException
{

    /**
     * TokenNameErrorException constructor.
     *
     * @param string $message
     */
    #[Pure]
    public function __construct(string $message)
    {

        parent::__construct($message);

    }

}