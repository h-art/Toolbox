<?php

/**
 * @author Camarda Camillo
 */

namespace Hart\Facebook\Wrappers;
/**
 *  REQUIRES: 		"facebook/php-sdk-v4": ">=4.0.8",
 */
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookCanvasLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;

/**
 * 
 * OPTIONAL, for laravel session handling
 */
use Hart\Wrappers\Facebook\LaravelFacebookRedirectLoginHelper ;




/**
 *	Common usage:
 * 
 * 
 *	 $fb_factory = new FacebookFactory($appid,$appsecret);
 *		$fb_factory->setFacebookRedirectLoginHelperUri($login_uri);
 *		$loginUrl = $fb_factory->getLoginUrl($scopes_array);
 *
 *		if($session = $fb_factory->getFacebookSession())
 *		{
 *			try
 *			{
 *				$response = $fb_factory->request('/me');
 *				$graphMe = $response->getGraphObject();
 *
 *				$response = $fb_factory->request('/me/feed');
 *				$graphFeed = $response->getGraphObject();
 *
 *				$logoutUrl = $fb_factory->getLogoutUrl($redirect_after_logout_uri);	
 *			}			
 *			catch(Facebook\FacebookAuthorizationException $e)
 *			{
 *				// $message = "errore blablabal";
 *			}		
 *		}
 *
 * 
 */
class FacebookFactory
{
	const SESSION_PREFIX = 'fb_facebook_factory_';

	protected 	$_facebook_redirect_login_helper = null,
				$_facebook_canvas_login_helper = null,
				$_facebook_session = null;


	public static function parseSignedRequest($signed_request,$secret)
	{
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
			

		// decode the data
		$sig = self::base64_url_decode($encoded_sig);
		$data = json_decode(self::base64_url_decode($payload), true);

		// confirm the signature
		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if ($sig !== $expected_sig) {
			error_log('Bad Signed JSON signature!');
			return null;
		}

		return $data;

	}

	protected static function base64_url_decode($input)
	{
		return base64_decode(strtr($input, '-_', '+/'));
	}

	public function __construct( $appid,$secret)
	{				
		FacebookSession::setDefaultApplication($appid,$secret);		
	}

	public function getFacebookRedirectLoginHelper()
	{		
		return $this->_facebook_redirect_login_helper;
	}

	public function getFacebookCanvasLoginHelper()
	{
		if(!$this->_facebook_canvas_login_helper )
		{
			$this->useFacebookCAnvasLoginHelper();
		}

		return $this->_facebook_canvas_login_helper;
	}

	public function useFacebookCAnvasLoginHelper()
	{
		$this->_facebook_canvas_login_helper = new FacebookCanvasLoginHelper();
	}

	public function setFacebookRedirectLoginHelperUri($uri)
	{
		/* FOR LARAVEL SESSION USAGE */
		//$this->_facebook_redirect_login_helper = new LaravelFacebookRedirectLoginHelper($uri);
		
		// OR 
		$this->_facebook_redirect_login_helper = new FacebookRedirectLoginHelper($uri);
	}

	/**
	 * Tries to set the session from the redirecturl helper
	 * 
	 * @return bool	true => session correctly set	|	false => session not set
	 */
	protected function setFbSessionFromRedirectUrlHelper()
	{
		if($this->_facebook_redirect_login_helper)
		{
			try
			{
				$this->_facebook_session = $this->_facebook_redirect_login_helper->getSessionFromRedirect();
				if($this->_facebook_session)
				{
					$session_token = $this->_facebook_session->getToken();
					\Session::put(self::SESSION_PREFIX.'session_token',$session_token);
				}					
			} catch(FacebookRequestException $ex) {
				throw $ex;
			} catch(\Exception $ex) {
				// When validation fails or other local issuesù
				throw $ex;
			}
		}

		

		return (bool)$this->_facebook_session;
	}

	/**
	 * Tries to set the session from the redirecturl helper
	 * 
	 * @return bool	true => session correctly set	|	false => session not set
	 */
	protected function setFbSessionFromCanvasLoginHelper()
	{
		try
		{
			$this->_facebook_session = $this->_facebook_canvas_login_helper->getSession();
			if($this->_facebook_session)
			{
				$session_token = $this->_facebook_session->getToken();
				\Session::put(self::SESSION_PREFIX.'session_token',$session_token);
			}					
		} 
		catch(FacebookRequestException $ex) 
		{
			throw $ex;
		} 
		catch(\Exception $ex) 
		{
			// When validation fails or other local issuesù
			throw $ex;
		}

		return (bool)$this->_facebook_session;

	}

	public function getFacebookSession($token = null)
	{
		if(!$this->_facebook_session)
		{
			// controllo prima nella sessione utente
			$token = \Session::get(self::SESSION_PREFIX.'session_token',$token);
			if($token)
			{
				$this->_facebook_session =	new FacebookSession($token);
			}
			else
			{
				if(!$this->_facebook_redirect_login_helper && !$this->_facebook_canvas_login_helper )
				{
					throw new \Exception("FacebookRedirectLoginHelper AND CanvasLoginHelper not init. ", 1);					
				}

				// non c'e' in sessione utente, vedo se ce l'ho dal redirect login helper
				$session_set_from_redirect_helper = $this->setFbSessionFromRedirectUrlHelper();

				// se non l'ho trovata dal redirect helper ma ho il canvas helper provo con il canvas helper
				if(!$session_set_from_redirect_helper && $this->_facebook_canvas_login_helper)
				{
					$this->setFbSessionFromCanvasLoginHelper();								
				}
			}			
		}

		return $this->_facebook_session;
	}

	public function hasFacebookSession()
	{
		return (bool) $this->getFacebookSession();
	}

	/**
	 * [request description]
	 * @param	[type] $path			 [description]
	 * @param	string $method		 [description]
	 * @param	[type] $parameters [description]
	 * @param	[type] $version		[description]
	 * @param	[type] $etag			 [description]
	 * @return Facebook\FacebookResponse [description]
	 * 
	 * @throws Facebook\FacebookAuthorizationException exception when the user hasn't given the app the permission it needs
	 * @throws \Exception description
	 */
	public function request( $path, $method = 'GET',	$parameters = null, $version = null, $etag = null)
	{
		if(!$this->getFacebookSession())
		{
			throw new \Exception("No facebook session");			
		}

		$request = new FacebookRequest($this->getFacebookSession(), $method, $path, $parameters = null, $version = null, $etag = null);

		$response = false;

		try
		{
			$response = $request->execute();		
		}
		catch(FacebookAuthorizationException $ex)
		{
			$this->signout();
			throw $ex;
		}
		catch(\Exception $ex)
		{
			//$this->signout();
			throw	$ex;		
		}

		
		return $response;

		//$graphObject = $response->getGraphObject();
	}

	public function getLoginUrl($scopes)
	{
		return $this->_facebook_redirect_login_helper->getLoginUrl($scopes);
	}

	public function getLogoutUrl($next_url)
	{
		return $this->_facebook_redirect_login_helper->getLogoutUrl($this->_facebook_session,$next_url);
	}

	public function signout()
	{		
		$this->_facebook_redirect_login_helper = null;
		$this->_facebook_session = null;
		\Session::forget(self::SESSION_PREFIX.'session_token');
	}



}