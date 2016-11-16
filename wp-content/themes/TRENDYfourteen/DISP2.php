<?php
/**
 * The template for displaying !!!!Trendy templated!!! pages
 *
 */
 /* Template Name: DISP2 */ 
get_header(); ?>

<div id="main-content" class="main-content">

<?php
$postlist = get_posts( 'orderby=menu_order&sort_order=asc' );
$posts = array();
foreach ( $postlist as $post ) {
   $posts[] += $post->ID;
}

$current = array_search( get_the_ID(), $posts );
$prevID = $posts[$current-1];
$nextID = $posts[$current+1];
?>

<div class="navigation">
<?php if ( !empty( $prevID ) ): ?>
<div class="alignleft">
<a href="<?php echo get_permalink( $prevID ); ?>"
  title="<?php echo get_the_title( $prevID ); ?>">Previous</a>
</div>
<?php endif;
if ( !empty( $nextID ) ): ?>
<div class="alignright">
<a href="<?php echo get_permalink( $nextID ); ?>" 
 title="<?php echo get_the_title( $nextID ); ?>">Next</a>
</div>
<?php endif; ?>
</div><!-- .navigation -->

<?php
get_sidebar();
get_footer();
