<?php

/**
 *  @author Camarda Camillo
 * 
 *  USAGE EXAMPLE:
 *  $x = new CurlCaller($url);
 *  $result = $x->get($params);
 *  $result = $x->post($params);
 */

namespace Hart\Utility\Webservice;

class CurlWebservice extends BaseWebservice
{
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	protected 	$_url = null,
				$_headers = null,
				$_method = 'GET';


	public function __construct($url)
	{
		$this->_url = $url;
	}
	public function get($params = array())
	{
		$this->_method = self::METHOD_GET;
		return $this->query($params);

	}

	public function post($params = array())
	{
		$this->_method = self::METHOD_POST;
		return $this->query($params);
	}

	public function setHeaders($params)
	{
		$this->_headers = $params;

	}

	protected function query($params = array())
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


			$this->setLastResult(curl_exec($ch));
			$this->setLastHttpResponse(curl_getinfo($ch, CURLINFO_HTTP_CODE));

		curl_close($ch);
		if(200 === $this->getLastHttpResponse())
		{
			return $this->getLastResult();
		}
		else
		{			
			throw new \Exception(" {$this->getLastHttpResponse()} error occurred.Message: {$this->getLastResult()}", 1);
		}

	}
}