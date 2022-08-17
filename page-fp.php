<?php
/**
* Template Name: Create Product
*
* @link https://developer.wordpress.org/themes/basics/template-hierarchy/
*
* @package wt636
**/
get_header();
?>

	<main id="primary" class="site-main fp_page-wrap">
        <?php do_shortcode( 'fp-form', true);?>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();