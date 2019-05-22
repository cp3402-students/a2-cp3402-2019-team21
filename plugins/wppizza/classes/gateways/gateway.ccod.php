<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
/*
	GATEWAY : CREDIT CARD ON DELIVERY
*/
?>
<?php
function wppizza_register_wppizza_gateway_ccod( $gateways ) {	
	$gateways[] = 'WPPIZZA_GATEWAY_CCOD'; 
	return $gateways;
}
add_filter( 'wppizza_register_gateways', 'wppizza_register_wppizza_gateway_ccod' );

	
/**must start with WPPIZZA_GATEWAY_**/
class WPPIZZA_GATEWAY_CCOD{

	/*****************************
		[required]
	*****************************/
	/*
		@type : string( must NOT be empty)
		@param : gateway version number
	*/
	public $gatewayVersion = WPPIZZA_VERSION;

	/*
		@type : string (must NOT be empty)
		@param : string
		gateway name
	*/
	public $gatewayName = 'Credit Card on Delivery';

	/*
		@type : array
		@param : options of gateway
	*/
	public $gatewayOptions = array();	

	/*
		@type : string (can be empty)
		@param : string
		additional description of gateway displayed in ADMIN area
	*/
	public $gatewayDescription = '';

	/*
		@type : string (can be empty)
		@param : string
		default printed under gateway options FRONTEND
		can be changed/localized/emptied in admin
	*/
	public $gatewayAdditionalInfo = '';

	/*
		@type : bool
		@param : bool
		enable discounts and surcharges 
	*/
	public $gatewayDiscountsSurcharges = true;

	/*
		@type : bool
		@param : bool
		automatically enable on install
	*/
	public $gatewayAutoEnable = false;

	
	/******************************
		[optional]
		but recommended
	******************************/
	/*
		@type : string
		@param : 'prepay' or 'cod'.
		omit or set distinctly to "prepay" (i.e for cc's and other payment processors)
		set to 'cod' to simply have another payment option that works like the wppizza inbuilt cod payment
		(if you want that, simply set the parameters above, empty the construct and delete all methods)
	*/
	public $gatewayType = 'cod';



/******************************************************************************************************************
*
*
*	[construct]
*
*
******************************************************************************************************************/
	function __construct() {
		/************************
			[get gateway options]
		************************/
		$this -> gatewayOptions = get_option(__CLASS__, 0);
	}
}
?>