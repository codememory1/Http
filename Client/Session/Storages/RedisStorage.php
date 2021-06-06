<?php

namespace Codememory\HttpFoundation\Client\Session\Storages;

use Codememory\Database\Redis\Connections\Connection;
use Codememory\Database\Redis\Exceptions\IncorrectRedisHostOrPortException;
use Codememory\Database\Redis\Exceptions\IncorrectRedisPasswordOrUsernameException;
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
     * @throws IncorrectRedisHostOrPortException
     * @throws IncorrectRedisPasswordOrUsernameException
     */
    public function storage(): SessionStorageInterface
    {

        $redisConnection = new Connection(new Redis());

        return new NativeSessionStorage([], new RedisSessionHandler($redisConnection->makeConnection(), [
            'prefix' => Utils::PREFIX
        ]));

    }

}