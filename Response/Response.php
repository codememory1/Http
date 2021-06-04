<?php

namespace Codememory\HttpFoundation\Response;

use Codememory\Components\JsonParser\Exceptions\JsonErrorException;
use Codememory\Components\JsonParser\JsonParser;
use Codememory\HttpFoundation\Client\Header\Header;
use Codememory\HttpFoundation\Exceptions\DownloadableResourceNotFoundException;
use Codememory\HttpFoundation\Exceptions\HeaderNotFoundInSubException;
use Codememory\HttpFoundation\Interfaces\ResponseInterface;

/**
 * Class Response
 * @package Codememory\HttpFoundation\Response
 *
 * @author  Codememory
 */
class Response implements ResponseInterface
{

    /**
     * @var Download
     */
    protected Download $download;

    /**
     * @var Header
     */
    protected Header $header;

    /**
     * @var int
     */
    private int $responseCode = 200;

    /**
     * @var string|null
     */
    private ?string $content = null;

    /**
     * @var array
     */
    private array $headers = [];

    /**
     * Response constructor.
     *
     * @param Header $header
     */
    public function __construct(Header $header)
    {

        $this->header = $header;
        $this->download = new Download($header);

    }

    /**
     * @inheritDoc
     */
    public function create(?string $content = null, int $responseCode = 200, array $headers = []): ResponseInterface
    {

        $this
            ->setContent($content)
            ->setResponseCode($responseCode)
            ->setHeaders($headers);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function setContent(?string $content = null): ResponseInterface
    {

        $this->content = $content;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function getContent(): ?string
    {

        return $this->content;

    }

    /**
     * @inheritDoc
     */
    public function sendContent(): ResponseInterface
    {

        echo $this->content;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function setHeaders(array $headers): ResponseInterface
    {

        $this->headers = $headers;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function setResponseCode(int $code): ResponseInterface
    {

        $this->responseCode = $code;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function getResponseCode(): int
    {

        return $this->header->getHttpStatus();

    }

    /**
     * @inheritDoc
     */
    public function sendHeaders(): ResponseInterface
    {

        $this->header
            ->setResponseCode($this->responseCode)
            ->set($this->headers)
            ->send();

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function sendContentAndHeaders(): ResponseInterface
    {

        $this->sendContent()->sendHeaders();

        return $this;

    }

    /**
     * @inheritDoc
     * @throws JsonErrorException
     */
    public function json(mixed $data, int $responseCode = 200, array $headers = []): ResponseInterface
    {

        $jsonParser = new JsonParser();
        $dataInJson = $jsonParser->setData($data)->encode();

        $this->setContentType('application/json');

        $this
            ->create($dataInJson, $responseCode, $headers)
            ->sendContentAndHeaders();

        return $this;

    }

    /**
     * @inheritDoc
     * @throws DownloadableResourceNotFoundException
     */
    public function giveForDownload(string $file, ?string $rename = null, array $headers = []): ResponseInterface
    {

        $download = $this->download->accept($file);

        if (null !== $download) {
            $download->rename($rename);
        }

        $download->addHeaders($headers)->make();

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function setContentType(string $type): ResponseInterface
    {

        $this->header->setContentType($type)->send();

        return $this;

    }

    /**
     * @inheritDoc
     * @throws HeaderNotFoundInSubException
     */
    public function setCharset(string $charset): ResponseInterface
    {

        $this->header->setCharset($charset)->send();

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function isContinue(): bool
    {

        return 100 === $this->getResponseCode();

    }

    /**
     * @inheritDoc
     */
    public function isOk(): bool
    {

        return 200 === $this->getResponseCode();

    }

    /**
     * @inheritDoc
     */
    public function isRedirect(): bool
    {

        return in_array($this->getResponseCode(), [301, 302]);

    }

    /**
     * @inheritDoc
     */
    public function isForbidden(): bool
    {

        return 403 === $this->getResponseCode();

    }

    /**
     * @inheritDoc
     */
    public function isNotFound(): bool
    {

        return 404 === $this->getResponseCode();

    }

    /**
     * @inheritDoc
     */
    public function isInformational(): bool
    {

        return $this->getResponseCode() >= 100 && $this->getResponseCode() < 200;

    }

    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {

        return $this->getResponseCode() >= 200 && $this->getResponseCode() < 300;

    }

    /**
     * @inheritDoc
     */
    public function isRedirection(): bool
    {

        return $this->getResponseCode() >= 300 && $this->getResponseCode() < 400;

    }

    /**
     * @inheritDoc
     */
    public function isClientError(): bool
    {

        return $this->getResponseCode() >= 400 && $this->getResponseCode() < 500;

    }

    /**
     * @inheritDoc
     */
    public function isServerError(): bool
    {

        return $this->getResponseCode() >= 500;

    }

}