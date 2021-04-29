<?php

namespace Codememory\Http\Request\Upload;

/**
 * Class BootInfo
 * @package System\Http\Request\Upload
 *
 * @author  Codememory
 */
class BootInfo
{

    /**
     * @var array
     */
    private array $data;

    /**
     * BootInfo constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {

        $this->data = $data;

    }

    /**
     * @return string
     */
    public function name(): string
    {

        return $this->data['name'];

    }

    /**
     * @return string
     */
    public function expansion(): string
    {

        return $this->data['expansion'];

    }

    /**
     * @return string
     */
    public function tmpName(): string
    {

        return $this->data['tmp_name'];

    }

}