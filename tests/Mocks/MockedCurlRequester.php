<?php
namespace Mocks;

use Hart\Utility\Requester\CurlRequester;

class MockedCurlRequester extends CurlRequester
{
	public static $result_string ='{"key1":"value1","key2":"value2","key3":[1,2,3,5,8,13]}';
	public static $result_array = array(
			'key1'=>'value1',
			'key2'=>'value2',
			'key3'=>array(1,2,3,5,8,13)
	);

	public static $result_status_test = "123";

	public function query($url,$params = array())
	{
		$this->setLastResult(self::$result_string);
		$this->setLastHttpResponse(self::$result_status_test);
		return $this->getLastResult();
	}

}