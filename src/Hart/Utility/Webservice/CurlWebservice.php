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

use Hart\Utility\Requester\BaseRequester;
use Hart\Utility\Requester\CurlRequester;

class CurlWebservice extends BaseWebservice
{
	
	public function __construct(BaseRequester $requester)
	{
		$this->setRequester($requester);
	}

	public function get($params = array())
	{
		$this->getRequester()->setMethod(CurlRequester::METHOD_GET);
		
		return $this->query($params);
	}

	public function post($params = array())
	{
		$this->getRequester()->setMethod(CurlRequester::METHOD_POST);
		
		return $this->query($params);
	}

	/**
	 * [query description]
	 * @param  string $url    [description]
	 * @param  array  $params [description]
	 * @return mixed        [description]
	 * @throws \Exception
	 */
	public function query($url, $params = array())
	{
	
		$result = null;
		try
		{
			$result = $this->getRequester()->query($url, $params);	
		}
		catch (\Exception $e)
		{
			throw $e;		
		}

		return $result;		
	}

	public function getLastHttpResponse()
	{
		return $this->getRequester()->getLastHttpResponse();
	}

	public function getLastResult()
	{
		return $this->getRequester()->getLastResult();
	}



}