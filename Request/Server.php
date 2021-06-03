<?php

namespace Codememory\HttpFoundation\Request;

use Codememory\Support\Arr;

/**
 * Class Server
 * @package Codememory\HttpFoundation\Request
 *
 * @author  Codememory
 */
class Server
{

    /**
     * @var array
     */
    private array $parameters;

    /**
     * Server constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {

        $this->parameters = $parameters;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set new item to $_SERVER super global array
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): Server
    {

        $this->parameters[$key] = $value;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get the value by key from the $_SERVER array if the key does
     * not exist, the $ default argument will be returned
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {

        if (!$this->has($key)) {
            return $default;
        }

        return $this->all()[$key];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of $_SERVER array
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function all(): array
    {

        return $this->parameters;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check the existence of an element with a key in the $_SERVER mass
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {

        return Arr::exists($this->all(), $key);

    }

}