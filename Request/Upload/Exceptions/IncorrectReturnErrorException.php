<?php

namespace Codememory\Http\Request\Upload\Exceptions;

use ErrorException;
use JetBrains\PhpStorm\Pure;

/**
 * Class IncorrectReturnErrorException
 * @package System\Http\Request\Upload\Exceptions
 *
 * @author  Codememory
 */
class IncorrectReturnErrorException extends ErrorException
{

    /**
     * IncorrectReturnErrorException constructor.
     */
    #[Pure] public function __construct()
    {

        parent::__construct('RequestCallback to handle file upload error, should return an error (string)');

    }

}