<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $this->layout->get_css(); ?>
<style type="text/css">
	body, html {
        background:none;
    }
    div.container {
        padding:30px;
        width:800px;
    }
</style>
<?php echo $this->layout->get_javascript(); ?>
<title><?php echo $this->layout->get_title(); ?></title>
</head>

<body onload="window.print()">

	<div class="container">

		<?php $this->load->view($this->layout->get_view_script()); ?>

    </div>

</body>
</html>
