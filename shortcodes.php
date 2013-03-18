<?php


/**
 * Create a javascript slideshow of each top level element in the
 * shortcode.  All attributes are optional, but may default to less than ideal
 * values.  Available attributes:
 * 
 * height     => css height of the outputted slideshow, ex. height="100px"
 * width      => css width of the outputted slideshow, ex. width="100%"
 * transition => length of transition in milliseconds, ex. transition="1000"
 * cycle      => length of each cycle in milliseconds, ex cycle="5000"
 * animation  => The animation type, one of: 'slide' or 'fade'
 *
 * Example:
 * [slideshow height="500px" transition="500" cycle="2000"]
 * <img src="http://some.image.com" .../>
 * <div class="robots">Robots are coming!</div>
 * <p>I'm a slide!</p>
 * [/slideshow]
 **/
function sc_slideshow($attr, $content=null){
	$content = cleanup(str_replace('<br />', '', $content));
	$content = DOMDocument::loadHTML($content);
	$html    = $content->childNodes->item(1);
	$body    = $html->childNodes->item(0);
	$content = $body->childNodes;
	
	# Find top level elements and add appropriate class
	$items = array();
	foreach($content as $item){
		if ($item->nodeName != '#text'){
			$classes   = explode(' ', $item->getAttribute('class'));
			$classes[] = 'slide';
			$item->setAttribute('class', implode(' ', $classes));
			$items[] = $item->ownerDocument->saveXML($item);
		}
	}
	
	$animation = ($attr['animation']) ? $attr['animation'] : 'slide';
	$height    = ($attr['height']) ? $attr['height'] : '100px';
	$width     = ($attr['width']) ? $attr['width'] : '100%';
	$tran_len  = ($attr['transition']) ? $attr['transition'] : 1000;
	$cycle_len = ($attr['cycle']) ? $attr['cycle'] : 5000;
	
	ob_start();
	?>
	<div 
		class="slideshow <?=$animation?>"
		data-tranlen="<?=$tran_len?>"
		data-cyclelen="<?=$cycle_len?>"
		style="height: <?=$height?>; width: <?=$width?>;"
	>
		<?php foreach($items as $item):?>
		<?=$item?>
		<?php endforeach;?>
	</div>
	<?php
	$html = ob_get_clean();
	
	return $html;
}
add_shortcode('slideshow', 'sc_slideshow');


function sc_search_form() {
	ob_start();
	?>
	<div class="search">
		<?get_search_form()?>
	</div>
	<?
	return ob_get_clean();
}
add_shortcode('search_form', 'sc_search_form');


/**
 * Include the defined publication, referenced by pub title:
 *
 *     [publication name="Where are the robots Magazine"]
 **/
function sc_publication($attr, $content=null){
	$pub      = @$attr['pub'];
	$pub_name = @$attr['name'];
	$pub_id   = @$attr['id'];
	
	if (!$pub and is_numeric($pub_id)){
		$pub = get_post($pub);
	}
	if (!$pub and $pub_name){
		$pub = get_page_by_title($pub_name, OBJECT, 'publication');
	}
	
	$pub->url   = get_post_meta($pub->ID, "publication_url", True);
	$pub->thumb = get_the_post_thumbnail($pub->ID, 'publication-thumb');
	
	ob_start(); ?>
	
	<div class="pub">
		<a class="track pub-track" title="<?=$pub->post_title?>" data-toggle="modal" href="#pub-modal-<?=$pub->ID?>">
			<?=$pub->thumb?>
			<span><?=$pub->post_title?></span>
		</a>
		<p class="pub-desc"><?=$pub->post_content?></p>
		<div class="modal hide fade" id="pub-modal-<?=$pub->ID?>" role="dialog" aria-labelledby="<?=$pub->post_title?>" aria-hidden="true">
			<iframe src="<?=$pub->url?>" width="100%" height="100%" scrolling="no"></iframe>
			<a href="#" class="btn" data-dismiss="modal">Close</a>
		</div>
	</div>
	
	<?php
	return ob_get_clean();
}
add_shortcode('publication', 'sc_publication');



function sc_person_picture_list($atts) {
	$categories		= ($atts['categories']) ? $atts['categories'] : null;
	$org_groups		= ($atts['org_groups']) ? $atts['org_groups'] : null;
	$limit			= ($atts['limit']) ? (intval($atts['limit'])) : -1;
	$join			= ($atts['join']) ? $atts['join'] : 'or';
	
	$args 			= array(
						'post_type' 	 => 'person',
						'posts_per_page' => $limit,
						'tax_query' 	 => array(
							'relation' 		=> $join,
							array(
								'taxonomy' 		=> 'org_groups',
								'field' 		=> 'slug',
								'terms' 		=> $org_groups,
							),
							array(
								'taxonomy' 		=> 'category',
								'field' 		=> 'slug',
								'terms' 		=> $categories,
							),
						),
						'meta_key'		=> 'person_orderby_name',		
						'orderby'		=> 'meta_value',
						'order'			=> 'ASC',
					);
	$people			= get_posts($args);
	
	ob_start();
	
	?><div class="person-picture-list"><?
	$count = 0;
	foreach($people as $person) {
		
		$image_url = (get_featured_image_url($person->ID)) ? get_featured_image_url($person->ID) : get_bloginfo('stylesheet_directory').'/static/img/no-photo.jpg';
		$title = get_post_meta($person->ID, 'person_jobtitle', true);
		$link = ($person->post_content != '') ? True : False;
		$email = get_post_meta($person->ID, 'person_email', true);
		
		?>
		<div class="person-picture-wrap">
			<div class="person-img">
				<? if($link) {?><a href="<?=get_permalink($person->ID)?>"><? } ?>
				<img src="<?=$image_url?>" />
				<? if($link) {?></a><? } ?>
			</div>
			<div class="person-info">
				<h4>
				<? if($link) {?><a href="<?=get_permalink($person->ID)?>"><? } ?>
				<span class="name"><?=Person::get_name($person)?><? if ($title != '') { ?></span><span class="title">, <?=$title?></span><? } else { ?></span><? } ?>
				<? if($link) {?></a><? } ?>
				</h4>
				<? if ($email != '') { ?><p class="email"><i class="icon-envelope"></i> <a href="mailto:<?=$email?>"><?=$email?></a></p><? } ?>
				<? if($link) {?><div class="bio"><?=apply_filters('the_content', $person->post_content);?></div><? } ?>
			</div>
		</div>
		<?
		$count++;
	}
	?>
	</div>
	<?
	return ob_get_clean();
}
add_shortcode('person-picture-list', 'sc_person_picture_list');

/**
 * Post search
 *
 * @return string
 * @author Chris Conover
 **/
function sc_post_type_search($params=array(), $content='') {
	$defaults = array(
		'post_type_name'         => 'post',
		'taxonomy'               => 'category',
		'show_empty_sections'    => false,
		'non_alpha_section_name' => 'Other',
		'column_width'           => 'span4',
		'column_count'           => '3',
		'order_by'               => 'title',
		'order'                  => 'ASC',
		'show_sorting'           => True,
		'default_sorting'        => 'term',
		'show_sorting'           => True
	);

	$params = ($params === '') ? $defaults : array_merge($defaults, $params);

	$params['show_empty_sections'] = (bool)$params['show_empty_sections'];
	$params['column_count']        = is_numeric($params['column_count']) ? (int)$params['column_count'] : $defaults['column_count'];
	$params['show_sorting']        = (bool)$params['show_sorting'];

	if(!in_array($params['default_sorting'], array('term', 'alpha'))) {
		$params['default_sorting'] = $default['default_sorting'];
	}

	// Resolve the post type class
	if(is_null($post_type_class = get_custom_post_type($params['post_type_name']))) {
		return '<p>Invalid post type.</p>';
	}
	$post_type = new $post_type_class;

	// Set default search text if the user didn't
	if(!isset($params['default_search_text'])) {
		$params['default_search_text'] = 'Find a '.$post_type->singular_name;
	}

	// Register if the search data with the JS PostTypeSearchDataManager
	// Format is array(post->ID=>terms) where terms include the post title
	// as well as all associated tag names
	$search_data = array();
	foreach(get_posts(array('numberposts' => -1, 'post_type' => $params['post_type_name'])) as $post) {
		$search_data[$post->ID] = array($post->post_title);
		foreach(wp_get_object_terms($post->ID, 'post_tag') as $term) {
			$search_data[$post->ID][] = $term->name;
		}
	}
	?>
	<script type="text/javascript">
		if(typeof PostTypeSearchDataManager != 'undefined') {
			PostTypeSearchDataManager.register(new PostTypeSearchData(
				<?=json_encode($params['column_count'])?>,
				<?=json_encode($params['column_width'])?>,
				<?=json_encode($search_data)?>
			));
		}
	</script>
	<?

	// Split up this post type's posts by term
	$by_term = array();
	foreach(get_terms($params['taxonomy']) as $term) {
		$posts = get_posts(array(
			'numberposts' => -1,
			'post_type'   => $params['post_type_name'],
			'tax_query'   => array(
				array(
					'taxonomy' => $params['taxonomy'],
					'field'    => 'id',
					'terms'    => $term->term_id
				)
			),
			'orderby'     => $params['order_by'],
			'order'       => $params['order']
		));

		if(count($posts) == 0 && $params['show_empty_sections']) {
			$by_term[$term->name] = array();
		} else {
			$by_term[$term->name] = $posts;
		}
	}

	// Split up this post type's posts by the first alpha character
	$by_alpha = array();
	$by_alpha_posts = get_posts(array(
		'numberposts' => -1,
		'post_type'   => $params['post_type_name'],
		'orderby'     => 'title',
		'order'       => 'alpha'
	));
	foreach($by_alpha_posts as $post) {
		if(preg_match('/([a-zA-Z])/', $post->post_title, $matches) == 1) {
			$by_alpha[strtoupper($matches[1])][] = $post;
		} else {
			$by_alpha[$params['non_alpha_section_name']][] = $post;
		}
	}
	ksort($by_alpha);

	if($params['show_empty_sections']) {
		foreach(range('a', 'z') as $letter) {
			if(!isset($by_alpha[strtoupper($letter)])) {
				$by_alpha[strtoupper($letter)] = array();
			}
		}
	}

	$sections = array(
		'post-type-search-term'  => $by_term,
		'post-type-search-alpha' => $by_alpha,
	);

	ob_start();
	?>
	<div class="post-type-search">
		<div class="post-type-search-header">
			<form class="post-type-search-form" action="." method="get">
				<label style="display:none;">Search</label>
				<input type="text" class="span3" placeholder="<?=$params['default_search_text']?>" />
			</form>
		</div>
		<div class="post-type-search-results "></div>
		<? if($params['show_sorting']) { ?>
		<div class="btn-group post-type-search-sorting">
			<button class="btn<?if($params['default_sorting'] == 'term') echo ' active';?>"><i class="icon-list-alt"></i></button>
			<button class="btn<?if($params['default_sorting'] == 'alpha') echo ' active';?>"><i class="icon-font"></i></button>
		</div>
		<? } ?>
	<?

	foreach($sections as $id => $section) {
		$hide = false;
		switch($id) {
			case 'post-type-search-alpha':
				if($params['default_sorting'] == 'term') {
					$hide = True;
				}
				break;
			case 'post-type-search-term':
				if($params['default_sorting'] == 'alpha') {
					$hide = True;
				}
				break;
		}
		?>
		<div class="<?=$id?>"<? if($hide) echo ' style="display:none;"'; ?>>
			<? foreach($section as $section_title => $section_posts) { ?>
				<? if(count($section_posts) > 0 || $params['show_empty_sections']) { ?>
					<div>
						<h3><?=esc_html($section_title)?></h3>
						<div class="row">
							<? if(count($section_posts) > 0) { ?>
								<? $posts_per_column = ceil(count($section_posts) / $params['column_count']); ?>
								<? foreach(range(0, $params['column_count'] - 1) as $column_index) { ?>
									<? $start = $column_index * $posts_per_column; ?>
									<? $end   = $start + $posts_per_column; ?>
									<? if(count($section_posts) > $start) { ?>
									<div class="<?=$params['column_width']?>">
										<ul>
										<? foreach(array_slice($section_posts, $start, $end) as $post) { ?>
											<li data-post-id="<?=$post->ID?>"><?=$post_type->toHTML($post)?></li>
										<? } ?>
										</ul>
									</div>
									<? } ?>
								<? } ?>
							<? } ?>
						</div>
					</div>
				<? } ?>
			<? } ?>
		</div>
		<?
	}
	?> </div> <?
	return ob_get_clean();
}
add_shortcode('post-type-search', 'sc_post_type_search');


/**
 * Output site contact info.
 *
 * @return string
 * @author Jo Greybill
 **/
function sc_contact_info($attrs) {
	$location = $attrs['location'];
	$options = get_option(THEME_OPTIONS_NAME);
	
	switch ($location) {
		case $options['location_name_1']:
		default:
			$locationnum = 1;
			break;
		case $options['location_name_2']:
			$locationnum = 2;
			break;
		case $options['location_name_3']:
			$locationnum = 3;
			break;
	}
	
	$output = '';
		
	$output .= '<span class="location-name-'.$locationnum.'"><strong>'.$options['location_name_'.$locationnum].'</strong></span>';
	if($options['location_address_'.$locationnum]) { $output .= '<br/><span class="location-address-'.$locationnum.'">'.nl2br($options['location_address_'.$locationnum]).'</span>'; }
	if($options['location_phone_'.$locationnum]) { $output .= '<br/><span class="location-phone-'.$locationnum.'">'.$options['location_phone_'.$locationnum].'</span>'; }
	if($options['location_fax_'.$locationnum]) { $output .= '<br/><span class="location-fax-'.$locationnum.'">'.$options['location_fax_'.$locationnum].' fax</span>'; }
		
	return $output;
}
add_shortcode('site-contact-info', 'sc_contact_info');


/**
 * Output site contact email, as defined in Theme Options.
 *
 * @return string
 * @author Jo Greybill
 **/
function sc_contact_email() {
	$options = get_option(THEME_OPTIONS_NAME);
	ob_start();
	
	if($options['site_contact']) { 
		print '<a class="contact-email" href="mailto:'.$options['site_contact'].'">'.$options['site_contact'].'</a>'; 
	}
	else { print 'No site contact email provided.'; }
	
	return ob_get_clean(); 
}
add_shortcode('site-contact-email', 'sc_contact_email');

/**
 * Output site contact email, as defined in Theme Options.
 *
 * @return string
 * @author Jo Greybill
 **/
function sc_contact_phone() {
	$options = get_option(THEME_OPTIONS_NAME);
	ob_start();
	
	if($options['site_phone']) { 
		print '<a class="contact-phone" href="tel:'.$options['site_phone'].'">'.$options['site_phone'].'</a>'; 
	}
	else { print 'No site contact phone number provided.'; }
	
	return ob_get_clean(); 
}
add_shortcode('site-contact-phone', 'sc_contact_phone');


/**
 * Create a page subheader (h3) that correlates to an item that is
 * dynamically added to a sidebar.
 *
 * Essentially, all this shortcode does is offer an easy way
 * for users to specify an anchor on the page without much
 * HTML knowledge.
 *
 * @return string
 * @author Jo Greybill
 **/
function sc_sidebar_subheader($atts, $content = null) {
	return '<h3 class="title-sidebarnav" id="'.sanitize_title($content).'">'.do_shortcode($content).'</h3>';
}
add_shortcode('sidebar-subheader', 'sc_sidebar_subheader');


/**
 * Displays a list of FAQs by FAQGroup
 *
 *		[faqs group="Housing and Campus Life"]
 *
 **/
function sc_faqs($attr) {		
	$group_name 	= @$attr['group'];
	$group_id		= get_term_by('name', $group_name, 'faq_groups') ? get_term_by('name', $group_name, 'faq_groups')->term_id : 'faq-group-all';
	$args			= array(
		'post_type'		=> 'faq',
		'numberposts'	=> -1,
		'order'			=> 'ASC',
		'orderby'		=> 'post_date',
	);
	// if a group is specified, add on a taxonomy array to $args
	if ($group_id !== 'faq-group-all') {
		$tax_array = array(
			'tax_query' 	=> array(
				array(
					'taxonomy' 	=> 'faq_groups',
					'field' 	=> 'id',
					'terms' 	=> $group_id,
				)
			),
		);
		$args = array_merge($args, $tax_array);
	}
	// get the faqs
	$faqs 			= get_posts($args);
	
	if (count($faqs) < 1){ 
		return 'No FAQs found.'; 
	}
	else {	
		ob_start(); ?>
		
		<div class="accordion" id="faqgroup-<?=$group_id?>">
			<?php foreach ($faqs as $faq) { ?>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" href="#faq-<?=$faq->ID?>" data-parent="#faqgroup-<?=$group_id?>" data-toggle="collapse"><?=$faq->post_title?></a>
				</div>
				<div class="accordion-body collapse" id="faq-<?=$faq->ID?>">
					<div class="accordion-inner">
						<?=apply_filters('the_content', $faq->post_content)?>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>	
		
		<?php
		$html = ob_get_clean();
		return $html;
	}
	
}
add_shortcode('faqs', 'sc_faqs');


/**
 * Returns HTML markup for a link to a page, specified by its title.
 * A custom post type can be specified (default is 'page').  This parameter
 * requires the standard name for a post type (not the capitalized 'nicename').
 *
 * A custom class for the link can also be specified (default none).
 *
 * @return string
 * @author Jo Greybill
 **/
function sc_page_link($atts, $content=null) {
	$post_title = $atts['title'] ? $atts['title'] : NULL;
	$post_type = ($atts['post_type'] && post_type_exists($atts['post_type'])) ? $atts['post_type'] : 'page';
	$css_class = $atts['class'] ? $atts['class'] : NULL;
	
	if (!$post_title) { return 'No post title specified.'; } 
	else {
		$found_post = get_page_by_title($post_title, 'OBJECT', $post_type);
		if (!$found_post) { return 'No post found with post type "'.$post_type.'" and title "'.$post_title.'".'; }
		else {
			$link = get_permalink($found_post->ID);
			
			$output = '<a';
			if ($css_class) { 
				$output .= ' class="'.$css_class.'"'; 
			}
			$output .= ' href="'.$link.'">'.$content.'</a>';
			
			return $output;
		}
	}
}
add_shortcode('page-link', 'sc_page_link');
?>