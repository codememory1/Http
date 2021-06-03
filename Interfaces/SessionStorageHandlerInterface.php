<?php

namespace Codememory\HttpFoundation\Interfaces;

use Codememory\HttpFoundation\Client\Session\Utils;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

/**
 * Interface SessionStorageHandlerInterface
 * @package Codememory\HttpFoundation\Interfaces
 *
 * @author  Codememory
 */
interface SessionStorageHandlerInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Install session configuration utilities
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param Utils $utils
     *
     * @return void
     */
    public function setUtils(Utils $utils): void;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the session save handler object
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return SessionStorageInterface
     */
    public function storage(): SessionStorageInterface;

}