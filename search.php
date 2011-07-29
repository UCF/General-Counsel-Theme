<?php
	$options = get_option(THEME_OPTIONS_NAME);
	$domain  = $options['search_domain'];
	$limit   = (int)$options['search_per_page'];
	$start   = (is_numeric($_GET['start'])) ? (int)$_GET['start'] : 0;
	$results = get_search_results($_GET['s'], $start, $limit, $domain);
?>
<?php get_header();?>
	<div class="page-content" id="search-results">
		<div class="results span-16 append-2">
			<h2>Search results</h2>
			<?php get_search_form()?>
			
			<?php if(count($results['items'])):?>
				
			<ul class="result-list">
				<?php foreach($results['items'] as $result):?>
				<li class="item">
					<h3>
						<a class="ignore-external" href="<?=$result['url']?>">
							<span class="title sans <?=mimetype_to_application($result['mime'])?>"><?=$result['title']?></span>
							<span class="url sans"><?=$result['url']?></span>
						</a>
					</h3>
					<div class="snippet sans">
						<?=str_replace('<br>', '', $result['snippet'])?>
					</div>
				</li>
				<?php endforeach;?>
			</ul>
			
			<?php if($start + $limit < $results['number']):?>
			<a class="button more" href="./?s=<?=$_GET['s']?>&amp;start=<?=$start + $limit?>">More Results</a>
			<?php endif;?>
			
			<?php else:?>
				
			<p>No results found for "<?=htmlentities($_GET['s'])?>".</p>
			
			<?php endif;?>
		</div>
		
		<?php
			// Remove search widget if included, redundant
			ob_start();
			get_search_form();
			$search = ob_get_clean();
			
			ob_start();
			get_sidebar();
			$sidebar = str_replace($search, '', ob_get_clean());
		?>
		<div id="sidebar" class="span-6 last">
			<?=$sidebar?>
		</div>
		
		<div class="clear"><!-- --></div>
		<?php get_template_part('templates/below-the-fold'); ?>
	</div>
<?php get_footer();?>