<?php

namespace Codememory\HttpFoundation\Response;

use Codememory\FileSystem\File;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\HttpFoundation\Client\Header\Header;
use Codememory\HttpFoundation\Exceptions\DownloadableResourceNotFoundException;
use Codememory\Support\Arr;

/**
 * Class Download
 * @package System\Http\Response
 *
 * @author  Codememory
 */
class Download
{

    /**
     * @var Header
     */
    private Header $header;

    /**
     * @var FileInterface
     */
    private FileInterface $filesystem;

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
    private array $headers = [
        'Accept-Ranges'       => 'bytes',
        'Content-Length'      => '{size}',
        'Content-Disposition' => 'attachment; filename={filename}',
        'Cache-Control'       => 'no-cache'
    ];

    /**
     * Download constructor.
     */
    public function __construct(Header $header)
    {

        $this->header = $header;
        $this->filesystem = new File();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * This method takes 1 argument, this is the path of what you need to download
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $file
     *
     * @return Download
     */
    public function accept(string $file): Download
    {

        $this->accept = $file;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method is needed to rename the file name and send the
     * file for download with a new name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $newName
     *
     * @return Download
     */
    public function rename(string $newName): Download
    {

        $this->renamedName = $newName;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>
     * Adding caching
     * <=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $value
     *
     * @return Download
     */
    public function cacheControl(string $value): Download
    {

        $this->headers['Cache-Control'] = $value;

        return $this;

    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function addHeaders(array $headers): Download
    {

        $this->headers = array_merge($this->headers, $headers);

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Send file for download
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     * @throws DownloadableResourceNotFoundException
     */
    public function make(): bool
    {

        $this->exist($this->accept);
        $this->assemblyHeaders();

        readfile($this->filesystem->getRealPath($this->accept));

        return true;

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
        }

        return $acceptName;

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

        $header->set($headers)->send();

    }

    /**
     * @param string $accept
     *
     * @throws DownloadableResourceNotFoundException
     */
    private function exist(string $accept): void
    {

        if (!$this->filesystem->exist($this->accept) === true) {
            throw new DownloadableResourceNotFoundException($accept);
        }

    }

}