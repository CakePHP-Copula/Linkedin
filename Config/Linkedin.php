<?php
/**
 * A Linkedin API Method Map
 *
 * Refer to the apis plugin for how to build a method map
 * https://github.com/ProLoser/CakePHP-Api-Datasources
 *
 */
$config['Apis']['Linkedin']['hosts'] = array(
	'oauth' => 'api.linkedin.com/uas/oauth',
	'rest' => 'api.linkedin.com/v1',
);
// http://developer.linkedin.com/docs/DOC-1251
$config['Apis']['Linkedin']['oauth'] = array(
	'authorize' => 'authorize', // Example URI: api.linkedin.com/uas/oauth/authorize
	'request' => 'requestToken',
	'access' => 'accessToken',
	'login' => 'authenticate', // Like authorize, just auto-redirects
	'logout' => 'invalidateToken',
);
$config['Apis']['Linkedin']['read'] = array(
	// field
	'people' => array(
		// api url
		'people/id=' => array(
			// required conditions
			'id',
		),
		'people/url=' => array(
			'url',
		),
		'people/~' => array(),
	),
	'people-search' => array(
		'people-search' => array(
		// optional conditions the api call can take
			'optional' => array(
				'keywords',
				'first-name',
				'last-name',
				'company-name',
				'current-company',
				'title',
				'current-title',
				'school-name',
				'current-school',
				'country-code',
				'postal-code',
				'distance',
				'start',
				'count',
				'facet',
				'facets',
				'sort',
			),
		),
	),
);

$config['Apis']['Linkedin']['write'] = array(
	// http://developer.linkedin.com/docs/DOC-1044
	'mailbox' => array(
		'people/~/mailbox' => array(
			'subject',
			'body',
			'recipients',
		),
	),
);

$config['Apis']['Linkedin']['update'] = array(
);

$config['Apis']['Linkedin']['delete'] = array(
);