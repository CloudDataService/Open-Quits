<?php

class Layout {

	protected $_view_script;
	protected $_javascript = array();
	protected $_css = array();
	protected $_breadcrumbs = array();
	protected $_nav = array();
	protected $_sub_nav = array();
	protected $_title = array();
	private $_version = 22;

	public function __construct()
	{
		$this->_version = config_item('version');
	}

	public function set_view_script($view_script)
	{
		$this->_view_script = $view_script;
	}

	public function get_view_script()
	{
		return $this->_view_script;
	}

	public function set_title($title)
	{
		if (is_array($title))
		{
			foreach ($title as $title) {
				$this->_title[] = $title;
			}
		}
		else
		{
			$this->_title[] = $title;
		}
	}

	public function get_title($delimiter = '-')
	{
		$html = '';
		$this->_title = array_reverse($this->_title);
		$end = end($this->_title);

		foreach ($this->_title as $title)
		{
			if ($title == $end)
			{
				$html .= $title;
			}
			else
			{
				$html .= $title . ' ' . $delimiter . ' ';
			}
		}

		return $html;
	}

	public function clear_title()
	{
		$this->_title = array();

		return $this;
	}

	public function set_breadcrumb($title, $link = '')
	{
		if (is_array($title))
		{
			foreach($title as $title => $link)
			{
				$this->_breadcrumbs[$title] = $link;
			}
		}
		else
		{
			$this->_breadcrumbs[$title] = $link;
		}
	}

	public function get_breadcrumbs($delimiter = '&raquo;')
	{
		$html = '';
		$end = end($this->_breadcrumbs);

		foreach ($this->_breadcrumbs as $title => $link)
		{
			if ($link == $end)
			{
				$html .= $title;
			}
			else
			{
				$html .= '<a href="' . $link . '">' . $title . '</a>' . ' ' . $delimiter . ' ';
			}
		}

		return $html;
	}

	public function get_breadcrumb_key()
	{
		return key($this->_breadcrumbs);
	}

	public function set_javascript($scripts, $dir = '/scripts')
	{
		if (is_array($scripts))
		{
			foreach ($scripts as $script)
			{
				$this->_javascript[] = $dir . $script;
			}
		}
		else
		{
			$this->_javascript[] = $dir . $scripts;
		}
	}

	public function get_javascript()
	{
		$html = '';

		foreach ($this->_javascript as $script)
		{
			$html .= '<script type="text/javascript" src="' . $script . '?v=' . $this->_version . '"></script>' . PHP_EOL;
		}

		return $html;
	}

	public function set_css($css, $dir = '/css/')
	{
		if (is_array($css))
		{
			foreach ($css as $css)
			{
				$this->_css[] = $dir . $css;
			}
		}
		else
		{
			$this->_css[] = $dir . $css;
		}
	}

	public function get_css()
	{
		$html = '';

		foreach ($this->_css as $css)
		{
			$html .= '<link href="' . $css . '?v=' . $this->_version . '" rel="stylesheet" type="text/css" />' . PHP_EOL;
		}

		return $html;
	}

	/* navigation */
	public function set_nav($title, $href = '')
	{
		if (is_array($title))
		{
			foreach ($title as $title => $href)
			{
				$this->_nav[$title] = $href;
			}
		}
		else
		{
			$this->_nav[$title] = $href;
		}
	}

	public function get_nav()
	{
		$html = '<ul class="tabs">';

		foreach ($this->_nav as $title => $href)
		{
			$html .= '<li class="' . (in_array($title, $this->_title) ? 'selected ' : '') . (isset($this->_sub_nav[$title]) ? 'has_more ' : '') . '">';
			$html .= ($href ? '<a href="' . $href . '">' . $title . '</a>' : '<span>' . $title . '</span>');

			if (isset($this->_sub_nav[$title]))
			{
				$html .= '<ul class="sub_nav">';

				foreach ($this->_sub_nav[$title] as $title => $href)
				{
					$html .= '<li>' . PHP_EOL;
					$html .= '    <a href="' . $href . '">' . $title . '</a>' . PHP_EOL;
					$html .= '</li>' . PHP_EOL;
				}

				$html .= '</ul>' . PHP_EOL;
			}

			$html .= '</li>' . PHP_EOL;
		}

		$html .= '</ul>' . PHP_EOL;

		return $html;
	}

	function set_sub_nav($sub_nav = array())
	{
		foreach ($sub_nav as $parent => $nav)
		{
			$this->_sub_nav[$parent] = $nav;
		}
	}
}
