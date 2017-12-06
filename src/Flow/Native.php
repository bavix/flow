<?php

namespace Bavix\Flow;

use Bavix\FlowNative\FlowNative;

class Native extends FlowNative
{

    /**
     * @var Flow
     */
    protected $flow;

    /**
     * @param Flow $flow
     */
    public function setFlow(Flow $flow)
    {
        $this->flow = $flow;
    }

    public function render($view, array $arguments = [])
    {
        $this->content->mergeData([
            'flow' => $this->flow
        ]);

        return parent::render($view, $arguments);
    }

}
