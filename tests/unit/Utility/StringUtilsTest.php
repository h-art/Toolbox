<?php
namespace Utility;

use Hart\Utility\StringUtils;

class StringUtilsTest extends \Codeception\TestCase\Test
{
   use \Codeception\Specify; 

   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function test_it_removes_emoji_from_a_string()
    {
        $this->specify("Remove emoji from string", function() {
            $test_characters = array(
                "🍹",
                "🚬",
                "🍀",
                "🎸",
                "🐨",
                "🍓"
            );

            foreach($test_characters as $char)
            {
                $this->assertEquals("" , StringUtils::removeEmoji($char) );    
            }
            
        });
    }

    public function test_it_replaces_accented_characters_from_a_string()
    {

        $this->specify("Accents", function() {
            $this->assertEquals("aeeiouU" , StringUtils::replaceAccentedCharacters("àèéìòùÛ") );    
        });



    }

    public function test_it_sluggifies_a_string()
    {
        $this->specify("Empty string", function() {
            $this->assertEquals("" , StringUtils::sluggify("") );    
        });

        $this->specify("Spaces to dashes", function() {
            $this->assertEquals("hello-world" , StringUtils::sluggify("hello world") );    
        });

        $this->specify("Accents", function() {
            $this->assertEquals("url-with-accents" , StringUtils::sluggify("Ûrl with àccents") );    
        });

        $this->specify("Uppercase to lowercase", function() {
            $this->assertEquals("hello-world" , StringUtils::sluggify("HeLLo WoRlD") );    
        });

        $this->specify("Initial spaces", function() {
            $this->assertEquals("this-is-a-not-trimmed-string" , StringUtils::sluggify("  this is a not trimmed string") );    
        });

    }

}