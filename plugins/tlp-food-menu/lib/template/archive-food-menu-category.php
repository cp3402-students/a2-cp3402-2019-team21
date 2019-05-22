<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();
global $TLPfoodmenu;
?>
<div class="container tlp-food-menu">
    <div class="category-title">
        <h3 class="page-title"><?php echo single_cat_title( "", false ); ?></h3>
    </div>
	<?php
	$html     = null;
	$settings = get_option( $TLPfoodmenu->options['settings'] );
	$colClass = " tlp-col-md-6 tlp-col-lg-6 tlp-col-sm-12 paddingl0";

	if ( have_posts() ) {
		$html  .= '<div class="tlp-food-menu">';
		$count = 0;
		while ( have_posts() ) : the_post();
			$html .= '<div class="single-item ' . esc_attr( $colClass ) . '">';
                $html .= '<div class="tlp-equal-height food-item">';
                    $html .= '<div class="image-area tlp-col-md-5 tlp-col-lg-5 tlp-col-sm-6 paddingl0 "><a href="' . get_permalink() . '" title="' . get_the_title() . '">';
                    if ( has_post_thumbnail() ) {
                        $html .= get_the_post_thumbnail( get_the_ID(), 'medium' );
                    } else {
                        $html .= "<img src='" . $TLPfoodmenu->assetsUrl . 'images/demo-100x100.png' . "' alt='" . get_the_title() . "' />";
                    }
                    $html   .= '</a></div>';
                    $html   .= '<div class="tlp-col-md-7 tlp-col-lg-7 tlp-col-sm-6 padding0">';
                        $html   .= '<div class="title">';
                            $html   .= '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
                            $html   .= '<span class="price">' . $TLPfoodmenu->getPriceWithLabel() . '</span>';
                        $html   .= '</div>';
                        $html   .= '<p>' . $TLPfoodmenu->string_limit_words( get_the_content(), 5 ) . '</p>';
                    $html .= '</div>';
                $html .= '</div>';
			$html .= '</div>';
		endwhile;
		$html .= '</div>';
	} else {
		$html .= "<p>" . __( 'No food found.', 'tlp-food-menu' ) . "</p>";
	}
	echo $html;
	?>
</div>
<?php get_footer(); ?>
