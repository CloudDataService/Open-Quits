<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $this->layout->get_css(); ?>
<?php echo $this->layout->get_javascript(); ?>
<title><?php echo $this->layout->get_title(); ?></title>
<link rel="shortcut icon" href="/img/style/favicon.ico" type="image/x-icon" />
<link rel="icon" href="/img/style/favicon.ico" type="image/x-icon" />
</head>

<body class="<?php if ($this->session->userdata('sp_id')) echo 'sp-' . $this->session->userdata('sp_id') ?>">

	<div class="auth_container">

		<div class="auth">

			<?php if($tel_support = $this->session->userdata('tel_support')) : ?>
				<p class="support">Need help? Call us on <?php echo $tel_support; ?></p>
			<?php endif; ?>

			<p>You are logged in as <?php echo $this->session->userdata('fname') . ' ' . $this->session->userdata('sname'); ?>
			<?php if ($this->session->userdata('pct_name')) echo '(' . $this->session->userdata('pct_name') . ')'; ?>
			<a href="/logout" class="logout">Logout</a></p>

		</div>

	</div>

	<?php if ($this->session->userdata('sp_id')): ?>
		<div class="container">
			<div class="general-error ip-alert"><strong>To All Providers.</strong> If you have a query or problem please call the programme management team on 0800 531 6317.</div>
		</div>
	<?php endif; ?>

	<div class="container">
		<?php if (isset($blocked_ips) && count($blocked_ips) > 0): ?>
			<div class="general-error ip-alert">There are 1 or more entries in the blocked IPs list. <a href="/admin/options/security-options#blocked_ips">View them here</a>.</div>
		<?php endif; ?>

		<div class="banner">
			<a href="/"><img src="/img/style/logo.png" alt="Call It Quits" /></a>

			<?php if($this->session->userdata('sp_id')) : ?>
				<p><?php echo $this->session->userdata('sp_name'); ?></p>
			<?php else : ?>
				<p><img src="/img/style/NHS-logo-sotw.png" alt="NHS South of Tyne and Wear logo" /></p>
			<?php endif; ?>
		</div>

		<div class="nav">
			<?php echo $this->layout->get_nav(); ?>

			<div class="clear"></div>
		</div>

		<div class="breadcrumbs">
			<h1><?php echo $this->layout->get_breadcrumb_key(); ?></h1>
			<p><?php echo $this->layout->get_breadcrumbs(); ?></p>
		</div>

		<div class="body_content">
			<?php if($this->session->flashdata('action')) : ?>
				<div class="action">
					<p><?php echo $this->session->flashdata('action'); ?></p>
				</div>
			<?php endif; ?>

			<?php $this->load->view($this->layout->get_view_script()); ?>
			<div class="clear"></div>
		</div>
	</div>
</body>
</html>
