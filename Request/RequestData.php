<?php

namespace Codememory\HttpFoundation\Request;

use Codememory\Support\Arr;

/**
 * Class RequestData
 * @package Codememory\HttpFoundation\Request
 *
 * @author  Codememory
 */
class RequestData
{

    /**
     * @var array
     */
    private array $data;

    /**
     * RequestData constructor.
     *
     * @param array $data
     */
    public function __construct(array &$data)
    {

        $this->data = &$data;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add a new element to the data array and using 3 arguments,
     * you can specify the check for the existence of an element
     * with the given key
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     * @param mixed  $value
     * @param bool   $checkExistKey
     *
     * @return bool
     */
    public function set(string $key, mixed $value, bool $checkExistKey = false): bool
    {

        if ($checkExistKey) {
            if (!array_key_exists($key, $this->data)) {
                $this->data[$key] = $value;

                return true;
            }

            return false;
        }

        $this->data[$key] = $value;

        return true;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns data by key if missing, null will be returned
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed
    {

        return Arr::set($this->data)::get($key);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the entire dataset
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function all(): array
    {

        return $this->data;

    }

}