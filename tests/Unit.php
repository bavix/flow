<?php

namespace Tests;

use Bavix\Flow\Flow;
use Bavix\Helpers\Dir;
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

    protected $lastView;
    protected $folder = 'tmp';

    /**
     * @var Flow
     */
    protected $flow;

    protected function configure()
    {
        return [
            'folders' => [
                $this->folder => sys_get_temp_dir()
            ]
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->flow = new Flow(null, $this->configure());
    }

    /**
     * @param $code
     *
     * @return bool|string
     */
    protected function path($code)
    {
        $this->lastView = $view = 'flow__' . Str::random();
        $tmp = \sys_get_temp_dir() . '/' . $this->lastView . $this->flow->ext();
        File::put($tmp, $code);

        // cleanup
        register_shutdown_function(function () use ($view, $tmp) {
            @File::remove(\sys_get_temp_dir() . '/' . $this->folder . '/' . $view . '.php');
            @File::remove($tmp);
            @Dir::remove(\sys_get_temp_dir() . '/' . $this->folder);
        });
        // /cleanup

        return $this->folder . ':' . $this->lastView;
    }

    /**
     * @param string $code
     * @param array $data
     * @param Flow $flow
     *
     * @return string
     */
    protected function eval($code, array $data = [], $flow = null)
    {
        return ($flow ?? $this->flow)->render(
            $this->path($code),
            $data
        );
    }

}
