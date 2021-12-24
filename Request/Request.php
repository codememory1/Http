<?php

namespace Codememory\HttpFoundation\Request;

use Codememory\Components\JsonParser\Exceptions\JsonErrorException;
use Codememory\Components\JsonParser\JsonParser;
use Codememory\HttpFoundation\Client\Cookie\Cookie;
use Codememory\HttpFoundation\Client\Header\Header;
use Codememory\HttpFoundation\Client\Session\Session;
use Codememory\HttpFoundation\Client\Url;
use Codememory\HttpFoundation\Exceptions\InvalidFileInputNameException;
use Codememory\HttpFoundation\Interfaces\RequestInterface;
use Codememory\Screw\Exceptions\IncorrectReturnInOptionsException;
use Codememory\Screw\Exceptions\InvalidOptionException;
use Codememory\Screw\HttpRequest;
use Codememory\Screw\Options\HeadersOption;
use Codememory\Screw\Options\ParamsOption;
use Codememory\Screw\Response\Response;
use Codememory\Support\Arr;
use Codememory\Support\Str;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request as SFRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Request
 * @package Codememory\HttpFoundation\Request
 *
 * @author  Codememory
 */
class Request implements RequestInterface
{

    private const SESSION_STORAGE_NAME = 'request:storage:%s';

    /**
     * @var Header
     */
    public Header $header;

    /**
     * @var Cookie
     */
    public Cookie $cookie;

    /**
     * @var Server
     */
    public Server $server;

    /**
     * @var Url
     */
    public Url $url;

    /**
     * @var Token
     */
    public Token $cdmToken;

    /**
     * @var SessionInterface
     */
    public SessionInterface $session;

    /**
     * @var SFRequest
     */
    private SFRequest $sfRequest;

    /**
     * @var RequestData|null
     */
    private ?RequestData $requestData = null;

    /**
     * Request constructor.
     * @throws ReflectionException
     */
    public function __construct()
    {

        $this->initialization();

        $this->sfRequest = new SFRequest(server: $this->server->all());

    }

    /**
     * @inheritDoc
     * @throws JsonErrorException
     */
    public function input(null|string $key = null): mixed
    {

        $jsonParser = new JsonParser();
        $inputs = [];
        $query = file_get_contents('php://input');

        if (!empty($query)) {
            if (preg_match('/^(([^=&]+=([^=&]+|))&?)*$/', $query)) {
                $inputs = explode('&', $query);

                Arr::map($inputs, function (int $key, string $value) {
                    return [
                        Str::trimAfterSymbol($value, '='),
                        urldecode(Str::trimToSymbol($value, '='))
                    ];
                });
            } else {
                $inputs = $jsonParser->setData($query)->decode();
            }
        }

        if (null === $key) {
            return $inputs;
        }

        return Arr::set($inputs)::get($key);

    }

    /**
     * @inheritDoc
     * @throws JsonErrorException
     */
    public function post(): RequestInterface
    {

        Arr::merge($_POST, $this->input());

        $this->requestData = new RequestData($_POST);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function query(): RequestInterface
    {

        $this->requestData = new RequestData($_GET);

        return $this;

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function all(): array
    {

        return $this->requestData->all();

    }

    /**
     * @inheritDoc
     * @throws InvalidFileInputNameException
     */
    #[ArrayShape(['name' => "array", 'type' => "array", 'tmp_name' => "array"])]
    public function files(string $inputName): array
    {

        if (!array_key_exists($inputName, $_FILES)) {
            throw new InvalidFileInputNameException($inputName);
        }

        $files = new Files($_FILES[$inputName]);

        return $files->getFilesData();

    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, bool $checkExistKey = false): RequestInterface
    {

        $this->requestData->set($key, $value, $checkExistKey);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null, bool $trim = true, bool $escapingHtml = false): mixed
    {

        $value = Arr::set($this->requestData->all())::get($key) ?: $default;

        if($trim) {
            $value = trim($value);
        }

        if($escapingHtml) {
            $value = htmlspecialchars($value);
        }

        return $value;

    }

    /**
     * @inheritDoc
     */
    public function addToStorage(string $key, mixed $value): RequestInterface
    {

        $sessionName = sprintf(self::SESSION_STORAGE_NAME, $key);

        $this->session->set($sessionName, $value);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function addArrayToStorage(array $data): RequestInterface
    {

        foreach ($data as $key => $value) {
            $this->addToStorage((string) $key, $value);
        }

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function getFromStorage(string $key, bool $removeAfterReturn = true): mixed
    {

        $sessionName = sprintf(self::SESSION_STORAGE_NAME, $key);
        $dataFromStorage = $this->session->get($sessionName);

        if ($removeAfterReturn) {
            $this->removeFromStorage($key);
        }

        return $dataFromStorage;

    }

    /**
     * @inheritDoc
     */
    public function removeFromStorage(string $key): RequestInterface
    {

        $sessionName = sprintf(self::SESSION_STORAGE_NAME, $key);

        $this->session->remove($sessionName);

        return $this;

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getMethod(): string
    {

        return Str::toUppercase($_SERVER['REQUEST_METHOD']);

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function filled(int|string $key): bool
    {

        return !empty($this->requestData->all()[$key] ?? null);

    }

    /**
     * @inheritDoc
     */
    public function missing(string $key): bool
    {

        return !Arr::exists($this->requestData->all(), $key);

    }

    /**
     * @inheritDoc
     */
    public function whenFilled(string $key, callable $action): mixed
    {

        if ($this->filled($key)) {
            return call_user_func($action, $this->requestData->get($key));
        }

        return false;

    }

    /**
     * @inheritDoc
     */
    public function whenMissing(string $key, callable $action): mixed
    {

        if ($this->missing($key)) {
            return call_user_func($action, $this->requestData->get($key));
        }

        return false;

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function isMethod(string $method): bool
    {

        return $this->getMethod() === Str::toUppercase($method);

    }

    /**
     * @inheritDoc
     * @throws IncorrectReturnInOptionsException
     * @throws InvalidOptionException
     * @throws GuzzleException
     */
    public function createRequest(string $url, string $method, array $data = [], array $headers = []): GuzzleResponse
    {

        $request = new HttpRequest();

        $request
            ->setUrl($url)
            ->setMethod($method)
            ->option(HttpRequest::O_HEADERS, function (HeadersOption $headersOption) use ($headers) {
                foreach ($headers as $name => $value) {
                    $headersOption->header($name, $value);
                }

                return $headersOption;
            })
            ->option(HttpRequest::O_PARAMS, function (ParamsOption $paramsOption) use ($data) {
                $paramsOption->form($data);

                return $paramsOption;
            })
            ->send();

        $response = new Response($request);

        return $response->getResponseGuzzle();

    }

    /**
     * @inheritDoc
     */
    public function getHeader(string $key, mixed $default = null): mixed
    {

        if (false === $this->hasHeader($key)) {
            return $default;
        }

        return $this->header->getHeader($key);

    }

    /**
     * @inheritDoc
     */
    public function hasHeader(string $key): bool
    {

        return $this->header->hasHeader($key);

    }

    /**
     * @inheritDoc
     */
    public function getIp(): string
    {

        return $this->sfRequest->getClientIp();

    }

    /**
     * @inheritDoc
     */
    public function isPath(string $path): bool
    {

        $path = preg_quote($this->url->getUrl($path), '/');

        Str::replace($path, '\*', '.*');

        return preg_match(sprintf('/^%s$/', $path), $this->url->getUrl());

    }

    /**
     * @return void
     * @throws ReflectionException
     */
    private function initialization(): void
    {

        $this->session = (new Session)->getSession();
        $this->header = new Header();
        $this->cookie = new Cookie($this->header);
        $this->server = new Server($_SERVER);
        $this->url = new Url();
        $this->cdmToken = new Token($this, $this->header, new JsonParser());

    }

}