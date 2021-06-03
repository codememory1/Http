<?php

namespace Codememory\HttpFoundation\Client\Session\Flash;

use Codememory\HttpFoundation\Interfaces\FlashInterface;
use Codememory\HttpFoundation\Interfaces\RequestInterface;
use Codememory\Support\Arr;

/**
 * Class Flash
 * @package Codememory\HttpFoundation\Client\Session\Flash
 *
 * @author  Codememory
 */
class Flash implements FlashInterface
{

    private const FLASH_SESSION_NAME = '__cdm-flushes';

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var array
     */
    private array $flushes = [];

    /**
     * Flash constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {

        $this->request = $request;

    }

    /**
     * @inheritDoc
     */
    public function create(string $type, string $message): FlashInterface
    {

        $this->flushes[$type] = $message;

        $this->request->session->set(self::FLASH_SESSION_NAME, $this->flushes);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function get(string $type): string|bool
    {

        if ($this->has($type)) {
            return $this->all()[$type];
        }

        return false;

    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {

        $flushes = $this->request->session->get(self::FLASH_SESSION_NAME) ?: [];

        $this->request->session->remove(self::FLASH_SESSION_NAME);

        return $flushes;

    }

    /**
     * @inheritDoc
     */
    public function has(string $type): bool
    {

        return Arr::exists($this->all(), $type);

    }

    /**
     * @inheritDoc
     */
    public function getTypes(): array
    {

        return array_keys($this->all());

    }

}