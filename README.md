Prezly's Draft-PHP
==================

Simple Draft.js model implemented in PHP.

Usage
-----
```php
$json = '{
    "blocks":[
        {
           "key": "7si2a",
           "text": "Say hello to world!",
           "type": "unstyled",
           "depth": 0,
           "inlineStyleRanges": [
               {
                    "style": "BOLD",
                    "offset": 0,
                    "length": 9
               }
           ],
           "entityRanges": [
               {
                    "key": "0",
                    "offset": 0,
                    "length": 9
               }
           ],
           "data": {}
         }
    ],
    "entityMap":{
        "0":{
            "type":"link",
            "mutability":"MUTABLE",
            "data":{
               "href":"https://www.prezly.com/"
            }
        }
    }
}';
$contentState = \Prezly\DraftPhp\Converter::convertFromJson($json);
// or
$rawState = json_decode($json); // Note: raw state should be an stdClass object, not an associative array
$contentState = \Prezly\DraftPhp\Converter::convertFromRaw($rawState);

var_dump($contentState);
/*
  Prezly\DraftPhp\Model\ContentState {
    -_blocks: array:1 [
      0 => Prezly\DraftPhp\Model\ContentBlock {#1507
        -_key: "7si2a"
        -_type: "unstyled"
        -_text: "Say hello to world!"
        -_characterList: array:19 [
          0 => Prezly\DraftPhp\Model\CharacterMetadata {#1506
            -_style: ["BOLD"]
            -_entity: "0"
          }
          1 => Prezly\DraftPhp\Model\CharacterMetadata {#1506}
          2 => Prezly\DraftPhp\Model\CharacterMetadata {#1506}
          3 => Prezly\DraftPhp\Model\CharacterMetadata {#1506}
          4 => Prezly\DraftPhp\Model\CharacterMetadata {#1506}
          5 => Prezly\DraftPhp\Model\CharacterMetadata {#1506}
          6 => Prezly\DraftPhp\Model\CharacterMetadata {#1506}
          7 => Prezly\DraftPhp\Model\CharacterMetadata {#1506}
          8 => Prezly\DraftPhp\Model\CharacterMetadata {#1506}
          9 => Prezly\DraftPhp\Model\CharacterMetadata {#1490
            -_style: []
            -_entity: null
          }
          10 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
          11 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
          12 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
          13 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
          14 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
          15 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
          16 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
          17 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
          18 => Prezly\DraftPhp\Model\CharacterMetadata {#1490}
        ]
        -_depth: 0
        -_data: []
      }
    ]
    -_entityMap: array:1 [
      0 => Prezly\DraftPhp\Model\EntityInstance {#1505
        -_type: "link"
        -_mutability: "MUTABLE"
        -_data: array:1 [
          "href" => "https://www.prezly.com/"
        ]
      }
    ]
  }
 */
 
var_dump($contentState->blocks[0]->characterList[0]);
/*
  Prezly\DraftPhp\Model\CharacterMetadata {#1506
    -_style: ["BOLD"]
    -_entity: "0"
  }
*/

var_dump($contentState->getEntity($contentState->blocks[0]->characterList[0]->entity);
/*
  Prezly\DraftPhp\Model\EntityInstance {#1505
    -_type: "link"
    -_mutability: "MUTABLE"
    -_data: array:1 [
      "href" => "https://www.prezly.com/"
    ]
  }
*/
```

Notes on implementation 
-----------------------

1. ContentState now holds an `$entityMap` property and has `->getEntity(string $entityKey)` method.
   This approach allows to incapsulate all the data coming from JSON into a single object and then use it for rendering.
   
   Having global static pool of entities (as in native Draft.js implementation, and another PHP port of Draft.js model) 
   is not that useful. Global state gets into your way when you need to render multiple content states in  a single PHP process.
   Also it complicates testing.
   
2. All the model classes are immutable. That's achived by storing all the data in private properies 
   providing getters only as public API (`getXxxx` methods + magic `__get()` method to emulate read-only public props).
   
Other implementations
---------------------

- [webstronauts/draft-php](https://github.com/webstronauts/draft-php) — a one-to-one port of Draft.js model specs, well tested

License
-------
[MIT](./LICENSE)

Credits
-------
Brought to you by [Prezly](https://www.prezly.com/) — CRM software crafted for PR communication 
