<?php
namespace Prezly\DraftPhp\Tests;

use PHPUnit_Framework_TestCase;
use Prezly\DraftPhp\Converter;
use Prezly\DraftPhp\Model\ContentState;
use Prezly\DraftPhp\Serializer;

class SerializerTest extends PHPUnit_Framework_TestCase
{
    const FIXTURES_DIR = __DIR__ . '/content_state';

    /**
     * @test
     * @dataProvider fixtures
     *
     * @param string $json
     */
    public function it_should_serialize_content_to_equivalent_json(string $json)
    {
        $serialized = $this->serialize($this->convert($json));

        $this->assertJsonStringEqualsJsonString(trim($json), $serialized);
    }

    public function fixtures()
    {
        $handle = opendir(self::FIXTURES_DIR);
        $dataset = [];

        while (($file = readdir($handle)) !== false) {
            if (substr($file, -5) === '.json' && strpos($file, '12_link') !== false) {
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
        $serializer = new Serializer();
        return $serializer->serialize($contentState, $options);
    }
}
