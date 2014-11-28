<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/css/home.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/scripts/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/scripts/views/default/home.js"></script>
<?php echo $this->layout->get_javascript(); ?>
<title><?php echo $this->layout->get_title(); ?></title>
</head>

<body>

	<div class="container">
    	<a href="/"><img src="/img/style/logo_login.png" alt="Call It Quits" class="logo" /></a>
    	<div class="body_content">
       		<?php $this->load->view($this->layout->get_view_script()); ?>

            <div class="clear"></div>
        </div>

    </div>

</body>
</html>
