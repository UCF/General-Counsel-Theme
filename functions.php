<?php
require_once('functions/base.php');   			# Base theme functions
require_once('functions/feeds.php');			# Where functions related to feed data live
require_once('custom-taxonomies.php');  		# Where per theme taxonomies are defined
require_once('custom-post-types.php');  		# Where per theme post types are defined
require_once('functions/admin.php');  			# Admin/login functions
require_once('functions/config.php');			# Where per theme settings are registered
require_once('shortcodes.php');         		# Per theme shortcodes

//Add theme-specific functions here.


/**
 * Allow shortcodes in widgets
 **/
add_filter('widget_text', 'do_shortcode');


/**
 * Add sidebar links to a sidebar nav element, based on
 * header elements designated in the post content specified.
 * Intended for a single level of navigational elements (no
 * child links.)
 * Returns the nav list markup if successful.
 * 
 * @return string
 * @author Jo Greybill
 **/
function create_sidebar_nav($post_id) {
	$post = get_post($post_id);
	if (!$post) { return 'Invalid post ID, or post does not exist.'; }
	else {
		$links = array();
		$post_content = apply_filters('the_content', $post->post_content); // parse shortcodes
		
		if (!$post_content || $post_content == '') { return 'No post content found.'; } 
		else {
			
			// Disable warnings for bad markup, because it's so prevalent
			libxml_use_internal_errors(true);
			// Use PHP DomDocument class to load up the post content
			$dom 	= new DomDocument();
			$dom->loadHtml($post_content);
			
			// Traverse the DOM and add each .title-sidebarnav's ID and value to the array of links
			$xpath 	= new DomXPath($dom);
			foreach ($xpath->query('//*[@class = "title-sidebarnav"]') as $element) {
				foreach ($element->attributes as $attr) {
					if ($attr->nodeName == 'id') { 
						$links[$attr->nodeValue] .= $element->nodeValue; 
					}
				}
			}
		}
		
		if (empty($links)) { return 'No designated sidebar nav elements found in post content.'; }
		else {
			// Add anchor links to the stacked nav list based on the element ID's collected
			$output = '<ul class="span3 nav nav-tabs nav-stacked">';
			foreach ($links as $link=>$val) {
				$output .= '<li><a href="#'.$link.'">'.$val.'<i class="icon-chevron-right pull-right"></i></a></li>';
			}
			$output .= '</ul>';
			
			// Finally, return html content for nav list:
			return $output;
		}
	}
}

?>