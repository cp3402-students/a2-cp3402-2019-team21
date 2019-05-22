<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 7/13/2017
 * Time: 10:00 AM
 */

namespace GDGallery\Controllers\Admin;

use GDGallery\Helpers\SettingsPageBuilder;
use GDGallery\Helpers\View;
use GDGallery\Models\Gallery;


class SettingsController
{

    private $options;

    /* public function __construct()
     {
         $this->settingsFileds();
     }*/

    public function settingsFileds()
    {
        $builder = new SettingsPageBuilder();

        $builder->setPageTitle(__('Views / Styles', 'gdgallery'));

        $builder->addTabs(array(
            "justified" => array('title' => __('Justified', 'gdgallery')),
            "tiles" => array('title' => __('Tiles', 'gdgallery')),
            "carousel" => array('title' => __('Carousel', 'gdgallery')),
            "slider" => array('title' => __('Slider', 'gdgallery')),
            "grid" => array('title' => __('Grid', 'gdgallery')),
            "lightbox" => array('title' => __('Lightbox settings', 'gdgallery')),
        ));

        $builder->addSections(array(
            'element_style_justified' => array(
                'title' => __('Element Styles and Settings', 'gdgallery'),
                'description' => __('Set image title options from this section', 'gdgallery'),
                "tab" => "justified"
            ),
            'load_more_justified' => array(
                'title' => __('Load More Styles', 'gdgallery'),
                'description' => __('Set load more options from this section', 'gdgallery'),
                "tab" => "justified"
            ),
            'pagination_justified' => array(
                'title' => __('Pagination Styles', 'gdgallery'),
                'description' => __('Set Pagination styles from this section ', 'gdgallery'),
                "tab" => "justified"
            ),
            'element_style_tiles' => array(
                'title' => __('Element Styles and Settings', 'gdgallery'),
                'description' => __('Set image title options from this section ', 'gdgallery'),
                "tab" => "tiles"
            ),
            'load_more_tiles' => array(
                'title' => __('Load More Styles', 'gdgallery'),
                'description' => __('Set load more options from this section ', 'gdgallery'),
                "tab" => "tiles"
            ),
            'pagination_tiles' => array(
                'title' => __('Pagination Styles', 'gdgallery'),
                'description' => __('Set Pagination styles from this section ', 'gdgallery'),
                "tab" => "tiles"
            ),
            'element_style_carousel' => array(
                'title' => __('Element Styles and Settings', 'gdgallery'),
                'description' => __('Set image title options from this section ', 'gdgallery'),
                "tab" => "carousel"
            ),
            'components_carousel' => array(
                'title' => __('Navigation Styles and Settings', 'gdgallery'),
                'description' => __('Set navigation options from this section ', 'gdgallery'),
                "tab" => "carousel"
            ),
            'element_style_slider' => array(
                'title' => __('Element Styles and Settings', 'gdgallery'),
                'description' => __('Set image title options from this section', 'gdgallery'),
                "tab" => "slider"
            ),
            'components_slider' => array(
                'title' => __('Navigation Styles and Settings', 'gdgallery'),
                'description' => __('Set navigation options from this section ', 'gdgallery'),
                "tab" => "slider"
            ),
            'element_style_grid' => array(
                'title' => __('Element Styles and Settings', 'gdgallery'),
                'description' => __('Set image title options from this section', 'gdgallery'),
                "tab" => "grid"
            ),
            'components_grid' => array(
                'title' => __('Navigation Styles and Settings', 'gdgallery'),
                'description' => __('Set navigation options from this section ', 'gdgallery'),
                "tab" => "grid"
            ),
            'wide_lightbox' => array(
                'title' => __('Wide Type Styles', 'gdgallery'),
                'description' => __('Set lightbox options for "Wide" type from this section ', 'gdgallery'),
                "tab" => "lightbox"
            ),
            'compact_lightbox' => array(
                'title' => __('Compact Type Styles', 'gdgallery'),
                'description' => __('Set lightbox options for "Compact" type from this section ', 'gdgallery'),
                "tab" => "lightbox"
            ),


        ));

        $builder->addFields(array(
            /*********** Justify options  ****************/

            'show_title_justified' => array(
                'type' => 'select',
                'label' => __('Title Option', 'gdgallery'),
                'options' => array(
                    '0' => __('Always on', 'gdgallery'),
                    '1' => __('On hover', 'gdgallery'),
                    '2' => __('Disable', 'gdgallery')
                ),
                'section' => 'element_style_justified',
                'help' => __('Choose how to display Image title', 'gdgallery')
            ),
            'title_position_justified' => array(
                'type' => 'select',
                'label' => __('Title Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery')
                ),
                'section' => 'element_style_justified',
                'help' => __('Choose title position', 'gdgallery')
            ),
            'title_vertical_position_justified' => array(
                'type' => 'select',
                'label' => __('Title Vertical Position', 'gdgallery'),
                'options' => array(
                    'inside_top' => __('Top', 'gdgallery'),
                    'inside_bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'element_style_justified',
                'help' => __('Choose title position', 'gdgallery')
            ),
            'title_appear_type_justified' => array(
                'type' => 'select',
                'label' => __('Title On Hover Type', 'gdgallery'),
                'options' => array(
                    'slide' => __('Slide', 'gdgallery'),
                    'fade' => __('Fade', 'gdgallery'),
                ),
                'section' => 'element_style_justified',
                'help' => __('Choose title on hover effect', 'gdgallery')
            ),
            'title_size_justified' => array(
                'type' => 'number',
                'label' => __('Title Font Size', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set title font size in px', 'gdgallery')
            ),
            'title_color_justified' => array(
                'type' => 'color',
                'label' => __('Title Color', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set title color in HEXadecimal color system', 'gdgallery')
            ),
            'title_background_color_justified' => array(
                'type' => 'color',
                'label' => __('Title Background Color', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set title background color in HEXadecimal color system', 'gdgallery')
            ),
            'title_background_opacity_justified' => array(
                'type' => 'number',
                'label' => __('Title Background Opacity (%)', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set title background opacity in percentage', 'gdgallery')
            ),
            'margin_justified' => array(
                'type' => 'number',
                'label' => __('Image Margin', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set image margin in px', 'gdgallery')
            ),
            'border_width_justified' => array(
                'type' => 'number',
                'label' => __('Image Border Width', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set image border width in px', 'gdgallery')
            ),
            'border_color_justified' => array(
                'type' => 'color',
                'label' => __('Image Border Color', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set image border color in HEXadecimal color system', 'gdgallery')
            ),
            'border_radius_justified' => array(
                'type' => 'number',
                'label' => __('Image Border Radius', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set image border radius in px', 'gdgallery')
            ),

            'show_icons_justified' => array(
                'type' => 'checkbox',
                'label' => __('Lightbox', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Choose whether to turn Lightbox on/off', 'gdgallery')
            ),
            'show_link_icon_justified' => array(
                'type' => 'checkbox',
                'label' => __('URL Icon', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Choose whether to turn URL icon on/off', 'gdgallery')
            ),
            'item_as_link_justified' => array(
                'type' => 'checkbox',
                'label' => __('Image As Link', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Set image as link', 'gdgallery'),
            ),
            'link_new_tab_justified' => array(
                'type' => 'checkbox',
                'label' => __('Open Link In New Tab', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Choose whether to open the link in a new tab', 'gdgallery')
            ),
            'on_hover_overlay_justified' => array(
                'type' => 'checkbox',
                'label' => __('On Hover Overlay', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Turn on hover overlay on/off ', 'gdgallery')
            ),
            'image_hover_effect_justified' => array(
                'type' => 'select',
                'label' => __('Image Hover Effect', 'gdgallery'),
                'options' => array(
                    'blur' => __('none', 'gdgallery'),
                    'bw' => __('Black and White', 'gdgallery'),
                    'sepia' => __('Sepia', 'gdgallery')
                ),
                'section' => 'element_style_justified',
                'help' => __('Choose image hover effect', 'gdgallery')
            ),
            'image_hover_effect_reverse_justified' => array(
                'type' => 'checkbox',
                'label' => __('Image On Hover Reversed Effect', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Choose whether to turn reversed effect on/off', 'gdgallery')
            ),
            'shadow_justified' => array(
                'type' => 'checkbox',
                'label' => __('Image Element Shadow', 'gdgallery'),
                'section' => 'element_style_justified',
                'help' => __('Choose whether to turn image element shadow on/off', 'gdgallery')
            ),


            'lightbox_type_justified' => array(
                'type' => 'select',
                'label' => __('Lightbox Type', 'gdgallery'),
                'options' => array(
                    'wide' => __('Wide', 'gdgallery'),
                    'compact' => __('Compact', 'gdgallery')
                ),
                'section' => 'element_style_justified',
                'help' => __('Choose Lightbox type', 'gdgallery')
            ),
            'load_more_text_justified' => array(
                'type' => 'text',
                'label' => __('Load More', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set the text you want to appear on the button', 'gdgallery')
            ),
            'load_more_position_justified' => array(
                'type' => 'select',
                'label' => __('Load More Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'load_more_justified',
                'help' => __('Set load more button position', 'gdgallery')
            ),
            'load_more_font_size_justified' => array(
                'type' => 'number',
                'label' => __('Font Size', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more text font size in px', 'gdgallery')
            ),
            'load_more_vertical_padding_justified' => array(
                'type' => 'number',
                'label' => __('Vertical Padding', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more vertical padding in px', 'gdgallery')
            ),
            'load_more_horisontal_padding_justified' => array(
                'type' => 'number',
                'label' => __('Horizontal Padding', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more horizontal padding in px', 'gdgallery')
            ),
            'load_more_border_width_justified' => array(
                'type' => 'number',
                'label' => __('Border Width', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more button border width in px', 'gdgallery')
            ),
            'load_more_border_radius_justified' => array(
                'type' => 'number',
                'label' => __('Border Radius', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more button border radius in px', 'gdgallery')
            ),
            'load_more_border_color_justified' => array(
                'type' => 'color',
                'label' => __('Border Color', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more button border color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_color_justified' => array(
                'type' => 'color',
                'label' => __('Button Text Color', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more button text color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_background_color_justified' => array(
                'type' => 'color',
                'label' => __('Background Color', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more button background color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_font_family_justified' => array(
                'type' => 'select',
                'label' => __('Font Type', 'gdgallery'),
                'options' => array(
                    'monospace' => __('monospace', 'gdgallery'),
                    'cursive' => __('cursive', 'gdgallery'),
                    'fantasy' => __('fantasy', 'gdgallery'),
                    'sans-serif' => __('sans-serif', 'gdgallery'),
                    'serif' => __('serif', 'gdgallery'),
                ),
                'section' => 'load_more_justified',
                'help' => __('Choose load more button text font type', 'gdgallery')
            ),
            'load_more_hover_border_color_justified' => array(
                'type' => 'color',
                'label' => __('On Hover Border Color', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set load more button border on hover color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_hover_color_justified' => array(
                'type' => 'color',
                'label' => __('Text On Hover Color', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set text on hover color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_hover_background_color_justified' => array(
                'type' => 'color',
                'label' => __('On Hover Background Color', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set background color for load more button text on hover in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_loader_justified' => array(
                'type' => 'checkbox',
                'label' => __('Show Loading Icon', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Choose whether to turn loading icon on/off', 'gdgallery')
            ),
            'load_more_loader_color_justified' => array(
                'type' => 'color',
                'label' => __('Loading Icon color', 'gdgallery'),
                'section' => 'load_more_justified',
                'help' => __('Set color for loading icon in HEXadecimal color system', 'gdgallery')
            ),


            'pagination_position_justified' => array(
                'type' => 'select',
                'label' => __('Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'pagination_justified',
                'help' => __('Set pagination position', 'gdgallery')
            ),
            'pagination_font_size_justified' => array(
                'type' => 'number',
                'label' => __('Font size', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set pagination font size in px', 'gdgallery')
            ),
            'pagination_vertical_padding_justified' => array(
                'type' => 'number',
                'label' => __('Vertical Padding', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set pagination vertical padding in px', 'gdgallery')
            ),
            'pagination_horisontal_padding_justified' => array(
                'type' => 'number',
                'label' => __('Horizontal Padding', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set pagination horizontal padding in px', 'gdgallery')
            ),
            'pagination_margin_justified' => array(
                'type' => 'number',
                'label' => __('Margin', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set margin value between elements in px', 'gdgallery')
            ),
            'pagination_border_width_justified' => array(
                'type' => 'number',
                'label' => __('Border Width', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set border width value in px', 'gdgallery')
            ),
            'pagination_border_radius_justified' => array(
                'type' => 'number',
                'label' => __('Border Radius', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set border radius value in px', 'gdgallery')
            ),
            'pagination_border_color_justified' => array(
                'type' => 'color',
                'label' => __('Border Color', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set border color in HEXadecimal color system', 'gdgallery')
            ),
            'pagination_color_justified' => array(
                'type' => 'color',
                'label' => __('Pagination Elements Color', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set pagination elements color in HEXadecimal color system', 'gdgallery')
            ),
            'pagination_background_color_justified' => array(
                'type' => 'color',
                'label' => __('Background Color', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set pagination elements background color in HEXadecimal color system', 'gdgallery')
            ),
            'pagination_font_family_justified' => array(
                'type' => 'select',
                'label' => __('Font Type', 'gdgallery'),
                'options' => array(
                    'monospace' => __('monospace', 'gdgallery'),
                    'cursive' => __('cursive', 'gdgallery'),
                    'fantasy' => __('fantasy', 'gdgallery'),
                    'sans-serif' => __('sans-serif', 'gdgallery'),
                    'serif' => __('serif', 'gdgallery'),
                ),
                'section' => 'pagination_justified',
                'help' => __('Choose pagination element font type', 'gdgallery')
            ),
            'pagination_hover_border_color_justified' => array(
                'type' => 'color',
                'label' => __('On Hover Border Color', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set pagination element border on hover color', 'gdgallery')
            ),
            'pagination_hover_color_justified' => array(
                'type' => 'color',
                'label' => __('On Hover Color', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set pagination element on hover color', 'gdgallery')
            ),
            'pagination_hover_background_color_justified' => array(
                'type' => 'color',
                'label' => __('On Hover Background Color', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set pagination element on hover background color', 'gdgallery')
            ),
            'pagination_nav_type_justified' => array(
                'type' => 'select',
                'label' => __('Navigation Type', 'gdgallery'),
                'options' => array(
                    '0' => __('Arrows', 'gdgallery'),
                    '1' => __('Text', 'gdgallery'),
                    '2' => __('Only Numbers', 'gdgallery')
                ),
                'section' => 'pagination_justified',
                'help' => __('Choose navigation type', 'gdgallery')
            ),
            'pagination_nav_text_justified' => array(
                'type' => 'text',
                'label' => __('Navigation Text', 'gdgallery'),
                'section' => 'pagination_justified',
                'help' => __('Set navigation text. Note that text must be separated with comma', 'gdgallery')
            ),
            'pagination_nearby_pages_justified' => array(
                'type' => 'select',
                'label' => __('Visible Page Quantity', 'gdgallery'),
                'options' => array(
                    'All' => __('All', 'gdgallery'),
                    '1' => "1",
                    '2' => "2",
                    '3' => "3",
                    '4' => "4",
                    '5' => "5",
                ),
                'section' => 'pagination_justified',
                'help' => __('Visible Page Quantity (Set visible page quantity)', 'gdgallery')
            ),


            /****************** tiles options *******************/
            'show_title_tiles' => array(
                'type' => 'select',
                'label' => __('Title Option', 'gdgallery'),
                'options' => array(
                    '0' => __('Always on', 'gdgallery'),
                    '1' => __('On hover', 'gdgallery'),
                    '2' => __('Disable', 'gdgallery')
                ),
                'section' => 'element_style_tiles',
                'help' => __('Show / Hide Title', 'gdgallery')
            ),
            'title_position_tiles' => array(
                'type' => 'select',
                'label' => __('Title Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery')
                ),
                'section' => 'element_style_tiles',
                'help' => __('Choose title position', 'gdgallery')
            ),
            'title_vertical_position_tiles' => array(
                'type' => 'select',
                'label' => __('Title Vertical Position', 'gdgallery'),
                'options' => array(
                    'inside_top' => __('Top', 'gdgallery'),
                    'inside_bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'element_style_tiles',
                'help' => __('Choose title position', 'gdgallery')
            ),
            'title_appear_type_tiles' => array(
                'type' => 'select',
                'label' => __('Title On Hover Type', 'gdgallery'),
                'options' => array(
                    'slide' => __('Slide', 'gdgallery'),
                    'fade' => __('Fade', 'gdgallery'),
                ),
                'section' => 'element_style_tiles',
                'help' => __('Choose title on hover effect', 'gdgallery')
            ),
            'title_size_tiles' => array(
                'type' => 'number',
                'label' => __('Title Font Size', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set title font size in px', 'gdgallery')
            ),
            'title_color_tiles' => array(
                'type' => 'color',
                'label' => __('Title Color', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set title color in HEXadecimal color system', 'gdgallery')
            ),
            'title_background_color_tiles' => array(
                'type' => 'color',
                'label' => __('Title Background Color', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Choose title background color in HEXadecimal color system', 'gdgallery')
            ),
            'title_background_opacity_tiles' => array(
                'type' => 'number',
                'label' => __('Title Background Opacity (%)', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set title background opacity in percentage', 'gdgallery')
            ),
            'margin_tiles' => array(
                'type' => 'number',
                'label' => __('Image Margin', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set image margin in px', 'gdgallery')
            ),
            'col_width_tiles' => array(
                'type' => 'number',
                'label' => __('Image Width', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set image width in px', 'gdgallery')
            ),
            'min_col_tiles' => array(
                'type' => 'number',
                'label' => __('Minimal Columns', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set minimal column number', 'gdgallery')
            ),
            'border_width_tiles' => array(
                'type' => 'number',
                'label' => __('Image Border Width', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set image border width in px', 'gdgallery')
            ),
            'border_color_tiles' => array(
                'type' => 'color',
                'label' => __('Image Border Color', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set image border color in HEXadecimal color system', 'gdgallery')
            ),
            'border_radius_tiles' => array(
                'type' => 'number',
                'label' => __('Image Border Radius', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set image border radius in px', 'gdgallery')
            ),

            'show_icons_tiles' => array(
                'type' => 'checkbox',
                'label' => __('Lightbox', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Choose whether to turn Lightbox on/off', 'gdgallery')
            ),
            'show_link_icon_tiles' => array(
                'type' => 'checkbox',
                'label' => __('URL Icon', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Choose whether to turn URL icon on/off', 'gdgallery')
            ),
            'item_as_link_tiles' => array(
                'type' => 'checkbox',
                'label' => __('Image As Link', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Set image as link', 'gdgallery')
            ),
            'link_new_tab_tiles' => array(
                'type' => 'checkbox',
                'label' => __('Open Link In New Tab', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Choose whether to open the link in a new tab', 'gdgallery')
            ),
            'on_hover_overlay_tiles' => array(
                'type' => 'checkbox',
                'label' => __('On Hover Overlay', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Turn on hover overlay on/off ', 'gdgallery')
            ),
            'image_hover_effect_tiles' => array(
                'type' => 'select',
                'label' => __('Image Hover Effect', 'gdgallery'),
                'options' => array(
                    'blur' => __('none', 'gdgallery'),
                    'bw' => __('Black and White', 'gdgallery'),
                    'sepia' => __('Sepia', 'gdgallery')
                ),
                'section' => 'element_style_tiles',
                'help' => __('Choose image hover effect', 'gdgallery')
            ),
            'image_hover_effect_reverse_tiles' => array(
                'type' => 'checkbox',
                'label' => __('Image On Hover Reversed Effect', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Choose whether to turn reversed effect on/off', 'gdgallery')
            ),
            'shadow_tiles' => array(
                'type' => 'checkbox',
                'label' => __('Image Element Shadow', 'gdgallery'),
                'section' => 'element_style_tiles',
                'help' => __('Choose whether to turn image element shadow on/off', 'gdgallery')
            ),


            'lightbox_type_tiles' => array(
                'type' => 'select',
                'label' => __('Lightbox Type', 'gdgallery'),
                'options' => array(
                    'wide' => __('Wide', 'gdgallery'),
                    'compact' => __('Compact', 'gdgallery')
                ),
                'section' => 'element_style_tiles',
                'help' => __('Choose Lightbox type', 'gdgallery')
            ),
            'load_more_text_tiles' => array(
                'type' => 'text',
                'label' => __('Load More', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set the text you want to appear on the button', 'gdgallery')
            ),
            'load_more_position_tiles' => array(
                'type' => 'select',
                'label' => __('Load More Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'load_more_tiles',
                'help' => __('Set load more button position', 'gdgallery')
            ),
            'load_more_font_size_tiles' => array(
                'type' => 'number',
                'label' => __('Font Size', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more text font size in px', 'gdgallery')
            ),
            'load_more_vertical_padding_tiles' => array(
                'type' => 'number',
                'label' => __('Vertical Padding', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more vertical padding in px', 'gdgallery')
            ),
            'load_more_horisontal_padding_tiles' => array(
                'type' => 'number',
                'label' => __('Horizontal Padding', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more horizontal padding in px', 'gdgallery')
            ),
            'load_more_border_width_tiles' => array(
                'type' => 'number',
                'label' => __('Border Width', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more button border width in px', 'gdgallery')
            ),
            'load_more_border_radius_tiles' => array(
                'type' => 'number',
                'label' => __('Border Radius', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more button border radius in px', 'gdgallery')
            ),
            'load_more_border_color_tiles' => array(
                'type' => 'color',
                'label' => __('Border Color', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more button border color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_color_tiles' => array(
                'type' => 'color',
                'label' => __('Button Text Color', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more button text color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_background_color_tiles' => array(
                'type' => 'color',
                'label' => __('Background Color', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more button background color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_font_family_tiles' => array(
                'type' => 'select',
                'label' => __('Font Type', 'gdgallery'),
                'options' => array(
                    'monospace' => __('monospace', 'gdgallery'),
                    'cursive' => __('cursive', 'gdgallery'),
                    'fantasy' => __('fantasy', 'gdgallery'),
                    'sans-serif' => __('sans-serif', 'gdgallery'),
                    'serif' => __('serif', 'gdgallery'),
                ),
                'section' => 'load_more_tiles',
                'help' => __('Choose load more button text font type', 'gdgallery')
            ),
            'load_more_hover_border_color_tiles' => array(
                'type' => 'color',
                'label' => __('On Hover Border Color', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set load more button border on hover color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_hover_color_tiles' => array(
                'type' => 'color',
                'label' => __('Text On Hover Color', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set text on hover color in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_hover_background_color_tiles' => array(
                'type' => 'color',
                'label' => __('On Hover Background Color', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set background color for load more button text on hover in HEXadecimal color system', 'gdgallery')
            ),
            'load_more_loader_tiles' => array(
                'type' => 'checkbox',
                'label' => __('Show Loading Icon', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Choose whether to turn loading icon on/off', 'gdgallery')
            ),
            'load_more_loader_color_tiles' => array(
                'type' => 'color',
                'label' => __('Loading Icon color', 'gdgallery'),
                'section' => 'load_more_tiles',
                'help' => __('Set color for loading icon in HEXadecimal color system', 'gdgallery')
            ),


            'pagination_position_tiles' => array(
                'type' => 'select',
                'label' => __('Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination position', 'gdgallery')
            ),
            'pagination_font_size_tiles' => array(
                'type' => 'number',
                'label' => __('Font Size', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination font size in px', 'gdgallery')
            ),
            'pagination_vertical_padding_tiles' => array(
                'type' => 'number',
                'label' => __('Vertical Padding', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination vertical padding in px', 'gdgallery')
            ),
            'pagination_horisontal_padding_tiles' => array(
                'type' => 'number',
                'label' => __('Horizontal Padding', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination horizontal padding in px', 'gdgallery')
            ),
            'pagination_margin_tiles' => array(
                'type' => 'number',
                'label' => __('Margin', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set margin value between elements in px', 'gdgallery')
            ),
            'pagination_border_width_tiles' => array(
                'type' => 'number',
                'label' => __('Border Width', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set border width value in px', 'gdgallery')
            ),
            'pagination_border_radius_tiles' => array(
                'type' => 'number',
                'label' => __('Border Radius', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set border radius value in px', 'gdgallery')
            ),
            'pagination_border_color_tiles' => array(
                'type' => 'color',
                'label' => __('Border Color', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set border color in HEXadecimal color system', 'gdgallery')
            ),
            'pagination_color_tiles' => array(
                'type' => 'color',
                'label' => __('Pagination Elements Color', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination elements color in HEXadecimal color system', 'gdgallery')
            ),
            'pagination_background_color_tiles' => array(
                'type' => 'color',
                'label' => __('Background Color', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination elements background color in HEXadecimal color system', 'gdgallery')
            ),
            'pagination_font_family_tiles' => array(
                'type' => 'select',
                'label' => __('Font Type', 'gdgallery'),
                'options' => array(
                    'monospace' => __('monospace', 'gdgallery'),
                    'cursive' => __('cursive', 'gdgallery'),
                    'fantasy' => __('fantasy', 'gdgallery'),
                    'sans-serif' => __('sans-serif', 'gdgallery'),
                    'serif' => __('serif', 'gdgallery'),
                ),
                'section' => 'pagination_tiles',
                'help' => __('Choose pagination element font type', 'gdgallery')
            ),
            'pagination_hover_border_color_tiles' => array(
                'type' => 'color',
                'label' => __('On Hover Border Color', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination element border on hover color', 'gdgallery')
            ),
            'pagination_hover_color_tiles' => array(
                'type' => 'color',
                'label' => __('On Hover Color', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination element on hover color', 'gdgallery')
            ),
            'pagination_hover_background_color_tiles' => array(
                'type' => 'color',
                'label' => __('On Hover Background Color', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set pagination element background color on hover', 'gdgallery')
            ),
            'pagination_nav_type_tiles' => array(
                'type' => 'select',
                'label' => __('Navigation Type', 'gdgallery'),
                'options' => array(
                    '0' => __('Arrows', 'gdgallery'),
                    '1' => __('Text', 'gdgallery'),
                    '2' => __('Only Numbers', 'gdgallery')
                ),
                'section' => 'pagination_tiles',
                'help' => __('Choose navigation type', 'gdgallery')
            ),
            'pagination_nav_text_tiles' => array(
                'type' => 'text',
                'label' => __('Navigation Text', 'gdgallery'),
                'section' => 'pagination_tiles',
                'help' => __('Set navigation text. Note that text must be separated with comma', 'gdgallery')
            ),
            'pagination_nearby_pages_tiles' => array(
                'type' => 'select',
                'label' => __('Visible Page Quantity', 'gdgallery'),
                'options' => array(
                    'All' => __('All', 'gdgallery'),
                    '1' => "1",
                    '2' => "2",
                    '3' => "3",
                    '4' => "4",
                    '5' => "5",
                ),
                'section' => 'pagination_tiles',
                'help' => __('Set visible page quantity', 'gdgallery')
            ),

            /*****************  carousel options  ******************/
            'show_title_carousel' => array(
                'type' => 'select',
                'label' => __('Title Option', 'gdgallery'),
                'options' => array(
                    '0' => __('Always on', 'gdgallery'),
                    '1' => __('On hover', 'gdgallery'),
                    '2' => __('Disable', 'gdgallery')
                ),
                'section' => 'element_style_carousel',
                'help' => __('Choose how to display Image title', 'gdgallery')
            ),
            'title_position_carousel' => array(
                'type' => 'select',
                'label' => __('Title Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery')
                ),
                'section' => 'element_style_carousel',
                'help' => __('Choose title position', 'gdgallery')
            ),
            'title_vertical_position_carousel' => array(
                'type' => 'select',
                'label' => __('Title Vertical Position', 'gdgallery'),
                'options' => array(
                    'inside_top' => __('Top', 'gdgallery'),
                    'inside_bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'element_style_carousel',
                'help' => __('Choose title position', 'gdgallery')
            ),
            'title_appear_type_carousel' => array(
                'type' => 'select',
                'label' => __('Title On Hover Type', 'gdgallery'),
                'options' => array(
                    'slide' => __('Slide', 'gdgallery'),
                    'fade' => __('Fade', 'gdgallery'),
                ),
                'section' => 'element_style_carousel',
                'help' => __('Choose title on hover effect', 'gdgallery')
            ),
            'title_size_carousel' => array(
                'type' => 'number',
                'label' => __('Title Font Size', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set title font size in px', 'gdgallery')
            ),
            'title_color_carousel' => array(
                'type' => 'color',
                'label' => __('Title Color', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Choose title color in HEXadecimal color system', 'gdgallery')
            ),
            'title_background_color_carousel' => array(
                'type' => 'color',
                'label' => __('Title Background Color', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set title background color in HEXadecimal color system', 'gdgallery')
            ),
            'title_background_opacity_carousel' => array(
                'type' => 'number',
                'label' => __('Title Background Opacity (%)', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set title background opacity in percentage', 'gdgallery')
            ),
            'margin_carousel' => array(
                'type' => 'number',
                'label' => __('Image Margin', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set image margin in px', 'gdgallery')
            ),
            'width_carousel' => array(
                'type' => 'number',
                'label' => __('Image Width', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set image width in px', 'gdgallery')
            ),
            'height_carousel' => array(
                'type' => 'number',
                'label' => __('Image Height', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set image height in px', 'gdgallery')
            ),
            'position_carousel' => array(
                'type' => 'select',
                'label' => __('Carousel Position', 'gdgallery'),
                'options' => array(
                    'left' => __('left', 'gdgallery'),
                    'center' => __('center', 'gdgallery'),
                    'right' => __('right', 'gdgallery'),
                ),
                'section' => 'element_style_carousel',
                'help' => __('Choose carousel position', 'gdgallery')
            ),
            'show_background_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Background', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Choose whether to turn background on/off', 'gdgallery')
            ),
            'background_color_carousel' => array(
                'type' => 'color',
                'label' => __('Background Color', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set carousel background color', 'gdgallery')
            ),
            'border_width_carousel' => array(
                'type' => 'number',
                'label' => __('Image Border Width', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set image border width in px', 'gdgallery')
            ),
            'border_color_carousel' => array(
                'type' => 'color',
                'label' => __('Image Border Color', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set image border color in HEXadecimal color system', 'gdgallery')
            ),
            'border_radius_carousel' => array(
                'type' => 'number',
                'label' => __('Image Border Radius', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set element border radius in px', 'gdgallery')
            ),

            'show_icons_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Lightbox', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Choose whether to turn Lightbox on/off', 'gdgallery')
            ),
            'show_link_icon_carousel' => array(
                'type' => 'checkbox',
                'label' => __('URL Icon', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Choose whether to turn URL icon on/off', 'gdgallery')
            ),
            'item_as_link_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Image As Link', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Set image as link', 'gdgallery')
            ),
            'link_new_tab_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Open Link In New Tab', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Choose whether to open the link in a new tab', 'gdgallery')
            ),
            'on_hover_overlay_carousel' => array(
                'type' => 'checkbox',
                'label' => __('On Hover Overlay', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Turn on hover overlay on/off', 'gdgallery')
            ),
            'image_hover_effect_carousel' => array(
                'type' => 'select',
                'label' => __('Image Hover Effect', 'gdgallery'),
                'options' => array(
                    'blur' => __('none', 'gdgallery'),
                    'bw' => __('Black and White', 'gdgallery'),
                    'sepia' => __('Sepia', 'gdgallery')
                ),
                'section' => 'element_style_carousel',
                'help' => __('Choose image hover effect', 'gdgallery')
            ),
            'image_hover_effect_reverse_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Image On Hover Reversed Effect', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Choose whether to turn reversed effect on/off', 'gdgallery')
            ),
            'shadow_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Image Element Shadow', 'gdgallery'),
                'section' => 'element_style_carousel',
                'help' => __('Choose whether to turn image element shadow on/off', 'gdgallery')
            ),


            'lightbox_type_carousel' => array(
                'type' => 'select',
                'label' => __('Lightbox Type', 'gdgallery'),
                'options' => array(
                    'wide' => __('Wide', 'gdgallery'),
                    'compact' => __('Compact', 'gdgallery')
                ),
                'section' => 'element_style_carousel',
                'help' => __('Choose Lightbox type', 'gdgallery')
            ),
            'enable_nav_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Enable Navigation', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Turn navigation on/off', 'gdgallery')
            ),
            'nav_num_carousel' => array(
                'type' => 'number',
                'label' => __('Number Of Navigated Elements ', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Set number of elements to scroll after clicking on next/prev button', 'gdgallery'),
                "max" => 5
            ),
            'scroll_duration_carousel' => array(
                'type' => 'number',
                'label' => __('Scrolling Duration (ms)', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Set scrolling duration in ms', 'gdgallery')
            ),
            'autoplay_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Autoplay', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Choose whether to turn autoplay on/off', 'gdgallery')
            ),
            'autoplay_timeout_carousel' => array(
                'type' => 'number',
                'label' => __('Autoplay Timeout (ms)', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Set autoplay timeout in ms', 'gdgallery')
            ),
            'autoplay_direction_carousel' => array(
                'type' => 'select',
                'label' => __('Autoplay Direction', 'gdgallery'),
                'options' => array(
                    'left' => __('left', 'gdgallery'),
                    'right' => __('right', 'gdgallery')
                ),
                'section' => 'components_carousel',
                'help' => __('Choose autoplay direction', 'gdgallery')
            ),
            'autoplay_pause_hover_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Autoplay Pause On Hover', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Choose whether to turn autoplay pause on hover on/off', 'gdgallery')
            ),
            'enable_nav_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Enable Navigation', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Choose whether to turn navigation on/off', 'gdgallery')
            ),
            'nav_vertical_position_carousel' => array(
                'type' => 'select',
                'label' => __('Navigation Vertical Position', 'gdgallery'),
                'options' => array(
                    'top' => __('Top', 'gdgallery'),
                    'bottom' => __('Bottom', 'gdgallery')
                ),
                'section' => 'components_carousel',
                'help' => __('Choose navigation vertical position', 'gdgallery')
            ),
            'nav_horisontal_position_carousel' => array(
                'type' => 'select',
                'label' => __('Navigation Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery')
                ),
                'section' => 'components_carousel',
                'help' => __('Choose navigation horizontal position', 'gdgallery')
            ),
            'play_icon_carousel' => array(
                'type' => 'checkbox',
                'label' => __('Play/Pause Icon', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Choose whether to turn play/pause icon on/off', 'gdgallery')
            ),
            'icon_space_carousel' => array(
                'type' => 'number',
                'label' => __('Space Between Icons', 'gdgallery'),
                'section' => 'components_carousel',
                'help' => __('Set space between icons in px', 'gdgallery')
            ),


            /********* Slider options ************/
            'width_slider' => array(
                'type' => 'number',
                'label' => __('Image Width', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set image width in px', 'gdgallery')
            ),
            'height_slider' => array(
                'type' => 'number',
                'label' => __('Image Height', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set image height in px', 'gdgallery')
            ),
            'autoplay_slider' => array(
                'type' => 'checkbox',
                'label' => __('Autoplay', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn autoplay on/off', 'gdgallery')
            ),
            'play_interval_slider' => array(
                'type' => 'number',
                'label' => __('Autoplay Timeout (ms)', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set autoplay timeout in ms', 'gdgallery')
            ),
            'transition_speed_slider' => array(
                'type' => 'number',
                'label' => __('Autoplay Speed (ms)', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set autoplay speed in ms', 'gdgallery')
            ),
            'pause_on_hover_slider' => array(
                'type' => 'checkbox',
                'label' => __('Pause On Hover', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn pause on hover on/off', 'gdgallery')
            ),
            'scale_mode_slider' => array(
                'type' => 'select',
                'label' => __('Image Behavior', 'gdgallery'),
                'options' => array(
                    'fit' => __('Fit', 'gdgallery'),
                    'fill' => __('Fill', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose image behavior type', 'gdgallery')
            ),
            'transition_slider' => array(
                'type' => 'select',
                'label' => __('Effects', 'gdgallery'),
                'options' => array(
                    'slide' => __('Slide', 'gdgallery'),
                    'fade' => __('Fade', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose image effect type', 'gdgallery')
            ),


            'loader_type_slider' => array(
                'type' => 'select',
                'label' => __('Loading Icon Type', 'gdgallery'),
                'options' => array(
                    '1' => __('type 1', 'gdgallery'),
                    '2' => __('type 2', 'gdgallery'),
                    '3' => __('type 3', 'gdgallery'),
                    '4' => __('type 4', 'gdgallery'),
                    '5' => __('type 5', 'gdgallery'),
                    '6' => __('type 6', 'gdgallery'),
                    '7' => __('type 7', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose loading type', 'gdgallery'),
                'html_class' => array("show_loader")
            ),
            'loader_color_slider' => array(
                'type' => 'select',
                'label' => __('Loading Icon Color', 'gdgallery'),
                'options' => array(
                    'white' => __('white', 'gdgallery'),
                    'black' => __('black', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose loading color', 'gdgallery')
            ),
            'bullets_slider' => array(
                'type' => 'checkbox',
                'label' => __('Bullets', 'gdgallery'),
                'section' => 'components_slider',
                'help' => __('Choose whether to turn bullets on/off', 'gdgallery')
            ),
            'bullets_horisontal_position_slider' => array(
                'type' => 'select',
                'label' => __('Bullets Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'components_slider',
                'help' => __('Choose bullets horizontal position', 'gdgallery')
            ),
            'bullets_vertical_position_slider' => array(
                'type' => 'select',
                'label' => __('Bullets Vertical Position', 'gdgallery'),
                'options' => array(
                    'top' => __('Top', 'gdgallery'),
                    'bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'components_slider',
                'help' => __('Choose bullets vertical position', 'gdgallery')
            ),
            'arrows_slider' => array(
                'type' => 'checkbox',
                'label' => __('Arrows', 'gdgallery'),
                'section' => 'components_slider',
                'help' => __('Choose whether to turn arrows on/off', 'gdgallery')
            ),
            'controls_always_on_slider' => array(
                'type' => 'checkbox',
                'label' => __('Controls Always On', 'gdgallery'),
                'section' => 'components_slider',
                'help' => __('Choose whether to turn controls always on on/off', 'gdgallery')
            ),
            'progress_indicator_slider' => array(
                'type' => 'checkbox',
                'label' => __('Progress Indicator', 'gdgallery'),
                'section' => 'components_slider',
                'help' => __('Choose whether to turn progress indicator on/off')
            ),
            'progress_indicator_type_slider' => array(
                'type' => 'select',
                'label' => __('Progress Indicator Type', 'gdgallery'),
                'options' => array(
                    'pie' => __('Pie', 'gdgallery'),
                    'bar' => __('Bar', 'gdgallery'),
                ),
                'section' => 'components_slider',
                'help' => __('Choose progress indicator type', 'gdgallery')
            ),
            'progress_indicator_horisontal_position_slider' => array(
                'type' => 'select',
                'label' => __('Progress Indicator Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'components_slider',
                'help' => __('Choose progress indicator position', 'gdgallery')
            ),
            'progress_indicator_vertical_position_slider' => array(
                'type' => 'select',
                'label' => __('Progress Indicator Vertical Position', 'gdgallery'),
                'options' => array(
                    'top' => __('Top', 'gdgallery'),
                    'bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'components_slider',
                'help' => __('Choose progress indicator position', 'gdgallery')
            ),
            'play_slider' => array(
                'type' => 'checkbox',
                'label' => __('Play/Pause Button', 'gdgallery'),
                'section' => 'components_slider',
                'help' => __('Choose whether to turn play/pause button on/off', 'gdgallery')
            ),
            'play_horizontal_position_slider' => array(
                'type' => 'select',
                'label' => __('Play/Pause Button Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'components_slider',
                'help' => __('Choose play/pause button horizontal position', 'gdgallery')
            ),
            'play_vertical_position_slider' => array(
                'type' => 'select',
                'label' => __('Play/Pause Button Vertical Position', 'gdgallery'),
                'options' => array(
                    'top' => __('Top', 'gdgallery'),
                    'bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'components_slider',
                'help' => __('Choose play/pause button vertical position', 'gdgallery')
            ),
            'fullscreen_slider' => array(
                'type' => 'checkbox',
                'label' => __('Fullscreen', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn fullscreen button on/off', 'gdgallery')
            ),
            'fullscreen_horisontal_position_slider' => array(
                'type' => 'select',
                'label' => __('Fullscreen Icon Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose fullscreen icon horizontal position', 'gdgallery')
            ),
            'fullscreen_vertical_position_slider' => array(
                'type' => 'select',
                'label' => __('Fullscreen  Icon Vertical Position', 'gdgallery'),
                'options' => array(
                    'top' => __('Top', 'gdgallery'),
                    'bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose fullscreen icon vertical position', 'gdgallery')
            ),
            'zoom_slider' => array(
                'type' => 'checkbox',
                'label' => __('Zoom', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn zoom control on/off', 'gdgallery')
            ),
            'zoom_panel_slider' => array(
                'type' => 'checkbox',
                'label' => __('Zoom Panel', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn zoom control on/off', 'gdgallery')
            ),
            'zoom_horisontal_panel_position_slider' => array(
                'type' => 'select',
                'label' => __('Zoom Panel Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose zoom panel horizontal position', 'gdgallery')
            ),
            'zoom_vertical_panel_position_slider' => array(
                'type' => 'select',
                'label' => __('Zoom Panel Vertical Position', 'gdgallery'),
                'options' => array(
                    'top' => __('Top', 'gdgallery'),
                    'bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose zoom panel vertical position', 'gdgallery')
            ),

            'video_play_type_slider' => array(
                'type' => 'select',
                'label' => __('Video Play Button Type', 'gdgallery'),
                'options' => array(
                    'round' => __('Round', 'gdgallery'),
                    'square' => __('Square', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose video play button type', 'gdgallery')
            ),
            'text_panel_slider' => array(
                'type' => 'checkbox',
                'label' => __('Text Panel', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn text panel on/off', 'gdgallery')
            ),
            'text_panel_always_on_slider' => array(
                'type' => 'checkbox',
                'label' => __('Text Panel Always On', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn text panel always on on/off', 'gdgallery')
            ),
            'text_title_slider' => array(
                'type' => 'checkbox',
                'label' => __('Title', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn title on/off', 'gdgallery')
            ),
            'text_panel_title_size_slider' => array(
                'type' => 'number',
                'label' => __('Title Font Size', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set title font size in px', 'gdgallery')
            ),
            'text_panel_title_color_slider' => array(
                'type' => 'color',
                'label' => __('Title Color', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set title color in HEXadecimal color system', 'gdgallery')
            ),
            'text_description_slider' => array(
                'type' => 'checkbox',
                'label' => __('Description', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn description on/off', 'gdgallery')
            ),
            'text_panel_desc_size_slider' => array(
                'type' => 'number',
                'label' => __('Description Font Size', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set description font size in px', 'gdgallery')
            ),
            'text_panel_desc_color_slider' => array(
                'type' => 'color',
                'label' => __('Description Color', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set description color in HEXadecimal color system', 'gdgallery')
            ),
            'text_panel_bg_slider' => array(
                'type' => 'checkbox',
                'label' => __('Text Panel Background', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn text panel background on/off', 'gdgallery')
            ),
            'text_panel_bg_color_slider' => array(
                'type' => 'color',
                'label' => __('Text Panel background Color', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set text panel background color in HEXadecimal color system', 'gdgallery')
            ),
            'text_panel_bg_opacity_slider' => array(
                'type' => 'number',
                'label' => __('Text Panel background Opacity (%)', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set Text Panel background Opacity in percentage', 'gdgallery')
            ),
            'carousel_slider' => array(
                'type' => 'checkbox',
                'label' => __('Gallery Loop', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn the gallery loop on/off', 'gdgallery')
            ),
            'playlist_slider' => array(
                'type' => 'checkbox',
                'label' => __('Playlist', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Choose whether to turn playlist on/off', 'gdgallery')
            ),
            'playlist_pos_slider' => array(
                'type' => 'select',
                'label' => __('Playlist Position', 'gdgallery'),
                'options' => array(
                    'right' => __('Right', 'gdgallery'),
                    'left' => __('Left', 'gdgallery'),
                    'bottom' => __('Bottom', 'gdgallery'),
                    'top' => __('Top', 'gdgallery'),
                ),
                'section' => 'element_style_slider',
                'help' => __('Choose playlist position', 'gdgallery')
            ),
            'thumb_width_slider' => array(
                'type' => 'number',
                'label' => __('Thumbnails width', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set playlist thumbnails width in px', 'gdgallery')
            ),
            'thumb_height_slider' => array(
                'type' => 'number',
                'label' => __('Thumbnails Height', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set playlist thumbnails height in px', 'gdgallery')
            ),
            'playlist_bg_slider' => array(
                'type' => 'color',
                'label' => __('Playlist background color', 'gdgallery'),
                'section' => 'element_style_slider',
                'help' => __('Set playlist background color in HEXadecimal color system', 'gdgallery')
            ),


            /********************  Grid options  ***********************/
            'width_grid' => array(
                'type' => 'number',
                'label' => __('Image Width', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose image width in px', 'gdgallery')
            ),
            'height_grid' => array(
                'type' => 'number',
                'label' => __('Image Height', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose image height in px', 'gdgallery')
            ),
            'num_rows_grid' => array(
                'type' => 'number',
                'label' => __('Gallery Rows Quantity', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set space between rows in px', 'gdgallery')
            ),
            'space_cols_grid' => array(
                'type' => 'number',
                'label' => __('Space Between Columns', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set space between columns in px', 'gdgallery')
            ),
            'space_rows_grid' => array(
                'type' => 'number',
                'label' => __('Space Between Rows', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set space between rows in px', 'gdgallery')
            ),
            'gallery_width_grid' => array(
                'type' => 'number',
                'label' => __('Container Width (%)', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set container width in percentage', 'gdgallery'),
                'max' => '100'
            ),
            'gallery_bg_grid' => array(
                'type' => 'checkbox',
                'label' => __('Gallery Background', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose whether to turn gallery background on/off', 'gdgallery')
            ),
            'gallery_bg_color_grid' => array(
                'type' => 'color',
                'label' => __('Gallery Background Color', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set gallery background color in HEXadecimal color system', 'gdgallery')
            ),

            'show_title_grid' => array(
                'type' => 'select',
                'label' => __('Title Option', 'gdgallery'),
                'options' => array(
                    '0' => __('Always On', 'gdgallery'),
                    '1' => __('On Hover', 'gdgallery'),
                    '2' => __('Never', 'gdgallery')
                ),
                'section' => 'element_style_grid',
                'help' => __('Choose how to display Image title', 'gdgallery')
            ),
            'title_position_grid' => array(
                'type' => 'select',
                'label' => __('Title Horizontal Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery')
                ),
                'section' => 'element_style_grid',
                'help' => __('Choose title horizontal position', 'gdgallery')
            ),
            'title_vertical_position_grid' => array(
                'type' => 'select',
                'label' => __('Title Vertical Position', 'gdgallery'),
                'options' => array(
                    'inside_top' => __('Top', 'gdgallery'),
                    'inside_bottom' => __('Bottom', 'gdgallery'),
                ),
                'section' => 'element_style_grid',
                'help' => __('Choose title vertical position', 'gdgallery')
            ),
            'title_appear_type_grid' => array(
                'type' => 'select',
                'label' => __('Title On Hover Type', 'gdgallery'),
                'options' => array(
                    'slide' => __('Slide', 'gdgallery'),
                    'fade' => __('Fade', 'gdgallery'),
                ),
                'section' => 'element_style_grid',
                'help' => __('Choose title on hover effect', 'gdgallery')
            ),
            'title_size_grid' => array(
                'type' => 'number',
                'label' => __('Title Font Size', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('set title font size in px', 'gdgallery')
            ),
            'title_color_grid' => array(
                'type' => 'color',
                'label' => __('Title Color', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set title color in HEXadecimal color system', 'gdgallery')
            ),
            'title_background_color_grid' => array(
                'type' => 'color',
                'label' => __('Title Background Color', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose title background color in HEXadecimal color system', 'gdgallery')
            ),
            'title_background_opacity_grid' => array(
                'type' => 'number',
                'label' => __('Title Background Opacity (%)', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set title background opacity in percentage', 'gdgallery')
            ),
            'border_width_grid' => array(
                'type' => 'number',
                'label' => __('Image Border Width', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set image border width in px', 'gdgallery')
            ),
            'border_color_grid' => array(
                'type' => 'color',
                'label' => __('Image Border Color', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set image border color in HEXadecimal color system', 'gdgallery')
            ),
            'border_radius_grid' => array(
                'type' => 'number',
                'label' => __('Image Border Radius', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set image border radius in px', 'gdgallery')
            ),

            'show_icons_grid' => array(
                'type' => 'checkbox',
                'label' => __('Lightbox', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose whether to turn Lightbox on/off', 'gdgallery')
            ),
            'show_link_icon_grid' => array(
                'type' => 'checkbox',
                'label' => __('URL Icon', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose whether to turn URL icon on/off', 'gdgallery')
            ),
            'item_as_link_grid' => array(
                'type' => 'checkbox',
                'label' => __('Image As Link', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Set image as link', 'gdgallery')
            ),
            'link_new_tab_grid' => array(
                'type' => 'checkbox',
                'label' => __('Open Link In New Tab', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose whether to open the link in a new tab', 'gdgallery')
            ),
            'on_hover_overlay_grid' => array(
                'type' => 'checkbox',
                'label' => __('On Hover Overlay', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Turn on hover overlay on/off', 'gdgallery')
            ),
            'image_hover_effect_grid' => array(
                'type' => 'select',
                'label' => __('Image Hover Effect', 'gdgallery'),
                'options' => array(
                    'blur' => __('none', 'gdgallery'),
                    'bw' => __('Black and White', 'gdgallery'),
                    'sepia' => __('Sepia', 'gdgallery')
                ),
                'section' => 'element_style_grid',
                'help' => __('Choose image hover effect', 'gdgallery')
            ),
            'image_hover_effect_reverse_grid' => array(
                'type' => 'checkbox',
                'label' => __('Image On Hover Reversed Effect', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose whether to turn reversed effect on/off', 'gdgallery')
            ),
            'shadow_grid' => array(
                'type' => 'checkbox',
                'label' => __('Image Element Shadow', 'gdgallery'),
                'section' => 'element_style_grid',
                'help' => __('Choose whether to turn image element shadow on/off', 'gdgallery')
            ),
            'lightbox_type_grid' => array(
                'type' => 'select',
                'label' => __('Lightbox Type', 'gdgallery'),
                'options' => array(
                    'wide' => __('Wide', 'gdgallery'),
                    'compact' => __('Compact', 'gdgallery')
                ),
                'section' => 'element_style_grid',
                'help' => __('Choose Lightbox type', 'gdgallery')
            ),
            'nav_type_grid' => array(
                'type' => 'select',
                'label' => __('Navigation Type', 'gdgallery'),
                'options' => array(
                    'bullets' => __('Bullets', 'gdgallery'),
                    'arrows' => __('Arrows', 'gdgallery'),
                ),
                'section' => 'components_grid',
                'help' => __('Choose navigation type', 'gdgallery')
            ),
            'nav_position_grid' => array(
                'type' => 'select',
                'label' => __('Navigation Position', 'gdgallery'),
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery')
                ),
                'section' => 'components_grid',
                'help' => __('Choose navigation position', 'gdgallery')
            ),
            'nav_offset_grid' => array(
                'type' => 'number',
                'label' => __('Navigation Offset', 'gdgallery'),
                'section' => 'components_grid',
                'help' => __('Navigation Offset in px', 'gdgallery')
            ),
            'bullets_margin_grid' => array(
                'type' => 'number',
                'label' => __('Bullets Margin from Top', 'gdgallery'),
                'section' => 'components_grid',
                'help' => __('Set bullets margin from the top in px', 'gdgallery')
            ),
            'bullets_color_grid' => array(
                'type' => 'select',
                'label' => __('Bullets color', 'gdgallery'),
                'options' => array(
                    'gray' => __('Gray', 'gdgallery'),
                    'blue' => __('Blue', 'gdgallery'),
                    'brown' => __('Brown', 'gdgallery'),
                    'green' => __('Green', 'gdgallery'),
                    'red' => __('Red', 'gdgallery'),
                ),
                'section' => 'components_grid',
                'help' => __('Choose bullets color type', 'gdgallery')
            ),
            'bullets_space_between_grid' => array(
                'type' => 'number',
                'label' => __('Space Between Bullets', 'gdgallery'),
                'section' => 'components_grid',
                'help' => __('Set space between bullets in px', 'gdgallery')
            ),
            'arrows_margin_grid' => array(
                'type' => 'number',
                'label' => __('Arrows margin from Top', 'gdgallery'),
                'section' => 'components_grid',
                'help' => __('Set arrows margin from the top in px', 'gdgallery')
            ),
            'arrows_space_between_grid' => array(
                'type' => 'number',
                'label' => __('Space Between Arrows', 'gdgallery'),
                'section' => 'components_grid',
                'help' => __('Set space between arrows in px', 'gdgallery')
            ),


            /*****  Lightbox ****/
            /*****  wide ****/
            'arrows_offset_wide' => array(
                'type' => 'number',
                'label' => __('Arrows Offset', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set arrows offset in px', 'gdgallery')
            ),
            'overlay_color_wide' => array(
                'type' => 'color',
                'label' => __('Overlay Color', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set overlay color in HEXadecimal color system', 'gdgallery')
            ),
            'overlay_opacity_wide' => array(
                'type' => 'number',
                'label' => __('Overlay Opacity (%)', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set overlay opacity in percentage', 'gdgallery')
            ),
            'top_panel_bg_color_wide' => array(
                'type' => 'color',
                'label' => __('Top Panel Background Color', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set top panel background color in HEXadecimal color system', 'gdgallery')
            ),
            'top_panel_opacity_wide' => array(
                'type' => 'number',
                'label' => __('Top Panel Opacity (%)', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set top panel opacity in percentage', 'gdgallery')
            ),
            'show_numbers_wide' => array(
                'type' => 'checkbox',
                'label' => __('Image Count', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Choose whether to turn image count on/off', 'gdgallery')
            ),
            'number_size_wide' => array(
                'type' => 'number',
                'label' => __('Image Count Text Size', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set image count text font size in px', 'gdgallery')
            ),
            'number_color_wide' => array(
                'type' => 'color',
                'label' => __('Image Count Text Color', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set image count text color in in HEXadecimal color system', 'gdgallery')
            ),
            'image_border_width_wide' => array(
                'type' => 'number',
                'label' => __('Image Border Width', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set image border width in px', 'gdgallery')
            ),
            'image_border_color_wide' => array(
                'type' => 'color',
                'label' => __('Image Border Color', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set image border color in HEXadecimal color system', 'gdgallery')
            ),
            'image_border_radius_wide' => array(
                'type' => 'number',
                'label' => __('Image Border radius', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set image border radius in px', 'gdgallery')
            ),
            'image_shadow_wide' => array(
                'type' => 'checkbox',
                'label' => __('Image Shadow', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Choose whether to turn image shadow on/off', 'gdgallery')
            ),
            'swipe_control_wide' => array(
                'type' => 'checkbox',
                'label' => __('Image Swipe', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Choose whether to turn Image Swipe on/off', 'gdgallery')
            ),
            'zoom_control_wide' => array(
                'type' => 'checkbox',
                'label' => __('Image Zoom', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Choose whether to turn image zoom on/off', 'gdgallery')
            ),

            'show_text_panel_wide' => array(
                'type' => 'checkbox',
                'label' => __('Text panel', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Choose whether to turn text panel on/off', 'gdgallery')
            ),
            'texpanel_paddind_vert_wide' => array(
                'type' => 'number',
                'label' => __('Text panel Vertical Padding', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set text panel vertical padding in px', 'gdgallery')
            ),
            'texpanel_paddind_hor_wide' => array(
                'type' => 'number',
                'label' => __('Text panel Horizontal Padding', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set text panel horizontal padding in px', 'gdgallery')
            ),
            'enable_title_wide' => array(
                'type' => 'checkbox',
                'label' => __(' Title', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Choose whether to turn title on/off', 'gdgallery')
            ),
            'title_color_wide' => array(
                'type' => 'color',
                'label' => __('Title Color', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set title color in HEXadecimal color system', 'gdgallery')
            ),
            'title_font_size_wide' => array(
                'type' => 'number',
                'label' => __('Title Font Size', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set title font size in px', 'gdgallery')
            ),
            'enable_desc_wide' => array(
                'type' => 'checkbox',
                'label' => __('Description', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Choose whether to turn description on/off', 'gdgallery')
            ),
            'desc_color_wide' => array(
                'type' => 'color',
                'label' => __('Description Color', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set description color in HEXadecimal color system', 'gdgallery')
            ),

            'desc_font_size_wide' => array(
                'type' => 'number',
                'label' => __('Description Font Size', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Set description font size in px', 'gdgallery')
            ),
            'text_position_wide' => array(
                'type' => 'select',
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery')
                ),
                'label' => __('Text Position', 'gdgallery'),
                'section' => 'wide_lightbox',
                'help' => __('Choose text position', 'gdgallery')
            ),

            /*****  compact ****/
            'arrows_offset_compact' => array(
                'type' => 'number',
                'label' => __('Arrows Offset', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set arrows offset in px', 'gdgallery')
            ),
            'overlay_color_compact' => array(
                'type' => 'color',
                'label' => __('Overlay Color', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set overlay color in HEXadecimal color system', 'gdgallery')
            ),
            'overlay_opacity_compact' => array(
                'type' => 'number',
                'label' => __('Overlay Opacity (%)', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set overlay opacity in percentage', 'gdgallery')
            ),
            'show_numbers_compact' => array(
                'type' => 'checkbox',
                'label' => __('Image Count', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Choose whether to turn image count on/off', 'gdgallery')
            ),
            'number_size_compact' => array(
                'type' => 'number',
                'label' => __('Image Count Text Size', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set image count text font size in px', 'gdgallery')
            ),
            'number_color_compact' => array(
                'type' => 'color',
                'label' => __('Image Count Text Color', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set image count text color in in HEXadecimal color system', 'gdgallery')
            ),
            'image_border_width_compact' => array(
                'type' => 'number',
                'label' => __('Image Border Width', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set image border width in px', 'gdgallery')
            ),
            'image_border_color_compact' => array(
                'type' => 'color',
                'label' => __('Image Border Color', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set image border color in HEXadecimal color system', 'gdgallery')
            ),
            'image_border_radius_compact' => array(
                'type' => 'number',
                'label' => __('Image Border radius', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set image border radius in px', 'gdgallery')
            ),
            'image_shadow_compact' => array(
                'type' => 'checkbox',
                'label' => __('Image Shadow', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Choose whether to turn image shadow on/off', 'gdgallery')
            ),
            'swipe_control_compact' => array(
                'type' => 'checkbox',
                'label' => __('Image Swipe', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Choose whether to turn Image Swipe on/off', 'gdgallery')
            ),
            'zoom_control_compact' => array(
                'type' => 'checkbox',
                'label' => __('Image Zoom', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Choose whether to turn image zoom on/off', 'gdgallery')
            ),

            'show_text_panel_compact' => array(
                'type' => 'checkbox',
                'label' => __('Text panel', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Choose whether to turn text panel on/off', 'gdgallery')
            ),
            'texpanel_paddind_vert_compact' => array(
                'type' => 'number',
                'label' => __('Text panel Vertical Padding', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set text panel vertical padding in px', 'gdgallery')
            ),
            'texpanel_paddind_hor_compact' => array(
                'type' => 'number',
                'label' => __('Text panel Horizontal Padding', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set text panel horizontal padding in px', 'gdgallery')
            ),
            'enable_title_compact' => array(
                'type' => 'checkbox',
                'label' => __(' Title', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Choose whether to turn title on/off', 'gdgallery')
            ),
            'title_color_compact' => array(
                'type' => 'color',
                'label' => __('Title Color', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set title color in HEXadecimal color system', 'gdgallery')
            ),
            'title_font_size_compact' => array(
                'type' => 'number',
                'label' => __('Title Font Size', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set title font size in px', 'gdgallery')
            ),
            'enable_desc_compact' => array(
                'type' => 'checkbox',
                'label' => __('Description', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Choose whether to turn description on/off', 'gdgallery')
            ),
            'desc_color_compact' => array(
                'type' => 'color',
                'label' => __('Description Color', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set description color in HEXadecimal color system', 'gdgallery')
            ),

            'desc_font_size_compact' => array(
                'type' => 'number',
                'label' => __('Description Font Size', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Set description font size in px', 'gdgallery')
            ),
            'text_position_compact' => array(
                'type' => 'select',
                'options' => array(
                    'left' => __('Left', 'gdgallery'),
                    'center' => __('Center', 'gdgallery'),
                    'right' => __('Right', 'gdgallery')
                ),
                'label' => __('Text Position', 'gdgallery'),
                'section' => 'compact_lightbox',
                'help' => __('Choose text position', 'gdgallery')
            ),

        ));

        $builder->render();

    }

    public function setOption($options)
    {
        $this->options = $options;
    }

    public function getOption()
    {
        return $this->options;
    }

    /**
     * Save settings
     */
    public static function save()
    {

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'gdgallery_settings')) {
            die(0);
        }

        if (!isset($_POST['settings']) || empty($_POST['settings']) || !is_array($_POST['settings'])) {
            die(0);
        }

        foreach ($_POST['settings'] as $key => $value) {
            \GDGallery()->settings->setOption($key, $value);
        }

        echo 'ok';
        die;
    }

}