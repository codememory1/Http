<?php

namespace Codememory\HttpFoundation\Request;

use Codememory\Support\Arr;
use Codememory\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Files
 * @package Codememory\HttpFoundation\Request
 *
 * @author  Codememory
 */
class Files
{

    /**
     * @var array
     */
    private array $files;

    /**
     * Files constructor.
     *
     * @param array $files
     */
    public function __construct(array $files)
    {

        $this->files = $files;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of uploaded file names
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getNames(): array
    {

        $names = $this->getDataByKey('name');

        return Arr::map($names, function (mixed $key, string $names) {
            return [Str::trimAfterSymbol($names, '.')];
        });

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of uploaded file mimetypes
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getTypes(): array
    {

        return $this->getDataByKey('type');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of uploaded file tmp names
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getTmpNames(): array
    {

        return $this->getDataByKey('tmp_name');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of uploaded file extensions
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getExtensions(): array
    {

        $names = $this->getDataByKey('name');

        return Arr::map($names, function (mixed $key, mixed $value) {
            return [Str::trimToSymbol($value, '.')];
        });

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of uploaded file sizes in byte
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getSizes(): array
    {

        return $this->getDataByKey('size');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of downloadable files with an expanded list of data
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array[]
     */
    public function getFilesData(): array
    {

        $filesData = [];

        $i = 0;
        while ($i < count($this->getNames())) {
            $fileData = [];

            foreach ($this->getFileDataStructure() as $key => $value) {
                $fileData[$key] = $value[$i];
            }

            $filesData[] = $fileData;

            ++$i;
        }

        return $filesData;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the data structure of the loaded file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return \array[][]
     */
    #[ArrayShape(['name' => "array", 'type' => "array", 'tmp_name' => "array", 'size' => "array", 'extension' => "array"])]
    private function getFileDataStructure(): array
    {

        return [
            'name'      => $this->getNames(),
            'type'      => $this->getTypes(),
            'tmp_name'  => $this->getTmpNames(),
            'size'      => $this->getSizes(),
            'extension' => $this->getExtensions()
        ];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of data by key from $_FILES
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     *
     * @return array
     */
    private function getDataByKey(string $key): array
    {

        if (is_array($this->files[$key])) {
            return $this->files[$key];
        }

        return [$this->files[$key]];

    }

}