<?php

class News extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_admin_logged_in();

		$this->load->model(array('model_news', 'model_pcts', 'news_categories_model'));
		$this->load->helper('array');

		$this->layout->set_title('News &amp; Training');
		$this->layout->set_breadcrumb('News &amp; Training', '/admin/news');
	}


	function index($page = 0)
	{
		if ($this->pct_id)
		{
			$_GET['pct_id'] = $this->pct_id;
		}

		$total = $this->model_news->get_total_news();

		$this->load->library('pagination');

		$config['base_url'] = '/admin/news/index/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$this->pagination->initialize($config);

		$this->data['news'] = $this->model_news->get_news($page, $config['per_page']);

		$this->data['total'] = ($this->data['news'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['news'])) . ' of ' . $total . '.' : '0 results');

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'] . '&amp;date_from=' . @$_GET['date_from'] . '&amp;date_to=' . @$_GET['date_to'];

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->data['news_categories'] = result_assoc($this->news_categories_model->get_all(), 'nc_id', 'nc_title', '(Any)');

		$this->layout->set_javascript('/views/admin/news/index.js');
		$this->layout->set_view_script('/admin/news/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function set($news_id = 0)
	{
		if($news_item = $this->model_news->get_news_item($news_id))
		{
			if(@$_GET['delete'])
			{
				$this->model_news->delete_news($news_item['id']);

				redirect('/admin/news');
			}

			$title = 'Update news';
		}
		else
		{
			$title = 'Add news';
		}

		if($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('title', '', 'required|strip_tags');
			$this->form_validation->set_rules('body', '', '');

			if($this->form_validation->run())
			{
				$this->model_news->set_news(@$news_item['id']);

				redirect('/admin/news');
			}

		}

		$this->data['news_item'] = $news_item;

		$this->data['title'] = $title;

		$this->data['pcts'] = $this->model_pcts->get_pcts_select();

		$this->data['news_categories'] = result_assoc($this->news_categories_model->get_active(), 'nc_id', 'nc_title');

		$this->layout->set_title($title);
		$this->layout->set_breadcrumb($title);
		$this->layout->set_css('/redactor/redactor.css', '/scripts/');

		if ($this->input->get('editor') == 'r')
		{
			$this->layout->set_javascript(array('/redactor/redactor.min.js', '/plugins/jquery.validate.js', '/views/admin/news/set.js'));
		}
		elseif ($this->input->get('editor') == 'c')
		{
			$this->layout->set_javascript(array('/ckeditor/ckeditor.js', '/plugins/jquery.validate.js', '/views/admin/news/set.js'));
		}
		else
		{
			$this->layout->set_javascript(array('/redactor/redactor.min.js', '/plugins/jquery.validate.js', '/views/admin/news/set.js'));
		}
		//
		$this->layout->set_view_script('/admin/news/set');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


}