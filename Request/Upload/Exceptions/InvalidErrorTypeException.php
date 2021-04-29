<?php

namespace Codememory\Http\Request\Upload\Exceptions;

use ErrorException;
use JetBrains\PhpStorm\Pure;

/**
 * Class InvalidErrorTypeException
 * @package System\Http\Request\Upload\Exceptions
 *
 * @author  Codememory
 */
class InvalidErrorTypeException extends ErrorException
{

    /**
     * @var string
     */
    private string $type;

    /**
     * InvalidErrorTypeException constructor.
     *
     * @param string $type
     */
    #[Pure] public function __construct(string $type)
    {

        $this->type = $type;

        parent::__construct(sprintf(
            'Error type "%s" for uploading files does not exist',
            $this->type
        ));
    }

    /**
     * @return string
     */
    public function getType(): string
    {

        return $this->type;

    }

}