<?php
namespace Utility\Webservice;

use \Mockery as m;

use Hart\Utility\Webservice\CurlWebservice;

use Mocks\MockedCurlRequester;

class CurlWebserviceTest extends \Codeception\TestCase\Test
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
 
 	public function test_it_calls_query_method()
 	{

 		$cr = new MockedCurlRequester();
 		$x = new CurlWebservice($cr);
 		
 		$this->assertEquals( MockedCurlRequester::$result_string , $x->get('https://www.domain.com') ); 		
 		
 	}

}