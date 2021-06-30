<?php

namespace Prezly\DraftPhp;

use InvalidArgumentException;
use Prezly\DraftPhp\Model\CharacterMetadata;
use Prezly\DraftPhp\Model\ContentBlock;
use Prezly\DraftPhp\Model\ContentState;
use Prezly\DraftPhp\Model\EntityInstance;
use Prezly\DraftPhp\Model\RawDraftContentBlock;
use Prezly\DraftPhp\Model\RawDraftContentState;
use Prezly\DraftPhp\Model\RawDraftEntity;

/**
 * Converts serialized ContentState presentation
 * (JSON string or raw JSON-decoded stdClass)
 * into properly-validated ContentState model.
 *
 * Mimics DraftJS converter API.
 *
 * Throws InvalidArgumentException if invalid JSON structure given.
 */
class Converter
{
    /**
     * Convert ContentState JSON string presentation
     * into ContentState model class.
     *
     * @param string $json
     * @return ContentState
     *@throws InvalidArgumentException when invalid JSON string or invalid JSON structure given.
     *
     */
    public static function convertFromJson(string $json): ContentState
    {
        $data = json_decode($json);

        if (is_null($data) or ! is_object($data)) {
            throw new InvalidArgumentException("Invalid JSON given: '$json'");
        }

        return self::convertFromRaw($data);
    }

    /**
     * Convert ContentState JSON-decoded stdClass presentation
     * into ContentState model class.
     *
     * @param \stdClass|RawDraftContentState $raw
     * @return ContentState
     *@throws InvalidArgumentException when invalid structure given.
     *
     */
    public static function convertFromRaw(object $raw): ContentState
    {
        if (! isset($raw->blocks)) {
            throw new InvalidArgumentException("Invalid JSON given: 'blocks' property is missing");
        }

        if (! isset($raw->entityMap)) {
            throw new InvalidArgumentException("Invalid JSON given: 'entityMap' property is missing");
        }

        $entityMap = [];
        foreach ($raw->entityMap as $key => $rawEntity) {
            $entityMap[$key] = self::convertEntityFromRaw($rawEntity);
        }

        $blocks = [];
        foreach ($raw->blocks as $rawBlock) {
            $blocks[] = self::convertBlockFromRaw($rawBlock);
        }

        return ContentState::createFromBlockArray($blocks, $entityMap);
    }

    /**
     * @param \stdClass|RawDraftEntity $rawEntity
     * @return EntityInstance
     */
    private static function convertEntityFromRaw(object $rawEntity): EntityInstance
    {
        return new EntityInstance(
            $rawEntity->type,
            $rawEntity->mutability,
            json_decode(json_encode($rawEntity->data), true) ?: []
        );
    }

    /**
     * @param \stdClass|RawDraftContentBlock $rawBlock
     * @return ContentBlock
     */
    private static function convertBlockFromRaw(object $rawBlock): ContentBlock
    {
        $characterList = [];
        for ($i = 0; $i < mb_strlen($rawBlock->text); $i++) {
            $style = [];
            $entity = null;
            foreach ($rawBlock->inlineStyleRanges as $inlineStyleRange) {
                if ($inlineStyleRange->offset <= $i and $i < ($inlineStyleRange->offset + $inlineStyleRange->length)) {
                    $style[] = $inlineStyleRange->style;
                }
            }
            foreach ($rawBlock->entityRanges as $entityRange) {
                if ($entityRange->offset <= $i and $i < ($entityRange->offset + $entityRange->length)) {
                    $entity = $entityRange->key;
                }
            }
            $characterList[] = CharacterMetadata::create($style, $entity);
        }

        return new ContentBlock(
            $rawBlock->key,
            $rawBlock->type,
            $rawBlock->text,
            $characterList,
            $rawBlock->depth,
            json_decode(json_encode($rawBlock->data), true) ?: [],
        );
    }
}
