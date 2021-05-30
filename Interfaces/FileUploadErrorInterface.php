<?php

namespace Codememory\HttpFoundation\Interfaces;

/**
 * Interface FileUploadErrorInterface
 * @package Codememory\HttpFoundation\Interfaces
 *
 * @author  Codememory
 */
interface FileUploadErrorInterface
{

    public const MIME = 'mime_type';
    public const EXTENSION = 'extension';
    public const NAME_BY_REGEX = 'name_by_regex';
    public const NUM_UPLOADS = 'number_uploads';
    public const SIZE = 'size';
    public const IMAGE = 'image';
    public const DIS_MIME = 'disallow_mime_type';
    public const DIS_EXTENSION = 'disallow_extension';

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Override error by type when uploading files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $text
     *
     * @return FileUploadErrorInterface
     */
    public function override(string $type, string $text): FileUploadErrorInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of all overridden errors
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the specific text of an overridden error by type
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     *
     * @return string|null
     */
    public function getError(string $type): ?string;

}