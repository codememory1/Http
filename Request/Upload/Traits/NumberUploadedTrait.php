<?php

namespace Codememory\Http\Request\Upload\Traits;

use Codememory\Http\Request\Upload\ErrorsInterface;

/**
 * Trait NumberUploadedTrait
 * @package System\Http\Request\Upload\Traits
 *
 * @author Codememory
 */
trait NumberUploadedTrait
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Minimum number of files to upload
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|int $min
     *
     * @return NumberUploadedTrait
     */
    private function minUpload(string|int $min): NumberUploadedTrait
    {

        if ($this->isAnyValue($min) === false) {
            if(count($this->getFiles()) < $min) {
                $this->setError(ErrorsInterface::E_MIN_LOAD);
            }
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Maximum number of files to upload
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|int $max
     *
     * @return NumberUploadedTrait
     */
    private function maxUpload(string|int $max): NumberUploadedTrait
    {

        if ($this->isAnyValue($max) === false) {
            if(count($this->getFiles()) > $max) {
                $this->setError(ErrorsInterface::E_MAX_LOAD);
            }
        }

        return $this;

    }

    /**
     * @return NumberUploadedTrait
     */
    private function numUploaded(): NumberUploadedTrait
    {

        $this
            ->minUpload($this->getInfo('numberUploaded.min'))
            ->maxUpload($this->getInfo('numberUploaded.max'));

        return $this;

    }

}