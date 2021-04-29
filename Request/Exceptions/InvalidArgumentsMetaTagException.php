<?php

namespace Codememory\Http\Request\Exceptions;

use ErrorException;
use JetBrains\PhpStorm\Pure;

/**
 * Class InvalidArgumentsMetaTagException
 * @package System\Http\Request\Exceptions
 *
 * @author  Codememory
 */
class InvalidArgumentsMetaTagException extends ErrorException
{

    /**
     * InvalidArgumentsMetaTagException constructor.
     */
    #[Pure] public function __construct()
    {

        parent::__construct('To get the meta tag, arguments 2 or 3 in the getMeta method must be filled');

    }

}