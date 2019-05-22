<?php
if (!defined('WPINC')) {
    die;
}

if (!class_exists('FmGutenBurg')):

    class FmGutenBurg
    {
        protected $version;

        function __construct() {
            $this->version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : TLP_FOOD_MENU_VERSION;
            add_action('enqueue_block_assets', array($this, 'block_assets'));
            add_action('enqueue_block_editor_assets', array($this, 'block_editor_assets'));
            if (function_exists('register_block_type')) {
                register_block_type('radiustheme/tlp-food-menu', array(
                    'render_callback' => array($this, 'render_shortcode'),
                ));
            }
        }

        static function render_shortcode($atts) {
            $shortcode = '[foodmenu';
	        if (isset($atts['column']) && !empty($atts['column']) && $col = absint($atts['column'])) {
		        $shortcode .= ' col="' . $col . '"';
	        }
	        if (isset($atts['orderby']) && !empty($atts['orderby'])) {
		        $shortcode .= ' orderby="' . $atts['orderby'] . '"';
	        }
	        if (isset($atts['order']) && !empty($atts['order'])) {
		        $shortcode .= ' order="' . $atts['order'] . '"';
	        }
	        if (isset($atts['cats']) && !empty($atts['cats']) && is_array($atts['cats'])) {
		        if(!empty($cats = array_filter($atts['cats']))){
			        $shortcode .= ' cat="' . implode(',',$cats) . '"';
		        }
	        }
	        if (isset($atts['isImageHide']) && !empty($atts['isImageHide'])) {
		        $shortcode .= ' hide-img="1"';
	        }
	        if (isset($atts['isLinkDisabled']) && !empty($atts['isLinkDisabled'])) {
		        $shortcode .= ' disable-link="1"';
	        }
	        if (isset($atts['titleColor']) && !empty($atts['titleColor'])) {
		        $shortcode .= ' title-color="' . $atts['titleColor'] . '"';
	        }
	        if (isset($atts['wrapperClass']) && !empty($atts['wrapperClass'])) {
		        $shortcode .= ' class="' . $atts['wrapperClass'] . '"';
	        }
            $shortcode .= ']';
            return do_shortcode($shortcode);
        }


        function block_assets() {
            wp_enqueue_style('wp-blocks');
        }

        function block_editor_assets() {
            global $TLPfoodmenu;
            // Scripts.
            wp_enqueue_script(
                'rt-tlp-food-menu-gb-block-js',
	            $TLPfoodmenu->assetsUrl . "js/tlp-food-menu-blocks.min.js",
                array('wp-blocks', 'wp-i18n', 'wp-element'),
                $this->version,
                true
            );
            wp_localize_script('rt-tlp-food-menu-gb-block-js', 'tlpFoodMenu', array(
                'column'      => $TLPfoodmenu->scColumns(),
                'orderby'     => $TLPfoodmenu->scOrderBy(),
                'order'       => $TLPfoodmenu->scOrder(),
                'cats'        => $TLPfoodmenu->getAllFmpCategoryList(),
                'icon'        => $TLPfoodmenu->assetsUrl . 'images/icon-16x16.png',
            ));
            wp_enqueue_style('wp-edit-blocks');
        }
    }

endif;