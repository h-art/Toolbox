<?php

namespace Hart\Utility\Webservice;

use Hart\Utility\Requester\BaseRequester;

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