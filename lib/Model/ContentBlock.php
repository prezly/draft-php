<?php

namespace Prezly\DraftPhp\Model;

/**
 * @property string $key
 * @property string $type
 * @property string $text
 * @property CharacterMetadata[] $characterList
 * @property int $depth
 * @property array $data
 */
class ContentBlock
{
    /** @var string */
    private $_key;
    /** @var string */
    private $_type;
    /** @var string */
    private $_text;
    /** @var CharacterMetadata[] */
    private $_characterList;
    /** @var int */
    private $_depth;
    /** @var array */
    private $_data = [];

    public function __construct(string $key, string $type, string $text, array $characterList, int $depth, array $data = [])
    {
        $this->_key = $key;
        $this->_type = $type;
        $this->_text = $text;
        $this->_characterList = $characterList;
        $this->_depth = $depth;
        $this->_data = $data;
    }

    public function getKey() : string
    {
        return $this->_key;
    }

    public function getType() : string
    {
        return $this->_type;
    }

    public function getText() : string
    {
        return $this->_text;
    }

    /**
     * @return CharacterMetadata[]
     */
    public function getCharacterList() : array
    {
        return $this->_characterList;
    }

    public function getLength() : int
    {
        return mb_strlen($this->_text);
    }

    public function getDepth() : int
    {
        return $this->_depth;
    }

    public function getInlineStyleAt(int $offset) : array
    {
        return $this->_characterList[$offset]->getStyle();
    }

    public function getEntityAt(int $offset)
    {
        return $this->_characterList[$offset]->getEntity();
    }

    public function getData() : array
    {
        return $this->_data;
    }

    public function __get($name)
    {
        // read-only access to private properties
        return $this->{'_' . $name};
    }
}
