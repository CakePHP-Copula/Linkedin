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