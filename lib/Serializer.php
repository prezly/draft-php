<?php

namespace Prezly\DraftPhp;

use Prezly\DraftPhp\Model\CharacterMetadata;
use Prezly\DraftPhp\Model\ContentBlock;
use Prezly\DraftPhp\Model\ContentState;
use Prezly\DraftPhp\Model\EntityInstance;
use Prezly\DraftPhp\Model\RawDraftContentBlock;
use Prezly\DraftPhp\Model\RawDraftContentState;
use Prezly\DraftPhp\Model\RawDraftEntity;
use Prezly\DraftPhp\Model\RawDraftEntityRange;
use Prezly\DraftPhp\Model\RawDraftInlineStyleRange;

/**
 * Serializes ContentState instance back into JSON re-presentation
 */
class Serializer
{
    /**
     * @param ContentState $contentState
     * @param int $options json_encode options bit mask
     * @return string
     */
    public static function serialize(ContentState $contentState, int $options = 0): string
    {
        $serializer = new self();

        $rawContentState = $serializer->serializeRaw($contentState);

        return json_encode($rawContentState, $options);
    }

    /**
     * @param ContentState $contentState
     * @return \stdClass|RawDraftContentState
     */
    public function serializeRaw(ContentState $contentState): \stdClass
    {
        /** @var RawDraftContentState $rawContentState */
        $rawContentState = (object) [
            'blocks'    => [],
            'entityMap' => (object) [],
        ];

        $isEntityUsed = [];

        foreach ($contentState->blocks as $block) {
            $rawContentState->blocks[] = $this->serializeBlock($block);

            foreach ($this->getUsedEntityKeys($block) as $key) {
                $isEntityUsed[$key] = true;
            }
        }

        foreach ($contentState->entityMap as $key => $entityInstance) {
            if (isset($isEntityUsed[$key])) {
                $rawContentState->entityMap->{$key} = $this->serializeEntity($entityInstance);
            }
        }

        return $rawContentState;
    }

    /**
     * @param ContentBlock $block
     * @return RawDraftContentBlock
     */
    private function serializeBlock(ContentBlock $block): object
    {
        return (object) [
            'key'               => $block->key,
            'type'              => $block->type,
            'text'              => $block->text,
            'depth'             => $block->depth,
            'inlineStyleRanges' => $this->serializeInlineStyleRanges($block),
            'entityRanges'      => $this->serializeEntityRanges($block),
            'data'              => $block->getData() ?: (object) [], // to force empty object instead of empty array
        ];
    }

    /**
     * @param ContentBlock $block
     * @return RawDraftInlineStyleRange[]
     */
    private function serializeInlineStyleRanges(ContentBlock $block): array
    {
        /** @var RawDraftInlineStyleRange[] $ranges Plain list of ranges */
        $ranges = [];

        /** @var RawDraftInlineStyleRange[] $current_ranges_map [ string $style => $inline_style_ranges, ... ] */
        $current_ranges_map = [];
        /** @var CharacterMetadata|null $prev_char */
        $prev_char = null;

        foreach ($block->getCharacterList() as $offset => $char) {
            if ($char === $prev_char) {
                // If it's the same metadata => prolong current ranges
                foreach ($char->style as $style) {
                    $current_ranges_map[$style]->length++;
                }
            } else {
                // Finalized ranges
                foreach (array_diff(array_keys($current_ranges_map), $char->style) as $style) {
                    $ranges[] = $current_ranges_map[$style];
                    unset($current_ranges_map[$style]);
                }

                // New ranges
                foreach (array_diff($char->style, array_keys($current_ranges_map)) as $style) {
                    $current_ranges_map[$style] = (object) [
                        'offset' => $offset,
                        'length' => 0,
                        'style'  => $style,
                    ];
                }

                // Prolong all current char style ranges
                foreach ($char->style as $style) {
                    $current_ranges_map[$style]->length++;
                }
            }

            $prev_char = $char;
        }

        return array_merge($ranges, array_values($current_ranges_map));
    }

    /**
     * @param ContentBlock $block
     * @return RawDraftEntityRange[]
     */
    private function serializeEntityRanges(ContentBlock $block): array
    {
        /** @var RawDraftEntityRange[] $ranges Plain list of ranges */
        $ranges = [];

        /** @var string|null $prev_char */
        $prev_entity = null;
        $current_range_idx = -1;

        foreach ($block->getCharacterList() as $offset => $char) {
            if ($char->entity !== null) { // Count only ranges with entity defined
                if ($char->entity === $prev_entity) {
                    // If it's the same entity => prolong current range
                    $ranges[$current_range_idx]->length++;
                } else {
                    // Otherwise => start a new range
                    $ranges[] = (object) [
                        'key'    => $char->entity,
                        'offset' => $offset,
                        'length' => 1,
                    ];
                    $current_range_idx = count($ranges) - 1;
                }
            }

            $prev_entity = $char->entity;
        }

        return $ranges;
    }

    /**
     * @param EntityInstance $entityInstance
     * @return RawDraftEntity
     */
    private function serializeEntity(EntityInstance $entityInstance): object
    {
        /** @var RawDraftEntity $entity */
        return (object) [
            'type'       => $entityInstance->type,
            'mutability' => $entityInstance->mutability,
            'data'       => $entityInstance->data,
        ];
    }

    /**
     * @param ContentBlock $block
     * @return string[]
     */
    private function getUsedEntityKeys(ContentBlock $block): array
    {
        $isEntityUsed = [];
        foreach ($block->getCharacterList() as $char) {
            if ($char->entity !== null) {
                $isEntityUsed[$char->entity] = true;
            }
        }
        return array_keys($isEntityUsed);
    }
}
