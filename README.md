# Installation

1. Download / clone the plugin to _plugins/linkedin_

2. Add the component: 

<pre><code>var $components = array(
	'Linkedin.Linkedin' => array(
		'appKey' => '--your api key--',
		'appSecret' => '--your secret code--',
	)
);</code></pre>

3. Create a login action in the controller to let people allow your app access to their LinkedIn

4. Once they're authenticated, call queries on the component (or the attached vendor attribute)

### Check Logged-in Status

You can check `oauth.linkedin.access` in the session to see if it is set/true when the member is logged in.

If you wish to go past the duration of the session, store the Linkedin->login() response somewhere and set it to Linkedin->settings['token'] before you start querying. There is a chance the token may expire on LinkedIn's side however.

### Additional Functionality

Since this is a work in progress, you can get direct access to the vendor by doing `$this->Linkedin->linkedin->whatever()` from the controller. The vendor is stored as a `$linkedin` attribute of the component and is deently well documented (along with LinkedIn's own API).

More and more methods however will be expanded in the component to be more consistent with CakePHP, and it is highly likely that a behavior or datasource will be developed also.

# Example Controller (For Clarification)

<pre><code>class MyController extends AppController {

	var $name = 'My';
	var $components = array(
		'Linkedin.Linkedin' => array(
			'appKey' => '--your api key--',
			'appSecret' => '--your secret code--',
		),
	);
	
	/**
	 * Scans the currently logged in user's profile for specified fields
	 * I'm using fields from http://developer.linkedin.com/docs/DOC-1061
	 */
	function linkedin_scan() {
		$data = $this->Linkedin->profile(null, array(
			'first-name', 'last-name', 'summary', 'specialties', 'associations', 'honors', 'interests', 'twitter-accounts', 
			'positions' => array('title', 'summary', 'start-date', 'end-date', 'is-current', 'company'), 
			'educations', 
			'certifications',
			'skills' => array('id', 'skill', 'proficiency', 'years'), 
			'recommendations-received'
		));
		if ($data) {
			$this->MyModel->save($data);
			$this->Session->setFlash('Success!');
		} else {
			$this->Session->setFlash('There was an error: ' . $this->Linkedin->response['error']);
		}
		$this->redirect(array('action' => 'index'));
	}
	
	/**
	 * Redirects to LinkedIn to allow access to the app
	 *
	 * $this->Linkedin->response has some useful info in it if $token isn't enough
	 */
	function linkedin_login($id = null) {
		if ($token = $this->Linkedin->login()) {
			$this->Account->id = $id;
			$this->Account->saveField('api_token', json_encode($token));
			$this->Session->setFlash('You logged in');
		} else {
			$this->Session->setFlash('There was an error');
		}
	}
	
	/**
	 * Renders the access token for LinkedIn useles
	 */
	function linkedin_logout() {
		if ($this->Linkedin->logout()) {
			// Should probably delete 'api_token'_
			$this->Session->setFlash('You logged out');
		} else {
			$this->Session->setFlash('There was an error');
		}
		$this->redirect(array('action' => 'index'));
	}</code></pre>
