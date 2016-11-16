<?php
/**
 * Core theme functions
 *
 * @package Fluida
 */

/**
 * Header image handler
 * Both as normal img and background image
 */
add_action ( 'cryout_headerimage_hook', 'fluida_header_image', 99 );
if ( ! function_exists( 'fluida_header_image' ) ) :
function fluida_header_image() {
	$limit = 0.75;
	$fluida_fheader = cryout_get_option( 'fluida_fheader' );
	$fluida_headerh = floor( cryout_get_option( 'fluida_headerheight' ) * $limit );
	$fluida_headerw = floor( cryout_get_option( 'fluida_sitewidth' ) * $limit );

	// Check if this is a post or page, if it has a thumbnail, and if it's a big one
	global $post;

	if ( get_header_image() != '' ) { $header_image = get_header_image(); }
	if ( is_singular() && has_post_thumbnail( $post->ID ) && $fluida_fheader &&
		( $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'fluida-header' ) )
		 ) :
			if ( ( absint($image[1]) >= $fluida_headerw ) && ( absint($image[2]) >= $fluida_headerh ) ) {
				// 'header' image is large enough
				$header_image = $image[0];
			} else {
				// 'header' image too small, try 'full' image instead
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				if ( ( absint($image[1]) >= $fluida_headerw ) && ( absint($image[2]) >= $fluida_headerh ) ) {
					// 'full' image is large enough
					$header_image = $image[0];
				} else {
					// even 'full' image is too small, don't return an image
					//$header_image = false;
				}
			}
	endif;

	if ( ! empty( $header_image ) ):?>
			<div class="header-image" <?php echo cryout_echo_bgimage( esc_url( $header_image ) ) ?>></div>
			<img class="header-image" alt="" src="<?php echo esc_url( $header_image ) ?>" />
			<?php cryout_header_widget_hook(); ?>
	<?php endif;
} // fluida_header_image()
endif;


/**
 * Adds title and description to header
 * Used in header.php
*/
if ( ! function_exists( 'fluida_title_and_description' ) ) :
function fluida_title_and_description() {

	$fluids = cryout_get_option( array( 'fluida_logoupload', 'fluida_siteheader' ) );

	if ( in_array( $fluids['fluida_siteheader'], array( 'logo', 'both' ) ) ) {
		echo fluida_logo_helper( $fluids['fluida_logoupload'] );
	}
	if ( in_array( $fluids['fluida_siteheader'], array( 'title', 'both' ) ) ) {
		$heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div';
		echo '<div id="site-text">';
		echo '<' . $heading_tag . cryout_schema_microdata( 'site-title', 0 ) . ' id="site-title">';
		echo '<span> <a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'description' ) ) . '" rel="home">' . esc_attr( get_bloginfo( 'name' ) ) . '</a> </span>';
		echo '</' . $heading_tag . '>';
		echo '<span id="site-description" ' . cryout_schema_microdata( 'site-description', 0 ) . ' >' . esc_attr( get_bloginfo( 'description' ) ). '</span>';
		echo '</div>';
	}
} // fluida_title_and_description()
endif;
add_action ( 'cryout_branding_hook', 'fluida_title_and_description' );

function fluida_logo_helper( $fluida_logo ) {
	if ( function_exists( 'the_custom_logo' ) ) {
		// WP 4.5+
		$wp_logo = str_replace( 'class="custom-logo-link"', 'id="logo" class="custom-logo-link" alt="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'"', get_custom_logo() );
		if ( ! empty( $wp_logo ) ) return '<div class="identity">' . $wp_logo . '</div>';
	} else {
		// older WP
		if ( ! empty( $fluida_logo ) ) :
			$img = wp_get_attachment_image_src( $fluida_logo, 'full' );
			return '<div class="identity"><a id="logo" href="' . esc_url( home_url( '/' ) ) . '" ><img title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" alt="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" src="' . esc_url( $img[0] ) . '" /></a></div>';
		endif;
	}
	return '';
} // fluida_logo_helper()

/**
 * Fluida back to top button
 * Creates div for js
*/
if ( ! function_exists( 'fluida_back_top' ) ) :
function fluida_back_top() {
	echo '<div id="toTop"><i class="icon-back2top"></i> </div>';
} // fluida_back_top()
endif;
add_action ( 'cryout_footer_hook', 'fluida_back_top' );


/**
 * Creates pagination for blog pages.
 */
if ( ! function_exists( 'fluida_pagination' ) ) :
function fluida_pagination( $pages = '', $range = 2, $prefix ='' ) {
	$pagination = cryout_get_option( 'fluida_pagination' );
	if ( $pagination && function_exists( 'the_posts_pagination' ) ):
		the_posts_pagination( array(
			'prev_text' => '<i class="icon-left-dir"></i>',
			'next_text' => '<i class="icon-right-dir"></i>',
			'mid_size' => $range
		) );
	else:
		//posts_nav_link();
		fluida_content_nav( 'nav-old-below' );
	endif;

} // fluida_pagination()
endif;

/**
 * Prev/Next page links
 */
if ( ! function_exists( 'fluida_nextpage_links' ) ) :
function fluida_nextpage_links( $defaults ) {
	$args = array(
		'link_before'      => '<em>',
		'link_after'       => '</em>',
	);
	$r = wp_parse_args( $args, $defaults );
	return $r;
} // fluida_nextpage_links()
endif;
add_filter( 'wp_link_pages_args', 'fluida_nextpage_links' );


/**
 * Footer Hook
 */
add_action( 'cryout_master_footer_hook', 'fluida_master_footer' );
function fluida_master_footer() {
	$fluida_theme = wp_get_theme();
	do_action( 'cryout_footer_hook' );
	echo '<div id="site-copyright">' . wp_kses_post( cryout_get_option( 'fluida_copyright' ) ) . '</div>';
	echo '<div style="display:block;float:right;clear: right;font-size: .9em;">' . __( "Powered by", "fluida" ) .
		'<a target="_blank" href="' . esc_html( $fluida_theme->get( 'ThemeURI' ) ) . '" title="';
	echo 'Fluida WordPress Theme by ' . 'Cryout Creations"> ' . 'Fluida' .'</a> &amp; <a target="_blank" href="' . "http://wordpress.org/";
	echo '" title="' . __( "Semantic Personal Publishing Platform", "fluida") . '"> ' . sprintf( " %s.", "WordPress" ) . '</a></div>';
}

/*
 * Sidebar handler
*/
if ( ! function_exists( 'fluida_get_sidebar' ) ) :
function fluida_get_sidebar() {
	global $post;
	if ( get_post() ) { $fluida_meta_layout = get_post_meta( $post->ID, '_fluida_layout', true ); }

	if ( isset( $fluida_meta_layout ) && $fluida_meta_layout ) {
		$fluida_sitelayout =  $fluida_meta_layout;
	}
	else $fluida_sitelayout = cryout_get_option( 'fluida_sitelayout' );

	switch( $fluida_sitelayout ) {

		case '2cSl':
			get_sidebar( 'left' );
		break;

		case '2cSr':
			get_sidebar( 'right' );
		break;

		case '3cSl' : case '3cSr' : case '3cSs' :
			get_sidebar( 'left' );
			get_sidebar( 'right' );
		break;

		default:
		break;
	}
} // fluida_get_sidebar()
endif;

/*
 * General layout class
 */
if ( ! function_exists( 'fluida_get_layout_class' ) ) :
function fluida_get_layout_class() {
	global $post;
	if ( get_post() ) { $fluida_meta_layout = get_post_meta( $post->ID, '_fluida_layout', true ); }

	if ( isset( $fluida_meta_layout ) && $fluida_meta_layout ) {
		$fluida_sitelayout =  $fluida_meta_layout;
	}
	else $fluida_sitelayout = cryout_get_option( 'fluida_sitelayout' );

	/*  If not, return the general layout */
	switch( $fluida_sitelayout ) {
		case '2cSl': return "two-columns-left"; break;
		case '2cSr': return "two-columns-right"; break;
		case '3cSl': return "three-columns-left"; break;
		case '3cSr' : return "three-columns-right"; break;
		case '3cSs' : return "three-columns-sided"; break;
		case '1c':
		default: return "one-column"; break;
	}
} // fluida_get_layout_class()
endif;

/**
* Checks the browser agent string for mobile ids and adds "mobile" class to body if true
* @return array list of classes.
*/
function fluida_mobile_body_class( $classes ){
	$browser = ( ! empty( $_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
	$keys = 'mobile|android|mobi|tablet|ipad|opera mini|series 60|s60|blackberry';
	if ( preg_match( "/($keys)/i", $browser ) ) : $classes[] = 'mobile'; endif; // mobile browser detected
	return $classes;
} // fluida_mobile_body_class()
add_filter( 'body_class', 'fluida_mobile_body_class');


/**
* Creates breadcrumbs with page sublevels and category sublevels.
* Hooked in master hook
*/
function fluida_breadcrumbs() {
	cryout_breadcrumbs(
		'<i class="icon-angle-right"></i>',						// $separator
		'<i class="icon-homebread"></i>', 						// $home
		1,														// $showCurrent
		'<span class="current">', 								// $before
		'</span>', 												// $after
		'<div id="breadcrumbs-container" class="cryout %1$s"><div id="breadcrumbs-container-inside"><div id="breadcrumbs"> <nav id="breadcrumbs-nav" %2$s>', // $wrapper_pre
		'</nav></div></div></div><!-- breadcrumbs -->', 		// $wrapper_post
		fluida_get_layout_class(),								// $layout_class
		__( 'Home', 'fluida' ),									// $text_home
		__( 'Archive for category', 'fluida' ),					// $text_archive
		__( 'Search results for', 'fluida' ), 					// $text_search
		__( 'Posts tagged', 'fluida' ), 						// $text_tag
		__( 'Articles posted by', 'fluida' ), 					// $text_author
		__( 'Not Found', 'fluida' ),							// $text_404
		__( 'Post format', 'fluida' ),							// $text_format
		__( 'Page', 'fluida' )									// $text_page
	);
} // fluida_breadcrumbs()


/**
 * Adds searchboxes to the appropriate menu location
 * Hooked in master hook
 */
if ( ! function_exists( 'cryout_search_menu' ) ) :
function cryout_search_menu( $items, $args ) {
$options = cryout_get_option( array( 'fluida_searchboxmain', 'fluida_searchboxfooter' ) );
	if( $args->theme_location == 'primary' && $options['fluida_searchboxmain'] ) {
		$container_class = 'menu-main-search';
		$items .= "<li class='" . $container_class . " menu-search-animated'><i class='search-icon'></i>" . get_search_form( false ) . " </li>";
	}
	if( $args->theme_location == 'footer' && $options['fluida_searchboxfooter'] ) {
		$container_class = 'menu-footer-search';
		$items .= "<li class='" . $container_class . "'>" . get_search_form( false ) . "</li>";
	}
	return $items;
} // cryout_search_mainmenu()
endif;

/* cryout_schema_microdata() moved to framework in 0.9.9/0.5.6 */

/**
 * Normalizes tags widget font when needed
 */
function fluida_normalizetags( $tags_html ) {
	$fluida_normalizetags = cryout_get_option( 'fluida_normalizetags' );
	if ( $fluida_normalizetags ) return preg_replace( '/font-size:.*?;/i', '', $tags_html ); else return $tags_html;
};
add_filter( 'wp_generate_tag_cloud', 'fluida_normalizetags' );


/**
* Master hook to bypass customizer options
*/
if ( ! function_exists( 'fluida_master_hook' ) ) :
function fluida_master_hook() {
	$fluida_interim_options = cryout_get_option( array(
		'fluida_breadcrumbs',
		'fluida_searchboxmain',
		'fluida_searchboxfooter',
		'fluida_comlabels')
	);
	if ( $fluida_interim_options['fluida_breadcrumbs'] )  add_action( 'cryout_breadcrumbs_hook', 'fluida_breadcrumbs' );
	if ( $fluida_interim_options['fluida_searchboxmain'] || $fluida_interim_options['fluida_searchboxfooter'] ) add_filter( 'wp_nav_menu_items', 'cryout_search_menu', 10, 2);

	if ( $fluida_interim_options['fluida_comlabels'] == 1 ) {
		add_filter( 'comment_form_default_fields', 'fluida_comments_form' );
		add_filter( 'comment_form_field_comment', 'fluida_comments_form_textarea' );
	}

	if ( cryout_get_option( 'fluida_socials_header' ) ) 		add_action( 'cryout_header_socials_hook', 'fluida_socials_menu_header', 30 );
	if ( cryout_get_option( 'fluida_socials_footer' ) ) 		add_action( 'cryout_footer_hook', 'fluida_socials_menu_footer', 17 );
	if ( cryout_get_option( 'fluida_socials_left_sidebar' ) ) 	add_action( 'cryout_before_primary_widgets_hook', 'fluida_socials_menu_left', 5 );
	if ( cryout_get_option( 'fluida_socials_right_sidebar' ) ) 	add_action( 'cryout_before_secondary_widgets_hook', 'fluida_socials_menu_right', 5 );
};
endif;
add_action( 'wp', 'fluida_master_hook' );


// Boxes image size
function fluida_lpbox_width( $options = array() ) {

	if ( $options['fluida_lpboxlayout1'] == 1 ) { $totalwidth = $options['fluida_sitewidth']; }
	else { $totalwidth = $options['fluida_sitewidth'] - $options['fluida_primarysidebar'] - $options['fluida_secondarysidebar']; }
	$options['fluida_lpboxwidth1'] = intval ( $totalwidth / $options['fluida_lpboxrow1'] );

	if ( $options['fluida_lpboxlayout2'] == 1 ) { $totalwidth = $options['fluida_sitewidth']; }
	else { $totalwidth = $options['fluida_sitewidth'] - $options['fluida_primarysidebar'] - $options['fluida_secondarysidebar']; }
	$options['fluida_lpboxwidth2'] = intval ( $totalwidth / $options['fluida_lpboxrow2'] );

	return $options;
} // fluida_lpbox_width()

// Used for the landing page blocks auto excerpts
function fluida_custom_excerpt( $text = '', $length = 35, $more = '...' ) {
	$raw_excerpt = $text;
	if ( '' != $text ) {
		$text = strip_shortcodes( $text );

		// This filter is documented in wp-includes/post-template.php
		$text = apply_filters( 'the_content', $text );
		$text = str_replace(']]>', ']]&gt;', $text);

		// Filters the number of words in an excerpt. Default 35.
		$excerpt_length = apply_filters( 'fluida_custom_excerpt_length', $length );

		// Filters the string in the "more" link displayed after a trimmed excerpt.
		$excerpt_more = apply_filters( 'fluida_custom_excerpt_more', $more );
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	return apply_filters( 'fluida_custom_excerpt', $text, $raw_excerpt );
} // fluida_custom_excerpt()


/* FIN */
