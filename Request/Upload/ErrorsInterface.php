<?php

namespace Codememory\Http\Request\Upload;

/**
 * Interface ErrorsInterface
 * @package System\Http\Request\Upload
 *
 * @author  Codememory
 */
interface ErrorsInterface
{

    public const E_EXPANSION = 'expansion';

    public const E_MIME_TYPE = 'mimeType';

    public const E_MIN_SIZE = 'minSize';
    public const E_MAX_SIZE = 'maxSize';

    public const E_MIN_LOAD = 'minLoad';
    public const E_MAX_LOAD = 'maxLoad';

    public const E_ONLY_IMAGE = 'onlyImg';

    public const E_WIDTH_IMAGE = 'widthImg';
    public const E_HEIGHT_IMAGE = 'heightImg';

}