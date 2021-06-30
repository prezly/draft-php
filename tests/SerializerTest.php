<?php

namespace Prezly\DraftPhp\Tests;

use PHPUnit\Framework\TestCase;
use Prezly\DraftPhp\Converter;
use Prezly\DraftPhp\Model\ContentState;
use Prezly\DraftPhp\Serializer;

class SerializerTest extends TestCase
{
    public const FIXTURES_DIR = __DIR__ . '/content_state';

    /**
     * @test
     * @dataProvider fixtures
     *
     * @param string $json
     */
    public function it_should_serialize_content_to_equivalent_json(string $json): void
    {
        $serialized = $this->serialize($this->convert($json));

        $this->assertJsonStringEqualsJsonString(trim($json), $serialized);
    }

    /**
     * @test
     * @dataProvider fixtures
     *
     * @param string $json
     */
    public function it_should_serialize_to_raw_content_state(string $json): void
    {
        $rawContentState = $this->serializeRaw($this->convert($json));

        $this->assertJsonStringEqualsJsonString($json, json_encode($rawContentState));
    }

    /**
     * @test
     * @dataProvider fixtures
     *
     * @param string $json
     */
    public function content_state_instance_should_be_json_serializable(string $json): void
    {
        $contentState = $this->convert($json);

        $this->assertJsonStringEqualsJsonString($json, json_encode($contentState));
    }

    public function fixtures()
    {
        $handle = opendir(self::FIXTURES_DIR);
        $dataset = [];

        while (($file = readdir($handle)) !== false) {
            if (substr($file, -5) === '.json') {
                $json = $this->loadFile(self::FIXTURES_DIR . '/' . $file);
                $dataset[$file] = [$json];
            }
        }

        return $dataset;
    }

    private function loadFile(string $path): string
    {
        return file_get_contents($path);
    }

    private function convert(string $json): ContentState
    {
        return Converter::convertFromJson($json);
    }

    private function serialize(ContentState $contentState, int $options = JSON_PRETTY_PRINT): string
    {
        return Serializer::serialize($contentState, $options);
    }

    private function serializeRaw(ContentState $contentState)
    {
        $serializer = new Serializer();

        return $serializer->serializeRaw($contentState);
    }
}
