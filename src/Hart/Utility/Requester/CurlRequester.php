<?php
namespace Hart\Utility\Requester;

use Hart\Utility\Requester\BaseRequester;

class CurlRequester extends BaseRequester
{
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	CONST HTTP_STATUS_OK = 200;


	protected 	$_lastResult = null,
				$_lastHttpResponse = null,
				$_headers = null,
				$_userAgent = null,
				$_method = 'GET';

	public function setMethod($method)
	{
		if(!in_array($method, $this->getAllowedMethods()))
		{
			throw new Exception("Method not allowed", 1);			
		}

		$this->_method = $method;
	}

	public function setUserAgent($ua)
	{
		$this->_userAgent = $ua;
	}

	protected function configureCurl($ch,$url,$params)
	{
		$querystring = http_build_query($params);
		switch($this->_method)
		{
			case self::METHOD_POST:
				curl_setopt($ch,CURLOPT_URL,$url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$querystring);
			break;

			case self::METHOD_GET:
				curl_setopt($ch,CURLOPT_URL,$url.'?'.$querystring);
			break;

		}

		if($this->_userAgent)
		{
			curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
		}

		if($this->_headers)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);						
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		return $ch;
	}


	public function query($url,$params = array())
	{
		

		$ch = curl_init();

		$ch = $this->configureCurl($ch,$url,$params);		

		$this->setLastResult(curl_exec($ch));
		$this->setLastHttpResponse(curl_getinfo($ch, CURLINFO_HTTP_CODE));

		curl_close($ch);

		if(self::HTTP_STATUS_OK === $this->getLastHttpResponse())
		{
			return $this->getLastResult();
		}
		else
		{			
			throw new \Exception(" {$this->getLastHttpResponse()} error occurred. Last Result: {$this->getLastResult()}", 1);
		}

	}

	public function getAllowedMethods()
	{
		return array(
			self::METHOD_GET,
			self::METHOD_POST,
		);
	}

	public function setHeaders($params)
	{
		$this->_headers = $params;
	}

	protected function setLastHttpResponse($response)
	{
		$this->_lastHttpResponse = $response;
	}

	protected function setLastResult($result)
	{
		$this->_lastResult = $result;
	}

	public function getLastHttpResponse()
	{
		return $this->_lastHttpResponse;
	}

	public function getLastResult()
	{
		return $this->_lastResult;
	}
}
