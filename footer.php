			<div class="row">
				<div class="span12" id="footer">
				
					<?=wp_nav_menu(array(
						'theme_location' => 'footer-menu', 
						'container' => 'false', 
						'menu_class' => 'menu horizontal', 
						'menu_id' => 'footer-menu', 
						'fallback_cb' => false,
						'depth' => 1,
						'walker' => new Bootstrap_Walker_Nav_Menu()
						));
					?>
					<div class="row" id="footer-widget-wrap">
						<div class="footer-widget-1 span3">
							<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Footer - Column One')):?>
								<a class="ignore-external" href="http://www.ucf.edu"><img src="<?=THEME_IMG_URL?>/logo.png" alt="" title="" /></a>
							<?php endif;?>
						</div>
						<div class="footer-widget-2 span4">
							<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Footer - Column Two')):?>
								<?php $options = get_option(THEME_OPTIONS_NAME);?>
								<?php if($options['site_contact'] or $options['organization_name']):?>
									<div class="maintained">
										Site maintained by the <br />
										<?php if($options['site_contact'] and $options['organization_name']):?>
										<a href="mailto:<?=$options['site_contact']?>"><?=$options['organization_name']?></a>
										<?php elseif($options['site_contact']):?>
										<a href="mailto:<?=$options['site_contact']?>"><?=$options['site_contact']?></a>
										<?php elseif($options['organization_name']):?>
										<?=$options['organization_name']?>
										<?php endif;?>
									</div>
									<?php endif;?>
								<div class="copyright">&copy; University of Central Florida</div>
							<?php endif;?>
						</div>
						<div class="footer-widget-3 span3 offset2">
							<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Footer - Column Three')):?>
								<div id="contact-widget">
									<div class="contact-widget-icon" id="contact-widget-phone">
										<a href="<?=get_permalink(get_page_by_title('Contact'))?>"></a>
										<div class="contact-widget-bubble-wrap">
											<div class="contact-widget-bubble">
												<strong class="contact-widget-title">Phone</strong>
												<p>Primary Phone: <?=do_shortcode('[site-contact-phone]')?></p>
												<p>(Click for more contact info)</p>
											</div>
										</div>
									</div>
									<div class="contact-widget-icon" id="contact-widget-map">
										<a href="<?=get_permalink(get_page_by_title('Contact'))?>"></a>
										<div class="contact-widget-bubble-wrap">
											<div class="contact-widget-bubble">
												<strong class="contact-widget-title">Map</strong>
												<p>(Click to view map)</p>
											</div>
										</div>
									</div>
									<div class="contact-widget-icon" id="contact-widget-email">
										<?=do_shortcode('[site-contact-email]')?>
										<div class="contact-widget-bubble-wrap">
											<div class="contact-widget-bubble">
												<strong class="contact-widget-title">Email</strong>
												<p>Email: <?=do_shortcode('[site-contact-email]')?></p>
											</div>
										</div>
									</div>
								</div>
							<?php endif;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
	<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?="\n".footer_()."\n"?>
</html>