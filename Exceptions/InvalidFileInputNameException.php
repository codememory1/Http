<?php

namespace Codememory\HttpFoundation\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class InvalidFileInputNameException
 * @package Codememory\HttpFoundation\Exceptions
 *
 * @author  Codememory
 */
class InvalidFileInputNameException extends UploaderException
{

    /**
     * InvalidFileInputNameException constructor.
     *
     * @param string $inputName
     */
    #[Pure]
    public function __construct(string $inputName)
    {

        parent::__construct(sprintf('Incorrect name(%s) of file upload input', $inputName));

    }

}