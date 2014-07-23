<?php

/**
 * @author Camarda Camillo
 * @version 0.1
 */

namespace Hart\Wrappers\Wikipedia;

use Hart\Utility\Requester\CurlRequester;
use Hart\Utility\Webservice\CurlWebservice;

/**
 * @see  http://en.wikipedia.org/w/api.php
 */
class WikipediaService extends CurlWebservice
{

	//http://en.wikipedia.org/w/api.php?format=json&action=query&titles=Main%20Page&prop=revisions&rvprop=content
	
	CONST DEFAULT_USER_AGENT = "Hart WikipediaService v. 0.1 - http://http://www.h-art.com/";
	
	protected 	$_base_urls = array(
					'en' => "http://en.wikipedia.org/w/api.php",
					'it' => "http://it.wikipedia.org/w/api.php"
				),
				$_language = 'it',
				$_params = array(
					'format'	=>'json',
					'action'	=>'query',
					'prop'		=>'revisions',
					'rvprop'	=>'content',
					'redirects' =>'true'
				);

	protected static $_instance = null;



	public static function getInstance()
	{
		if(!self::$_instance)
		{
			$cr = self::getCurlRequester();
			self::$_instance = new self($cr);
		}

		return self::$_instance;
	}

	public function search($searchs)
	{
		if(!is_array($searchs))
		{
			$searchs = array($searchs);
		}

		$tmp = array();
		foreach($searchs as $s)
		{
			$tmp[] = urlencode($s);
		}

		$searchs = implode('|',$tmp);


		$mergedParams = array_merge($this->_params,array('titles' => $searchs));
		try
		{
			return json_decode(parent::get($this->getBaseUrl(),$mergedParams),1);
		}
		catch(\Exception $e)
		{
			return json_encode(array("error"=>$e->getMessage()));
		}
		
	}

	public function get($url,$params = array())
	{
		throw new Exception("Use the search() method.", 1);	
	}

	public function post($url,$params = array())
	{
		throw new Exception("Only GET method allowed", 1);	
	}

	public function setLanguage($language)
	{
		$this->_language = $language;
	}

	public function setLanguage($language)
	{
		$this->_language = $language;
	}

	public function getLanguage()
	{
		return $this->_language;
	}

	public function setUserAgent($ua)
	{
		$this->getRequester()->setUserAgent($ua);
	}

	protected static function getCurlRequester()
	{
		$cr = new CurlRequester();
		$cr->setUserAgent(self::DEFAULT_USER_AGENT);

		return $cr;	
	}

	protected function getBaseUrl()
	{
		if(!isset($this->_base_urls[$this->getLanguage()]))
		{
			throw new Exception("Base Url for language {$this->getLanguage()} not found", 1);		
		}

		return $this->_base_urls[$this->getLanguage()];
	}
}