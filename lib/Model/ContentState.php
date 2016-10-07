<?php namespace Prezly\DraftPhp\Model;

use InvalidArgumentException;

/**
 * @property ContentBlock[] $blocks
 * @property EntityInstance[] $entityMap
 */
class ContentState
{
    /** @var ContentBlock[] */
    private $_blocks;
    /** @var EntityInstance[] */
    private $_entityMap;

    /**
     * @param ContentBlock[] $blocks
     * @param EntityInstance[] $entityMap
     * @return ContentState
     */
    public static function createFromBlockArray(array $blocks, array $entityMap) : ContentState
    {
        return new self($blocks, $entityMap);
    }

    /**
     * @param ContentBlock[] $blocks
     * @param EntityInstance[] $entityMap
     */
    private function __construct(array $blocks, array $entityMap)
    {
        foreach ($blocks as $b) {
            if ( ! $b instanceof ContentBlock) {
                throw new InvalidArgumentException('$blocks array may contain only ContentBlock objects');
            }
        }
        foreach ($entityMap as $e) {
            if ( ! $e instanceof EntityInstance) {
                throw new InvalidArgumentException('$entityMap map may contain only EntityInstance objects');
            }
        }

        $this->_blocks = $blocks;
        $this->_entityMap = $entityMap;
    }

    public function getEntity(string $key) : EntityInstance
    {
        return $this->entityMap[$key];
    }
    
    public function isEmpty() : bool
    {
        if (count($this->_blocks) === 0) {
            return true;
        }
        if ( ! empty($this->_entityMap) OR count($this->_blocks) > 1) {
            return false;
        }

        // Now `count($this->_blocks) === 1`
        $block = $this->_blocks[0];
        return strlen($block->text) === 0 AND $block->type !== BlockType::ATOMIC;
    }

    function __get($name)
    {
        // public read-only access to private properties
        return $this->{'_' . $name};
    }
}
