<?php
if (!class_exists('FmWidget')):

    /**
     *
     */
    class FmWidget extends WP_Widget
    {

        /**
         * TLP TEAM widget setup
         */
	    public function __construct() {

            $widget_ops = array('classname' => 'widget_tlp_fm', 'description' => __('Display Food menu', 'tlp-food-menu'));
            parent::__construct('widget_tlp_fm', __('TPL Food Menu', 'tlp-food-menu'), $widget_ops);
        }

        /**
         * display the widgets on the screen.
         */
        function widget($args, $instance) {
            extract($args);
            global $TLPfoodmenu;
            wp_enqueue_style( 'tlp-fm-css', $TLPfoodmenu->assetsUrl . 'css/tlpfoodmenu.css' );
            wp_enqueue_script( 'tlp-fm-js',  $TLPfoodmenu->assetsUrl. 'js/tlpfoodmenu.js', array('jquery'), '', true );

            $number = (isset($instance['number']) ? ($instance['number'] ? (int)$instance['number'] : 2 ) : 2);
            $rawCat = (isset($instance['cat']) ? ($instance['cat'] ? $instance['cat'] : null) : null);
            $cat = array();
            if (isset($rawCat)) {
                $rca = explode(",", $rawCat);
                if (!empty($rca)) {
                    foreach ($rca as $c) {
                        $cat[] = $c;
                    }
                }
            }
            if(!empty($cat)){
                $txq = array(
                    array(
                        'taxonomy' => 'food-category',
                        'field' => 'term_id',
                        'terms' => $cat,
                        'operator' => 'IN',
                        ),
                    );
            }else{
                $txq = null;
            }

            //if($number>4){$number=4;};
            
            $bss="tlp-col-md-3 tlp-col-lg-3 tlp-col-sm-12";

            echo $before_widget;
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
        ?>
     
                <?php

            $args = array(
                'post_type' => $TLPfoodmenu->post_type,
                'post_status' => 'publish',
                'posts_per_page' => $number,
                'orderby' => 'date',
                'order' => 'DESC',
                'tax_query' => $txq,
                );

            $teamQuery = new WP_Query($args);
            $html = null;
            if ($teamQuery->have_posts()) {

                $html.= "<div class='container-fluid '>";
                $html.= "<div class='row tlp-food-menu'>";

                while ($teamQuery->have_posts()):
                    $teamQuery->the_post();

                    if (has_post_thumbnail()) {
                        $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'team-thumb');
                        $img = $image[0];
                    }
                    else {
                        $img = $TLPfoodmenu->assetsUrl . 'images/demo.png';
                    }

                    $gTotal = $TLPfoodmenu->getPriceWithLabel();
                    $html.= "<div class='{$bss}'>";
                    $html .= "<div class='tlp-equal-height fm-widget'>";
                        $html.= "<div class='tlp-thum'><img src='$img' /></div>";
                        $html.= "<h3><a href='" . get_the_permalink() . "'>" . get_the_title() . "</a></h3>";
                    $html.= '<div class="food-info">';
                        $html.= '<span class="price">'.$gTotal.'</span>';
                    $html.= "</div>";
                    $html.= "</div>";
                    $html.= "</div>";
                endwhile;
                $html.= "</div>";
                $html.= "</div>"; 

                wp_reset_postdata();
            }else{
                $html .= "<p>".__('No post found','tlp-food-menu')."</p>";
            }

            echo $html;
        ?>
    

            <?php
            echo $after_widget;
        }

        function form($instance) {

            $defaults = array('title' => '', 'number' => 4,);

            $instance = wp_parse_args((array)$instance, $defaults); ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'tlp-food-menu'); ?></label>
                <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" /></p>

            <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of item to show : ', 'tlp-food-menu'); ?></label>
                <input type="text" size="2" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $instance['number']; ?>" /></p>

            <p><label for="<?php echo @$this->get_field_id('cat'); ?>"><?php _e('Category id (eg. 1,2,7) leave it blank for all:', 'tlp-food-menu'); ?></label>
                <input type="text" size="10" id="<?php echo @$this->get_field_id('cat'); ?>" name="<?php echo @$this->get_field_name('cat'); ?>" value="<?php echo @$instance['cat']; ?>" /></p>

            <?php
        }

        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
            $instance['number'] = (!empty($new_instance['number'])) ? (int)($new_instance['number']) : '';
            $instance['cat'] = (!empty($new_instance['cat'])) ? sanitize_text_field($new_instance['cat']) : '';
            return $instance;
        }
    }
endif;
