<?php
/**
 * Flickr DataSource
 * 
 * [Short Description]
 *
 * @package default
 * @author Dean Sofer
 **/
class Linkedin extends RestSource {

	/**
	 * The description of this data source
	 *
	 * @var string
	 */
	public $description = 'Linkedin DataSource Driver';
	
	/**
	 * Loads HttpSocket class
	 *
	 * @param array $config
	 * @param HttpSocket $Http
	 */
	public function __construct($config) {
		$config['method'] = 'OAuth';
		parent::__construct($config);
	}
	
	/**
	 * Lets you use the fields in Model::find() for linkedin
	 *
	 * @param string $model 
	 * @param string $queryData 
	 * @return void
	 * @author Dean Sofer
	 */
	public function read(&$model, $queryData = array()) {
		$path = '';
		if (isset($model->request['uri']['path']))
			$path = $model->request['uri']['path'];
		$model->request['uri']['path'] = $path . $this->fieldSelectors($queryData['fields']);

		return parent::read($model, $queryData);
	}
	
	/**
	 * Formats an array of fields into the url-friendly nested format
	 *
	 * @param array $fields 
	 * @return string $fields
	 * @link http://developer.linkedin.com/docs/DOC-1014
	 */
	public function fieldSelectors($fields = array()) {
		$result = '';
		if (!empty($fields)) {
			if (is_array($fields)) {
				foreach ($fields as $group => $field) {
					if (is_string($group)) {
						$fields[$group] = $group . $this->fieldSelectors($field);
					}
				}
				$fields = implode(',', $fields);
			}
			$result .= ':(' . $fields . ')';
		}
		return $result;
	}
	
	/**
	 * Issues request and returns response as an array decoded according to the
	 * response's content type if the response code is 200, else triggers the
	 * $model->onError() method (if it exists) and finally returns false.
	 *
	 * @param mixed $model Either a CakePHP model with a request property, or an
	 * array in the format expected by HttpSocket::request or a string which is a
	 * URI.
	 * @return mixed The response or false
	 */
	public function request(&$model) {
		$model->request['header']['x-li-format'] = 'json';
		return parent::request($model);
	}
}