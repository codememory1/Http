<?php

namespace Codememory\Http\Request;

use Codememory\Components\JsonParser\JsonParser;
use Codememory\Http\Client\Header\Header;
use Codememory\Support\Str;
use JetBrains\PhpStorm\Pure;
use PHPHtmlParser\Dom;
//use Symfony\Component\HttpFoundation\Session\Session;
use Codememory\Http\Request\Upload\Uploader;
use Codememory\Http\Client\Url;

/**
 * Class Request
 * @package System\Http
 *
 * @author  Codememory
 */
class Request
{

    public const REQUEST_DATA_POST = 1;
    public const REQUEST_DATA_QUERY = 2;
    public const REQUEST_DATA_ALL = 3;

    private const DEFAULT_OLD_METHOD = 'POST';
    private const SESSION_KEY_OLD_DATA = 'old:request';

    /**
     * @var Dom
     */
    public Dom $dom;

    /**
     * @var Parser
     */
    public Parser $parser;

    /**
     * @var Uploader
     */
    public Uploader $upload;

    /**
     * @var mixed|Session
     */
//    public Session $session;

    /**
     * @var mixed|CdmToken
     */
    public CdmToken $cdmToken;

    /**
     * @var int
     */
    private int $selectedType = self::REQUEST_DATA_ALL;

    /**
     * @var array
     */
    private array $booleans = ['1', 1, true, 'true', 'yes', 'on', 'enabled'];

    /**
     * Request constructor.
     */
    public function __construct()
    {

        $this->initializer();

    }

    private function initializer(): void
    {

        $this->dom = new Dom();
        $this->parser = new Parser($this->dom);
        $this->upload = new Uploader($this);
//        $this->session = new Session();
        $this->cdmToken = new CdmToken($this, new Header(), new JsonParser());

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set the type of selection given when calling the `all()` method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int $type
     *
     * @return $this
     */
    public function selectType(int $type): Request
    {

        $this->selectedType = $type;

        return $this;

    }

    /**
     * @param array      $data
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    #[Pure] private function retrieval(array $data, string $key, mixed $default = null): mixed
    {

        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        return $default;

    }

    /**
     * @param array             $data
     * @param string|array|null $keys
     * @param mixed|null        $default
     *
     * @return mixed
     */
    private function dataRetrieval(array $data, string|null|array $keys = null, mixed $default = null): mixed
    {

        if (null === $keys) {
            return $data;
        } else if (is_array($keys)) {
            $updatedData = [];

            foreach ($keys as $key => $keyName) {
                $updatedData[$keyName] = $data[$keyName];
            }

            return $updatedData;
        } else {
            return $this->keyCall($keys, $data);
        }

    }

    /**
     * @param string $keys
     * @param array  $data
     *
     * @return mixed
     */
    private function keyCall(string $keys, array $data): mixed
    {

        $splitKeyString = explode('.', $keys);

        foreach ($splitKeyString as $key) {
            if (array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return null;
            }
        }

        return $data;

    }

    /**
     * @param array $arrayData
     * @param mixed ...$args
     *
     * @return array|string|null
     */
    #[Pure] private function getBodyByKeys(array $arrayData, ...$args): array|string|null
    {

        $argsData = $arrayData;

        foreach ($args as $key) {
            if (array_key_exists($key, $arrayData)) {
                $argsData[$key] = $arrayData[$key];
            }
        }

        return count($args) === 1 ? $arrayData[$args[0]] : $argsData;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get Body response from php://input
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param mixed ...$args
     *
     * @return array|string
     */
    public function getBody(...$args): array|string
    {

        $input = file_get_contents('php://input');
        parse_str($input, $arrayData);

        return $this->getBodyByKeys(array_merge($arrayData, $_POST), ...$args);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get POST data that is sent by the form
     * --------------------------------------------------------------
     * 1 argument key to get
     * 2 - the argument is the default value if value is obtained
     * through the key === null
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $key
     * @param mixed|null  $default
     *
     * @return mixed
     */
    public function post(?string $key = null, mixed $default = null): mixed
    {

        return $this->dataRetrieval($this->getBody(), $key, $default);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get GET parameters that go to the URL via `?`
     * --------------------------------------------------------------
     * 1 argument key to get
     * 2 - the argument is the default value if value is obtained
     * through the key === null
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $key
     * @param mixed|null  $default
     *
     * @return mixed
     */
    public function query(?string $key = null, mixed $default = null): mixed
    {

        return $this->dataRetrieval($_GET, $key, $default);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns an array of multiple superglobal data POST, GET,
     * php://input
     * ----------------------------------------------------------------
     * 1 argument key to get
     * 2 - the argument is the default value if value is obtained
     * through the key === null
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|array|null $key
     * @param mixed|null        $default
     *
     * @return mixed
     */
    public function all(string|null|array $key = null, mixed $default = null): mixed
    {

        $certainData = [];
        $data = match ($this->selectedType) {
            1 => $this->post(),
            2 => $this->query(),
            3 => array_merge($this->post(), $this->query())
        };

        if (is_array($key)) {
            foreach ($key as $keyName) {
                $certainData[$keyName] = $this->dataRetrieval($data, $keyName, $default);
            }
        } else {
            $certainData = $this->dataRetrieval($data, $key, $default);
        }

        return $certainData;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Temporarily save the request data, and exactly until the next page
     * & refresh, where this data will be received. If you call the pure
     * & [ setOldData() ] method before the default data is saved, it is GET and POST
     * & You can also add your own data by specifying 2 arguments
     * & the first argument is the actual key by which it will be possible to call
     * & upon receipt, the second argument is already data(of any type)
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $key
     * @param mixed       $supplement
     *
     * @return $this
     */
    public function setOldData(?string $key = null, mixed $supplement = null): Request
    {

        $data = [
            'GET'  => $this->query(),
            'POST' => array_merge($this->post()),
        ];
        if (null !== $key) {
            $data[$key] = $supplement;
        }

        $this->session->set(self::SESSION_KEY_OLD_DATA, $data);

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Getting temporary data from a request. Not a great example of how this
     * & method works. There are 2 pages On one page there is a form, and on another
     * & page the request method [ setOldData() ] is called; now on the page with the form,
     * & you can call the old method in which the request data is located. By default,
     * & the POST request data is stored there, GET if the form was sent by POST request,
     * & then you can get the data like this [ old(<key|null>, null, 'POST') ]
     * & You can also add temporary pigs. Let's remake a little the setOldData method
     * & on the second page [ setOldData('file', ['name' => 'image']); ]
     * & Now, to get this data, we need to execute the old method in this way
     * & [ old('name', null, 'file'); ]
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|array $key
     * @param mixed        $default
     * @param string       $method
     *
     * @return mixed
     */
    public function old(string|array $key, mixed $default = null, string $method = self::DEFAULT_OLD_METHOD): mixed
    {

        $certainData = $default;
        $data = $this->session->get(self::SESSION_KEY_OLD_DATA) ?? [];

        if ([] !== $data) {
            if (is_array($key)) {
                foreach ($key as $keyName) {
                    $certainData = $this->dataRetrieval($data[$method], $keyName, $default);
                }
            } else {
                $certainData = $this->dataRetrieval($data[$method], $key, $default);
            }
        }

        $data = $certainData;
        $this->session->remove(self::SESSION_KEY_OLD_DATA);

        return $data ?: $default;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get an object of class Files in which information about
     * & the superglobal array $_FILES
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $input
     *
     * @return Files
     */
    #[Pure] public function file(string $input): Files
    {

        return new Files($input);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>
     * & ast to boolean
     * <=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     *
     * @return bool
     */
    public function inBoolean(string $key): bool
    {

        return in_array($this->all($key), $this->booleans);

    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    #[Pure] public function isBoolean(mixed $data): bool
    {

        return in_array($data, $this->booleans);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Check a value from an array for filling
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     *
     * @return bool
     */
    public function isFilled(string $key): bool
    {

        return null === $this->all($key) ||
        empty($this->all($key)) ? false : true;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Check any value from the specified array for filling
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $keys
     *
     * @return bool
     */
    public function anyFilled(array $keys): bool
    {

        foreach ($keys as $key) {
            if ($this->isFilled($key)) {
                return true;
            }
        }

        return false;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Check Any value from an array for empty
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $keys
     *
     * @return bool
     */
    public function anyNotFilled(array $keys): bool
    {

        foreach ($keys as $key) {
            if (!$this->isFilled($key)) {
                return true;
            }
        }

        return false;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Execute active if value is filled
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $key
     * @param callable $callback
     *
     * @return mixed
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     */
    public function whenFiled(string $key, callable $callback): mixed
    {

        if ($this->isFilled($key)) {
            return call_user_func($callback);
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Check the key for existence
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     *
     * @return bool
     */
    public function missing(string $key): bool
    {

        return array_key_exists($key, $this->all());

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns true if at least 1 of the specified keys in the array exists
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $keys
     *
     * @return bool
     */
    public function anyMissing(array $keys): bool
    {

        foreach ($keys as $key) {
            if (!$this->missing($key)) {
                return true;
            }
        }

        return false;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & RequestCallback if the specified key is missing
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $key
     * @param callable $callback
     *
     * @return $this
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     */
    public function whenMissing(string $key, callable $callback): mixed
    {

        if (!$this->missing($key)) {
            return call_user_func($callback);
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Return current URL-path
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    #[Pure] public function path(): string
    {

        return Url::current();

    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function isPathString(string $path): bool
    {

        $path = str_replace(['/'], ['\/'], $path);

        if (preg_match(sprintf('/^%s$/', $path), $this->path())) {
            return true;
        }

        return false;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Check the current URL-path for the one specified in the argument
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|array $path
     *
     * @return bool
     */
    public function isPath(string|array $path): bool
    {

        if (is_string($path)) {
            return $this->isPathString($path);
        } else {
            foreach ($path as $item) {
                if ($this->isPathString($item)) {
                    return true;
                }
            }

            return false;
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Return the current request method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function method(): string
    {

        return Str::toUppercase($_SERVER['REQUEST_METHOD']);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Check the current request method for the method specified in the argument
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $method
     *
     * @return bool
     */
    public function isMethod(string $method): bool
    {

        return strtoupper($method) === $this->method();

    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get(string $property): mixed
    {

        return $this->all($property);

    }

}