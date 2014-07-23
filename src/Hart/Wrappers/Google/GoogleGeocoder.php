<?php
namespace Hart\Wrappers\Google;
/**
 * @author Camarda Camillo
 * @version 0.1
 * 
 */


/**
 * Common Usage:  
 *    $c = new Hart\Wrappers\Google\GoogleGeocoder('API_KEY');
 *
 *    $result = $c->setResponseType(Hart\Wrappers\GoogleGeocoder::RESPONSE_COORDS_ONLY)->geoCode('H-art, Roncade Treviso')->toString();
 *
*/
class GoogleGeocoder
{
    const GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/';

    const RESPONSE_COORDS_ONLY = false;
    const RESPONSE_FULL = true;
    // es:
    // https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=true_or_false&key=API_KEY

    protected $_api_key = null,
              $_output = 'json',
              $_sensor = 'false', // stringa!
              $_responseType = self::RESPONSE_COORDS_ONLY,
              $_use_api_key = true
              ; 

    public function __construct($api_key)
    {
      $this->_api_key = $api_key;
    }

    protected function buildParameterString($address)
    {
      $parameterString = sprintf("?address=%s&sensor=%s",
                                  urlencode($address),
                                  $this->_sensor
                                  
                                );

      if($this->_use_api_key)
      {
        $parameterString.= "&key=".$this->_api_key;
      }

      return $parameterString;

    }

    /**
     * @param address  , indirizzo da geocodare
    */

    public function geocode($address)
    {
      $resp_json = $this->call($address);
      $resp = json_decode($resp_json, true);

      
      if($resp['status']='OK')
      {
        if(self::RESPONSE_FULL === $this->_responseType)
        {
          $this->_result = $resp;          
        }
        else
        {
          if(!count($resp['results']))
          {
            $this->_result = FALSE;
          }
          else
          {
            $this->_result = $resp['results'][0]['geometry']['location'];
          }
          
        }
      }
      else
      {
        $this->_result = FALSE;
      }

      return $this;
    }


    public function setResponseType($type)
    {
      $this->_responseType = $type;
      return $this;
    }

    public function disableApiKey()
    {
      $this->_use_api_key = false;
      return $this;
    }

    public function enableApiKey()
    {
      $this->_use_api_key = true;
      return $this;
    }

    public function toArray()
    {
      return $this->_result;
    }

    public function toString($separator = ' ')
    {
      if($this->_responseType !== self::RESPONSE_COORDS_ONLY)
      {
        throw new \Exception("Can't convert to string a full response :( use ->setResponseType(GoogleGeocoder::RESPONSE_COORDS_ONLY) instead", 1);          
      }

      if(is_array($this->_result))
      {
        return implode($separator, $this->_result);
      }
      else
      {
        return FALSE;
      }     
    }

    public function toJSON()
    {
      if(is_array($this->_result))
      {
        return json_encode($this->_result);
      }
      else
      {
        return FALSE;
      }        
    }




    protected function call($address)
    {
      $url = self::GEOCODE_URL.$this->_output.$this->buildParameterString($address);
      $c = curl_init();
      curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($c, CURLOPT_URL, $url);
      $contents = curl_exec($c);
      curl_close($c);

      if ($contents) 
      {
        return $contents;
      }
      else
      {
        return FALSE;
      }

    }
}