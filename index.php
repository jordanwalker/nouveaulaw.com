<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
 
header("HTTP/1.1 301 Moved Permanently"); 
header("Location: http://www.sfmlawgroup.com");
 
 
 
 
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require('./wp-blog-header.php');
