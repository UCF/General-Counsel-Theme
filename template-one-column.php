<?php
/**
 * Template Name: One Column
 **/
?>
<?php get_header(); the_post();?>
	<div class="row page-content" id="<?=$post->post_name?>">
		<div class="span12">
			<article>
				<? if(!is_front_page())	{ ?>
						<h2><?php the_title();?></h2>
				<? } ?>
				<?php the_content();?>
			</article>
		</div>
	</div>
	<?
	if(get_post_meta($post->ID, 'page_show_fold', True) == 'on'): 
		get_template_part('includes/below-the-fold'); 
	endif
	?>
<?php get_footer();?>