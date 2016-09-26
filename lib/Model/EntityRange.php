<?php namespace Prezly\DraftPhp\Model;

/**
 * @property int $key
 * @property int $offset
 * @property int $length
 */
class EntityRange
{
    /** @var int */
    private $_key;
    /** @var int */
    private $_offset;
    /** @var int */
    private $_length;

    public function __construct(int $key, int $offset, int $length)
    {
        $this->_key = $key;
        $this->_offset = $offset;
        $this->_length = $length;
    }

    function __get($name)
    {
        // public read-only access to private properties
        return $this->{'_' . $name};
    }
}