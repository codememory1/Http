<?php

namespace Codememory\Http\Request\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class CdmTokenNameErrorException
 * @package System\Http\Request\Exceptions
 *
 * @author  Codememory
 */
class CdmTokenNameErrorException extends CdmTokenException
{

    /**
     * CdmTokenNameErrorException constructor.
     *
     * @param string $message
     */
    #[Pure] public function __construct(string $message)
    {

        parent::__construct($message);

    }

}