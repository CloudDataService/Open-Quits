<?php

class News extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		$this->auth->check_service_provider_logged_in();

		$this->load->model(array('model_news', 'news_categories_model'));
	}


	function index($nc_id = 1, $page = 0)
	{
		$_GET['pct_id'] = $this->session->userdata('pct_id');
		$_GET['nc_id'] = $nc_id = (int) $nc_id;

		$news_category = $this->news_categories_model->get($nc_id);

		if ($nc_id === 1)
		{
			$this->layout->set_title('News');
			$this->layout->set_breadcrumb('News', '/service-providers/news/index/1');
		}
		else
		{
			$this->layout->set_title($news_category['nc_title']);
			$this->layout->set_breadcrumb($news_category['nc_title'], '/service-providers/news/index/' . $news_category['nc_id']);
		}


		$this->session->unset_userdata('total_new_news_' . $nc_id);

		$total = $this->model_news->get_total_news();

		$this->load->library('pagination');

		$config['base_url'] = '/service-providers/news/index/' . $nc_id;

		$config['total_rows'] = $total;

		$config['per_page'] = 5;

		$config['uri_segment'] = 5;

		$this->pagination->initialize($config);

		$this->data['news'] = $this->model_news->get_news($page, $config['per_page']);

		$this->data['total'] = ($this->data['news'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['news'])) . ' of ' . $total . '.' : '0 results');

		$this->layout->set_view_script('/service_providers/news/index');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


	function item($news_id)
	{
		if( ! $news_item = $this->model_news->get_news_item($news_id))
			show_404();

		$this->data['news_item'] = $news_item;

		$news_category = $this->news_categories_model->get($news_item['nc_id']);

		if ($news_item['nc_id'] === 1)
		{
			$this->layout->set_title('News');
			$this->layout->set_breadcrumb('News', '/service-providers/news/index/1');
		}
		else
		{
			$this->layout->set_title($news_category['nc_title']);
			$this->layout->set_breadcrumb($news_category['nc_title'], '/service-providers/news/index/' . $news_category['nc_id']);
		}

		$this->layout->set_title($news_item['title']);
		$this->layout->set_breadcrumb($news_item['title']);
		$this->layout->set_view_script('service_providers/news/item');
		$this->load->vars($this->data);
		$this->load->view('/layouts/default');
	}


}