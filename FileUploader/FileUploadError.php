<?php

namespace Codememory\HttpFoundation\FileUploader;

use Codememory\HttpFoundation\Interfaces\FileUploadErrorInterface;
use Codememory\Support\Arr;

/**
 * Class FileUploadError
 * @package Codememory\HttpFoundation\FileUploader
 *
 * @author  Codememory
 */
class FileUploadError implements FileUploadErrorInterface
{

    /**
     * @var array|string[]
     */
    public array $errors = [];

    /**
     * @inheritDoc
     */
    public function override(string $type, string $text): FileUploadErrorInterface
    {

        $this->errors[$type] = $text;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function getErrors(): array
    {

        return $this->errors;

    }

    /**
     * @inheritDoc
     */
    public function getError(string $type): ?string
    {

        return Arr::set($this->errors)::get($type);

    }

}