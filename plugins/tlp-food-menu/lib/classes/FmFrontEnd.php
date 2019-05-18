<?php

if(!class_exists('FmFrontEnd')):

    class FmFrontEnd
    {
        function __construct()
        {
            add_action( 'wp_head', array($this, 'custom_css') );
        }
        function custom_css(){
            global $TLPfoodmenu;
            $settings = get_option($TLPfoodmenu->options['settings']);
            if(isset($settings['others']['css'])){
                if($settings['others']['css']) {
                    echo "<style>";
                        echo $settings['others']['css'];
                    echo "</style>";
                }
            }
        }

    }

endif;