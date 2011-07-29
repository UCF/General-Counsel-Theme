<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?="\n".header_()."\n"?>
		<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<style>article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}</style>
		<link href="http://cdn.ucf.edu/webcom/-/css/blueprint-ie.css" rel="stylesheet" media="screen, projection">
		<![endif]-->
		<script type="text/javascript">
			<?php if(GA_ACCOUNT):?>
			
			var GA_ACCOUNT      = '<?=GA_ACCOUNT?>';
			<?php endif;?>
			<?php if(CB_UID):?>
			
			var CB_UID          = '<?=CB_UID?>';
			var CB_DOMAIN       = '<?=CB_DOMAIN?>';
			<?php endif?>
			
			var _sf_startpt     = (new Date()).getTime();
			var _gaq            = _gaq || [];
		</script>
	</head>
	<body class="<?=body_classes()?>">
		<div id="blueprint-container" class="container">
			<div id="header" class="span-24 last">
				<h1 class="span-10 sans"><a href="<?=bloginfo('url')?>"><?=bloginfo('name')?></a></h1>
				<div class="span-14 last">
				<?=get_menu('header-menu', 'menu horizontal', 'header-menu')?>
				</div>
			</div>