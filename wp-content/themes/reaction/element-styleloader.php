<?php ?>
<!-- THIS FILE LOADS ALL THE CUSTOMIZATIONS FROM THE THEME OPTIONS PANEL  -->


<!-- Custom CSS Modifications from the Admin Panel -->
<style type="text/css">

/* Insert the rest of the custom CSS from the admin panel */ 
<?php echo get_option_tree('customcss'); ?> 
	 
	 
	/* Add a custom bg if it exists */
	<?php $homepage_bg = get_option_tree("default_bg");
			
	if(get_custom_field('custom_background_image')) { ?>
		body, body:after {background: url('<?php echo get_custom_field('custom_background_image',true); ?>') top left fixed repeat;}
		h2.title span, ul.tabs li a.active {background: none;}
	<?php } elseif (isset($homepage_bg[0])) { ?>
	 		body, body:after {background: url('<?php echo get_option_tree("default_bg"); ?>') top left fixed repeat;}
	 		h2.title span, ul.tabs li a.active {background: none;}
	<?php } else {} ?>
	 
	<?php global $theme_options; ?>
	
	
	body, #section-tophat,
	#section-footer,
	#section-sub-footer{
		background-repeat: repeat;
	 	background-position: top center;
	 	background-attachment: fixed;
	}
	
	/* CUSTOM BG INSERTER FOR TOPHAT, FOOTER, SUBFOOTER */
	<?php $tophat_bg = get_option_tree("tophat_background_image");
		  $tophat_color = get_option_tree("tophat_background_color");
		  $footer_bg = get_option_tree("footer_background_image");
		  $footer_color = get_option_tree("footer_background_color");
		  $subfooter_bg = get_option_tree("subfooter_background_image");
		  $subfooter_color = get_option_tree("subfooter_background_color");
	?>
	
	<?php if (isset($tophat_color[0])) { ?>
	 		#section-tophat, #section-tophat:after {
	 			background-image: url('');
	 			background-color: <?php echo get_option_tree("tophat_background_color"); ?>;
 			}
	<?php } ?>
	<?php if (isset($tophat_bg[0])) { ?>
	 		#section-tophat, #section-tophat:after {
	 			background-image: url('<?php echo get_option_tree("tophat_background_image"); ?>');
 			}
	<?php } ?>
	
	<?php if (isset($footer_color[0])) { ?>
	 		#section-footer, #section-footer:after {
	 			background-image: url('');
	 			background-color: <?php echo get_option_tree("footer_background_color"); ?>;
 			}
	<?php } ?>
	<?php if (isset($footer_bg[0])) { ?>
	 		#section-footer, #section-footer:after {
	 			background-image: url('<?php echo get_option_tree("footer_background_image"); ?>');
 			}
	<?php } ?>
	
	<?php if (isset($subfooter_color[0])) { ?>
	 		#section-sub-footer, #section-sub-footer:after {
	 			background-image: url('');
	 			background-color: <?php echo get_option_tree("subfooter_background_color"); ?>;
 			}
	<?php } ?>
	<?php if (isset($subfooter_bg[0])) { ?>
	 		#section-sub-footer, #section-sub-footer:after {
	 			background-image: url('<?php echo get_option_tree("subfooter_background_image"); ?>');
 			}
	<?php } ?>
	 
	 
	/* This is your link hover color */
	<?php $link_hover_color = get_option_tree("link_hover_color"); if (isset($link_hover_color[0])) { ?>		
		#section-header li a:hover, a:hover {color: <?php echo get_option_tree('link_hover_color');?>;}
	<?php } else {} ?>	
	
	/* This is your link color */
	<?php $link_color = get_option_tree("link_color"); if (isset($link_color[0])) { ?>		
		#section-header li a, a {color: <?php echo get_option_tree('link_color'); ?>;}
		.sf-menu > li:hover{box-shadow: 0px -2px 0px <?php echo get_option_tree('link_color'); ?>;}
	<?php } else {} ?>
	
	/* This is your visited link color */
	<?php $link_visited_color = get_option_tree("link_visited_color"); if (isset($link_visited_color[0])) { ?>
		a:visited {color: <?php echo get_option_tree('link_visited_color'); ?>;}
	<?php } else {} ?>		
	
	
</style>

<!-- ALTERNATIVE HEADLINE FONT OVERRIDE - For TypeKit Insertion -->	
<?php $altfont = get_option_tree("alt_fontreplace"); if (isset($altfont[0])) { 	
	echo get_option_tree("alt_fontreplace");
	} else {} ?>
<!-- // END HEADLINE FONT OVERRIDE -->	


<!-- Hide the top bar / optional -->
<?php if (get_option_tree('top_hat') == 'No') { ?>	
	<style type="text/css">
	#section-tophat{display: none; height: 0px !important; margin: 0; padding: 0;}
	</style>
<?php } ?> 



<!-- Check for Column Flipping -->
<?php if(get_custom_field('column_flip') == 'Yes') : ?>

<style type="text/css">
	.main-content-area .eleven.columns{float: right !important;}
</style>

<?php endif; ?>


<!-- Check for Force-Hiding of the Breakout Row -->
<?php if(get_custom_field('breakout_hide') == 'Yes') : ?>

<style type="text/css">
	#breakout-row{display: none;}
</style>

<?php endif; ?>

<!-- Force the Breakout Row on just the homepage -->
<?php if(get_option_tree('homepage_breakout_section') == 'Yes') { ?>

<style type="text/css">
	.home #breakout-row{display: inherit;}
</style>

<?php } ?>