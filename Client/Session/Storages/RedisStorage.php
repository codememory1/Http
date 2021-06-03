<?php

namespace Codememory\HttpFoundation\Client\Session\Storages;

use Codememory\HttpFoundation\Client\Session\Utils;
use Codememory\HttpFoundation\Interfaces\SessionStorageHandlerInterface;
use Redis;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

/**
 * Class RedisStorage
 * @package Codememory\HttpFoundation\Client\Session\Storages
 *
 * @author  Codememory
 */
class RedisStorage implements SessionStorageHandlerInterface
{

    /**
     * @var Utils|null
     */
    private ?Utils $utils = null;

    /**
     * @inheritDoc
     */
    public function setUtils(Utils $utils): void
    {

        $this->utils = $utils;

    }

    /**
     * @inheritDoc
     */
    public function storage(): SessionStorageInterface
    {

        return new NativeSessionStorage([], new RedisSessionHandler($this->connect(), [
            'prefix' => Utils::PREFIX
        ]));

    }

    /**
     * @return Redis
     */
    private function connect(): Redis
    {

        $redis = new Redis();

        $redis->connect(env('redis.host'), env('redis.port'));

        if(null !== env('redis.password')) {
            $redis->auth(env('redis.password'));
        }

        return $redis;

    }

}