<?php

namespace Codememory\Http\Request\Upload\Exceptions;

use ErrorException;
use JetBrains\PhpStorm\Pure;

/**
 * Class InvalidInputNameException
 * @package System\Http\Request\Upload\Exceptions
 *
 * @author  Codememory
 */
class InvalidInputNameException extends ErrorException
{

    /**
     * InvalidInputNameException constructor.
     *
     * @param string $file
     * @param int    $line
     */
    #[Pure] public function __construct(string $file, int $line)
    {

        parent::__construct(sprintf(
            'Input name for uploading files is not specified in file: %s at line: %s',
            $file,
            $line
        ));

    }

}