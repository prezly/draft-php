<?php

namespace Prezly\DraftPhp\Constants;

interface BlockType
{
    // This is used to represent a normal text block (paragraph).
    public const UNSTYLED = 'unstyled';
    public const HEADER_ONE = 'header-one';
    public const HEADER_TWO = 'header-two';
    public const HEADER_THREE = 'header-three';
    public const HEADER_FOUR = 'header-four';
    public const HEADER_FIVE = 'header-five';
    public const HEADER_SIX = 'header-six';
    public const UNORDERED_LIST_ITEM = 'unordered-list-item';
    public const ORDERED_LIST_ITEM = 'ordered-list-item';
    public const BLOCKQUOTE = 'blockquote';
    public const PULLQUOTE = 'pullquote';
    public const CODE = 'code-block';
    public const ATOMIC = 'atomic';
}
