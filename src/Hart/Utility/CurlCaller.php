<?php

/**
 *  @author Camarda Camillo
 * 
 *  USAGE EXAMPLE:
 *  $x = new CurlCaller($url);
 *  $result = $x->get($params);
 *  $result = $x->post($params);
 */

namespace Hart\Utility;

class CurlCaller
{
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	protected 	$_url = null,
				$_headers = null,
				$_method = 'GET',
				$_lastResult = null,
				$_lastHttpResponse = null;


	public function __construct($url)
	{
		$this->_url = $url;

	}

	public function getLastHttpResponse()
	{
		return $this->_lastHttpResponse;
	}

	public function getLastResult()
	{
		return $this->_lastResult;
	}

	public function get($params = array())
	{
		$this->_method = self::METHOD_GET;
		return $this->genericCurl($params);

	}

	public function post($params = array())
	{
		$this->_method = self::METHOD_POST;
		return $this->genericCurl($params);
	}

	public function setHeaders($params)
	{
		$this->_headers = $params;

	}

	protected function genericCurl($params = array())
	{

		$querystring = http_build_query($params);

		$ch = curl_init();

	
		switch($this->_method)
		{
			case self::METHOD_POST:
				curl_setopt($ch,CURLOPT_URL,$this->_url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$querystring);
			break;

			case self::METHOD_GET:
				curl_setopt($ch,CURLOPT_URL,$this->_url.'?'.$querystring);
			break;

		}

		if($this->_headers)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);						
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


		$this->_lastResult = curl_exec($ch);

		$this->_lastHttpResponse = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if(200 === $this->_lastHttpResponse)
		{
			return $this->_lastResult;
		}
		else
		{			
			throw new \Exception(" {$this->_lastHttpResponse} error occurred.Message: {$this->_lastResult}", 1);
		}

	}
}