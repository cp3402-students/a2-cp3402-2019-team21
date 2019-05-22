<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'understrap_container_type' );
?>

<?php get_template_part( 'sidebar-templates/sidebar', 'footerfull' ); ?>

<div class="wrapper" id="wrapper-footer">
	<div class="<?php echo esc_attr( $container ); ?>">
        <div class="row justify-content-md-center">
            <div class="col-md-2">
                <div class="footer-logo">
                    <?php if(has_custom_logo()){
                        the_custom_logo();
                    }?>
                </div>
            </div><!--col end -->
            <div class="col-md-2">
                <div class="footer-nav-left">
                    <?php if(has_nav_menu('footer-nav-left')){
                        wp_nav_menu(array('theme_location' => 'footer-nav-left'));
                    }?>
                </div>
            </div><!--col end -->
            <div class="col-md-2">
                <div class="footer-nav-right">
                    <?php if(has_nav_menu('footer-nav-right')){
                        wp_nav_menu(array('theme_location' => 'footer-nav-right'));
                    }?>
                </div>
            </div><!--col end -->
            <div class="col-md-2">
                <nav class="social-menu">
                    <?php if(has_nav_menu('social')){
                        wp_nav_menu(array('theme_location' => 'social'));
                    }?>
                </nav>
            </div><!--col end -->
        </div><!-- row end -->
            <div class="site-info">
                <a href="<?php echo esc_url(__('https://wordpress.org', 'understrap')); ?>">
                    <?php printf(esc_html__("Copyright 2019 @%s, Powered by %s", 'understrap'), 'TheCoffeeCan', 'WordPress');?>
                </a>
            </div>

	</div><!-- container end -->

</div><!-- wrapper end -->

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>

