<?php
/**
 * Plugin Name: Food Menu
 * Plugin URI: http://demo.radiustheme.com/wordpress/plugins/food-menu/
 * Description: Fully responsive and mobile friendly WP food menu display plugin for restaurant, cafes, bars, coffee house, fast food.
 * Author: RadiusTheme
 * Version: 2.2.60
 * Text Domain: tlp-food-menu
 * Domain Path: /languages
 * Author URI: https://radiustheme.com/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );
define( 'TLP_FOOD_MENU_VERSION', $plugin_data['Version'] );
define('TLP_FOOD_MENU_PLUGIN_PATH', dirname(__FILE__));
define('TLP_FOOD_MENU_PLUGIN_ACTIVE_FILE_NAME', plugin_basename( __FILE__ ));
define('TLP_FOOD_MENU_PLUGIN_URL', plugins_url('', __FILE__));
define('TLP_FOOD_MENU_LANGUAGE_PATH', dirname( plugin_basename( __FILE__ ) ) . '/languages');

require ('lib/init.php');

