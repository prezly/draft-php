<?php
namespace Prezly\DraftPhp;

use Prezly\DraftPhp\Model\ContentBlock;
use Prezly\DraftPhp\Model\ContentState;
use Prezly\DraftPhp\Model\EntityInstance;

/**
 * Serializes ContentState instance back into JSON re-presentation
 */
class Serializer
{
    /**
     * @param \Prezly\DraftPhp\Model\ContentState $contentState
     * @param int $options json_encode options bit mask
     * @return string
     */
    public function serialize(ContentState $contentState, int $options = 0): string
    {
        $rawContentState = $this->serializeContentState($contentState);

        return json_encode($rawContentState, $options);
    }

    /**
     * @param ContentState $contentState
     * @return \Prezly\DraftPhp\Model\RawDraftContentState
     */
    private function serializeContentState(ContentState $contentState)
    {
        /**
         * @var $rawContentState \Prezly\DraftPhp\Model\RawDraftContentState
         */
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
     * @param \Prezly\DraftPhp\Model\ContentBlock $block
     * @return \Prezly\DraftPhp\Model\RawDraftContentBlock
     */
    private function serializeBlock(ContentBlock $block)
    {
        /** @var \Prezly\DraftPhp\Model\RawDraftContentBlock $rawBlock */
        $rawBlock = (object) [
            'key'               => $block->key,
            'type'              => $block->type,
            'text'              => $block->text,
            'depth'             => $block->depth,
            'inlineStyleRanges' => $this->serializeInlineStyleRanges($block),
            'entityRanges'      => $this->serializeEntityRanges($block),
            'data'              => $block->getData() ?: (object) [], // to force empty object instead of empty array
        ];

        return $rawBlock;
    }

    /**
     * @param \Prezly\DraftPhp\Model\ContentBlock $block
     * @return \Prezly\DraftPhp\Model\RawDraftInlineStyleRange[]
     */
    private function serializeInlineStyleRanges(ContentBlock $block): array
    {
        /** @var \Prezly\DraftPhp\Model\RawDraftInlineStyleRange[] $ranges Plain list of ranges */
        $ranges = [];

        /** @var \Prezly\DraftPhp\Model\RawDraftInlineStyleRange[] $current_ranges_map [ string $style => $inline_style_ranges, ... ] */
        $current_ranges_map = [];
        /** @var \Prezly\DraftPhp\Model\CharacterMetadata|null $prev_char */
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

        $ranges = array_merge($ranges, array_values($current_ranges_map));

        return $ranges;
    }

    /**
     * @param \Prezly\DraftPhp\Model\ContentBlock $block
     * @return \Prezly\DraftPhp\Model\RawDraftEntityRange[]
     */
    private function serializeEntityRanges(ContentBlock $block): array
    {
        /** @var \Prezly\DraftPhp\Model\RawDraftEntityRange[] $ranges Plain list of ranges */
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
     * @param \Prezly\DraftPhp\Model\EntityInstance $entityInstance
     * @return \Prezly\DraftPhp\Model\RawDraftEntity
     */
    private function serializeEntity(EntityInstance $entityInstance)
    {
        /** @var \Prezly\DraftPhp\Model\RawDraftEntity $entity */
        $entity = (object) [
            'type'       => $entityInstance->type,
            'mutability' => $entityInstance->mutability,
            'data'       => $entityInstance->data,
        ];

        return $entity;
    }

    /**
     * @param \Prezly\DraftPhp\Model\ContentBlock $block
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
