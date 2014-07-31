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
	
	///w/api.php?action=parse&format=json&page=the%20Matrix&prop=sections
	//
	CONST DEFAULT_USER_AGENT = "Hart WikipediaService v. 0.1 - http://http://www.h-art.com/";
	
	protected 	$_base_url,
				$_language = 'it',
				$_params = array(
					'format'	=>'json',
					'action'	=>'query',
					'prop'		=>'revisions',
					'rvprop'	=>'content',
					'redirects' =>'true'
				);

	protected static $_instance = null;



	public static function getInstance($wikiApiUrl = "http://it.wikipedia.org/w/api.php")
	{	

		if(!$wikiApiUrl)
		{
			throw new Exception("Missing api endpoint", 1);			
		}
		
		if(!self::$_instance)
		{
			$cr = self::getCurlRequester();
			self::$_instance = new self($cr);
			self::$_instance->setBaseUrl($wikiApiUrl);
		}

		return self::$_instance;
	}

	/**
	 * Returns the content of a wikipedia page
	 * 
	 * @param  string  $searchs pages to search
	 * @param  integer $section [description] (only if we are searching a single page)
	 * @return [type]           [description]
	 */
	public function search($searchs,$section = 0)
	{

		$searchs = $this->composeTitles($searchs);
		$search_counts = is_array($searchs)? count($searchs) : 1;


		$mergedParams = array_merge($this->_params,array('titles' => $searchs));
		if($section && (1 === $search_counts ) )
		{
			$mergedParams['rvsection'] = $section;
		}

		try
		{
			return json_decode(parent::get($this->getBaseUrl(),$mergedParams),1);
		}
		catch(\Exception $e)
		{
			return json_encode(array("error"=>$e->getMessage()));
		}		
	}

	/**
	 * return the sections of a certain page
	 * @param  string $search Name of the page
	 * @return JSON string the result
	 */
	public function getSections($search)
	{
		$mergedParams = array(
			'format'	=>'json',
			'action'	=> 'parse',
			'prop'		=> 'sections',
			'page'		=> $this->composeTitles($search),
			'redirects'	=> 'true',
		);

//http://it.wikipedia.org/w/api.php?format=json&action=parse&prop=sections&page=The%20Matrix&redirects=true
		try
		{			
			$result = json_decode(parent::get($this->getBaseUrl(),$mergedParams),1);

			return $result;
		}
		catch(\Exception $e)
		{
			return json_encode(array("error"=>$e->getMessage()));
		}

		
	}

	/**
	 * Search images for a page
	 * @param  Page name $searchs [description]
	 * @return JSON string         [description]
	 */
	public function searchImages($searchs)
	{
		
		$searchs = $this->composeTitles($searchs);

		$mergedParams = array_merge($this->_params,array(
			'titles' 	=> $searchs,
			'prop'		=> 'images'
			));
		try
		{			
			return json_decode(parent::get($this->getBaseUrl(),$mergedParams),1);
		}
		catch(\Exception $e)
		{
			return json_encode(array("error"=>$e->getMessage()));
		}		
	}


	/**
	 * Given images unique wiki-urls ( ex:  )
	 * returns info about the images
	 * 
	 * @param  string|array $searchs Images unique urls
	 * @return JSON string          [description]
	 */
	public function searchImage($searchs)
	{
		$searchs = $this->composeTitles($searchs,false);


		$mergedParams = array_merge($this->_params,array(
			'titles' 	=> $searchs,
			'iiprop'	=> 'url',
			'prop'		=> 'imageinfo',
			
		));
		unset($mergedParams['rvprop']);

		try
		{			
			return json_decode(parent::get($this->getBaseUrl(),$mergedParams),1);
		}
		catch(\Exception $e)
		{
			return json_encode(array("error"=>$e->getMessage()));
		}
	}


	/**
	 * Utility function to convert string| array of page names to pipe-separated string 
	 * @param  [type]  $titles    [description]
	 * @param  boolean $urlencode [description]
	 * @return [type]             [description]
	 */
	protected function composeTitles($titles,$urlencode = true )
	{
		if(!is_array($titles))
		{
			$titles = array($titles);
		}

		$tmp = array();
		foreach($titles as $s)
		{
			if($urlencode)
			{
				$tmp[] = urlencode($s);	
			}
			else
			{
				$tmp[] = $s;
			}
			
		}

		$titles = implode('|',$tmp);
		return $titles;
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

	public function setBaseUrl($url)
	{
		$this->_base_url = $url;
	}

	public function getBaseUrl()
	{	
		return $this->_base_url;
	}
}