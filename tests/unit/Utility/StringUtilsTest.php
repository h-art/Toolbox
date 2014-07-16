<?php
namespace Utility;


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
    public function testMe()
    {
        $this->specify("pippo", function() {
            $this->assertTrue(true);
            $this->assertTrue(false);
        });



    }

}