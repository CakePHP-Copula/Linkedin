# Installation

### Step 1: Download / clone the following plugins: 

 * **LinkedIn** to _Plugin/Linkedin_
 * [HttpSocketOauth plugin](https://github.com/ProLoser/http_socket_oauth) (ProLoser fork) to _Plugin/HttpSocketOauth_
 * [Apis plugin](https://github.com/ProLoser/CakePHP-Api-Datasources) to _Plugin/Apis_

### Step 2: Setup your `database.php`

```php
var $linkedin = array(
	'datasource' => 'Linkedin.Linkedin',
	'login' => '<linkedin api key>',
	'password' => '<linkedin api secret>',
);
```

### Step 3: Install the Apis-OAuth Component for authentication

```php
MyController extends AppController {
	var $components = array(
		'Apis.Oauth' => 'linkedin',
	);
	
	function connect() {
		$this->Oauth->connect();
	}
	
	function linkedin_callback() {
		$this->Oauth->callback();
	}
}
```

### Step 4: Use the datasource normally 
Look inside the Config map for a list of endpoints or add more yourself!
The `'fields'` key optionally lets you specify what fields you want returned. [Here is a list for reference](https://developer.linkedin.com/documents/profile-fields)

```php
Class MyModel extends AppModel {

	function readProfile() {
		$this->setDataSource('linkedin');
		$data = $this->find('all', array(
			'path' => 'people/~',
			'fields' => array(
				'first-name', 'last-name', 'summary', 'specialties', 'associations', 'honors', 'interests', 'twitter-accounts', 
				'positions' => array('title', 'summary', 'start-date', 'end-date', 'is-current', 'company'), 
				'educations', 
				'certifications',
				'skills' => array('id', 'skill', 'proficiency', 'years'), 
				'recommendations-received',
			),
		));
		$this->setDataSource('default');
	}
}
```

You can also pass search parameters to certain endpoints:

```php
$data = $this->find('all', array(
	'path' => 'people-search',
	'conditions' => array(
		'count' => 500,
		'firstName' => 'bob',
		'lastName' => 'dillan',
		'company' => 'columbia records club',
	),
	'fields' => array(
		'first-name', 'last-name',
		'positions' => array('title', 'company'),
	),
));
```