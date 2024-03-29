<?php
/*
 * Template Name: Hybrid Blog (3-columns)
*/

get_header(); 
?>


<!-- Super Container -->
<div class="super-container full-width main-content-area hybrid-blog-3" id="section-content">

	<!-- 960 Container -->
	<div class="container">		
		
		<?php get_template_part( 'element', 'pagecaption' ); ?>
		
		<!-- CONTENT -->
		<div class="sixteen columns content">		
			
			<!-- Page Title -->
			<?php if(get_custom_field('hide_title') == 'Yes') : else : ?>
			<div class="sixteen columns content">			
				<h1 class="title"><span><?php the_title(); ?></span></h1>	
			</div>
			<?php endif; ?>
			
			<!-- Page Content (if it exists) -->
			<?php while ( have_posts() ) : the_post(); ?>	
			<div class="sixteen columns content">
				<?php the_content(); ?>			
			</div>	
			<?php endwhile; ?>	
		</div>
			
			<!-- ============================================== -->	
			
	</div>
	<div class="container">			
			
			<!-- CATEGORY QUERY + START OF THE LOOP -->
			<?php get_template_part( 'element', 'categoryfilterquery' ); ?>
			<?php while (have_posts()) : the_post(); ?>		
									
				<div class="one-third column hybrid">
				
				
				<!-- THE POST EXCERPT -->	
				<div class="the_content post type-post hentry excerpt clearfix" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	
					
					<hr class="partial-bottom" />
						
					
					<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
					 
				 	
				 	
				 	<!-- Thumbnail + Excerpt-->
				 	<?php if (has_post_thumbnail( $post->ID )) {
				 								
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
						<div class="">
							<div class="aside"> 
								<a href="<?php if (get_option_tree('open_as_lightbox') == 'Yes') { echo $lightbox_link; } else { the_permalink(); } ?>" <?php if (get_option_tree('open_as_lightbox') == 'Yes') { ?>data-rel="prettyPhoto[Gallery]"<?php } ?>>
									<img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>" />
								</a>
							</div> 
						</div>															
					 
						<div class="">
							<?php echo excerpt(30); ?>
							<a href="<?php the_permalink(); ?>" class="readmore"> <?php _e('Read More', 'skeleton') ?> &raquo;</a>
						</div> 
						
					
					<?php } else { ?>	 
						<div>
							<?php echo excerpt(30); ?>
							<a href="<?php the_permalink(); ?>" class="readmore"> <?php _e('Read More', 'skeleton') ?> &raquo;</a>
						</div> 
					<?php } ?>					
					
				
					
				</div>
				<!-- /THE POST EXCERPT -->
				
				
				</div>
							
			<?php endwhile; ?>
			<!-- /STOP LOOP -->
			
			
			<!-- ============================================== -->		
			
		<div class="sixteen columns content">	
		
		<!-- Previous / More Entries -->
		<br />
		<hr />
		<div class="article_nav">
			<div class="p button"><?php next_posts_link(__('Previous Posts', 'skeleton')); ?></div>
			<div class="m button"><?php previous_posts_link(__('Next Posts', 'skeleton')); ?></div>
		</div>
		<br class="clearfix" />
		<!-- </Previous / More Entries -->
		
		</div>	
		<!-- /CONTENT -->
		
		
		<!-- ============================================== -->
			
				

	</div>
	<!-- /End 960 Container -->
	
</div>
<!-- /End Super Container -->


<!-- ============================================== -->


<?php get_footer(); ?>