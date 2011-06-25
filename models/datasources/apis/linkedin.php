<?php
/**
 * Flickr DataSource
 * 
 * [Short Description]
 *
 * @package default
 * @author Dean Sofer
 **/
class Linkedin extends ApisSource {
	
	// TODO: Relocate to a dedicated schema file
	var $_schema = array();
	
    protected $options = array(
        'scheme'   				=> 'http',
        'format'     			=> 'json',
        'user_agent' 			=> 'CakePHP LinkedIn Datasource',
        'http_port'  			=> 80,
        'timeout'    			=> 10,
        'login'      			=> null,
        'token'      			=> null,
        'param_separator'		=> '&',
        'key_value_separator'	=> '=',
		'permissions'			=> 'read', // read, write, delete
    );

	/**
	 * Formats an array of fields into the url-friendly nested format
	 *
	 * @param array $fields 
	 * @return string $fields
	 * @link http://developer.linkedin.com/docs/DOC-1014
	 */
	function fieldSelectors($fields = array()) {
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
}