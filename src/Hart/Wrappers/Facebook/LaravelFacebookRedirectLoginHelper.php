<?php
namespace Hart\Wrappers\Facebook;
/**
 * @author Camarda Camillo
 * @version 0.1
 */

/**
 *  REQUIRES: 		"facebook/php-sdk-v4": ">=4.0.8",
 */
use Facebook\FacebookRedirectLoginHelper;

class LaravelFacebookRedirectLoginHelper extends FacebookRedirectLoginHelper
{
	protected function storeState($state)
    {
        \Session::put('facebook.state', $state);
    }

    protected function loadState()
    {
        return $this->state =  \Session::get('facebook.state');
    }

}