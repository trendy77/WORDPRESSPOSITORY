<?php
function my_theme_enqueue_styles() {
    $parent_style = 'fluida-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style'),
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}   add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


function trendypublishing_copyright() {
global $wpdb;
$copyright_dates = $wpdb->get_results("
SELECT
YEAR(min(post_date_gmt)) AS firstdate,
YEAR(max(post_date_gmt)) AS lastdate
FROM
$wpdb->posts
WHERE
post_status = 'publish'
");
$output = '';
if($copyright_dates) {
$copyright = "&copy; " . $copyright_dates[0]->firstdate;
if($copyright_dates[0]->firstdate != $copyright_dates[0]->lastdate) {
$copyright .= '-' . $copyright_dates[0]->lastdate;
}$output = $copyright;
}return $output;
}

function remove_footer_admin () {
              echo 'TRENDY PUBLISHING <a href="http://www.organisemybiz.com.au" target="OrganiseMyBiz Australia">   </a> </p>';
}
add_filter('admin_footer_text', 'remove_footer_admin');


function donate_shortcode( $atts ) {
    extract(shortcode_atts(array(
    'text' => 'Donate to keep our Ticker, ticking!',
      'account' => 'tfisher811@gmail.com',
        'for' =>0.99'',= ), $atts));
        global $post;
        if (!$for) $for = str_replace(" "," ",$post->post_title);
        return '<a class="donateLink" href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business='.$account.'&item_name=Donation for '.$for.'">'.$text.'</a>';
} add_shortcode('donate', 'donate_shortcode');
?>
