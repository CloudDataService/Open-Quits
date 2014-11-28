<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redactor extends My_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->auth->check_admin_logged_in();
	}




	/**
	 * Handle the uploading of a file
	 */
	public function upload_file()
	{
		// Determine max upload size of document
		$max_upload = (int) (ini_get('upload_max_filesize'));
		$max_post = (int) (ini_get('post_max_size'));
		$max_file_size = min($max_upload, $max_post);

		// Init upload of file
		$config['upload_path'] = realpath(FCPATH . '/resources/');
		$config['encrypt_name'] = TRUE;
		$config['allowed_types'] = 'pdf|doc|docx|odt|fodt|xls|xlsx|ods|fods|ppt|pptx|pps|ppsx|odp|fodp|mdb|odg|fodg|vsd|vsdx';
		$config['max_size']	= $max_file_size * 1024;
		$this->load->library('upload', $config);

		// Attempt file upload
		if (isset($_FILES['file']['name']) && ! empty($_FILES['file']['name']))
		{
			if ($this->upload->do_upload('file'))
			{
				$upload_data = $this->upload->data();
				$file_name = $upload_data['file_name'];

				$res = array(
					'filelink' => base_url('resources/' . $file_name),
					'filename' => $file_name,
				);
			}
			else
			{
				$res = array(
					'error' => strip_tags($this->upload->display_errors()),
				);
			}
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($res));
	}




	/**
	 * Handle the uploading of an iamge
	 */
	public function upload_image()
	{
		// Determine max upload size of document
		$max_upload = (int) (ini_get('upload_max_filesize'));
		$max_post = (int) (ini_get('post_max_size'));
		$max_file_size = min($max_upload, $max_post);

		// Init upload of file
		$config['upload_path'] = realpath(FCPATH . '/resources/');
		$config['encrypt_name'] = TRUE;
		$config['allowed_types'] = 'jpg|png|jpeg';
		$config['max_size']	= 16384;
		$config['max_width'] = 4096;
		$config['max_height'] = 4096;
		$this->load->library('upload', $config);


		// Attempt file upload
		if (isset($_FILES['file']['name']) && ! empty($_FILES['file']['name']))
		{
			if ($this->upload->do_upload('file'))
			{
				$upload = $this->upload->data();

				$img_config['image_library'] = 'gd2';
				$img_config['source_image']	= $upload['full_path'];
				$img_config['maintain_ratio'] = true;
				$img_config['width']  = 640;
				$img_config['height']  = 640;
				$this->load->library('image_lib', $img_config);
				//print_r($img_config);


				if ( ! $this->image_lib->resize())
				{
				    $res = array(
				    	'error' => strip_tags($this->image_lib->display_errors()),
				    );
				}
				else
				{
					$url = base_url('resources/' . $upload['file_name']);
					$res = array(
						'filelink' => $url,
					);
				}

			}
			else
			{
				$res = array(
					'error' => strip_tags($this->upload->display_errors()),
				);
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($res));
	}




	public function images()
	{
		$images_json = array();
		$this->json = array();
		return;

		/*
		$this->load->model('images_model');
		$this->images_model->order_by('i_id', 'asc');
		$images = $this->images_model->get_all();

		foreach ($images as $image)
		{
			$images_json[] = array(
				'thumb' => image_src($image, 'c100x100'),
				'image' => image_src($image, 'w940'),
			);
		}
		*/

		$this->json = $images_json;
	}


}
