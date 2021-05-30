<?php

namespace Codememory\HttpFoundation\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class TokenGenerationErrorException
 * @package System\Http\Request\Exceptions
 *
 * @author  Codememory
 */
class TokenGenerationErrorException extends TokenException
{

    /**
     * TokenGenerationErrorException constructor.
     */
    #[Pure]
    public function __construct()
    {

        parent::__construct('CdmToken was not generated, perhaps the token name was not specified');

    }

}