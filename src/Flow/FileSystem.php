<?php

namespace Bavix\Flow;

use Bavix\Helpers\Dir;
use Bavix\Helpers\File;

class FileSystem
{

    /**
     * @var Flow
     */
    protected $flow;

    /**
     * @var string
     */
    protected $path;

    /**
     * FileSystem constructor.
     *
     * @param Flow   $flow
     * @param string $path
     */
    public function __construct(Flow $flow, string $path)
    {
        $this->flow = $flow;
        $this->path = $path;
    }

    /**
     * @param string $view
     *
     * @return bool
     */
    public function has(string $view): bool
    {
        $path      = $this->get($view);
        $real      = $this->flow->native()->path($view . $this->flow->ext());
        $directory = \dirname($path);

        if ($this->flow->debugMode() || !File::exists($path))
        {
            Dir::make($directory);

            return false;
        }

        return \filemtime($path) > \filemtime($real);
    }

    /**
     * @param string $view
     * @param string $data
     */
    public function set(string $view, string $data)
    {
        \file_put_contents(
            $this->get($view),
            $data
        );
    }

    /**
     * @param string $view
     *
     * @return string
     */
    public function get(string $view): string
    {
        return $this->path . '/' . preg_replace('~\W~', '/', $view) . '.php';
    }

}
