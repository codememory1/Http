<?php

namespace Codememory\HttpFoundation\Interfaces;

use Codememory\Components\UnitConversion\Conversion;

/**
 * Interface UploaderInterface
 * @package Codememory\HttpFoundation\Interfaces
 *
 * @author  Codememory
 */
interface UploaderInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check the name of the loaded file by regex
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $regex
     *
     * @return UploaderInterface
     */
    public function regexOfNames(string $regex): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Allow uploading files of a specific mime type
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $types
     *
     * @return UploaderInterface
     */
    public function mimeTypes(array $types): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Specify which extensions are allowed when uploading a file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $extensions
     *
     * @return UploaderInterface
     */
    public function extensions(array $extensions): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Prevent some extensions when uploading a file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $extensions
     *
     * @return UploaderInterface
     */
    public function disallowExtensions(array $extensions): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Prevent specific file types from uploading
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $types
     *
     * @return UploaderInterface
     */
    public function disallowMimeTypes(array $types): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Specify what size of files should be when uploading
     * in different systems of reconciliation. Default bytes
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int|null $min
     * @param int|null $max
     * @param string   $unit
     *
     * @return UploaderInterface
     */
    public function size(?int $min = null, ?int $max = null, string $unit = Conversion::CURRENT): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Specify the range of downloaded files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int      $min
     * @param int|null $max
     *
     * @return UploaderInterface
     */
    public function numberUploads(int $min = 0, int $max = null): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A custom handler that accepts a callback as an argument, which,
     * in turn, must return a boolean value so that the uploader
     * can understand if it should try to upload files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $handler
     *
     * @return UploaderInterface
     */
    public function customHandler(callable $handler): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Using this method, you can override error messages, when uploading
     * files, the callback takes an argument of type FileUploadErrorInterface
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $handler
     *
     * @return UploaderInterface
     */
    public function customErrorHandler(callable $handler): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Specify the path where the files will be uploaded
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     *
     * @return UploaderInterface
     */
    public function whereToSave(string $path): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * An additional method with which you can handle the
     * success status of file uploads
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $handler
     *
     * @return UploaderInterface
     */
    public function handlerAfterUpload(callable $handler): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Using this method, you can change the data of uploaded files,
     * for example, change their name to hash and so on, callback
     * takes a data argument that must be a link
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $handler
     *
     * @return UploaderInterface
     */
    public function changeUploadFilesData(callable $handler): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add an error, this method is only needed when a
     * custom handler is used
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $message
     *
     * @return UploaderInterface
     */
    public function addError(string $type, string $message): UploaderInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Method that downloads all files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return MakeUploadInterface
     */
    public function make(): MakeUploadInterface;

}