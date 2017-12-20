<?php

namespace Tests;

use Bavix\Helpers\File;
use Bavix\Helpers\Str;

/**
 * Class Unit
 *
 * @package Tests
 *
 * @codeCoverageIgnore
 */
class Unit extends \Bavix\Tests\Unit
{

    /**
     * @var string
     */
    protected $ext = 'bxf';

    /**
     * @param $code
     *
     * @return bool|string
     */
    protected function path($code)
    {
        $tmp = \sys_get_temp_dir() . '/flow__' . Str::random() . $this->ext;
        File::put($tmp, $code);
        return $tmp;
    }

}
