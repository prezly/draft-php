<?php namespace Prezly\DraftPhp\Model;

use Prezly\DraftPhp\Constants\EntityType;

/**
 * @property string $type
 * @property string $mutability
 * @property array|null $data
 */
class EntityInstance
{
    /**
     * @var string One of EntityType constants
     * @see EntityType
     */
    private $_type;
    /**
     * @var string One of EntityMutability constants
     * @see EntityMutability
     */
    private $_mutability;
    /** @var array|null */
    private $_data;

    public function __construct(string $type, string $mutability, array $data = [])
    {
        $this->_type = $type;
        $this->_mutability = $mutability;
        $this->_data = $data;
    }

    public function __get($name)
    {
        // public read-only access to private properties
        return $this->{'_' . $name};
    }
}