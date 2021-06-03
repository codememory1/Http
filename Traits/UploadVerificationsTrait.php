<?php

namespace Codememory\HttpFoundation\Traits;

use Codememory\Components\UnitConversion\Conversion;
use Codememory\Components\UnitConversion\Units\FromBytes;
use Codememory\HttpFoundation\FileUploader\Uploader;
use Codememory\HttpFoundation\Interfaces\FileUploadErrorInterface;
use Codememory\HttpFoundation\Interfaces\UploadDataInterface;

/**
 * Trait UploadVerificationsTrait
 * @package Codememory\HttpFoundation\Traits
 *
 * @author  Codememory
 */
trait UploadVerificationsTrait
{

    /**
     * @return UploadVerificationsTrait
     */
    private function nameVerification(): static
    {

        $this->iterationFilesData(function (UploadDataInterface $uploadData, Uploader $uploader) {
            if ('*' !== $uploader->regexOfNames) {
                if (!preg_match($uploader->regexOfNames, $uploadData->getName())) {
                    $this->addError(
                        FileUploadErrorInterface::NAME_BY_REGEX,
                        sprintf('The name of the uploaded files does not match the regex %s', $uploader->regexOfNames)
                    );

                    return false;
                }
            }

            return true;
        });

        return $this;

    }

    /**
     * @return UploadVerificationsTrait
     */
    private function mimeTypeVerification(): static
    {

        $this->iterationFilesData(function (UploadDataInterface $uploadData, Uploader $uploader) {
            if ('*' !== $uploader->mimeTypes) {
                if (!in_array($uploadData->getMime(), $uploader->mimeTypes)) {
                    $this->addError(
                        FileUploadErrorInterface::MIME,
                        sprintf('The type of uploaded files must be [%s]', implode(',', $uploader->mimeTypes))
                    );

                    return false;
                }
            }

            return true;
        });

        return $this;

    }

    /**
     * @return $this
     */
    private function extensionVerification(): static
    {

        $this->iterationFilesData(function (UploadDataInterface $uploadData, Uploader $uploader) {
            if ('*' !== $uploader->extensions) {
                if (!in_array($uploadData->getExtension(), $uploader->extensions)) {
                    $this->addError(
                        FileUploadErrorInterface::EXTENSION,
                        sprintf('The extension of the downloaded files must be [%s]', implode(',', $uploader->extensions))
                    );

                    return false;
                }
            }

            return true;
        });

        return $this;

    }

    /**
     * @return $this
     */
    private function disallowMimeTypeVerification(): static
    {

        $this->iterationFilesData(function (UploadDataInterface $uploadData, Uploader $uploader) {
            if ([] !== $uploader->disallowMimeTypes) {
                if (in_array($uploadData->getMime(), $uploader->disallowMimeTypes)) {
                    $this->addError(
                        FileUploadErrorInterface::DIS_MIME,
                        sprintf('The type of uploaded files must not be of types [%s]', implode(',', $uploader->disallowMimeTypes))
                    );

                    return false;
                }
            }

            return true;
        });

        return $this;

    }

    /**
     * @return $this
     */
    private function disallowExtensionVerification(): static
    {

        $this->iterationFilesData(function (UploadDataInterface $uploadData, Uploader $uploader) {
            if ([] !== $uploader->disallowExtensions) {
                if (in_array($uploadData->getExtension(), $uploader->disallowExtensions)) {
                    $this->addError(
                        FileUploadErrorInterface::DIS_EXTENSION,
                        sprintf('The extension of uploaded files must not be of extensions [%s]', implode(',', $uploader->disallowExtensions))
                    );

                    return false;
                }
            }

            return true;
        });

        return $this;

    }

    /**
     * @return $this
     */
    private function sizeVerification(): static
    {

        $conversion = new Conversion();

        $this->iterationFilesData(function (UploadDataInterface $uploadData, Uploader $uploader) use ($conversion) {
            $size = $conversion
                ->setConvertibleNumber($uploadData->getSize())
                ->from(new FromBytes())
                ->get($uploader->size['unit']);

            if ('*' !== $uploader->size['min']) {
                if ($size < $uploader->size['min']) {

                    $this->addError(
                        FileUploadErrorInterface::SIZE,
                        sprintf('The minimum download size must be %s%s', $uploader->size['min'], $conversion->getCurrentUnit())
                    );

                    return false;
                }
            }

            if ('*' !== $uploader->size['max']) {
                if ($size > $uploader->size['max']) {
                    $this->addError(
                        FileUploadErrorInterface::SIZE,
                        sprintf('The maximum download size must be %s%s', $uploader->size['max'], $conversion->getCurrentUnit())
                    );

                    return false;
                }
            }

            return true;
        });

        return $this;

    }

    /**
     * @return $this
     */
    private function numberUploadsVerification(): static
    {

        $this->iterationFilesData(function (UploadDataInterface $uploadData, Uploader $uploader) {
            if (count($this->filesData) < $uploader->numberUploads['min']) {
                $this->addError(
                    FileUploadErrorInterface::NUM_UPLOADS,
                    sprintf('The minimum number of downloadable files should be %s', $uploader->numberUploads['min'])
                );

                return false;
            }

            if ('*' !== $uploader->numberUploads['max'] && count($this->filesData) > $uploader->numberUploads['max']) {
                $this->addError(
                    FileUploadErrorInterface::NUM_UPLOADS,
                    sprintf('The maximum number of downloadable files should be %s', $uploader->numberUploads['max'])
                );

                return false;
            }

            return true;
        });

        return $this;

    }

}