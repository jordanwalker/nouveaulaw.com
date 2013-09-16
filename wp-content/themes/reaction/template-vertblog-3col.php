<?php
/*
 * Template Name: Traditional Blog (Big Images)
*/

get_header(); 
?>


<!-- Super Container -->
<div class="super-container full-width main-content-area" id="section-content">

	<!-- 960 Container -->
	<div class="container">		
		
		<?php get_template_part( 'element', 'pagecaption' ); ?>	
		
		
		<!-- CONTENT -->
		<div class="eleven columns content">		
			
			<!-- Page Title -->
			<?php if(get_custom_field('hide_title') == 'Yes') : else : ?>
			<div class="eleven columns content">			
				<h1 class="title"><span><?php the_title(); ?></span></h1>	
			</div>
			<?php endif; ?>
			
			<!-- Page Content (if it exists) -->
			<?php while ( have_posts() ) : the_post(); ?>	
			<div class="eleven columns content">
				<?php the_content(); ?>			
			</div>	
			<?php endwhile; ?>	
			
			
			<!-- ============================================== -->			
			
			
			<!-- CATEGORY QUERY + START OF THE LOOP -->
			<?php get_template_part( 'element', 'categoryfilterquery' ); ?>
			<?php while (have_posts()) : the_post(); ?>		
									
			
								<!-- THE POST EXCERPT -->	
				<div class="the_content post type-post hentry excerpt clearfix" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	
					
					<hr class="partial-bottom" />
					
					<div class="my-avatar">
						<a href="<?php the_permalink(); ?>">
							<?php echo get_avatar( get_the_author_meta('email'), '32' ); ?>
						</a>
					</div>		
					
					<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
					
					<div class="meta"> 
						<?php the_category(', ') ?>. <?php comments_popup_link(__ ('No Comments', 'skeleton'), __ ('1 Comment', 'skeleton'), __ngettext ('% comment', '% comments', get_comments_number (),'skeleton')); ?>							
					</div>	 
				 	
				 	
				 	<!-- Thumbnail + Excerpt-->
				 	<?php if (has_post_thumbnail( $post->ID )) {
				 		
						// Check for Sencha preferences, set the variable if the user wants it.
						// Unused as of 1.04 for the time being until some bugs get sorted out
				 		if (get_option_tree('sencha') == 'Yes') { 
							$sencha = 'http://src.sencha.io/';
						} else {
							$sencha = '';
						} 
						
						// Grab the URL for the thumbnail (featured image)
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); 
						
						// Check for a lightbox link, if it exists, use that as the value. 
						// If it doesn't, use the featured image URL from above.
						if(get_custom_field('lightbox_link')) { 							
							$lightbox_link = get_custom_field('lightbox_link'); 							
						} else {							
							$lightbox_link = $image[0];							
						}
						
						?>
						<div class="eleven columns alpha">
							<div class="aside"> 
								<a href="<?php if (get_option_tree('open_as_lightbox') == 'Yes') { echo $lightbox_link; } else { the_permalink(); } ?>" <?php if (get_option_tree('open_as_lightbox') == 'Yes') { ?>data-rel="prettyPhoto[Gallery]"<?php } ?>>
									<img src="<?php echo $sencha; ?><?php echo $image[0]; ?>" alt="<?php the_title(); ?>" />
								</a>
							</div> 
						</div>															
					 
						<div class="eleven columns omega">
							<div class="date">
								<p><span><?php the_time('jS');?></span>	
								<?php the_time('F');?>							
								</p>
							</div>
								
							<?php the_excerpt(); ?>
						</div> 
						
						<br class="clearfix" />
					
					<?php } else { ?>	 
						<div>
							<div class="date">
								<p><span><?php the_time('jS');?></span>	
								<?php the_time('F');?>							
								</p>
							</div>
							
							<?php the_excerpt(); ?>
						</div> 
					<?php } ?>					
					
					
					<!-- META AREA -->
					<div class="meta-space">					
						<div class="tags clearfix <?php echo get_option_tree('tags_color'); ?>">
							<img src="<?php echo WP_THEME_URL; ?>/assets/images/theme/tag.png" class="tag_icon" />
							<?php the_tags(' ',' '); ?>				
						</div>				
					</div> 
					<!-- /META AREA -->
				</div>
				<!-- /THE POST EXCERPT -->
				
							
			<?php endwhile; ?>
			<!-- /STOP LOOP -->
			
			
			<!-- ============================================== -->		
			
		
		<!-- Previous / More Entries -->
		<!-- <br /> -->
		<!-- <hr /> -->
		<div class="article_nav">
			<div class="p button"><?php next_posts_link(__('Previous Posts', 'skeleton')); ?></div>
			<div class="m button"><?php previous_posts_link(__('Next Posts', 'skeleton')); ?></div>
		</div>
		<br class="clearfix" />
		<!-- </Previous / More Entries -->
		
		</div>	
		<!-- /CONTENT -->
		
		
		<!-- ============================================== -->
		
		<!-- SIDEBAR -->
		<div class="five columns sidebar sidebar-1">
			
			<?php dynamic_sidebar( 'default-widget-area' ); ?>	
				
		</div>
		<!-- /SIDEBAR -->
		
		<!-- SIDEBAR --> 
		<div class="two columns sidebar sidebar-2">
			
			<?php dynamic_sidebar( 'bottom-left-rail' ); ?>	
				
		</div>
		<!-- /SIDEBAR -->	
		
		
		<!-- SIDEBAR --> 
		<div class="three columns sidebar sidebar-3">
			
			<?php dynamic_sidebar( 'bottom-right-rail' ); ?>	
				
		</div>
		<!-- /SIDEBAR -->			
				

	</div>
	<!-- /End 960 Container -->
	
</div>
<!-- /End Super Container -->


<!-- ============================================== -->


<?php get_footer(); ?>