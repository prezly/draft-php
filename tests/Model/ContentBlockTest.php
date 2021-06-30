<?php namespace Prezly\DraftPhp\Tests\Model;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prezly\DraftPhp\Constants\BlockType;
use Prezly\DraftPhp\Model\CharacterMetadata;
use Prezly\DraftPhp\Model\ContentBlock;

class ContentBlockTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_construct_empty_text_instances()
    {
        $block = new ContentBlock('0', BlockType::UNSTYLED, '', [], 0);

        $this->assertEquals('', $block->text);
        $this->assertEquals([], $block->characterList);
    }

    /**
     * @test
     */
    public function it_should_construct_non_empty_text_instances()
    {
        $text = 'Hello';
        $chars = array_pad([], mb_strlen($text), CharacterMetadata::create([]));

        $block = new ContentBlock('0', BlockType::UNSTYLED, $text, $chars, 0);

        $this->assertEquals($text, $block->text);
        $this->assertEquals($chars, $block->characterList);
    }

    /**
     * @test
     */
    public function it_should_throw_if_invalid_character_list_array_given()
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentBlock('0', BlockType::UNSTYLED, '?', ['Not a CharacterMetadata'], 0);
    }

    /**
     * @test
     */
    public function it_should_throw_if_text_length_not_matching_character_list_length()
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentBlock('0', BlockType::UNSTYLED, '?', [/* should have 1 CharacterMetadata item */], 0);
    }
}
