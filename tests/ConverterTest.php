<?php namespace Prezly\DraftPhp\Tests;

use PHPUnit_Framework_TestCase;
use Prezly\DraftPhp\Constants\BlockType;
use Prezly\DraftPhp\Model\ContentBlock;
use Prezly\DraftPhp\Model\ContentState;
use Prezly\DraftPhp\Converter;
use Prezly\DraftPhp\Model\EntityInstance;
use stdClass;

class ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_convert_raw_stdClass_structure_to_content_state()
    {
        $raw = $this->stdClass([
            'blocks' => [],
            'entityMap' => new stdClass(),
        ]);
        $state = Converter::convertFromRaw($raw);
        $this->assertInstanceOf(ContentState::class, $state);
    }

    /**
     * @test
     */
    public function it_should_convert_json_to_content_state()
    {
        $json = json_encode([
            'blocks' => [],
            'entityMap' => new stdClass(),
        ]);
        $state = Converter::convertFromJson($json);
        $this->assertInstanceOf(ContentState::class, $state);
    }

    /**
     * @test
     */
    public function it_should_validate_if_blocks_property_is_present()
    {
        $raw = $this->stdClass(([
            'not_blocks' => [],
            'entityMap' => new stdClass(),
        ]));
        $this->setExpectedException(\InvalidArgumentException::class);
        Converter::convertFromRaw($raw);
    }

    /**
     * @test
     */
    public function it_should_validate_if_entityMap_property_is_present()
    {
        $raw = $this->stdClass([
            'blocks' => [],
            'not_entityMap' => new stdClass(),
        ]);
        $this->setExpectedException(\InvalidArgumentException::class);
        Converter::convertFromRaw($raw);
    }

    /**
     * @test
     */
    public function it_should_convert_inner_json_objects_to_draft_js_model_objects()
    {
        $json = json_encode([
            'blocks' => [
                [
                    'key' => uniqid(),
                    'type' => BlockType::UNSTYLED,
                    'text' => 'Hello World!',
                    'depth' => 0,
                    'inlineStyleRanges' => [
                        [
                            'offset' => 6,
                            'length' => 5,
                            'style'  => 'BOLD',
                        ]
                    ],
                    'entityRanges' => [
                        [
                            'offset' => 0,
                            'length' => 5,
                            'key'  => '0',
                        ]
                    ],
                    'data' => null,
                ],
            ],
            'entityMap' => [
                '0' => [
                    'type'       => 'link',
                    'mutability' => 'MUTABLE',
                    'data'       => [
                        'href' => 'http=>//www.ivan.com',
                    ],
                ],
            ],
        ]);

        $contentState = Converter::convertFromJson($json);
        $this->assertInstanceOf(ContentState::class, $contentState);

        $this->assertCount(1, $contentState->blocks);
        foreach ($contentState->blocks as $block) {
            $this->assertInstanceOf(ContentBlock::class, $block);
        }

        $this->assertCount(1, $contentState->entityMap);
        foreach ($contentState->entityMap as $entity) {
            $this->assertInstanceOf(EntityInstance::class, $entity);
        }

        return $contentState;
    }

    private function stdClass(array $props) : stdClass
    {
        $o = new stdClass();
        foreach ($props as $p => $v) {
            $o->{$p} = $v;
        }
        return $o;
    }
}