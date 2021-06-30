<?php

namespace Prezly\DraftPhp\Model;

use InvalidArgumentException;

/**
 * @property array $style
 * @property string|null $entity
 */
class CharacterMetadata
{
    /** @var CharacterMetadata[] */
    private static $pool = [];

    /** @var array */
    private $_style;
    /** @var string */
    private $_entity;

    /**
     * @param string[] $style
     * @param string|null $entity
     * @return static
     */
    public static function create(array $style, string $entity = null)
    {
        foreach ($style as $s) {
            if (! is_string($s)) {
                throw new InvalidArgumentException('$style should be an array of strings');
            }
        }
        $style = array_unique($style);
        sort($style);

        $fingerprint = md5(serialize($style) . '/' . $entity);

        if (! isset(self::$pool[$fingerprint])) {
            self::$pool[$fingerprint] = new static($style, $entity);
        }

        return self::$pool[$fingerprint];
    }

    private function __construct(array $style, string $entity = null)
    {
        $this->_style = $style;
        $this->_entity = $entity;
    }

    /**
     * @return string[]
     */
    public function getStyle(): array
    {
        return $this->_style;
    }

    public function hasStyle(string $style): bool
    {
        return in_array($style, $this->_style);
    }

    public function getEntity(): ?string
    {
        return $this->_entity;
    }

    public function __get($name)
    {
        // public read-only access to internal properties
        return $this->{'_' . $name};
    }
}
