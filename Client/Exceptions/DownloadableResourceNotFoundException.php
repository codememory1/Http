<?php

namespace Codememory\Http\Client\Exceptions;

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
    #[Pure] public function __construct(string $resourceName)
    {

        parent::__construct(sprintf(
            'Unable to download file %s this file does not exist, or the name is incorrect',
            $resourceName
        ));

    }

}