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
    	$this->_mocked_curl_requester = new MockedCurlRequester();
    }

    protected function _after()
    {
    }

    // tests
 
 	public function test_get_method_returns()
 	{
 		$x = new CurlWebservice($this->_mocked_curl_requester); 		
 		$this->assertEquals( MockedCurlRequester::$result_string , $x->get('https://www.domain.com') ); 			 		
 	}

 	public function test_post_method_returns()
 	{
 		$x = new CurlWebservice($this->_mocked_curl_requester); 		
 		$this->assertEquals( MockedCurlRequester::$result_string , $x->post('https://www.domain.com') ); 			 		
 	}

 	public function test_it_receives_last_result()
 	{
		$x = new CurlWebservice($this->_mocked_curl_requester); 		

 		$x->post("https://www.domain.com");
 		$this->assertEquals( MockedCurlRequester::$result_string  , $x->getLastResult() ); 					 		
 	}

 	public function test_it_receives_last_http_status()
 	{
		$x = new CurlWebservice($this->_mocked_curl_requester); 		
 		$x->post("https://www.domain.com");
 		$this->assertEquals( MockedCurlRequester::$result_status_test , $x->getLastHttpResponse() ); 			 		
 	}



}