<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package understrap
 */


// Test commmit Belle
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="error-404-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<div class="col-md-12 content-area" id="primary">

				<main class="site-main" id="main">

					<section class="error-404 not-found">

						<header class="page-header">

							<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'understrap' ); ?></h1>

						</header><!-- .page-header -->

						<div class="page-content">

							<p class="center-content"><?php esc_html_e( 'It looks like nothing was found at this location. Return to the Home page?', 'understrap' ); ?></p>

							<?php if(has_custom_logo()) : ?>
							    <div class="center-content">
                                    <?php the_custom_logo() ?>
                                </div>
                            <?php else : ?>
                                <div class="center-content button-outline">
                                    <a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url">Home</a>
                                </div>
							<?php endif; ?>

						</div><!-- .page-content -->

					</section><!-- .error-404 -->

				</main><!-- #main -->

			</div><!-- #primary -->

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #error-404-wrapper -->

<?php get_footer(); ?>
