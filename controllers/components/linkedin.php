<?php
/**
* LinkedIn Component
*/
App::import('vendor', 'Linkedin.linkedin');
class LinkedinComponent extends Object {
	
	var $components = array('Session');
	var $controller;
	var $linkedin;
	var $response;
	var $settings;
	
	function initialize(&$controller, $settings = array()) {
		$this->controller = $controller;
		$this->settings = $settings;
		if ($this->Session->check('oauth.linkedin.access')) {
			$this->settings['token'] = $this->Session->read('oauth.linkedin.access');
		}
	}
	
	function startup(&$controller) {
		$options = Configure::read('linkedin');
		$options['callbackUrl'] = Router::url(null, true) . '?' . LinkedIn::_GET_TYPE . '=initiate&' . LinkedIn::_GET_RESPONSE . '=1';
		$this->linkedin = new LinkedIn($options);
		$this->linkedin->setResponseFormat(LinkedIn::_RESPONSE_JSON);
		if (!empty($this->settings['token'])) {
			$this->linkedin->setTokenAccess($this->settings['token']);
		}
	}
	
	function response($response) {
		if (!$response['success'])
			return false;
		
		$response = $response['linkedin'];
		if ($this->linkedin->getResponseFormat() == LinkedIn::_RESPONSE_JSON || $this->linkedin->getResponseFormat() == LinkedIn::_RESPONSE_JSONP)
			$response = json_decode($response, true);
		
		return $response;
	}
	
	/**
	 * Formats an array of fields into a url-friendly nested format
	 *
	 * @param string $fields 
	 * @return void
	 * @author Dean Sofer
	 */
	function fields($fields = array()) {
		$result = '';
		if (!empty($fields)) {
			if (is_array($fields)) {
				foreach ($fields as $group => $field) {
					if (is_string($group)) {
						$fields[$group] = $group . $this->fields($field);
					}
				}
				$fields = implode(',', $fields);
			}
			$result .= ':(' . $fields . ')';
		}
		return $result;
	}
	
	function login() {		
		// check for response from LinkedIn
		$_GET[LinkedIn::_GET_RESPONSE] = (isset($_GET[LinkedIn::_GET_RESPONSE])) ? $_GET[LinkedIn::_GET_RESPONSE] : '';
		if(!$_GET[LinkedIn::_GET_RESPONSE]) {
			// LinkedIn hasn't sent us a response, the user is initiating the connection
			
			// send a request for a LinkedIn access token
			$this->response = $this->linkedin->retrieveTokenRequest();
			if($this->response['success'] === TRUE) {
				// split up the response and stick the LinkedIn portion in the user session
				$this->Session->write('oauth.linkedin.request', $this->response['linkedin']);
				
				// redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
				$this->controller->redirect(LinkedIn::_URL_AUTH . $this->Session->read('oauth.linkedin.request.oauth_token'));
			} else {
				// bad token request
				return false;
			}
		} else {
			// LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
			$this->response = $this->linkedin->retrieveTokenAccess(
				$_GET['oauth_token'],
				$this->Session->read('oauth.linkedin.request.oauth_token_secret'),
				$_GET['oauth_verifier']
			);
			if($this->response['success'] === TRUE) {
				// the request went through without an error, gather user's 'access' tokens
				$this->Session->write('oauth.linkedin.access', $this->response['linkedin']);
				
				// set the user as authorized for future quick reference
				$this->Session->write('oauth.linkedin.authorized', true);
				// redirect the user back to the demo page
				return $this->response['linkedin'];
			} else {
				// bad token access
				return false;
			}
		}
	}
	
	function logout() {
		$this->response = $this->linkedin->revoke();
		$this->Session->delete('oauth');
		if($this->response['success'] === TRUE) {
			// revocation successful, clear session
			return true;
		} else {
			// revocation failed
			return false;
		}
	}
	
	/**
	 * Returns the profile details of a linkedin member
	 *
	 * @param string $match Leave empty for current user, or pass an id or public profile url
	 * @param array $fields refer to http://developer.linkedin.com/docs/DOC-1061
	 * @return mixed $response
	 * @author Dean Sofer
	 */
	function profile($match = null, $fields = array()) {
		if ($match == null) {
			$match = '~';
		} elseif (is_int(strpos($match, 'linkedin.com'))) {
			$match = 'url=' . urlencode($match);
		}
		$match .= $this->fields($fields);
		return $this->response($this->linkedin->profile($match));
	}
}