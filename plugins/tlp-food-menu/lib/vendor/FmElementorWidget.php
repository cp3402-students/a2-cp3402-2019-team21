<?php

class FmElementorWidget extends \Elementor\Widget_Base
{

    public function get_name() {
        return 'food-menu';
    }

    public function get_title() {
        return __('Food Menu', 'tlp-food-menu');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function _register_controls() {
        global $TLPfoodmenu;
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Food Menu', 'tlp-food-menu'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'col',
            array(
                'type'    => \Elementor\Controls_Manager::SELECT2,
                'id'      => 'col',
                'label'   => __('Column', 'tlp-food-menu'),
                'options' => $TLPfoodmenu->scColumns()
            )
        );
        $this->add_control(
            'orderby',
            array(
                'type'    => \Elementor\Controls_Manager::SELECT2,
                'id'      => 'orderby',
                'label'   => __('Order By', 'tlp-food-menu'),
                'options' => $TLPfoodmenu->scOrderBy()
            )
        );
        $this->add_control(
            'order',
            array(
                'type'    => \Elementor\Controls_Manager::SELECT2,
                'id'      => 'order',
                'label'   => __('Order', 'tlp-food-menu'),
                'options' => $TLPfoodmenu->scOrder()
            )
        );
        $this->add_control(
            'cat',
            array(
                'type'     => \Elementor\Controls_Manager::SELECT2,
                'id'       => 'cat',
                'label'    => __('Category', 'tlp-food-menu'),
                'options'  => $TLPfoodmenu->getAllFmpCategoryList(),
                'multiple' => true
            )
        );
        $this->add_control(
            'hide-img',
            array(
                'label'        => __('Hide image', 'tlp-food-menu'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Hide', 'tlp-food-menu'),
                'label_off'    => __('Show', 'tlp-food-menu'),
                'return_value' => 1
            )
        );
        $this->add_control(
            'disable-link',
            array(
                'label'        => __('Disable Link', 'tlp-food-menu'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Disable', 'tlp-food-menu'),
                'label_off'    => __('Enable', 'tlp-food-menu'),
                'return_value' => 1,
            )
        );
        $this->add_control(
            'class',
            array(
                'label' => __('Wrapper Class', 'tlp-food-menu'),
                'type'  => \Elementor\Controls_Manager::TEXT,
            )
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'tlp-food-menu'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'title-color',
            array(
                'label'     => __('Title color', 'tlp-food-menu'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'scheme'    => array(
                    'type'  => \Elementor\Scheme_Color::get_type(),
                    'value' => \Elementor\Scheme_Color::COLOR_1,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .title' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $shortcode = '[foodmenu';
        if (isset($settings['col']) && !empty($settings['col'])) {
            $shortcode .= ' col="' . $settings['col'] . '"';
        }
        if (isset($settings['orderby']) && !empty($settings['orderby'])) {
            $shortcode .= ' orderby="' . $settings['orderby'] . '"';
        }
        if (isset($settings['order']) && !empty($settings['order'])) {
            $shortcode .= ' order="' . $settings['order'] . '"';
        }
        if (isset($settings['cat']) && !empty($settings['cat']) && is_array($settings['cat'])) {
            $shortcode .= ' cat="' . implode(',', $settings['cat']) . '"';
        }
        if (isset($settings['hide-img']) && !empty($settings['hide-img'])) {
            $shortcode .= ' hide-img="1"';
        }
        if (isset($settings['disable-link']) && !empty($settings['disable-link'])) {
            $shortcode .= ' disable-link="1"';
        }
        if (isset($settings['title-color']) && !empty($settings['title-color'])) {
            $shortcode .= ' title-color="' . $settings['title-color'] . '"';
        }
        if (isset($settings['class']) && !empty($settings['class'])) {
            $shortcode .= ' class="' . $settings['class'] . '"';
        }
        $shortcode .= ']';

        echo do_shortcode($shortcode);
    }
}