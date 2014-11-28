<?php

class Mmf_presenter
{


	public function __construct($mmf = array())
	{
		$this->data = $mmf;
	}


	/**
	 * Get a field value from the data array. Returns N/A on empty value
	 *
	 * @return string		The value of the field requested or N/A if empty
	 */
	public function get($field, $default = '--')
	{
		return element($field, $this->data, $default);
	}


	/**
	 * Return an edit link with icon
	 *
	 * @return string		Image tag wrapped in anchor
	 */
	public function edit()
	{
		return anchor('service-providers/mail-merge/set-field/' . $this->get('mmf_id', ''),
			'<img src="/img/icons/pencil.png" alt="Edit">');
	}


	public function insert_link()
	{
		return '<a href="#" rel="' . $this->get('mmf_name') . '"
			title="' . $this->get('mmf_description') . '">['  . $this->get('mmf_name') . ']</a>';
	}


}
