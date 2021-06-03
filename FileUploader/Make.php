<?php

namespace Codememory\HttpFoundation\FileUploader;

use Codememory\FileSystem\File;
use Codememory\HttpFoundation\Interfaces\FileUploadErrorInterface;
use Codememory\HttpFoundation\Interfaces\MakeUploadInterface;
use Codememory\HttpFoundation\Interfaces\UploadDataInterface;
use Codememory\HttpFoundation\Traits\UploadVerificationsTrait;
use JetBrains\PhpStorm\Pure;

/**
 * Class Make
 * @package Codememory\HttpFoundation\FileUploader
 *
 * @author  Codememory
 */
class Make implements MakeUploadInterface
{

    use UploadVerificationsTrait;

    /**
     * @var array
     */
    private array $filesData;

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @var Uploader
     */
    private Uploader $uploader;

    /**
     * @var FileUploadErrorInterface
     */
    private FileUploadErrorInterface $uploadError;

    /**
     * @var bool
     */
    private bool $successUpload = false;

    /**
     * Make constructor.
     *
     * @param Uploader                 $uploader
     * @param FileUploadErrorInterface $uploadError
     * @param array                    $filesData
     */
    public function __construct(Uploader $uploader, FileUploadErrorInterface $uploadError, array $filesData)
    {

        $this->uploader = $uploader;
        $this->uploadError = $uploadError;
        $this->filesData = $filesData;

    }

    /**
     * @inheritDoc
     */
    public function addError(string $type, string $defaultMessage): MakeUploadInterface
    {

        $this->errors[$type] = null !== $this->uploadError->getError($type) ? $this->uploadError->getError($type) : $defaultMessage;

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
    #[Pure]
    public function getFirstError(): string|bool
    {

        if ([] === $this->getErrors()) {
            return false;
        }

        return $this->getErrors()[array_key_first($this->getErrors())];

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function isSuccess(): bool
    {

        return [] === $this->getErrors() && $this->successUpload;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The main method that calls in itself all the verifications
     * and if they are successful, the method uploads all
     * files to the folder
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    public function makeUpload(): void
    {

        $this
            ->nameVerification()
            ->mimeTypeVerification()
            ->extensionVerification()
            ->disallowMimeTypeVerification()
            ->disallowExtensionVerification()
            ->sizeVerification()
            ->numberUploadsVerification()
            ->upload();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A handler method that uploads files to a folder
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return $this
     */
    private function upload(): static
    {

        $filesystem = new File();

        if ($this->isSuccess() && $this->uploader->customHandler) {
            $this->iterationFilesData(function (UploadDataInterface $uploadData, Uploader $uploader) use ($filesystem) {
                $filename = sprintf('%s/%s.%s', $uploader->pathToSave, $uploadData->getName(), $uploadData->getExtension());

                if (!$filesystem->exist($uploader->pathToSave) || !move_uploaded_file($uploadData->getTmp(), $filesystem->getRealPath($filename))) {
                    return false;
                }

                $filesystem->setPermission($filename);

                return true;
            });
        } else {
            $this->successUpload = false;
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an object with which you can get the data
     * of the loaded file at the oop level
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $fileData
     *
     * @return UploadDataInterface
     */
    #[Pure]
    private function getFileData(array $fileData): UploadDataInterface
    {

        return new UploadFileData($fileData);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Iterate through all uploaded files and call an additional
     * handler as callback, this callback should return boolean
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $handler
     *
     * @return MakeUploadInterface
     */
    private function iterationFilesData(callable $handler): MakeUploadInterface
    {

        foreach ($this->filesData as $index => $fileData) {
            $this->successUpload = call_user_func($handler, $this->getFileData($fileData), $this->uploader, $index);
        }

        return $this;

    }

}