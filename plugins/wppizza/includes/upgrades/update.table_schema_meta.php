<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
global $wpdb;

/*********************************************
	collation
*********************************************/
$charset_collate = $wpdb->get_charset_collate();

/**********************************************
	use defined meta table or set default for current blog
**********************************************/
$meta_table = empty($meta_table) ?  $wpdb->prefix . WPPIZZA_TABLE_ORDERS_META : $meta_table ;

/*********************************************
	meta table schema sql
*********************************************/
$meta_table_schema_sql = array();

$meta_table_schema_sql['create_'] = 'CREATE TABLE '. $meta_table .' ('.PHP_EOL;
	$meta_table_schema_sql['meta_id'] = "meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".PHP_EOL;
	$meta_table_schema_sql['order_id'] = "order_id INT(10) NOT NULL DEFAULT '0',".PHP_EOL;//must be exactly the same as id in orders table for queries to use indexes!!
	$meta_table_schema_sql['meta_key'] = "meta_key VARCHAR(190) NULL DEFAULT NULL COLLATE utf8mb4_unicode_ci,".PHP_EOL;
	$meta_table_schema_sql['meta_value'] = "meta_value LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$meta_table_schema_sql['PRIMARY_KEY'] = "PRIMARY KEY  (meta_id),".PHP_EOL;
	$meta_table_schema_sql['KEY_order_id'] = "KEY order_id (order_id),".PHP_EOL;
	$meta_table_schema_sql['KEY_meta_key'] = "KEY meta_key (meta_key)".PHP_EOL;
$meta_table_schema_sql['_create'] = ') ' .$charset_collate .';';


?>