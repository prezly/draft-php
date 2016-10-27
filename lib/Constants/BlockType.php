<?php

namespace Prezly\DraftPhp\Constants;

interface BlockType
{
    // This is used to represent a normal text block (paragraph).
    const UNSTYLED = 'unstyled';
    const HEADER_ONE = 'header-one';
    const HEADER_TWO = 'header-two';
    const HEADER_THREE = 'header-three';
    const HEADER_FOUR = 'header-four';
    const HEADER_FIVE = 'header-five';
    const HEADER_SIX = 'header-six';
    const UNORDERED_LIST_ITEM = 'unordered-list-item';
    const ORDERED_LIST_ITEM = 'ordered-list-item';
    const BLOCKQUOTE = 'blockquote';
    const PULLQUOTE = 'pullquote';
    const CODE = 'code-block';
    const ATOMIC = 'atomic';
}
