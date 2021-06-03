<?php

namespace Codememory\HttpFoundation\FileUploader;

use Codememory\HttpFoundation\Interfaces\UploadDataInterface;
use Codememory\Support\Str;
use JetBrains\PhpStorm\Pure;

/**
 * Class UploadFileData
 * @package Codememory\HttpFoundation\FileUploader
 *
 * @author  Codememory
 */
class UploadFileData implements UploadDataInterface
{

    /**
     * @var array
     */
    private array $fileData;

    /**
     * UploadFileData constructor.
     *
     * @param array $fileData
     */
    public function __construct(array $fileData)
    {

        $this->fileData = $fileData;

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getName(): string
    {

        return $this->fileData['name'];

    }

    /**
     * @inheritDoc
     */
    public function getMime(): string
    {

        return $this->fileData['type'];

    }

    /**
     * @inheritDoc
     */
    public function getTmp(): string
    {

        return $this->fileData['tmp_name'];

    }

    /**
     * @inheritDoc
     */
    public function getExtension(): string
    {

        return $this->fileData['extension'];

    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {

        return $this->fileData['size'];

    }
}