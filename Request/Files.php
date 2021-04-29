<?php

namespace Codememory\Http\Request;

use Codememory\FileSystem\File;

/**
 * Class Files
 * @package System\Http\Request
 *
 * @author  Codememory
 */
class Files
{

    /**
     * @var string
     */
    private string $input;

    /**
     * @var array
     */
    private array $collectedData = [];

    /**
     * Files constructor.
     *
     * @param string $input
     */
    public function __construct(string $input)
    {

        $this->input = $input;

    }

    /**
     * @return array
     */
    private function files(): array
    {

        return $_FILES[$this->input];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns information if the path is a picture; otherwise it
     * & will return an empty array, which indicates the path is not a picture
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     *
     * @return array
     */
    private function withImage(string $path): array
    {

        $info = getimagesize($path);

        if (is_array($info)) {
            preg_match('/width=\"(?<width>\d+)\"\sheight=\"(?<height>\d+)\"/', $info[3], $match);

            return [
                'width'  => (int) $match['width'],
                'height' => (int) $match['height'],
                'bits'   => $info['bits']
            ];
        }

        return [];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns an array of ready information for all uploaded files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array  $files
     * @param string $fullName
     * @param string $tmpName
     * @param int    $index
     *
     * @return array
     */
    private function readyData(array $files, string $fullName, string $tmpName, int $index): array
    {

        return [
            'name'      => $this->getName($fullName),
            'tmp'       => $tmpName,
            'mime'      => $this->getMimeType($files, $index),
            'expansion' => $this->getExpansion($fullName),
            'type'      => $this->getType($tmpName),
            'size'      => $this->getSize($tmpName),
            'image'     => $this->getImageInfo($tmpName)
        ];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method splits the fully qualified name and returns an array
     * & with the name and extension
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $fullName
     *
     * @return array
     */
    private function splitName(string $fullName): array
    {

        $splitName = explode('.', $fullName);

        return [
            'name'      => $splitName[array_key_first($splitName)],
            'expansion' => $splitName[array_key_last($splitName)],
        ];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns the filename without extension
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $fullName
     *
     * @return string
     */
    private function getName(string $fullName): string
    {

        return $this->splitName($fullName)['name'];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns the extension of the downloaded file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $fullName
     *
     * @return string|null
     */
    private function getExpansion(string $fullName): ?string
    {

        return $this->splitName($fullName)['expansion'];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns an array of complete information
     * & about the loaded file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $tmpName
     *
     * @return array
     */
    private function getAllInfo(string $tmpName): array
    {

        $filesystem = new File();

        return $filesystem->info->getAllInfo(sprintf('*%s', $tmpName));

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns the type of the uploaded file
     *
     * Example: file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $tmpName
     *
     * @return string
     */
    private function getType(string $tmpName): string
    {

        return $this->getAllInfo($tmpName)['type'];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns the mimeType of the uploaded file
     *
     * Example: image/png
     * Example: text/javascript
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $files
     * @param int   $index
     *
     * @return string
     */
    private function getMimeType(array $files, int $index): string
    {

        return $files['type'][$index];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns an array of sizes of the loaded
     * & file in different units.
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $tmpName
     *
     * @return array
     */
    private function getSize(string $tmpName): array
    {

        return $this->getAllInfo($tmpName)['size'];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns information about the uploaded file
     * & if the uploaded file is an image
     *
     * Example:
     *      width  => 500
     *      height => 200,
     *      bits   => 10
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $tmpName
     *
     * @return array
     */
    private function getImageInfo(string $tmpName): array
    {

        return $this->withImage($tmpName);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Build from the old array of uploaded files into a new one and
     * & with all the information of the uploaded file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $files
     *
     * @return array
     */
    private function collect(array $files): array
    {

        $collected = [];

        foreach ($files['name'] as $index => $name) {
            $tmp = $files['tmp_name'][$index];

            if (!empty($tmp)) {
                $collected[] = $this->readyData($files, $name, $tmp, $index);
            }
        }

        return $collected;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Rebuild the array of the loaded file into an associative array,
     * & that is, if the input name is not an array (avatar []), then this
     * & method will work
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $files
     *
     * @return array[]
     */
    private function assemblyWithNormalFormat(array $files): array
    {

        return [
            'name'     => [$files['name']],
            'type'     => [$files['type']],
            'tmp_name' => [$files['tmp_name']]
        ];

    }

    /**
     *
     * Assembly handler
     *
     */
    private function handler(): void
    {

        if (is_array($this->files()['name'])) {
            $this->collectedData = $this->collect($this->files());
        } else {
            $this->collectedData = $this->collect(
                $this->assemblyWithNormalFormat($this->files())
            );
        }

    }

    /**
     * Execute processing
     *
     * @return $this
     */
    private function make(): Files
    {

        $this->handler();

        return $this;

    }

    /**
     * @return bool
     */
    public function missing(): bool
    {

        return isset($_FILES[$this->input]);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns all the collected and ready
     * & information about all uploaded files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getFile(): array
    {

        $this->make();

        return $this->collectedData;

    }

}