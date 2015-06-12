<?php

/**
 * @author Camarda Camillo
 * @version 0.2
 * @edited by Zanardo Alex
 */

namespace Hart\Wrappers\Wikipedia;

use Hart\Utility\Requester\CurlRequester;
use Hart\Utility\Webservice\CurlWebservice;

/**
 * @see  http://en.wikipedia.org/w/api.php
 */
class WikipediaService extends CurlWebservice
{
    const DEFAULT_USER_AGENT = "Hart WikipediaService v. 0.1 - http://www.h-art.com/";

    protected $_base_url;
    protected $_language = 'it';
    protected $_params = array(
        "format" => "json",
        "action" => "query",
        "prop" => "revisions",
        "rvprop" => "content",
        "redirects" => "true",
        "generator" => "search",
        "continue" => "",
    );

    protected static $_instance = null;

    public static function getInstance($wikiApiUrl = "https://it.wikipedia.org/w/api.php")
    {
        if (!$wikiApiUrl) {
            throw new Exception("Missing api endpoint", 1);
        }

        if (!self::$_instance) {
            $cr = self::getCurlRequester();
            self::$_instance = new self($cr);
            self::$_instance->setBaseUrl($wikiApiUrl);
        }

        return self::$_instance;
    }//fine function

    /**
     * Returns the content of a wikipedia page
     *
     * @param  string  $searchs pages to search
     * @param  integer $section [description] (only if we are searching a single page)
     * @return [type]  [description]
     */
    public function search($searchs, $section = false, $pageId = null)
    {
        $searchs = $this->composeTitles($searchs);
        $search_counts = is_array($searchs) ? count($searchs) : 1;

        $mergedParams = array_merge($this->_params, array('gsrsearch' => $searchs));

        if (($section !== false) && (1 === $search_counts)) {
            $mergedParams['rvsection'] = $section;
        }

        try {
            return json_decode(parent::get($this->getBaseUrl(), $mergedParams), 1);
        } catch (\Exception $e) {
            return json_encode(array("error" => $e->getMessage()));
        }
    }//fine function

    //http://en.wikipedia.org/w/api.php?action=parse&page=Joe%20Satriani&prop=text&section=2&format=json
    public function getSectionHtml($page, $section, $pageId = null)
    {
        $mergedParams = array_merge($this->_params, array(
            'action' => 'parse',
            'prop' => 'text',
            'section' => $section,
        ));

        unset($mergedParams["redirects"]);
        unset($mergedParams["continue"]);

        if ($pageId !== null) {
            $mergedParams["pageid"] = $pageId;
        } else {
            $mergedParams["page"] = $this->composeTitles($search);
            $mergedParams["redirects"] = "true";
        }

        unset($mergedParams["rvprop"]);
        unset($mergedParams["generator"]);

        try {
            return json_decode(parent::get($this->getBaseUrl(), $mergedParams), 1);
        } catch (\Exception $e) {
            return json_encode(array("error" => $e->getMessage()));
        }
    }//fine function

    /**
     * return the sections of a certain page
     * @param  string $search Name of the page
     * @return JSON   string the result
     */
    public function getSections($search, $pageId = null)
    {
        $mergedParams = array(
            "format" => "json",
            "action" => "parse",
            "prop" => "sections",
        );

        if ($pageId !== null) {
            $mergedParams["pageid"] = $pageId;
        } else {
            $mergedParams["page"] = $this->composeTitles($search);
            $mergedParams["redirects"] = "true";
        }

        try {
            $result = json_decode(parent::get($this->getBaseUrl(), $mergedParams), 1);

            if (isset($result['error'])) {
                return json_encode(array('error' => $result['error']));
            }

            return $result;
        } catch (\Exception $e) {
            return json_encode(array("error" => $e->getMessage()));
        }
    }//fine function

    /**
     * Search images for a page
     * @param  Page name $searchs [description]
     * @return JSON      string         [description]
     */
    public function searchImages($searchs)
    {
        $searchs = $this->composeTitles($searchs);

        $mergedParams = array_merge($this->_params, array(
            'titles' => $searchs,
            'prop' => 'images',
        ));

        try {
            return json_decode(parent::get($this->getBaseUrl(), $mergedParams), 1);
        } catch (\Exception $e) {
            return json_encode(array("error" => $e->getMessage()));
        }
    }//fine function

    /**
     * Given images unique wiki-urls ( ex:  )
     * returns info about the images
     *
     * @param  string|array $searchs Images unique urls
     * @return JSON         string          [description]
     */
    public function searchImage($searchs)
    {
        $searchs = $this->composeTitles($searchs, false);

        $mergedParams = array_merge($this->_params, array(
            'titles' => $searchs,
            'iiprop' => 'url',
            'prop' => 'imageinfo',
        ));

        unset($mergedParams['rvprop']);

        try {
            return json_decode(parent::get($this->getBaseUrl(), $mergedParams), 1);
        } catch (\Exception $e) {
            return json_encode(array("error" => $e->getMessage()));
        }
    }//fine function

    /**
     * Utility function to convert string| array of page names to pipe-separated string
     * @param  [type]  $titles    [description]
     * @param  boolean $urlencode [description]
     * @return [type]  [description]
     */
    protected function composeTitles($titles, $urlencode = true)
    {
        if (!is_array($titles)) {
            $titles = array($titles);
        }

        $tmp = array();

        foreach ($titles as $s) {
            $s = ucwords($s);

            if ($urlencode) {
                $tmp[] = urlencode($s);
            } else {
                $tmp[] = $s;
            }
        }//fine foreach

        $titles = implode('|', $tmp);

        return $titles;
    }//fine function

    public function get($url, $params = array())
    {
        throw new Exception("Use the search() method.", 1);
    }//fine function

    public function post($url, $params = array())
    {
        throw new Exception("Only GET method allowed", 1);
    }//fine function

    public function setLanguage($language)
    {
        $this->_language = $language;
    }//fine function

    public function getLanguage()
    {
        return $this->_language;
    }//fine function

    public function setUserAgent($ua)
    {
        $this->getRequester()->setUserAgent($ua);
    }//fine function

    protected static function getCurlRequester()
    {
        $cr = new CurlRequester();
        $cr->setUserAgent(self::DEFAULT_USER_AGENT);

        return $cr;
    }//fine function

    public function setBaseUrl($url)
    {
        $this->_base_url = $url;
    }//fine function

    public function getBaseUrl()
    {
        return $this->_base_url;
    }//fine function
}
