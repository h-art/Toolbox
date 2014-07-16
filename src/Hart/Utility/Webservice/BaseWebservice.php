<?php

namespace Hart\Utility\Webservice;

abstract class BaseWebservice
{
	protected	$_lastResult = null,
				$_lastHttpResponse = null;


	abstract protected function query($params);

	public function getLastHttpResponse()
	{
		return $this->_lastHttpResponse;
	}

	protected function setLastHttpResponse($response)
	{
		$this->_lastHttpResponse = $response;
	}

	public function getLastResult()
	{
		return $this->_lastResult;
	}

	protected function setLastResult($result)
	{
		$this->_lastResult = $result;
	}
}