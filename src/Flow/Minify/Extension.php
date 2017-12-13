<?php

namespace Bavix\Flow\Minify;

abstract class Extension
{

    /**
     * @var string
     */
    protected $buffer;

    /**
     * Extension constructor.
     *
     * @param string $buffer
     */
    public function __construct(string $buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * @param string $replace
     *
     * @return string
     */
    protected function pattern(string $replace): string
    {
        return '~' . \preg_quote($replace, '~') . '~ui';
    }

    /**
     * @param string $replace
     * @param string $replacement
     */
    protected function replace(string $replace, string $replacement)
    {
        $this->buffer = \preg_replace(
            $this->pattern($replace),
            $replacement,
            $this->buffer
        );
    }

    /**
     * @return void
     */
    public function apply()
    {
        foreach ($this->patterns() as $replace => $replacement)
        {
            $this->replace($replace, $replacement);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->buffer;
    }

    /**
     * @return array
     */
    abstract protected function patterns(): array;

}
