<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
global $wpdb;

/*********************************************
	collation
*********************************************/
$charset_collate = $wpdb->get_charset_collate();

/*********************************************
	filterable order status
	kind of unneccessary and will be removed,
	but leave it for the moment
*********************************************/
$dbOrderStatus = "'".implode("','",wppizza_order_status_default('keys', false, true))."'";

/**********************************************
	use defined table or set default for current blog
**********************************************/
$table = empty($table) ?  $wpdb->prefix . WPPIZZA_TABLE_ORDERS : $table ;


/*********************************************
	table schema sql
*********************************************/
$table_schema_sql = array();


$table_schema_sql['create_'] = 'CREATE TABLE '. $table .' ('.PHP_EOL;
	$table_schema_sql['id'] = "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".PHP_EOL;
	$table_schema_sql['wp_user_id'] = "wp_user_id INT(10) NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_date'] = "order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,".PHP_EOL;
	$table_schema_sql['order_date_utc'] = "order_date_utc TIMESTAMP,".PHP_EOL;
	$table_schema_sql['order_update'] = "order_update TIMESTAMP,".PHP_EOL;
	$table_schema_sql['order_delivered'] = "order_delivered TIMESTAMP,".PHP_EOL;
	$table_schema_sql['customer_details'] = "customer_details TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['customer_ini'] = "customer_ini TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['order_details'] = "order_details MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['order_ini'] = "order_ini MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['order_no_of_items'] = "order_no_of_items INT(10) NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_items_total'] = "order_items_total FLOAT NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_discount'] = "order_discount FLOAT NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_taxes'] = "order_taxes FLOAT NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_taxes_included'] = "order_taxes_included ENUM('Y','N') NULL DEFAULT 'N',".PHP_EOL;
	$table_schema_sql['order_delivery_charges'] = "order_delivery_charges FLOAT NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_handling_charges'] = "order_handling_charges FLOAT NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_tips'] = "order_tips FLOAT NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_self_pickup'] = "order_self_pickup ENUM('Y','N') NULL DEFAULT 'N',".PHP_EOL;
	$table_schema_sql['order_total'] = "order_total FLOAT NOT NULL DEFAULT '0',".PHP_EOL;
	$table_schema_sql['order_refund'] = "order_refund FLOAT NOT NULL DEFAULT '0',".PHP_EOL;	
	$table_schema_sql['order_status'] = "order_status ENUM(".$dbOrderStatus.") NOT NULL DEFAULT 'NEW',".PHP_EOL;
	$table_schema_sql['order_status_user_defined'] = "order_status_user_defined VARCHAR(64) NULL DEFAULT NULL,".PHP_EOL;
	$table_schema_sql['hash'] = "hash VARCHAR(96) NULL DEFAULT NULL,".PHP_EOL;
	$table_schema_sql['payment_status'] = "payment_status ENUM('INITIALIZED','COMPLETED','INPROGRESS','PAYMENT_PENDING','REFUNDED','REJECTED','AUTHORIZED','FAILED','EXPIRED','INVALID','CANCELLED','CAPTURED','UNCONFIRMED','CONFIRMED','ABANDONED') NULL DEFAULT 'INITIALIZED',".PHP_EOL;
	$table_schema_sql['transaction_id'] = "transaction_id VARCHAR(96) NULL DEFAULT NULL,".PHP_EOL;
	$table_schema_sql['transaction_details'] = "transaction_details TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['transaction_errors'] = "transaction_errors TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['display_errors'] = "display_errors TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['initiator'] = "initiator VARCHAR(32) NULL DEFAULT 'COD',".PHP_EOL;
	$table_schema_sql['mail_sent'] = "mail_sent ENUM('Y','N','ERROR') NULL DEFAULT 'N',".PHP_EOL;
	$table_schema_sql['mail_error'] = "mail_error TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['anonymised'] = "anonymised TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',".PHP_EOL;	
	$table_schema_sql['notes'] = "notes TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['session_id'] = "session_id VARCHAR(64) NULL DEFAULT NULL,".PHP_EOL;
	$table_schema_sql['email'] = "email VARCHAR(190) NULL DEFAULT NULL COLLATE utf8mb4_unicode_ci,".PHP_EOL;// 250 chars should be plenty for emails up to 100 chars or so when encrypted
	$table_schema_sql['ip_address'] = "ip_address VARCHAR(50) NULL DEFAULT NULL,".PHP_EOL;//40 to account for ipv6 !
	$table_schema_sql['user_data'] = "user_data TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['user_defined'] = "user_defined TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,".PHP_EOL;
	$table_schema_sql['PRIMARY_KEY'] = "PRIMARY KEY  (id),".PHP_EOL;
	$table_schema_sql['KEY_hash'] = "KEY hash (hash),".PHP_EOL;
	$table_schema_sql['KEY_wp_user_id'] = "KEY wp_user_id (wp_user_id),".PHP_EOL;
	$table_schema_sql['KEY_orderdate_paymentstatus'] = "KEY orderdate_paymentstatus (order_date,payment_status),".PHP_EOL;
	$table_schema_sql['KEY_payment_status'] = "KEY payment_status (payment_status),".PHP_EOL;
	$table_schema_sql['KEY_transaction_id'] = "KEY transaction_id (transaction_id),".PHP_EOL;
	$table_schema_sql['KEY_ident'] = "KEY ident (hash,payment_status,initiator),".PHP_EOL;
	$table_schema_sql['KEY_history'] = "KEY history (wp_user_id,order_date,payment_status),".PHP_EOL;
	$table_schema_sql['KEY_paymentstatus_userid'] = "KEY paymentstatus_userid (payment_status,wp_user_id),".PHP_EOL;
	$table_schema_sql['KEY_mail_sent'] = "KEY mail_sent (mail_sent),".PHP_EOL;
	$table_schema_sql['KEY_session_id'] = "KEY session_id (session_id),".PHP_EOL;
	$table_schema_sql['KEY_email'] = "KEY email (email)".PHP_EOL;
$table_schema_sql['_create'] = ') ' .$charset_collate .';';
?>