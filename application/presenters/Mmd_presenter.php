<?php

class Mmd_presenter
{


	public function __construct($mmd = array())
	{
		$this->data = $mmd;
	}


	/**
	 * Get a field value from the data array. Returns N/A on empty value
	 *
	 * @return string		The value of the field requested or N/A if empty
	 */
	public function get($field, $default = 'N/A')
	{
		return element($field, $this->data, $default);
	}


	public function preview()
	{
		return anchor('pct/mail-merge/preview/' . $this->get('mmd_id', ''),
			'<img src="/img/icons/page_word.png" alt="Preview">');
	}


	/**
	 * Return an edit link with icon
	 *
	 * @return string		Image tag wrapped in anchor
	 */
	public function edit()
	{
		return anchor('pct/mail-merge/set-document/' . $this->get('mmd_id', ''),
			'<img src="/img/icons/pencil.png" alt="Edit">');
	}


	/**
	 * Return a delete link with icon that triggers delete dialog
	 *
	 * @return string		Image tag wrapped in an anchor
	 */
	public function delete()
	{
		return '<a href="#" class="delete"
					data-title="Click Delete to remove this mail merge document."
					data-url="/pct/mail-merge/delete-document"
					data-id="' . $this->get('mmd_id') . '">
				<img src="/img/icons/cross.png" alt="Delete"></a>';
	}


	public function created($format = 'd/m/Y H:i')
	{
		return date($format, strtotime($this->get('rfmm_datetime')));
	}


	/**
	 * Return a delete link with icon that triggers delete dialog
	 *
	 * @return string		Image tag wrapped in an anchor
	 */
	public function delete_from_rf()
	{
		return '<a href="#" class="delete"
					data-title="Click Delete to remove this mail merge log entry from the request form."
					data-url="/pct/request-forms/delete-mail-merge"
					data-id="' . $this->get('rfmm_id') . '">
				<img src="/img/icons/cross.png" alt="Delete"></a>';
	}

}
