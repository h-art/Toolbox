<?php

/**
 * @author Camarda Camillo
 * @version 0.1
 */

namespace Hart\Utility\Webservice;

abstract class BaseWebservice
{
	protected $_requester = null;

	abstract public function query($params);

	abstract public function getLastHttpResponse();
	abstract public function getLastResult();

	public function getRequester()
	{
		return $this->_requester;
	}
	
	protected function setRequester($requester)
	{
		$this->_requester = $requester;
	}
}