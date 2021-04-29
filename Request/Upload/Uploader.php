<?php

namespace Codememory\Http\Request\Upload;

use Codememory\Http\Request\Request;
use Codememory\Http\Request\Upload\Exceptions\InvalidInputNameException;
use Codememory\Http\Request\Upload\Handler as UploadHandler;

/**
 * ========= ANY VALUE `*` =========
 *
 * Class Uploader
 * @package System\Http\Request\Upload
 *
 * @author  Codememory
 */
class Uploader extends UploadHandler
{

    public const UNIT_B = 'b';
    public const UNIT_MB = 'mb';
    public const UNIT_KB = 'kb';
    public const UNIT_GB = 'gb';

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var string|null
     */
    protected ?string $input = null;

    /**
     * @var int|array|string
     */
    private int|array|string $sizes = [
        'min' => '*',
        'max' => '*'
    ];

    /**
     * @var array|string
     */
    private array|string $types = '*';

    /**
     * @var array|string
     */
    private array|string $mimeTypes = '*';

    /**
     * @var array
     */
    private array $image = [
        'width'     => '*',
        'height'    => '*',
        'onlyImage' => false
    ];

    /**
     * @var int[]
     */
    private array $upload = [
        'min' => 1,
        'max' => '*'
    ];

    /**
     * @var string
     */
    private string $unit = self::UNIT_B;

    /**
     * @var string|null
     */
    private ?string $savePath = null;

    /**
     * Uploader constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        $this->request = $request;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set the name of the input from which the files are loaded
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return $this
     */
    public function setInputName(string $name): Uploader
    {

        $this->input = $name;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set uploaded sizes
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|int $minSizes minSize or `*` - any
     * @param string|int $maxSizes maxSize or `*` - any
     * @param string     $unit     Unit of reconciliation of size
     *
     * @return $this
     */
    public function size(string|int $minSizes, string|int $maxSizes, string $unit = 'b'): Uploader
    {

        $this->sizes['min'] = $minSizes === '*' ? '*' : $minSizes;
        $this->sizes['max'] = $maxSizes === '*' ? '*' : $maxSizes;
        $this->unit = $unit;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Add 1 or more allowed file extensions
     *
     * Example: [png, jpg]
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|array $types expansion uploaded file or `*` - any
     *
     * @return $this
     */
    public function expansion(string|array $types): Uploader
    {

        $this->types = $types;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Add MimeType for uploads
     *
     * Example: [image/png, image/jpg]
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|array $mimes
     *
     * @return $this
     */
    public function mimeType(string|array $mimes): Uploader
    {

        $this->mimeTypes = $mimes;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Upload image settings set max width, height and 3 argument,
     * & say uploaded files should be images
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int|string $width     Max width or `*` - any
     * @param int|string $height    Max height or `*` - any
     * @param bool       $onlyImage Upload images only
     *
     * @return $this
     */
    public function image(int|string $width = '*', int|string $height = '*', bool $onlyImage = false): Uploader
    {

        $this->image = [
            'width'     => $width !== '*' ? (int) $width : $width,
            'height'    => $height !== '*' ? (int) $height : $height,
            'onlyImage' => $onlyImage
        ];

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Settings number of downloaded files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int|string $min Min number upload
     * @param int|string $max Max number upload
     *
     * @return $this
     */
    public function numberUpload(int|string $min, int|string $max): Uploader
    {

        $this->upload = [
            'min' => $min,
            'max' => $max
        ];

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set path where files will be saved
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     *
     * @return Uploader
     */
    public function store(string $path): Uploader
    {

        $this->savePath = $path;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set all properties to default after loading
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Uploader
     */
    private function reset(): Uploader
    {

        $this->input = null;
        $this->sizes = '*';
        $this->types = '*';
        $this->mimeTypes = '*';
        $this->image = [
            'width'     => '*',
            'height'    => '*',
            'onlyImage' => false
        ];
        $this->upload = [
            'min' => 1,
            'max' => 1
        ];
        $this->unit = self::UNIT_B;

        return $this;

    }

    /**
     * @return BootParameters
     */
    protected function collectInformation(): BootParameters
    {

        $data = [
            'types'          => [
                'mime'      => $this->mimeTypes,
                'expansion' => $this->types
            ],
            'sizes'          => array_merge($this->sizes, ['unit' => $this->unit]),
            'image'          => $this->image,
            'numberUploaded' => $this->upload
        ];

        return new BootParameters($data);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>
     * & Make file upload
     * <=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable|null $callback
     *
     * @return $this
     * @throws InvalidInputNameException
     */
    public function make(null|callable $callback = null): Uploader
    {

        $currentDebug = debug_backtrace()[0];

        if (null === $this->input) {
            throw new InvalidInputNameException($currentDebug['file'], $currentDebug['line']);
        }

        $this->upload($this->savePath, $callback);
        $this->reset();

        return $this;

    }

}