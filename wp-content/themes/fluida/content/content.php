<?php
/**
 * The default template for displaying content
 *
 * @package Fluida
 */

$fluids = cryout_get_option( array( 'fluida_excerptarchive', 'fluida_excerptsticky', 'fluida_excerpthome' ) );
global $main_query; 

$temp_featured = get_posts( 
    array(
        'post_type' => 'custom_post',
        'custom_post-category' => 'featured-cat', 
        'posts_per_page' => 2)
        );
$featured_ids = wp_list_pluck( $temp_featured, 'ID' );

$query_args =  array(
                'post_type' => 'custom_post',  
                'posts_per_page' => $per_page, 
                'paged' => $current_page, 
                'post__not_in'   => $featured_ids
                );
$main_query = new WP_query ($query_args);

//displaying the two featured posts with their own query
$featured = new WP_query( array(
                            'post_type' => 'custom_post',
                            'custom_post-category' => 'featured-cat',
                            'posts_per_page' => 2)
                            );
while ($featured->have_posts ()) : $featured->the_post();
    the_title();
    the_excerpt();
endwhile;

//displaying the full query of the page
if ($main_query->have_posts ()) : 
    while ($main_query->have_posts ()) : $main_query->the_post();
        the_title();
        the_excerpt();
    endwhile;
endif;

?><?php cryout_before_article_hook(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); cryout_schema_microdata( 'article' ); ?>>

	<?php cryout_featured_hook(); ?>
	<div class="article-inner">
		<header class="entry-header">
			<?php cryout_post_title_hook(); ?>

			<?php the_title( sprintf( '<h2 class="entry-title"' . cryout_schema_microdata( 'entry-title', 0 )  . '><a href="%s" ' . cryout_schema_microdata( 'mainEntityOfPage', 0 ) . ' rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
			<?php cryout_meta_format_hook(); ?>

			<div class="entry-meta">
				<?php cryout_post_meta_hook(); ?>
			</div><!-- .entry-meta -->

		</header><!-- .entry-header -->

		<?php cryout_post_before_content_hook();

		$mode = 'excerpt'; // default
		if ( $fluids['fluida_excerptarchive'] == "full" ) { $mode = 'content'; }
		if ( is_sticky() && $fluids['fluida_excerptsticky'] == "full" ) { $mode = 'content'; }
		if ( $fluids['fluida_excerpthome'] == "full" && ! is_archive() && ! is_search() ) { $mode = 'content'; }
		if ( false != get_post_format() ) { $mode = 'content'; }

		switch ( $mode ) {
			case 'content': ?>

				<div class="entry-content" <?php cryout_schema_microdata( 'entry-content' ); ?>>
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'fluida' ), 'after' => '</div>' ) ); ?>
				</div><!-- .entry-content -->

			<?php break;

			case 'excerpt':
			default: ?>

				<div class="entry-summary" <?php cryout_schema_microdata( 'entry-summary' ); ?>>
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->
				<footer class="post-continue-container">
					<?php cryout_post_excerpt_hook(); ?>
				</footer>

			<?php break;
		}; ?>

		<?php cryout_post_after_content_hook();  ?>
	</div><!-- .article-inner -->
</article><!-- #post-<?php the_ID(); ?> -->

<?php cryout_after_article_hook(); ?>
