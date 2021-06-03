<?php

namespace Codememory\HttpFoundation\FileUploader;

use Closure;
use Codememory\Components\UnitConversion\Conversion;
use Codememory\HttpFoundation\Interfaces\FileUploadErrorInterface;
use Codememory\HttpFoundation\Interfaces\MakeUploadInterface;
use Codememory\HttpFoundation\Interfaces\RequestInterface;
use Codememory\HttpFoundation\Interfaces\UploaderInterface;

/**
 * Class Uploader
 * @package Codememory\HttpFoundation\FileUploader
 *
 * @property string                    $regexOfNames
 * @property string|array              $mimeTypes
 * @property string|array              $extensions
 * @property array                     $disallowExtensions
 * @property array                     $disallowMimeTypes
 * @property array                     $size
 * @property array                     $numberUploads
 * @property bool                      $customHandler
 * @property string                    $pathToSave
 * @property bool|Closure              $handlerAfterUpload
 * @property ?FileUploadErrorInterface $fileUploadError
 *
 * @author  Codememory
 */
class Uploader implements UploaderInterface
{

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var string
     */
    private string $inputName;

    /**
     * @var array
     */
    private array $filesData;

    /**
     * @var string
     */
    private string $regexOfNames = '*';

    /**
     * @var string|array
     */
    private string|array $mimeTypes = '*';

    /**
     * @var string|array
     */
    private string|array $extensions = '*';

    /**
     * @var array
     */
    private array $disallowExtensions = [];

    /**
     * @var array
     */
    private array $disallowMimeTypes = [];

    /**
     * @var array
     */
    private array $size = [
        'min'  => '*',
        'max'  => '*',
        'unit' => Conversion::CURRENT
    ];

    /**
     * @var array
     */
    private array $numberUploads = [
        'min' => 0,
        'max' => '*'
    ];

    /**
     * @var bool
     */
    private bool $customHandler = true;

    /**
     * @var string
     */
    private string $pathToSave = '/';

    /**
     * @var bool|Closure
     */
    private bool|Closure $handlerAfterUpload = false;

    /**
     * @var FileUploadErrorInterface|null
     */
    private ?FileUploadErrorInterface $fileUploadError = null;

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * Uploader constructor.
     *
     * @param RequestInterface $request
     * @param string           $inputName
     */
    public function __construct(RequestInterface $request, string $inputName)
    {

        $this->request = $request;
        $this->inputName = $inputName;
        $this->filesData = $this->request->files($inputName);

    }

    /**
     * @inheritDoc
     */
    public function regexOfNames(string $regex): UploaderInterface
    {

        $this->regexOfNames = $regex;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function mimeTypes(array $types): UploaderInterface
    {

        $this->mimeTypes = $types;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function extensions(array $extensions): UploaderInterface
    {

        $this->extensions = $extensions;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function disallowExtensions(array $extensions): UploaderInterface
    {

        $this->disallowExtensions = $extensions;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function disallowMimeTypes(array $types): UploaderInterface
    {

        $this->disallowMimeTypes = $types;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function size(?int $min = null, ?int $max = null, string $unit = Conversion::CURRENT): UploaderInterface
    {

        $this->size = [
            'min'  => $min ?: '*',
            'max'  => $max ?: '*',
            'unit' => $unit
        ];

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function numberUploads(int $min = 0, int $max = null): UploaderInterface
    {

        $this->numberUploads = [
            'min' => $min,
            'max' => $max ?: '*'
        ];

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function customHandler(callable $handler): UploaderInterface
    {

        $this->customHandler = call_user_func_array($handler, [$this->filesData, $this]);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function customErrorHandler(callable $handler): UploaderInterface
    {

        $fileUploadError = $this->getUploadError();

        call_user_func_array($handler, [&$fileUploadError]);

        $this->fileUploadError = $fileUploadError;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function whereToSave(string $path): UploaderInterface
    {

        $this->pathToSave = trim($path, '/');

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function handlerAfterUpload(callable $handler): UploaderInterface
    {

        $this->handlerAfterUpload = $handler;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function changeUploadFilesData(callable $handler): UploaderInterface
    {

        $filesData = call_user_func_array($handler, [&$this->filesData]);

        if(is_array($filesData)) {
            $this->filesData = $filesData;
        }

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function addError(string $type, string $message): UploaderInterface
    {

        $this->errors[$type] = $message;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function make(): MakeUploadInterface
    {

        $makeUpload = new Make(clone $this, $this->getUploadError(), $this->filesData);

        foreach ($this->errors as $type => $message) {
            $makeUpload->addError($type, $message);
        }

        $makeUpload->makeUpload();

        if (null !== $this->handlerAfterUpload && $makeUpload->isSuccess()) {
            call_user_func($this->handlerAfterUpload);
        }

        return $makeUpload;

    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get(string $property): mixed
    {

        return $this->$property;

    }

    /**
     * @return FileUploadErrorInterface
     */
    private function getUploadError(): FileUploadErrorInterface
    {

        if (!$this->fileUploadError instanceof FileUploadErrorInterface) {
            $this->fileUploadError = new FileUploadError();
        }

        return $this->fileUploadError;

    }

}
