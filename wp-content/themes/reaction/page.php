<?php get_header(); ?>


<!-- ============================================== -->


<!-- Frontpage Slider Conditional -->
<?php if(get_custom_field('frontpage_slider') == 'Yes') {  
	
	if(get_option_tree('frontpage_slider') == 'Yes') { 
		get_template_part( 'element', 'flexslider' ); 
	}	

} else {}; 
?>


<!-- PageSlider Conditional -->
<?php if(get_custom_field('image_slider') == 'Yes') {
	get_template_part( 'element', 'pageslider' ); 
} else {}; ?>


<!-- ============================================== -->

 
<!-- Super Container -->
<div class="super-container main-content-area" id="section-content">

	<!-- 960 Container -->
	<div class="container">
	
		<?php get_template_part( 'element', 'pagecaption' ); ?>
		
		<!-- CONTENT -->
		<div class="eleven columns content">
			
			<!-- THE POST LOOP -->
			<?php while ( have_posts() ) : the_post(); ?>	
				
				<?php if(get_custom_field('hide_title') == 'Yes') : else : ?>
				<h1 class="title"><span><?php the_title(); ?></span></h1>
				<?php endif; ?>
				
				<!-- Featured Image -->
				<?php if(get_option_tree('show_featured_image') == 'Yes') : ?>
	
					<?php if (has_post_thumbnail( $post->ID )) {
						
						if (get_option_tree('sencha') == 'Yes') { 
							$sencha = '';
						} else {
							$sencha = '';
						} 
								
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
						<a href="<?php echo $image[0]; ?>" data-rel="prettyPhoto">
							<img class="aligncenter featured-image" src="<?php echo $sencha; ?><?php echo $image[0]; ?>" alt="<?php the_title(); ?>" />
						</a>
								
					<br class="clearfix" />					
					<?php } else {} ?>	 
					
				<?php endif; ?>
			
				<?php the_content(); ?>
				
				<?php wp_link_pages('before=<br /><div id="page-links"><span>Pages:</span>&after=</div><hr />&link_before=<div>&link_after=</div>'); ?>
				
				
				
				
			<?php endwhile; ?>	
			
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