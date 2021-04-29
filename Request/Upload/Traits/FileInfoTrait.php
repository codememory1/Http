<?php

namespace Codememory\Http\Request\Upload\Traits;

use Codememory\Http\Request\Upload\ErrorsInterface;

/**
 * Trait FileInfoTrait
 * @package System\Http\Request\Upload\Traits
 *
 * @author  Codememory
 */
trait FileInfoTrait
{

    /**
     * @param string|int $size
     * @param int        $uploadSize
     *
     * @return FileInfoTrait
     */
    private function minSize(string|int $size, int $uploadSize): FileInfoTrait
    {

        if (!$this->isAnyValue($size)) {
            if ($uploadSize < $size) {
                $this->setError(ErrorsInterface::E_MIN_SIZE);
            }
        }

        return $this;

    }

    /**
     * @param string|int $size
     * @param int        $uploadSize
     *
     * @return FileInfoTrait
     */
    private function maxSize(string|int $size, int $uploadSize): FileInfoTrait
    {

        if (!$this->isAnyValue($size)) {
            if ($size > $uploadSize) {
                $this->setError(ErrorsInterface::E_MAX_SIZE);
            }
        }

        return $this;

    }

    /**
     * @param callable $callback
     *
     * @return FileInfoTrait
     */
    public function names(callable $callback): FileInfoTrait
    {

        $this->other['names'] = $callback;

        return $this;

    }

    /**
     * @param string $expansion
     *
     * @return FileInfoTrait
     */
    private function handlerExpansion(string $expansion): FileInfoTrait
    {

        $exp = $this->getInfo('types.expansion');

        if (!$this->isAnyValue($exp)) {
            if (!in_array($expansion, $this->getExpansion())) {
                $this->setError(ErrorsInterface::E_EXPANSION);
            }
        }

        return $this;

    }

    /**
     * @param string $uploadMime
     *
     * @return FileInfoTrait
     */
    private function handlerMime(string $uploadMime): FileInfoTrait
    {

        $mime = $this->getInfo('types.mime');

        if (!$this->isAnyValue($mime)) {
            if (!in_array($uploadMime, $this->getMimeType())) {
                $this->setError(ErrorsInterface::E_MIME_TYPE);
            }
        }

        return $this;

    }

    /**
     * @param int $uploadSize
     *
     * @return FileInfoTrait
     */
    private function sizeHandler(int $uploadSize): FileInfoTrait
    {

        $minSize = $this->getInfo('sizes.min');
        $maxSize = $this->getInfo('sizes.max');

        $this
            ->minSize($minSize, $uploadSize)
            ->maxSize($maxSize, $uploadSize);

        return $this;

    }

}