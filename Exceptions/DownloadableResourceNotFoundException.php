<?php

namespace Codememory\HttpFoundation\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class DownloadableResourceNotFoundException
 * @package System\Http\Exceptions
 *
 * @author  Codememory
 */
class DownloadableResourceNotFoundException extends ResponseException
{

    /**
     * DownloadableResourceNotFoundException constructor.
     *
     * @param string $resourceName
     */
    #[Pure]
    public function __construct(string $resourceName)
    {

        parent::__construct(sprintf('Невозможно загрузить файл %s, этот файл не существует.', $resourceName));

    }

}