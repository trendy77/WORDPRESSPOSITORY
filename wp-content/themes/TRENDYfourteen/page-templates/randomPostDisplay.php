<?php
/**
 * The template for displaying !!!!Trendy templated!!! pages
 *
 */
 /* Template Name: randomPostDisplay */ 
get_header(); ?>

<div id="main-content" class="main-content">

<?php
$args = array( 'posts_per_page' => 5, 'offset'=> 1, 'category' => 1 );

$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
	<li>
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</li>

<?php
 endforeach; 
wp_reset_postdata();



?>
 		</div><!-- #content -->
	</div><!-- #primary -->
	<?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
