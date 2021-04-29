<?php

namespace Codememory\Http\Request\Upload;

/**
 * Class BootParameters
 * @package System\Http\Request\Upload
 *
 * @author  Codememory
 */
class BootParameters
{

    /**
     * @var array
     */
    private array $data;

    /**
     * BootParameters constructor.
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
    public function mimeType(): string
    {

        return $this->data['types']['mime'];

    }

    /**
     * @return string
     */
    public function expansion(): string
    {

        return $this->data['types']['expansion'];

    }

    /**
     * @return string|int
     */
    public function minSize(): string|int
    {

        return $this->data['sizes']['min'];

    }

    /**
     * @return string|int
     */
    public function maxSize(): string|int
    {

        return $this->data['sizes']['max'];

    }

    /**
     * @return string|int
     */
    public function unitSize(): string|int
    {

        return $this->data['sizes']['unit'];

    }

    /**
     * @return string|int
     */
    public function width(): string|int
    {

        return $this->data['image']['width'];

    }

    /**
     * @return string|int
     */
    public function height(): string|int
    {

        return $this->data['image']['height'];

    }

    /**
     * @return bool
     */
    public function onlyImage(): bool
    {

        return $this->data['image']['onlyImage'];

    }

    /**
     * @return string|int
     */
    public function minUpload(): string|int
    {

        return $this->data['numberUploaded']['min'];

    }

    /**
     * @return string|int
     */
    public function maxUpload(): string|int
    {

        return $this->data['numberUploaded']['max'];

    }

    /**
     * @return array
     */
    public function inArray(): array
    {

        return $this->data;

    }

}