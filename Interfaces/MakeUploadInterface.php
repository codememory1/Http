<?php

namespace Codememory\HttpFoundation\Interfaces;

/**
 * Interface MakeUploadInterface
 * @package Codememory\HttpFoundation\Interfaces
 *
 * @author  Codememory
 */
interface MakeUploadInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add an error that will be displayed when some condition fails.
     * The first argument is the type of error, which will be used to
     * search for overridden errors, the second argument is the
     * default message, which will be displayed if there is no overridden
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $defaultMessage
     *
     * @return MakeUploadInterface
     */
    public function addError(string $type, string $defaultMessage): MakeUploadInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of active errors while uploading files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the first active error
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|bool
     */
    public function getFirstError(): string|bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a boolean value indicating whether the
     * files were downloaded successfully
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isSuccess(): bool;

}