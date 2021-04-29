<?php

namespace Codememory\Http\Request\Upload;

use Codememory\FileSystem\File;
use Codememory\Http\Request\Upload\Exceptions\IncorrectReturnErrorException;
use Codememory\Http\Request\Upload\Exceptions\InvalidErrorTypeException;
use Codememory\Http\Request\Upload\Traits\FileInfoTrait;
use Codememory\Http\Request\Upload\Traits\ImageTrait;
use Codememory\Http\Request\Upload\Traits\NumberUploadedTrait;
use JetBrains\PhpStorm\Pure;

/**
 * Class Handler
 * @package System\Http\Request\Upload
 *
 * @author  Codememory
 */
class Handler
{

    use ImageTrait;
    use FileInfoTrait;
    use NumberUploadedTrait;

    private const EM_EXPANSION = 'Некорректное расширение файла';
    private const EM_MIME_TYPE = 'Некорректный Mime Type';
    private const EM_MIN_SIZE = 'Размер загружаемого файла меньше разрешеного';
    private const EM_MAX_SIZE = 'Размер загружаемого файла больше больше разрешеного';
    private const EM_MIN_LOAD = 'Минимальное кол-во загружамых файлов ниже разрешеного';
    private const EM_MAX_LOAD = 'Максимальное кол-во загружамых файлов больше разрешеного';
    private const EM_ONLY_IMAGE = 'Загружаемые файлы должны быть изображениями';
    private const EM_WIDTH_IMAGE = 'Ширина загуражемого изображения больше разрешеного';
    private const EM_HEIGHT_IMAGE = 'Высота загуражемого изображения меньше разрешеного';

    /**
     * @var array|string[]
     */
    private array $errors = [
        'expansion' => self::EM_EXPANSION,
        'mimeType'  => self::EM_MIME_TYPE,
        'minSize'   => self::EM_MIN_SIZE,
        'maxSize'   => self::EM_MAX_SIZE,
        'minLoad'   => self::EM_MIN_LOAD,
        'maxLoad'   => self::EM_MAX_LOAD,
        'onlyImg'   => self::EM_ONLY_IMAGE,
        'widthImg'  => self::EM_WIDTH_IMAGE,
        'heightImg' => self::EM_HEIGHT_IMAGE
    ];

    /**
     * @var array
     */
    private array $other = [];

    /**
     * @var array
     */
    public array $activeErrors = [];

    /**
     * @var array
     */
    private array $downloadData = [];

    /**
     * @var bool
     */
    private bool $statusTheirTerms = true;

    /**
     * @return array
     */
    private function getFiles(): array
    {

        return $this->request
            ->file($this->input)
            ->getFile();

    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getError(string $type): string
    {

        return $this->errors[$type];

    }

    /**
     * @param string $error
     */
    private function setError(string $error): void
    {

        $this->activeErrors[$error] = $this->getError($error);

    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isAnyValue(mixed $value): bool
    {

        return $value === '*';

    }

    /**
     * @param string $keys
     *
     * @return mixed
     */
    private function getInfo(string $keys): mixed
    {

        $keys = explode('.', $keys);
        $data = $this->collectInformation()->inArray();

        foreach ($keys as $key) {
            $data = $data[$key];
        }

        return $data;

    }

    /**
     * @param string $keys
     *
     * @return string[]
     */
    private function infoInArray(string $keys): array
    {

        $data = $this->getInfo($keys);

        return is_string($data) ? [$data] : $data;

    }

    /**
     * @return string[]
     */
    private function getExpansion(): array
    {

        return $this->infoInArray('types.expansion');

    }

    /**
     * @return string[]
     */
    private function getMimeType(): array
    {

        return $this->infoInArray('types.mime');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Handle the error or change the error text to indicate the type of this error
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $typeError
     * @param callable $callback
     *
     * @return $this
     * @throws IncorrectReturnErrorException
     * @throws InvalidErrorTypeException
     */
    public function error(string $typeError, callable $callback): Handler
    {

        if (!array_key_exists($typeError, $this->errors)) {
            throw new InvalidErrorTypeException($typeError);
        } else {
            $error = call_user_func($callback, $this->collectInformation(), $this);

            if (!is_string($error)) {
                throw new IncorrectReturnErrorException();
            } else {
                $this->activeErrors[$typeError] = $error;
            }
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Add your own handler in which boolean should be returned
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function theirTerms(callable $callback): Handler
    {

        $this->other['theirTerms'] = $callback;

        return $this;

    }

    /**
     * @param array $file
     *
     * @return Handler
     */
    private function assembly(array $file): Handler
    {

        $this->downloadData[] = [
            'name'      => $file['name'],
            'expansion' => $file['expansion'],
            'tmp_name'  => $file['tmp']
        ];

        return $this;

    }

    /**
     * @param array  $file
     * @param string $unit
     */
    private function callHandler(array $file, string $unit): void
    {

        $this
            ->handlerExpansion($file['expansion'])
            ->handlerMime($file['mime'])
            ->sizeHandler($file['size'][$unit]);

        $this->onlyImage($file['image'])->image($file['image']);

    }

    /**
     * @param array $file
     *
     * @return array
     */
    private function callOther(array $file): array
    {

        $bootInfo = new BootInfo($file);

        if (isset($this->other['names'])) {
            $file['name'] = call_user_func($this->other['names'], $bootInfo, $this);
        }
        if (isset($this->other['theirTerms'])) {
            $call = call_user_func($this->other['theirTerms'], $this->collectInformation(), $bootInfo, $this);

            $this->statusTheirTerms = $call === true;
        }

        return $file;

    }

    /**
     * @return Handler
     */
    private function handlerFiles(): Handler
    {

        $unit = $this->collectInformation()->unitSize();

        foreach ($this->getFiles() as $index => $file) {

            $file = $this->callOther($file);

            $this
                ->assembly($file)
                ->callHandler($file, $unit);

        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the first active error
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    #[Pure] public function getActiveError(): ?string
    {

        $key = array_key_first($this->activeErrors);

        if (null !== $key) {
            return $this->activeErrors[$key];
        }

        return null;

    }

    /**
     * @param string|null   $path
     * @param callable|null $callback
     */
    protected function upload(?string $path, null|callable $callback)
    {

        $filesystem = new File();
        $this->handlerFiles();
        $loaded = false;

        if ($this->statusTheirTerms === true && $this->activeErrors === []) {
            foreach ($this->downloadData as $data) {
                $filename = sprintf('%s.%s', $data['name'], $data['expansion']);
                $realPath = $filesystem->getRealPath($path) . '/';

                $loaded = move_uploaded_file($data['tmp_name'], $realPath . $filename);
                $filesystem->setPermission(trim($path, '/') . '/' . $filename);
            }

            if ($loaded && null !== $callback) {
                call_user_func($callback);
            }
        }

    }

}