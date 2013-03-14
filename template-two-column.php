<?php
/**
 * Template Name: Two Column
 **/
?>
<?php get_header(); the_post();?>
	<div class="row page-content" id="<?=$post->post_name?>">
		<div id="sidebar" class="span3">
		<!--
			    <ul class="span3 nav nav-tabs nav-stacked">
					<li><a href="#">Link 1 <i class="icon-chevron-right pull-right"></i></a></li>
					<li><a href="#">Link 2 <i class="icon-chevron-right pull-right"></i></a></li>
					<li><a href="#">Link 3 <i class="icon-chevron-right pull-right"></i></a></li>
    			</ul>
				-->
				
			<?=create_sidebar_nav($post->ID);?>	
		</div>
		<div class="span9">
			<article>
				<? if(!is_front_page())	{ ?>
						<h2><?php the_title();?></h2>
				<? } ?>
				<?php the_content();?>
			</article>
		</div>
	</div>
	<?
	if(get_post_meta($post->ID, 'page_hide_fold', True) != 'on'): 
		get_template_part('includes/below-the-fold'); 
	endif
	?>
<?php get_footer();?>