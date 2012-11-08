<?php
/**
 * LinkedIn DataSource
 *
 * [Short Description]
 *
 * @package LinkedIn Plugin
 * @author Dean Sofer
 **/
App::uses('ApisSource', 'Apis.Model/Datasource');
class Linkedin extends ApisSource {

	/**
	 * The description of this data source
	 *
	 * @var string
	 */
	public $description = 'Linkedin DataSource Driver';

	/**
	 * Lets you use the fields in Model::find() for linkedin
	 *
	 * @param string $model
	 * @param string $queryData
	 * @return void
	 * @author Dean Sofer
	 */
	public function read(Model $model, $queryData = array(), $recursive = null) {
		$path = '';
		if (!isset($model->request)) {
			$model->request = array();
		}
		$this->fields = $queryData['fields'];
		return parent::read($model, $queryData);
	}

	/**
	 * Sets method = POST in request if not already set
	 *
	 * @param AppModel $model
	 * @param array $fields Unused
	 * @param array $values Unused
	 */
	public function create(Model $model, $fields = null, $values = null) {
		$data = array_combine($fields, $values);
		$data = json_encode($data);
		$model->request['body'] = $data;
		$model->request['header']['content-type'] = 'application/json';
		$fields = $values = null;
		return parent::create($model, $fields, $values);
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
	 * Just-In-Time callback for any last-minute request modifications
	 *
	 * @param object $model
	 * @param array $request
	 * @return array $request
	 * @author Dean Sofer
	 */
	public function beforeRequest($model, $request) {
		$request['header']['x-li-format'] = $this->options['format'];
		if (isset($this->fields)) {
			$request['uri']['path'] .= $this->fieldSelectors($this->fields);
			unset($this->fields);
		}
		return $request;
	}
}
