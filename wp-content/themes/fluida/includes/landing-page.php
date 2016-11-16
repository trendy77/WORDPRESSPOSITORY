<?php
/**
 * Landing page functions
 * Used in front-page.php
 *
 * @package Fluida
 */

/**
 * slider builder
 */
if ( ! function_exists('fluida_lpslider' ) ):
function fluida_lpslider() {
	$fluids = cryout_get_option( array( 'fluida_lpslider', 'fluida_lpsliderimage', 'fluida_lpslidertitle', 'fluida_lpslidertext', 'fluida_lpslidershortcode', 'fluida_lpsliderserious' ) );
	if ( $fluids['fluida_lpslider'] )
		switch ( $fluids['fluida_lpslider'] ):
			case 1:
				if ( is_string( $fluids['fluida_lpsliderimage'] ) ) {
					$image = $fluids['fluida_lpsliderimage'];
				}
				else {
					list( $image, ) = wp_get_attachment_image_src( $fluids['fluida_lpsliderimage'], 'full' );
				}
				fluida_lpslider_output( array(
					'image' => $image,
					'title' => wp_kses_post( $fluids['fluida_lpslidertitle'] ),
					'content' => wp_kses_post( $fluids['fluida_lpslidertext'] ),
				) );
			break;
			case 2:
				?> <div class="lp-dynamic-slider"> <?php
				echo do_shortcode( $fluids['fluida_lpslidershortcode'] );
				?> </div> <!-- lp-dynamic-slider --> <?php
			break;
			case 3:
				// header image
			break;
			case 4:
				?> <div class="lp-dynamic-slider"> <?php
					if (!empty( $fluids['fluida_lpsliderserious'] ) ) {
						echo do_shortcode( '[serious-slider id="' . $fluids['fluida_lpsliderserious'] . '"]' );
					}
				?> </div> <!-- lp-dynamic-slider --> <?php
			break;

			default:
			break;
		endswitch;

} //  fluida_lpslider()
endif;

/**
 * slider output
 */
if ( ! function_exists( 'fluida_lpslider_output' ) ):
function fluida_lpslider_output( $data ){
	foreach ( $data as $key => $value ) { ${"$key"} = $value; } ?>

		<section class="lp-staticslider">
			<?php if ( $image ) { ?>
				<img class="lp-staticslider-image" alt="<?php echo wp_kses_post( $title ) ?>" src="<?php echo esc_url( $image ); ?>">
			<?php } ?>
			<div class="staticslider-caption">
				<?php if ( $title ) { ?> <h1 class="staticslider-caption-title"><?php echo wp_kses_post( $title );?></h1><?php } ?>
				<span class="staticslider-sep"></span>
				<?php if ( $content ) { ?> <div class="staticslider-caption-text"><?php echo do_shortcode( $content ); ?></div><?php } ?>
			</div>
		</section><!-- .lp-staticslider -->

<?php
} // fluida_lpslider_output()
endif;


/**
 * blocks builder
 */
if ( ! function_exists( 'fluida_lpblocks' ) ):
function fluida_lpblocks() {
	$pageids = cryout_get_option( array( 'fluida_lpblockone', 'fluida_lpblocktwo', 'fluida_lpblockthree', 'fluida_lpblockfour') );
	$icon = cryout_get_option( array( 'fluida_lpblockoneicon', 'fluida_lpblocktwoicon', 'fluida_lpblockthreeicon', 'fluida_lpblockfouricon' ) );
	$fluida_lpblockscontent = cryout_get_option( 'fluida_lpblockscontent' );
	$count = 1;
	$pagecount = count (array_filter( $pageids) );
	if ( empty( $pagecount ) ) return;
	?>
	<section class="lp-blocks lp-blocks-rows-<?php echo $pagecount; ?>">
		<div class="lp-blocks-inside">
			<?php foreach ($pageids as $key => $pageid) {
				if ( !empty( $pageid ) ) {
					$page = get_post( $pageid );
					$data[$count] = array(
						'title' => $page->post_title,
						'text'	=> ( $fluida_lpblockscontent ? $page->post_content : fluida_custom_excerpt( $page->post_content ) ),
						'icon'	=> $icon[$key . 'icon'],
						'id' 	=> $count,
					);
					fluida_lpblock_output( $data[$count] );
					$count++;
				}
			} ?>
		</div>
	</section>
<?php } //fluida_lpblocks()
endif;

/**
 * blocks output
 */
if ( ! function_exists( 'fluida_lpblock_output' ) ):
function fluida_lpblock_output( $data ) { ?>
	<?php foreach ( $data as $key => $value ) { ${"$key"} = $value; } ?>
			<div class="lp-block block<?php echo absint( $id ); ?>">
					<i class="blicon-<?php echo esc_attr( $icon ); ?>"></i>
					<div class="lp-block-content">
						<?php if ($title) { ?><h5 class="lp-block-title"><?php echo wp_kses_post( $title ); ?></h5><?php } ?>
						<?php if ($text) { ?><div class="lp-block-text"><?php echo do_shortcode( $text ) ?></div><?php } ?>
						<?php /*<a class="lp-block-readmore" href="<?php echo esc_url( $link ); ?>" <?php echo esc_attr( $target ); ?>> <?php echo wp_kses_post( $readmore ); ?> </a>*/ ?>
					</div>
			</div><!-- lp-block -->
	<?php
} // fluida_lpblock_output()
endif;


/**
 * boxes builder
 */
if ( ! function_exists( 'fluida_lpboxes' ) ):
function fluida_lpboxes( $sid = 1 ) {
	$fluids = cryout_get_option(
				array(
					 'fluida_lpboxcat' . $sid,
					 'fluida_lpboxrow' . $sid,
					 'fluida_lpboxcount' . $sid,
					 'fluida_lpboxlayout' . $sid,
					 'fluida_lpboxmargins' . $sid,
					 'fluida_lpboxanimation' . $sid,
					 'fluida_lpboxreadmore' . $sid,
					 'fluida_lpboxlength' . $sid,
				 )
			 );
			 
	if ( $fluids['fluida_lpboxcount' . $sid] <= 0 ) return;

 	$box_counter = 1;
	$animated_class = "";
	if ( $fluids['fluida_lpboxanimation' . $sid] == 1 ) $animated_class = 'lp-boxes-animated';
	if ( $fluids['fluida_lpboxanimation' . $sid] == 2 ) $animated_class = 'lp-boxes-static';
    $custom_query = new WP_query();
    if ( ! empty( $fluids['fluida_lpboxcat' . $sid] ) ) $cat = '&category_name=' . $fluids['fluida_lpboxcat' . $sid]; else $cat = '';

    $custom_query->query( 'showposts=' . $fluids['fluida_lpboxcount' . $sid] . $cat . '&ignore_sticky_posts=1' );
    if ( $custom_query->have_posts() ) : ?>
		<section class="lp-boxes lp-boxes-<?php echo $sid ?> <?php  echo $animated_class ?> lp-boxes-rows-<?php echo absint( $fluids['fluida_lpboxrow' . $sid] ); ?>">
			<div class="<?php if ( $fluids['fluida_lpboxlayout' . $sid] == 2 ) { echo 'lp-boxes-inside'; } ?>
						<?php if ( $fluids['fluida_lpboxmargins' . $sid] == 2 ) { echo 'lp-boxes-margins'; } ?>
				">
    		<?php while ($custom_query->have_posts()) :
	            $custom_query->the_post();
	            $box = array();
	            $box['colno'] = $box_counter++;
	            $box['counter'] = $fluids['fluida_lpboxcount' . $sid];
	            $box['title'] = get_the_title();
	            $box['content'] = fluida_custom_excerpt( get_the_content(), $fluids['fluida_lpboxlength' . $sid] );
	            list($box['image'],) = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'fluida-lpbox-' . $sid );
	            $box['link'] = get_permalink();
				$box['readmore'] = $fluids['fluida_lpboxreadmore' . $sid];
	            $box['target'] = ''; // unused for now

            fluida_lpbox_output( $box );
        endwhile; ?>
			</div>
		</section><!-- .lp-boxes -->
<?php endif;
} //  fluida_lpboxes()
endif;

/**
 * boxes output
 */
if ( ! function_exists( 'fluida_lpbox_output' ) ):
function fluida_lpbox_output( $data ) {
	$randomness = array ( 6, 8, 1, 5, 2, 7, 3, 4 );
	foreach ( $data as $key => $value ) { ${"$key"} = $value; } ?>
			<div class="lp-box box<?php echo absint( $colno ); ?> ">
					<div class="lp-box-image lpbox-rnd<?php echo $randomness[$colno%8]; ?>">
						<?php if( ! empty( $image ) ) { ?><img alt="<?php echo wp_kses_post( $title ); ?>" src="<?php echo esc_url( $image ); ?>" /> <?php } ?>
						<a class="lp-box-link" href="<?php echo esc_url( $link ); ?>"><i class="blicon-plus"></i></a>
						<div class="lp-box-overlay"></div>
					</div>
					<div class="lp-box-content">
						<?php if ( ! empty( $title ) ) { ?><h5 class="lp-box-title"><?php echo wp_kses_post( $title ); ?></h5><?php } ?>
						<div class="lp-box-text">
							<div class="lp-box-text-inside">
								<?php echo do_shortcode( $content ) ?>
							</div>
							<a class="lp-box-readmore" href="<?php echo esc_url( $link ); ?>" <?php echo esc_attr( $target ); ?>> <?php echo wp_kses_post( $readmore ); ?> <i class="icon-right-dir"></i> </a>
						</div>
					</div>
			</div><!-- lp-box -->
	<?php
} // fluida_lpbox_output()
endif;


/**
 * text area builder
 */
if ( ! function_exists( 'fluida_lptext' ) ):
function fluida_lptext( $what = 'one' ) {
	$pageid = cryout_get_option( 'fluida_lptext' . $what );
	if ( ! empty( $pageid ) ) {
		$page = get_post( $pageid );
		$data = array(
			'title' => $page->post_title,
			'text'	=> $page->post_content,
			'id' 	=> $what,
		);
		list( $data['image'], ) = wp_get_attachment_image_src( get_post_thumbnail_id( $pageid ), 'full' );
		fluida_lptext_output( $data );
	}
} // fluida_lptext()
endif;

/**
 * text area output
 */
if ( ! function_exists( 'fluida_lptext_output' ) ):
function fluida_lptext_output( $data ){ ?>
	<section class="lp-text" id="lp-text-<?php echo esc_attr( $data['id'] ); ?>"<?php if( ! empty( $data['image'] ) ) { ?> style="background-image: url( <?php echo esc_url( $data['image'] ); ?>);" <?php } ?> >
		<?php if( ! empty( $data['image'] ) ) { ?><div class="lp-text-overlay"></div><?php } ?>
			<div class="lp-text-inside">
				<h2 class="lp-text-title"><?php echo wp_kses_post( $data['title'] ) ?></h2>
				<div class="lp-text-content"><?php echo do_shortcode( $data['text'] ) ?></div>
			</div>

	</section><!-- .lp-text-<?php echo esc_attr( $data['id'] ); ?> -->
<?php
} // fluida_lptext_output()
endif;

/**
 * page or posts output, also used when landing page is disabled
 */
if ( ! function_exists( 'fluida_lpindex' ) ):
function fluida_lpindex() {

	$fluida_landingpage = cryout_get_option ('fluida_landingpage');
	$fluida_lpposts = cryout_get_option ('fluida_lpposts');

	if ( is_page() ) {
		get_template_part( 'content/content', 'page' );
	} else {

		if ( $fluida_landingpage && ! $fluida_lpposts ) {
			// when posts are disabled, nothing to display
		} else {
			// area has extra wrapping on landing page
			if ( $fluida_landingpage ) { ?> <section id="lp-posts"> <div class="lp-posts-inside"> <?php }

			if ( have_posts() ) : ?>
				<div id="content-masonry"> <?php
					while ( have_posts() ) : the_post();
						get_template_part( 'content/content', get_post_format() );
					endwhile; ?>
				</div> <!-- content-masonry -->
				<?php fluida_pagination();
			else :
				get_template_part( 'content/content', 'notfound' );
			endif;

			// end extra wrapping
			if ( $fluida_landingpage ) { ?> </div> </section><!-- #lp-posts --> <?php }
		}
	} // end !is_page()

} // fluida_lpindex()
endif;

// FIN
