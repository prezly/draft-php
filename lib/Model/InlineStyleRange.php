<?php

namespace Prezly\DraftPhp\Model;

/**
 * @property string $style
 * @property int $offset
 * @property int $length
 */
class InlineStyleRange
{
    /** @var string */
    private $_style;
    /** @var int */
    private $_offset;
    /** @var int */
    private $_length;

    public function __construct(string $style, int $offset, int $length)
    {
        $this->_style = $style;
        $this->_offset = $offset;
        $this->_length = $length;
    }

    public function __get($name)
    {
        // public read-only access to private properties
        return $this->{'_' . $name};
    }
}
