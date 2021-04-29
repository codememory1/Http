<?php

namespace Codememory\Http\Request\Upload\Traits;

use Codememory\Http\Request\Upload\ErrorsInterface;

/**
 * Trait ImageTrait
 * @package System\Http\Request\Upload\Traits
 *
 * @author  Codememory
 */
trait ImageTrait
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Allow file uploads for images only
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $image
     *
     * @return ImageTrait
     */
    private function onlyImage(array $image): ImageTrait
    {

        if ($this->getInfo('image.onlyImage') && ($image === [])) {
            $this->setError(ErrorsInterface::E_ONLY_IMAGE);
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set maximum resolution width for all uploaded images or `*` - any
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|int $width
     * @param array      $image
     *
     * @return ImageTrait
     */
    private function width(string|int $width, array $image): ImageTrait
    {

        if (!$this->isAnyValue($width) && ($image['width'] > $width)) {
            $this->setError(ErrorsInterface::E_WIDTH_IMAGE);
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set maximum resolution height for all uploaded images or `*` - any
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|int $height
     * @param array      $image
     *
     * @return ImageTrait
     */
    private function height(string|int $height, array $image): ImageTrait
    {

        if (!$this->isAnyValue($height) && ($image['height'] > $height)) {
            $this->setError(ErrorsInterface::E_WIDTH_IMAGE);
        }

        return $this;

    }

    /**
     * @param array $image
     *
     * @return ImageTrait
     */
    private function image(array $image): ImageTrait
    {

        $width = $this->getInfo('image.width');
        $height = $this->getInfo('image.height');

        if ($image !== []) {
            $this->width($width, $image)->height($height, $image);
        }

        return $this;

    }

}