<?php
class LinkedinMessage extends LinkedinAppModel {

	function sendMessage($recipients, $subject, $body) {
		$this->request['uri']['path'] = 'people/~/mailbox';
		$data = array(
			'subject' => $subject,
			'body' => $body,
		);
		foreach ($recipients as $recipient) {
			$data['recipients']['values'][] = array('person' => array('_path' => $recipient));
		}
		$this->save($data);
	}
}