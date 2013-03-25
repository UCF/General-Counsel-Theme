<?php $options = get_option(THEME_OPTIONS_NAME);?>
<div class="row">
	<div id="below-the-fold" class="row-border-bottom-top">
		<div class="span3" id="below-the-fold-left">
			<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Bottom Left')):?><?php endif;?>
		</div>
		<div class="span5" id="below-the-fold-center">
			<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Bottom Center')):?><?php endif;?>
		</div>
		<div class="span4" id="below-the-fold-right">
			<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Bottom Right')):?>
				<h2>Notices</h2>
				<?=do_shortcode('[notice-list]')?>
			<?php endif;?>
		</div>
	</div>
</div>