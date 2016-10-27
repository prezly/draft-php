<?php

namespace Prezly\DraftPhp\Model;

/*
 * Classes here exist just to describe a complex stdClasses structure
 * of a draft raw content state (plain JSON objects).
 *
 * They are not intended to be instantiated. For better IDE experience only.
 * That's why we use `if (false) { ... }` here.
 */
if (false) {
    class RawDraftContentState
    {
        /** @var array|RawDraftContentBlock[] */
        public $blocks;
        /** @var array|RawDraftEntity[] */
        public $entityMap;
    }

    class RawDraftContentBlock
    {
        /** @var string|null */
        public $key;
        /** @var string One of BlockType constants */
        public $type;
        /** @var string */
        public $text;
        /** @var int|null */
        public $depth;
        /** @var RawDraftInlineStyleRange[]|null */
        public $inlineStyleRanges;
        /** @var RawDraftEntityRange[]|null */
        public $entityRanges;
        /** @var object|null */
        public $data;
    }

    class RawDraftEntity
    {
        /** @var string One of EntityType constants */
        public $type;
        /** @var string One of EntityMutability constants
         * @see EntityMutability */
        public $mutability;
        /** @var array|null */
        public $data;
    }

    class RawDraftInlineStyleRange
    {
        /** @var string */
        public $style;
        /** @var int */
        public $offset;
        /** @var int */
        public $length;
    }

    class RawDraftEntityRange
    {
        /** @var int */
        public $key;
        /** @var int */
        public $offset;
        /** @var int */
        public $length;
    }
}
