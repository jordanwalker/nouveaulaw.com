<?php
/*
 * Template Name: Traditional Blog (Small Images)
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
			<div class="eleven columns content alpha">
				<?php the_content(); ?>			
			</div>	
			<?php endwhile; ?>	
			
			
			<!-- ============================================== -->			
			
			
			<!-- CATEGORY QUERY + START OF THE LOOP -->
			<?php get_template_part( 'element', 'categoryfilterquery' ); ?>
			<?php while (have_posts()) : the_post(); ?>		
									
			
				<?php get_template_part( 'element', 'excerpt' ); ?>		
				
							
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