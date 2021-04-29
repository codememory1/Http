<?php

namespace System\Http\Client\Response;

use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Http\Client\Exceptions\DownloadableResourceNotFoundException;
use Codememory\Http\Client\Header\Header;

/**
 * Class Download
 * @package System\Http\Response
 *
 * @author  Codememory
 */
class Download
{

    /**
     * @var string
     */
    private string $accept;

    /**
     * @var string|null
     */
    private ?string $renamedName = null;

    /**
     * @var array
     */
    private array $headers = [];

    /**
     * @var Header
     */
    private Header $header;

    /**
     * @var FileInterface
     */
    private FileInterface $filesystem;

    /**
     * Download constructor.
     *
     * @param Header        $header
     * @param FileInterface $filesystem
     */
    public function __construct(Header $header, FileInterface $filesystem)
    {

        $this->header = $header;
        $this->filesystem = $filesystem;

        $this->addHeadersDefault();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & This method takes 1 argument, this is the path of what you need to download
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $file
     *
     * @return object
     */
    public function accept(string $file): object
    {

        $this->accept = $file;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method is needed to rename the file name and send the
     * & file for download with a new name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $newName
     *
     * @return object
     */
    public function rename(string $newName): object
    {

        $this->renamedName = $newName;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>
     * & Adding caching
     * <=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $value
     *
     * @return object
     */
    public function cacheControl(string $value): object
    {

        $this->headers['Cache-Control'] = $value;

        return $this;

    }

    /**
     * @return string
     */
    private function getCollectedName(): string
    {

        $acceptExplode = explode('/', $this->accept);
        $acceptName = array_pop($acceptExplode);

        if ($this->renamedName !== null) {
            return $this->renamedName;
        } else {
            return $acceptName;
        }

    }

    private function addHeadersDefault(): void
    {

        $this->headers = [
            'Accept-Ranges'       => 'bytes',
            'Content-Length'      => '{size}',
            'Content-Disposition' => 'attachment; filename={filename}',
            'Cache-Control'       => 'no-cache'
        ];

    }

    /**
     * @return void
     */
    private function assemblyHeaders(): void
    {

        $header = $this->header->setContentType('application/octet-stream');
        $headers = [];
        $replace = [
            '{size}'     => $this->filesystem->info->getSize($this->accept),
            '{filename}' => $this->getCollectedName()
        ];

        foreach ($this->headers as $name => $value) {
            $readyValue = str_replace(array_combine($replace, array_keys($replace)), $replace, $value);
            $headers[$name] = $readyValue;
        }
        $header
            ->set($headers)
            ->send();

        return;

    }

    /**
     * @param string $accept
     *
     * @throws DownloadableResourceNotFoundException
     */
    private function exists(string $accept): void
    {

        if (!$this->filesystem->exist($this->accept) === true) {
            throw new DownloadableResourceNotFoundException($accept);
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Send file for download
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     * @throws DownloadableResourceNotFoundException
     */
    public function make(): bool
    {

        $this->exists($this->accept);
        $this->assemblyHeaders();

        readfile($this->filesystem->getRealPath($this->accept));

        return true;

    }

}