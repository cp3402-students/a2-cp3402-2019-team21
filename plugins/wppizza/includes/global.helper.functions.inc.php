<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php
/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
*
*
*	replacement functions
*
*
*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/
/*******************************************************************
*
*	[replacement function for php <5.5 where array column is not available]
*
*******************************************************************/
    function wppizza_array_column($array, $column_name, $val_as_key = false){
    	if(empty($array) || count($array)<=0){return array();}
		$first_value = reset($array);
    	if(function_exists('array_column') && !is_object($first_value)){/* php >=5.4 , provided it's an array of arrays*/
    			$column = array_column($array, $column_name);
    			/* val as keys too */
    			if($val_as_key){
    				$column = array_combine($column, $column);
    			}
			return $column;
		}else{
			/* use array map if array_column does not exist or it's an array of objects */
        	$mapped =  array_map(function($element) use($column_name){
        		$column = is_object($element) ? $element->$column_name : $element[$column_name];
        		return  $column;
        	}, $array);
			/* val as keys too */
        	if($val_as_key){
				$mapped = array_combine($mapped, $mapped);
			}
        	return $mapped;
		}
    }
/*******************************************************************
*
*	[replacement function array_splice preserving keys]
*	array of int or keys, insert each key AFTER position/key set
*	if $before == true , insert BEFORE position/key set
*******************************************************************/
function wppizza_array_splice($array_original, $array_insert, $offset, $before = false) {

	/*
		make sure we have something to insert
	*/
	if(empty($array_insert)){
		return $array_original;
	}

	/*
		if offset is array of int or keys, insert each key after position/key set
	*/
	if(is_array($offset)){
		$c=0;
		$insert_keys = array_keys($array_insert);
		foreach($offset as $position){
			/* get position */
			$position = (is_string($position)) ? (array_search($position, array_keys($array_original))+1) : $position;

			/* splice and insert */
			$pre = array_slice($array_original, 0, $position, true);
			$insert[$insert_keys[$c]] = ($array_insert[$insert_keys[$c]]);
			$post =  array_slice($array_original, $position, NULL, true);

			/** resetting array with inserted values */
			$array_original = $pre + $insert + $post;

		$c++;
		}
	return $array_original;
	}

	/* using key to insert after specific module instead of numeric value */
	$offset = (is_string($offset)) ? (array_search($offset, array_keys($array_original))+1) : $offset;
	/* inserting befor */
	$offset_offset = ($before) ? 1 : 0;
	$set_offset = max(0,($offset-$offset_offset));/* should never be less than zero */

	$pre = array_slice($array_original, 0, $set_offset, true);
	$insert = $array_insert;
	$post =  array_slice($array_original, $set_offset, NULL, true);

	/*
		make sure we have arrays or + will throw fatal errors
		can really only happen if some sessions	are completely messed up
		(i.e plugin chnages/de-activated or similar)
		so let's not worry about this for now too much

		However, if the above applies, fatal errors will be thrown
		so allow for some constant that can be used in the wp-config.php to at least stop fatal errors being thrown
		BUT,  this constant should REALLY ONLY BE USED TEMPORARILY until the root cause (elsewhere) is found/fixed !!
		(clearing browser cache/cookies might be the only thing that's required in many cases )
	*/
	if(defined('WPPIZZA_TEMP_ARRAY_SPLICE_FORCE_ARRAYS')){
		$pre = !is_array($pre) ? array() : $pre;
		$insert = !is_array($insert) ? array() : $insert;
		$post = !is_array($post) ? array() : $post;
	}


	$combined = $pre + $insert + $post;

return $combined;
}


/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*
*
*
*	general helper functions
*
*
*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\/*\*/

/*******************************************************************
*
*	[comparing floats rounding to precision of 4
*	(that really should do the job for comparing prices in various places)-
*	returns bool]
*******************************************************************/
	function wppizza_floatcompare($a, $b, $operator){
		/* lets trim, cast to floats, and round to 4 decimals*/
		$a = number_format((float)trim($a),4, '.', '');
		$b = number_format((float)trim($b),4, '.', '');

		$bool = version_compare($a, $b , $operator);

	return $bool;
	}

/*******************************************************************
*
*	[helper function to make and return a hash and original string to check against ]
*
*******************************************************************/
	function wppizza_mkHash($array){
		$tohash=serialize($array);
		/** to make things really really unique, MD5 of microtime **/
		$add_time_hash = '';
		if(function_exists('microtime')){
			$add_time_hash .= md5(microtime(true));
		}else{
			$add_time_hash .= md5(time());
		}
		/*try sha256 first if that's an error, use md5*/
		$hash=''.hash("sha256","".AUTH_SALT."".$tohash."".NONCE_SALT."") . $add_time_hash . '';
		/* sha265 not available , use MD5 */
		if(!$hash || $hash==false || strlen($hash)<(64+32)){
			$hash='['.md5("".AUTH_SALT."".$tohash."".NONCE_SALT."") . $add_time_hash . ']';
		}

	return $hash;
	}
/*********************************************************
	[available/chosen style options]
*********************************************************/
function wppizza_public_styles($selected=''){
	$styles = array();

   	/*
	   	allow filtering - key must be "a-z,0-9,_,-" only -
	   	filter before set array to ensure vals of
	   	default, responsive, grid
	   	can not get changed

	   	modules can be thumbnail, prices, title, content, permalink, (and comments perhaps?)
   	*/
   	$styles = apply_filters('wppizza_filter_public_styles', $styles);

	$styles['default'] 		= array('label'=>__('Default', 'wppizza-admin'), 'dependency' => null, 'ext'=>'css', 'elements'=>'thumbnail, prices, title, content' );
	$styles['responsive'] 	= array('label'=>__('Responsive', 'wppizza-admin'), 'dependency' => null, 'ext'=>'css', 'elements'=>'title, thumbnail, prices, content' );
	$styles['grid'] 		= array('label'=>__('Grid', 'wppizza-admin'), 'dependency' => null, 'ext'=>'css.php', 'elements'=>'title, thumbnail, prices, content' );


   	$public_styles = array();
    foreach($styles as $key=>$val){
    	if($key==$selected){$d=' selected="selected"';}else{$d='';}
		$public_styles[$key]=array('selected' => $d , 'value' => $val['label'] , 'id' => $key , 'dependency' => $val['dependency'] , 'ext' => $val['ext'], 'elements' => $val['elements']);
    }
    return $public_styles;
}
/*********************************************************
	[which metabox (sizes,additives) options are being used]
*********************************************************/
function wppizza_options_in_use($type){

	global $wpdb;

	$optionsInUse=array();
	if($type=='sizes'){
		$optionsInUse['sizes']=array();
	}
	if($type=='additives'){
		$optionsInUse['additives']=array();
	}

	$get_sizes_and_additives = $wpdb->get_results("SELECT DISTINCT(meta_value) FROM $wpdb->postmeta WHERE meta_key = '".WPPIZZA_SLUG."' ");

	foreach($get_sizes_and_additives as $sizes_and_additives){
		$meta=maybe_unserialize($sizes_and_additives->meta_value);
		/*get size in use - unique*/
		if($type=='sizes'){
			/*meta sizes - add as keys too to make them automatically unique*/
			$size=$meta['sizes'];
			$optionsInUse['sizes'][$size]=$size;
		}

		if($type=='additives'){
			/*meta additives - add as keys too to make them automatically unique*/
			$additives=$meta['additives'];
			if(!empty($additives) && is_array($additives)){
				foreach($additives as $additive){
					$optionsInUse['additives'][$additive]=$additive;
				}
			}
		}
	}
	return $optionsInUse;
}
/*********************************************************
	[which mealsizes are available]
*********************************************************/
function wppizza_sizes_available($sort=false){
	global $wppizza_options;
	$sizes = $wppizza_options['sizes'];

	$availableSizes=array();
	if(is_array($sizes)){
		foreach($sizes as $l=>$m){
			foreach($m as $r=>$s){
				$availableSizes[$l]['lbl'][$r]=$sizes[$l][$r]['lbl'];
				$availableSizes[$l]['price'][$r]=$sizes[$l][$r]['price'];
			}
		}
		/**sort by name**/
		if($sort){
			$availableSizesSort=array();
			foreach($availableSizes as $l=>$m){
				$ident=empty($sizes[$l][0]['lbladmin']) ? '' : ' - '.$sizes[$l][0]['lbladmin'].'' ;
				$availableSizesSort[$l]['sort']=implode(", ",$m['lbl'])."".$ident."";
				$availableSizesSort[$l]['lbl']=$m['lbl'];
				$availableSizesSort[$l]['price']=$m['price'];
			}
			asort($availableSizesSort);
		return $availableSizesSort;
		}
	}
	return $availableSizes;
}


 /******************************************************
 *	construct link to pages . for example orderpage and amend order page
 * depending on several settings (ssl etc)
 * array of order page link and amend order link (if confirmation page used)
 ******************************************************/
function wppizza_page_links($selected = false){
	global $wppizza_options;


	if(empty($selected) || $selected == 'orderpage'){
		/* get orderpage link */
		$href['orderpage'] = get_page_link($wppizza_options['order_settings']['orderpage']);

		/* add nocache to order page link too if using cache  - to stop godaddy cache nonsense for example**/
		if(apply_filters('wppizza_filter_using_cache_plugin', false)){
			$href['orderpage'] = add_query_arg(array('nocache'=>1), $href['orderpage']);
		}
	}

	if(empty($selected) || $selected == 'amendorderlink'){
		/*confirmation page -> amend order link**/
		if($wppizza_options['confirmation_form']['confirmation_form_amend_order_link']>0){
			$href['amendorderlink']=get_page_link($wppizza_options['confirmation_form']['confirmation_form_amend_order_link']);
		}else{
			$href['amendorderlink']='';
		}
	}


	/*force ssl for checkout page*/
	$ssl_on_checkout = apply_filters('wppizza_filter_ssl_on_checkout', false);
	if(!empty($ssl_on_checkout) && !is_ssl()){
		if(empty($selected) || $selected == 'orderpage'){
			$href['orderpage'] = set_url_scheme($href['orderpage'], 'https');
		}

		if(empty($selected) || $selected == 'amendorderlink'){
			/*confirmation page -> set amend order link to ssl too if same as order page**/
			if($href['amendorderlink']!='' && $wppizza_options['confirmation_form']['confirmation_form_amend_order_link']==$wppizza_options['order_settings']['orderpage']){
				$href['amendorderlink'] = set_url_scheme($href['amendorderlink'], 'https');
			}
		}
	}

	/** return individual page */
	if(!empty($selected)){
		return $href[$selected];
	}

return $href;
}

/*******************************************************************
*
*	[helper function to store/use smtp password if used]
*	NOTE: this is by no means perfect but a lot better than the SMTP
*	plugins that are around on wordpress that store this stuff in plaintext
*	taken from http://blog.turret.io/the-missing-php-aes-encryption-example/
*	@param str (string to encrypt/decrypt)
*	@param bool (true to encrypt, false to decrypt)
*	@param str (passing an encryption key)
*	@param bool (should encryption alwasy result in the same hash for the same string)
*******************************************************************/
function wppizza_encrypt_decrypt($string, $encrypt=true, $static = false){

	/*if open ssl is not available, we'll just have to store it as plaintext i'm afraid*/
	if(function_exists('openssl_encrypt') && !empty($string)){
		$cipher='aes-256-cbc';
		/*
			make sure encryption_key is always 32 chars in case SECURE_AUTH_SALT ever changes.
			not sure if this is required though. distinct lack of documentation at php.net
			regarding openssl_encrypt
		*/
		$encryption_key = MD5(WPPIZZA_CRYPT_KEY);

		/*encrypting*/
		if($encrypt){
			/* if we need a ststic key, creta a 16bytes $iv from $encryption_key */
			$iv = empty($static) ? openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)) : pack('H*', $encryption_key);
			$encrypted = openssl_encrypt($string, $cipher, $encryption_key, 0, $iv);
			$encrypted = $encrypted . ':' . bin2hex($iv);
		return $encrypted;
		}

		/*de-crypting*/
		if(!$encrypt){
			$parts = explode(':', $string);
			$unhexIV = pack('H*', $parts[1]);
			$decrypted = openssl_decrypt($parts[0], $cipher, $encryption_key, 0, $unhexIV);
		return $decrypted;
		}

	}else{
		return $string;
	}
}
/*
*	encrypt/decrypt some data stored in db
*
*	@param str (string to encrypt/decrypt)
*	@param bool (true to encrypt, false to decrypt)
*	@param int (max length accepted. if an encrypted string would result in a hash > this value, string will not be hashed but stored unencrypted and - if necessary - truncated)
*	@param bool (should encryption alwasy result in the same hash for the same string ? (in case we want to run a query on an indexed column without having to decrypt every value first) )
*/
function wppizza_maybe_encrypt_decrypt($string, $encrypt = true, $max_length = false, $static = false){
	global $wppizza_options;
	/* skip if empty */
	if(empty($string)){return '' ;}

	//if(!empty($wppizza_options['settings']['privacy'])){
		/*
			encrypting
		*/
		if($encrypt === true){
			$encrypted_data = wppizza_encrypt_decrypt($string, $encrypt, $static);
			/*
				if max length set for var char columns for example that have a chr limit to make sure it can be saved
				(if necessary unencrypted and truncated)
			*/
			if(!empty($max_length) && strlen($encrypted_data) > (int)$max_length){
				$unencrypted_substr = substr($string, 0, $max_length);
				return $unencrypted_substr;
			}

		return $encrypted_data;
		}

		/*
			decrypting
		*/
		if($encrypt === false){
			$decrypted_data = wppizza_encrypt_decrypt($string, $encrypt );
			return $decrypted_data;
		}

	//}
return $string;
}
/*******************************************************************
*
*	[find serialization errors]
*
*******************************************************************/
function wppizza_serialization_errors($data1){
    $output='';
    //echo "<pre>";
    $data2 = preg_replace ( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'",$data1 );
    $max = (strlen ( $data1 ) > strlen ( $data2 )) ? strlen ( $data1 ) : strlen ( $data2 );

    $output.= $data1 . PHP_EOL;
    $output.= $data2 . PHP_EOL;

    for($i = 0; $i < $max; $i ++) {

        if (@$data1 {$i} !== @$data2 {$i}) {

            $output.= "Diffrence ". @$data1 {$i}. " != ". @$data2 {$i}. PHP_EOL;
            $output.= "\t-> ORD number ". ord ( @$data1 {$i} ). " != ". ord ( @$data2 {$i} ). PHP_EOL;
            $output.= "\t-> Line Number = $i" . PHP_EOL;

            $start = ($i - 20);
            $start = ($start < 0) ? 0 : $start;
            $length = 40;

            $point = $max - $i;
            if ($point < 20) {
                $rlength = 1;
                $rpoint = - $point;
            } else {
                $rpoint = $length - 20;
                $rlength = 1;
            }

            $output.= "\t-> Section Data1  = ". substr_replace ( substr ( $data1, $start, $length ). "<b style=\"color:green\">{$data1 {$i}}</b>", $rpoint, $rlength ). PHP_EOL;
            $output.= "\t-> Section Data2  = ". substr_replace ( substr ( $data2, $start, $length ). "<b style=\"color:red\">{$data2 {$i}}</b>", $rpoint, $rlength ). PHP_EOL;
        }

    }

	return $output;
}
/*********************************************************************
	set default order status's available in wppizza order table
*********************************************************************/
	function wppizza_order_status_default($kv=false, $selected=false, $install=false){
		global $wppizza_options;
		$txt = $wppizza_options['localization'];

		$orderStatus['NEW']						= ($txt['order_history_status_new']!='') ? $txt['order_history_status_new'] : __('NEW', 'wppizza-admin');/* we must always have at least this one */
		$orderStatus['ACKNOWLEDGED']			= $txt['order_history_status_acknowledged'];
		$orderStatus['ON_HOLD']					= $txt['order_history_status_on_hold'];
		$orderStatus['PROCESSED']				= $txt['order_history_status_processed'];
		$orderStatus['DELIVERED']				= $txt['order_history_status_delivered'];
		$orderStatus['REJECTED']				= $txt['order_history_status_rejected'];
		$orderStatus['REFUNDED']				= $txt['order_history_status_refunded'];
		$orderStatus['OTHER']					= $txt['order_history_status_other'];
		$orderStatus['CUSTOM_1']				= $txt['order_history_status_custom_1'];
		$orderStatus['CUSTOM_2']				= $txt['order_history_status_custom_2'];
		$orderStatus['CUSTOM_3']				= $txt['order_history_status_custom_3'];
		$orderStatus['CUSTOM_4']				= $txt['order_history_status_custom_4'];

		/* skip empty if not adding ENUM keys to db  */
		if(!$install){
		foreach($orderStatus as $osKey=>$osVal){
			if($osVal==''){
				unset($orderStatus[$osKey]);
			}
		}}

		/*only get keys**/
		if($kv && $kv=='keys'){
			$osKeys=array();
			foreach($orderStatus as $oKey=>$oVal){
				$osKeys[]=$oKey;
			}
			$orderStatus=$osKeys;
		}
		/** only get values (text/labels) **/
		if($kv && $kv=='vals'){
			$osKeys=array();
			foreach($orderStatus as $oKey=>$oVal){
				$osKeys[]=$oVal;
			}
			$orderStatus=$osKeys;
		}

		/*only get selected label**/
		if(!$kv && $selected){
			$orderStatus=$orderStatus[strtoupper($selected)];
		}

		/*only get selected array key|label**/
		if($kv && $selected){
			$sel=array();
			$sel[$selected] = $orderStatus[strtoupper($selected)];
			$orderStatus=$sel;
		}


		return $orderStatus;
	}
/***********************************************************
	admin mail delivery options
***********************************************************/
function wppizza_admin_mail_delivery_options($set_fieldname=false, $selected=false, $id='', $class='', $options_only = false){
	/* mail options */
	$mail_options = array();
	$mail_options['wp_mail'] = __('Plaintext', 'wppizza-admin');
	$mail_options['phpmailer'] = __('HTML', 'wppizza-admin');

	/* return options array only */
	if($options_only){
		return $mail_options;
	}

	/* admin fielname(s) */
	$fieldname=empty($set_fieldname) ? ''.WPPIZZA_SLUG.'[settings][mail_type]' :  $set_fieldname ;

	/* selected */
	$selected=!empty($selected) ? $selected : 'wp_mail' ;

	/*markup select dropdown */
	$markup='';
	$markup.="<select id='".$id."' class='".$class."' name='".$fieldname."'>";
		foreach($mail_options as $key=>$label){
			$markup.="<option value='".$key."' ".selected($selected,$key,false).">".$label."</option>";
		}
	$markup.= "</select>";


return $markup;
}
/***********************************************************
	wppizza item category
	forcing a category for an item
	notably shortcode attribute single="[int]" might not be associated
	with a category (for whatever reasons)
	however, if we want to group by categories we will need
	one, so we force the first
***********************************************************/
function wppizza_force_first_category($with_key = false){
	/* we need a category, lets just get the first one else use first available */
	/* sort by id for consistency */
	$terms = get_terms(WPPIZZA_TAXONOMY, array('orderby'=>'term_id'));
	$term = $terms[0];
	$category[$term->term_id]['id'] = $term->term_id;
	$category[$term->term_id]['slug'] = $term->slug;
	$category[$term->term_id]['name'] =  $term->name;
	$category[$term->term_id]['description'] = $term->description;
	$category[$term->term_id]['parent']=$term->parent;
	$category[$term->term_id]['count']=$term->count;

	if($with_key){
		return $category;
	}else{
		return $category[$term->term_id];
	}
}

/***********************************************************
	wppizza currencies
***********************************************************/
function wppizza_currencies($selected='',$returnValue=null){
	$items['---none---']='';
	$items['USD']='$';
	$items['GBP']='£';
	$items['EUR']='€';
	$items['CAD']='$';
	$items['CHF']='CHF';
	$items['ALL']='Lek';
	$items['AFN']='&#1547;';
	$items['ARS']='$';
	$items['AWG']='ƒ';
	$items['AUD']='$';
	$items['AZN']='&#1084;';
	$items['BSD']='$';
	$items['BBD']='$';
	$items['BYR']='p.';
	$items['BZD']='BZ$';
	$items['BMD']='$';
	$items['BOB']='$b';
	$items['BAM']='KM';
	$items['BWP']='P';
	$items['BGN']='&#1083;&#1074;';
	$items['BRL']='R$';
	$items['BND']='$';
	$items['KHR']='&#6107;';
	$items['KYD']='$';
	$items['CLP']='$';
	$items['CNY']='¥';
	$items['RMB']='¥';
	$items['COP']='$';
	$items['CRC']='¢';
	$items['HRK']='kn';
	$items['CUP']='&#8369;';
	$items['CZK']='Kc';
	$items['DKK']='kr';
	$items['DOP']='RD$';
	$items['XCD']='$';
	$items['EGP']='£';
	$items['SVC']='$';
	$items['EEK']='kr';
	$items['FKP']='£';
	$items['FJD']='$';
	$items['GHC']='¢';
	$items['GIP']='£';
	$items['GTQ']='Q';
	$items['GGP']='£';
	$items['GYD']='$';
	$items['HNL']='L';
	$items['HKD']='$';
	$items['HUF']='Ft';
	$items['ISK']='kr';
	$items['IDR']='Rp';
	$items['INR']='&#8377;';
	$items['IRR']='&#65020;';
	$items['IMP']='£';
	$items['ILS']='&#8362;';
	$items['JMD']='J$';
	$items['JPY']='¥';
	$items['JEP']='£';
	$items['KZT']='&#8376;';
	$items['KGS']='&#1083;';
	$items['LAK']='&#8365;';
	$items['LVL']='Ls';
	$items['LBP']='£';
	$items['LRD']='$';
	$items['LTL']='Lt';
	$items['MKD']='&#1076;';
	$items['MYR']='&#82;';
	$items['MUR']='&#8360;';
	$items['MXN']='$';
	$items['MNT']='&#8366;';
	$items['MZN']='MT';
	$items['NAD']='$';
	$items['NPR']='&#8360;';
	$items['ANG']='ƒ';
	$items['NZD']='$';
	$items['NIO']='C$';
	$items['NGN']='&#8358;';
	$items['KPW']='&#8361;';
	$items['NOK']='kr';
	$items['OMR']='&#65020;';
	$items['PKR']='&#8360;';
	$items['PAB']='B/.';
	$items['PYG']='Gs';
	$items['PEN']='S/.';
	$items['PHP']='&#8369;';
	$items['PLN']='&#122;&#322;';
	$items['QAR']='&#65020;';
	$items['RON']='lei';
	$items['RUB']='&#1088;';
	$items['SHP']='£';
	$items['SAR']='&#65020;';
	$items['RSD']='&#1056;&#1057;&#1044;';
	$items['RSD-ALT']='RSD';//hyphens and anything thereafter will be stripped to get the right ISO in frontend
	$items['SCR']='&#8360;';
	$items['SGD']='$';
	$items['SBD']='$';
	$items['SOS']='S';
	$items['ZAR']='R';
	$items['KRW']='&#8361;';
	$items['LKR']='&#8360;';
	$items['SEK']='kr';
	$items['SRD']='$';
	$items['SYP']='£';
	$items['TWD']='NT$';
	$items['THB']='&#3647;';
	$items['TTD']='TT$';
	$items['TRL']='£';
	$items['TVD']='$';
	$items['UAH']='&#8372;';
	$items['UYU']='$U';
	$items['UZS']='&#1083;';
	$items['VEF']='Bs';
	$items['VND']='&#8363;';
	$items['YER']='&#65020;';
	$items['ZWD']='Z$';
	$items['TRY']='&#8378;';
	$items['TND']='&#1583;&#46;&#1578;';
	$items['TND-ALT']='DT';
	$items['AED']='&#1583;&#46;&#1573;';
	$items['AOA']='Kz';
	$items['BDT']='Tk';
	$items['BHD']='BD';
	$items['CVE']='$';
	$items['DZD']='&#1583;&#1580;';
	$items['DZD-ALT']='DA';
	$items['ERN']='Nfk';
	$items['ERN-ALT']='&#4755;&#4693;&#4939;';
	$items['ETB']='Br';
	$items['GNF']='FG';
	$items['KWD']='&#1603;';
	$items['LYD']='LD';
	$items['MAD']='&#1583;&#46;&#1605;&#46;';
	$items['MDL']='leu';
	$items['MGA']='Ar';
	$items['MMK']='K';
	$items['MOP']='MOP$';
	$items['MRO']='UM';
	$items['MVR']='Rf';
	$items['MVR-ALT']='&#1923;';
	$items['MWK']='MK';
	$items['PGK']='K';
	$items['SDG']='&#1580;&#46;&#1587;&#46;';
	$items['SLL']='Le';
	$items['STD']='Db';
	$items['XPF']='F';
	$items['CFP']='F';

	$items = apply_filters('wppizza_filter_currencies',$items);

	if(!$returnValue){
	ksort($items);
    foreach($items as $key=>$val){
    	if($key==$selected){$d=' selected="selected"';}else{$d='';}
		$options[]=array('selected'=>''.$d.'','value'=>''.$val.'','id'=>''.$key.'');
    }}
    if($selected!='' && $returnValue){
    	$options=array('key'=>$selected,'val'=>$items[$selected]);
    }
	return $options;
}


/****************************************************
	 get country info

		@since 3.2.4

	 	@return array

	 	@key = set a key to return a different array keys
	 	@value = set value array to return as values as one string
	 	@selector = bool to include SELECT key
	 	@exclude = array of 3 letter ISOs to exclude certain key/values by key

	 if value not set/false return whole array
	 without selector
****************************************************/
function wppizza_country_info($key = false, $value = false, $selector = false, $exclude = false){

	$country_info=array();

	/* include selector */
	if($selector){
		$country_info['SELECT']=array('name' => __('--select--','wppizza-admin'), 'ISO' => '', 'ISO2' => '', 'prefix' => '');
	}

	$country_info['AFG'] = array('name' => __('Afghanistan','wppizza-admin'), 'ISO' => 'AFG', 'ISO2' => 'AF', 'prefix' => '93');
	$country_info['ALB'] = array('name' => __('Albania','wppizza-admin'), 'ISO' => 'ALB', 'ISO2' => 'AL', 'prefix' => '355');
	$country_info['DZA'] = array('name' => __('Algeria','wppizza-admin'), 'ISO' => 'DZA', 'ISO2' => 'DZ', 'prefix' => '213');
	$country_info['ASM'] = array('name' => __('American Samoa','wppizza-admin'), 'ISO' => 'ASM', 'ISO2' => 'AS', 'prefix' => '1684');
	$country_info['AND'] = array('name' => __('Andorra','wppizza-admin'), 'ISO' => 'AND', 'ISO2' => 'AD', 'prefix' => '376');
	$country_info['AGO'] = array('name' => __('Angola','wppizza-admin'), 'ISO' => 'AGO', 'ISO2' => 'AO', 'prefix' => '244');
	$country_info['AIA'] = array('name' => __('Anguilla','wppizza-admin'), 'ISO' => 'AIA', 'ISO2' => 'AI', 'prefix' => '1264');
	$country_info['ATG'] = array('name' => __('Antigua and Barbuda','wppizza-admin'), 'ISO' => 'ATG', 'ISO2' => 'AG', 'prefix' => '1268');
	$country_info['ARG'] = array('name' => __('Argentina','wppizza-admin'), 'ISO' => 'ARG', 'ISO2' => 'AR', 'prefix' => '54');
	$country_info['ARM'] = array('name' => __('Armenia','wppizza-admin'), 'ISO' => 'ARM', 'ISO2' => 'AM', 'prefix' => '374');
	$country_info['ABW'] = array('name' => __('Aruba','wppizza-admin'), 'ISO' => 'ABW', 'ISO2' => 'AW', 'prefix' => '297');
	$country_info['AUS'] = array('name' => __('Australia','wppizza-admin'), 'ISO' => 'AUS', 'ISO2' => 'AU', 'prefix' => '61');
	$country_info['AUT'] = array('name' => __('Austria','wppizza-admin'), 'ISO' => 'AUT', 'ISO2' => 'AT', 'prefix' => '43');
	$country_info['AZE'] = array('name' => __('Azerbaijan','wppizza-admin'), 'ISO' => 'AZE', 'ISO2' => 'AZ', 'prefix' => '994');
	$country_info['BHS'] = array('name' => __('Bahamas','wppizza-admin'), 'ISO' => 'BHS', 'ISO2' => 'BS', 'prefix' => '1242');
	$country_info['BHR'] = array('name' => __('Bahrain','wppizza-admin'), 'ISO' => 'BHR', 'ISO2' => 'BH', 'prefix' => '973');
	$country_info['BGD'] = array('name' => __('Bangladesh','wppizza-admin'), 'ISO' => 'BGD', 'ISO2' => 'BD', 'prefix' => '880');
	$country_info['BRB'] = array('name' => __('Barbados','wppizza-admin'), 'ISO' => 'BRB', 'ISO2' => 'BB', 'prefix' => '1246');
	$country_info['BLR'] = array('name' => __('Belarus','wppizza-admin'), 'ISO' => 'BLR', 'ISO2' => 'BY', 'prefix' => '375');
	$country_info['BEL'] = array('name' => __('Belgium','wppizza-admin'), 'ISO' => 'BEL', 'ISO2' => 'BE', 'prefix' => '32');
	$country_info['BLZ'] = array('name' => __('Belize','wppizza-admin'), 'ISO' => 'BLZ', 'ISO2' => 'BZ', 'prefix' => '501');
	$country_info['BEN'] = array('name' => __('Benin','wppizza-admin'), 'ISO' => 'BEN', 'ISO2' => 'BJ', 'prefix' => '229');
	$country_info['BMU'] = array('name' => __('Bermuda','wppizza-admin'), 'ISO' => 'BMU', 'ISO2' => 'BM', 'prefix' => '1441');
	$country_info['BTN'] = array('name' => __('Bhutan','wppizza-admin'), 'ISO' => 'BTN', 'ISO2' => 'BT', 'prefix' => '975');
	$country_info['BOL'] = array('name' => __('Bolivia','wppizza-admin'), 'ISO' => 'BOL', 'ISO2' => 'BO', 'prefix' => '591');
	$country_info['BIH'] = array('name' => __('Bosnia and Herzegovina','wppizza-admin'), 'ISO' => 'BIH', 'ISO2' => 'BA', 'prefix' => '387');
	$country_info['BWA'] = array('name' => __('Botswana','wppizza-admin'), 'ISO' => 'BWA', 'ISO2' => 'BW', 'prefix' => '267');
	$country_info['BRA'] = array('name' => __('Brazil','wppizza-admin'), 'ISO' => 'BRA', 'ISO2' => 'BR', 'prefix' => '55');
	$country_info['BRN'] = array('name' => __('Brunei Darussalam','wppizza-admin'), 'ISO' => 'BRN', 'ISO2' => 'BN', 'prefix' => '673');
	$country_info['BGR'] = array('name' => __('Bulgaria','wppizza-admin'), 'ISO' => 'BGR', 'ISO2' => 'BG', 'prefix' => '359');
	$country_info['BFA'] = array('name' => __('Burkina Faso','wppizza-admin'), 'ISO' => 'BFA', 'ISO2' => 'BF', 'prefix' => '226');
	$country_info['BDI'] = array('name' => __('Burundi','wppizza-admin'), 'ISO' => 'BDI', 'ISO2' => 'BI', 'prefix' => '257');
	$country_info['KHM'] = array('name' => __('Cambodia','wppizza-admin'), 'ISO' => 'KHM', 'ISO2' => 'KH', 'prefix' => '855');
	$country_info['CMR'] = array('name' => __('Cameroon','wppizza-admin'), 'ISO' => 'CMR', 'ISO2' => 'CM', 'prefix' => '237');
	$country_info['CAN'] = array('name' => __('Canada','wppizza-admin'), 'ISO' => 'CAN', 'ISO2' => 'CA', 'prefix' => '1');
	$country_info['CPV'] = array('name' => __('Cape Verde','wppizza-admin'), 'ISO' => 'CPV', 'ISO2' => 'CV', 'prefix' => '238');
	$country_info['CYM'] = array('name' => __('Cayman Islands','wppizza-admin'), 'ISO' => 'CYM', 'ISO2' => 'KY', 'prefix' => '1345');
	$country_info['CAF'] = array('name' => __('Central African Republic','wppizza-admin'), 'ISO' => 'CAF', 'ISO2' => 'CF', 'prefix' => '236');
	$country_info['TCD'] = array('name' => __('Chad','wppizza-admin'), 'ISO' => 'TCD', 'ISO2' => 'TD', 'prefix' => '235');
	$country_info['CHL'] = array('name' => __('Chile','wppizza-admin'), 'ISO' => 'CHL', 'ISO2' => 'CL', 'prefix' => '56');
	$country_info['CHN'] = array('name' => __('China','wppizza-admin'), 'ISO' => 'CHN', 'ISO2' => 'CN', 'prefix' => '86');
	$country_info['COL'] = array('name' => __('Colombia','wppizza-admin'), 'ISO' => 'COL', 'ISO2' => 'CO', 'prefix' => '57');
	$country_info['COM'] = array('name' => __('Comoros','wppizza-admin'), 'ISO' => 'COM', 'ISO2' => 'KM', 'prefix' => '269');
	$country_info['COD'] = array('name' => __('Congo - Democratic Republic of the','wppizza-admin'), 'ISO' => 'COD', 'ISO2' => 'CG', 'prefix' => '243');
	$country_info['COG'] = array('name' => __('Congo - Republic of the','wppizza-admin'), 'ISO' => 'COG', 'ISO2' => 'CD', 'prefix' => '242');
	$country_info['COK'] = array('name' => __('Cook Islands','wppizza-admin'), 'ISO' => 'COK', 'ISO2' => 'CK', 'prefix' => '682');
	$country_info['CRI'] = array('name' => __('Costa Rica','wppizza-admin'), 'ISO' => 'CRI', 'ISO2' => 'CR', 'prefix' => '506');
	$country_info['CIV'] = array('name' => __('Cote D\'Ivory','wppizza-admin'), 'ISO' => 'CIV', 'ISO2' => 'CI', 'prefix' => '225');
	$country_info['HRV'] = array('name' => __('Croatia','wppizza-admin'), 'ISO' => 'HRV', 'ISO2' => 'HR', 'prefix' => '385');
	$country_info['CUB'] = array('name' => __('Cuba','wppizza-admin'), 'ISO' => 'CUB', 'ISO2' => 'CU', 'prefix' => '53');
	$country_info['CYP'] = array('name' => __('Cyprus','wppizza-admin'), 'ISO' => 'CYP', 'ISO2' => 'CY', 'prefix' => '357');
	$country_info['CZE'] = array('name' => __('Czech Republic','wppizza-admin'), 'ISO' => 'CZE', 'ISO2' => 'CZ', 'prefix' => '420');
	$country_info['DNK'] = array('name' => __('Denmark','wppizza-admin'), 'ISO' => 'DNK', 'ISO2' => 'DK', 'prefix' => '45');
	$country_info['DJI'] = array('name' => __('Djibouti','wppizza-admin'), 'ISO' => 'DJI', 'ISO2' => 'DJ', 'prefix' => '253');
	$country_info['DMA'] = array('name' => __('Dominica','wppizza-admin'), 'ISO' => 'DMA', 'ISO2' => 'DM', 'prefix' => '1767');
	$country_info['DOM'] = array('name' => __('Dominican Republic','wppizza-admin'), 'ISO' => 'DOM', 'ISO2' => 'DO', 'prefix' => '18');
	$country_info['ECU'] = array('name' => __('Ecuador','wppizza-admin'), 'ISO' => 'ECU', 'ISO2' => 'EC', 'prefix' => '593');
	$country_info['EGY'] = array('name' => __('Egypt','wppizza-admin'), 'ISO' => 'EGY', 'ISO2' => 'EG', 'prefix' => '20');
	$country_info['SLV'] = array('name' => __('El Salvador','wppizza-admin'), 'ISO' => 'SLV', 'ISO2' => 'SV', 'prefix' => '503');
	$country_info['GNQ'] = array('name' => __('Equatorial Guinea','wppizza-admin'), 'ISO' => 'GNQ', 'ISO2' => 'GQ', 'prefix' => '240');
	$country_info['ERI'] = array('name' => __('Eritrea','wppizza-admin'), 'ISO' => 'ERI', 'ISO2' => 'ER', 'prefix' => '291');
	$country_info['EST'] = array('name' => __('Estonia','wppizza-admin'), 'ISO' => 'EST', 'ISO2' => 'EE', 'prefix' => '372');
	$country_info['ETH'] = array('name' => __('Ethiopia','wppizza-admin'), 'ISO' => 'ETH', 'ISO2' => 'ET', 'prefix' => '251');
	$country_info['FLK'] = array('name' => __('Falkland Islands / Malvinas','wppizza-admin'), 'ISO' => 'FLK', 'ISO2' => 'FK', 'prefix' => '500');
	$country_info['FRO'] = array('name' => __('Faroe Islands','wppizza-admin'), 'ISO' => 'FRO', 'ISO2' => 'FO', 'prefix' => '298');
	$country_info['FJI'] = array('name' => __('Fiji','wppizza-admin'), 'ISO' => 'FJI', 'ISO2' => 'FJ', 'prefix' => '679');
	$country_info['FIN'] = array('name' => __('Finland','wppizza-admin'), 'ISO' => 'FIN', 'ISO2' => 'FI', 'prefix' => '358');
	$country_info['FRA'] = array('name' => __('France','wppizza-admin'), 'ISO' => 'FRA', 'ISO2' => 'FR', 'prefix' => '33');
	$country_info['GUF'] = array('name' => __('French Guiana','wppizza-admin'), 'ISO' => 'GUF', 'ISO2' => 'GF', 'prefix' => '594');
	$country_info['PYF'] = array('name' => __('French Polynesia','wppizza-admin'), 'ISO' => 'PYF', 'ISO2' => 'PF', 'prefix' => '689');
	$country_info['GAB'] = array('name' => __('Gabon','wppizza-admin'), 'ISO' => 'GAB', 'ISO2' => 'GA', 'prefix' => '241');
	$country_info['GMB'] = array('name' => __('Gambia','wppizza-admin'), 'ISO' => 'GMB', 'ISO2' => 'GM', 'prefix' => '220');
	$country_info['GEO'] = array('name' => __('Georgia','wppizza-admin'), 'ISO' => 'GEO', 'ISO2' => 'GE', 'prefix' => '995');
	$country_info['DEU'] = array('name' => __('Germany','wppizza-admin'), 'ISO' => 'DEU', 'ISO2' => 'DE', 'prefix' => '49');
	$country_info['GHA'] = array('name' => __('Ghana','wppizza-admin'), 'ISO' => 'GHA', 'ISO2' => 'GH', 'prefix' => '233');
	$country_info['GIB'] = array('name' => __('Gibraltar','wppizza-admin'), 'ISO' => 'GIB', 'ISO2' => 'GI', 'prefix' => '350');
	$country_info['GRC'] = array('name' => __('Greece','wppizza-admin'), 'ISO' => 'GRC', 'ISO2' => 'GR', 'prefix' => '30');
	$country_info['GRL'] = array('name' => __('Greenland','wppizza-admin'), 'ISO' => 'GRL', 'ISO2' => 'GL', 'prefix' => '299');
	$country_info['GRD'] = array('name' => __('Grenada','wppizza-admin'), 'ISO' => 'GRD', 'ISO2' => 'GD', 'prefix' => '1473');
	$country_info['GLP'] = array('name' => __('Guadeloupe','wppizza-admin'), 'ISO' => 'GLP', 'ISO2' => 'GP', 'prefix' => '590');
	$country_info['GUM'] = array('name' => __('Guam','wppizza-admin'), 'ISO' => 'GUM', 'ISO2' => 'GU', 'prefix' => '1671');
	$country_info['GTM'] = array('name' => __('Guatemala','wppizza-admin'), 'ISO' => 'GTM', 'ISO2' => 'GT', 'prefix' => '502');
	$country_info['GGY'] = array('name' => __('Guernsey','wppizza-admin'), 'ISO' => 'GGY', 'ISO2' => 'GG', 'prefix' => '44');
	$country_info['GIN'] = array('name' => __('Guinea','wppizza-admin'), 'ISO' => 'GIN', 'ISO2' => 'GN', 'prefix' => '224');
	$country_info['GNB'] = array('name' => __('Guinea-Bissau','wppizza-admin'), 'ISO' => 'GNB', 'ISO2' => 'GW', 'prefix' => '245');
	$country_info['GUY'] = array('name' => __('Guyana','wppizza-admin'), 'ISO' => 'GUY', 'ISO2' => 'GY', 'prefix' => '592');
	$country_info['HTI'] = array('name' => __('Haiti','wppizza-admin'), 'ISO' => 'HTI', 'ISO2' => 'HT', 'prefix' => '509');
	$country_info['HND'] = array('name' => __('Honduras','wppizza-admin'), 'ISO' => 'HND', 'ISO2' => 'HN', 'prefix' => '504');
	$country_info['HKG'] = array('name' => __('Hong Kong','wppizza-admin'), 'ISO' => 'HKG', 'ISO2' => 'HK', 'prefix' => '852');
	$country_info['HUN'] = array('name' => __('Hungary','wppizza-admin'), 'ISO' => 'HUN', 'ISO2' => 'HU', 'prefix' => '36');
	$country_info['ISL'] = array('name' => __('Iceland','wppizza-admin'), 'ISO' => 'ISL', 'ISO2' => 'IS', 'prefix' => '354');
	$country_info['IND'] = array('name' => __('India','wppizza-admin'), 'ISO' => 'IND', 'ISO2' => 'IN', 'prefix' => '91');
	$country_info['IDN'] = array('name' => __('Indonesia','wppizza-admin'), 'ISO' => 'IDN', 'ISO2' => 'ID', 'prefix' => '62');
	$country_info['IRN'] = array('name' => __('Iran','wppizza-admin'), 'ISO' => 'IRN', 'ISO2' => 'IR', 'prefix' => '98');
	$country_info['IRQ'] = array('name' => __('Iraq','wppizza-admin'), 'ISO' => 'IRQ', 'ISO2' => 'IQ', 'prefix' => '964');
	$country_info['IRL'] = array('name' => __('Ireland','wppizza-admin'), 'ISO' => 'IRL', 'ISO2' => 'IE', 'prefix' => '353');
	$country_info['IMN'] = array('name' => __('Isle of Man','wppizza-admin'), 'ISO' => 'IMN', 'ISO2' => 'IM', 'prefix' => '44');
	$country_info['ISR'] = array('name' => __('Israel','wppizza-admin'), 'ISO' => 'ISR', 'ISO2' => 'IL', 'prefix' => '972');
	$country_info['ITA'] = array('name' => __('Italy','wppizza-admin'), 'ISO' => 'ITA', 'ISO2' => 'IT', 'prefix' => '39');
	$country_info['JAM'] = array('name' => __('Jamaica','wppizza-admin'), 'ISO' => 'JAM', 'ISO2' => 'JM', 'prefix' => '1876');
	$country_info['JPN'] = array('name' => __('Japan','wppizza-admin'), 'ISO' => 'JPN', 'ISO2' => 'JP', 'prefix' => '81');
	$country_info['JEY'] = array('name' => __('Jersey','wppizza-admin'), 'ISO' => 'JEY', 'ISO2' => 'JE', 'prefix' => '44');
	$country_info['JOR'] = array('name' => __('Jordan','wppizza-admin'), 'ISO' => 'JOR', 'ISO2' => 'JO', 'prefix' => '962');
	$country_info['KAZ'] = array('name' => __('Kazakhstan','wppizza-admin'), 'ISO' => 'KAZ', 'ISO2' => 'KZ', 'prefix' => '7');
	$country_info['KEN'] = array('name' => __('Kenya','wppizza-admin'), 'ISO' => 'KEN', 'ISO2' => 'KE', 'prefix' => '254');
	$country_info['PRK'] = array('name' => __('Korea North','wppizza-admin'), 'ISO' => 'PRK', 'ISO2' => 'KP', 'prefix' => '850');
	$country_info['KOR'] = array('name' => __('Korea South','wppizza-admin'), 'ISO' => 'KOR', 'ISO2' => 'KR', 'prefix' => '82');
	$country_info['KWT'] = array('name' => __('Kuwait','wppizza-admin'), 'ISO' => 'KWT', 'ISO2' => 'KW', 'prefix' => '965');
	$country_info['KGZ'] = array('name' => __('Kyrgyzstan','wppizza-admin'), 'ISO' => 'KGZ', 'ISO2' => 'KG', 'prefix' => '996');
	$country_info['LAO'] = array('name' => __('People\'s Democratic Republic','wppizza-admin'), 'ISO' => 'LAO', 'ISO2' => 'LA', 'prefix' => '856');
	$country_info['LVA'] = array('name' => __('Latvia','wppizza-admin'), 'ISO' => 'LVA', 'ISO2' => 'LV', 'prefix' => '371');
	$country_info['LBN'] = array('name' => __('Lebanon','wppizza-admin'), 'ISO' => 'LBN', 'ISO2' => 'LB', 'prefix' => '961');
	$country_info['LSO'] = array('name' => __('Lesotho','wppizza-admin'), 'ISO' => 'LSO', 'ISO2' => 'LS', 'prefix' => '266');
	$country_info['LBR'] = array('name' => __('Liberia','wppizza-admin'), 'ISO' => 'LBR', 'ISO2' => 'LR', 'prefix' => '231');
	$country_info['LBY'] = array('name' => __('Libya','wppizza-admin'), 'ISO' => 'LBY', 'ISO2' => 'LY', 'prefix' => '218');
	$country_info['LIE'] = array('name' => __('Liechtenstein','wppizza-admin'), 'ISO' => 'LIE', 'ISO2' => 'LI', 'prefix' => '423');
	$country_info['LTU'] = array('name' => __('Lithuania','wppizza-admin'), 'ISO' => 'LTU', 'ISO2' => 'LT', 'prefix' => '370');
	$country_info['LUX'] = array('name' => __('Luxembourg','wppizza-admin'), 'ISO' => 'LUX', 'ISO2' => 'LU', 'prefix' => '352');
	$country_info['MAC'] = array('name' => __('Macao','wppizza-admin'), 'ISO' => 'MAC', 'ISO2' => 'MO', 'prefix' => '853');
	$country_info['MKD'] = array('name' => __('Macedonia','wppizza-admin'), 'ISO' => 'MKD', 'ISO2' => 'MK', 'prefix' => '389');
	$country_info['MDG'] = array('name' => __('Madagascar','wppizza-admin'), 'ISO' => 'MDG', 'ISO2' => 'MG', 'prefix' => '261');
	$country_info['MWI'] = array('name' => __('Malawi','wppizza-admin'), 'ISO' => 'MWI', 'ISO2' => 'MW', 'prefix' => '265');
	$country_info['MYS'] = array('name' => __('Malaysia','wppizza-admin'), 'ISO' => 'MYS', 'ISO2' => 'MY', 'prefix' => '60');
	$country_info['MDV'] = array('name' => __('Maldives','wppizza-admin'), 'ISO' => 'MDV', 'ISO2' => 'MV', 'prefix' => '960');
	$country_info['MLI'] = array('name' => __('Mali','wppizza-admin'), 'ISO' => 'MLI', 'ISO2' => 'ML', 'prefix' => '223');
	$country_info['MLT'] = array('name' => __('Malta','wppizza-admin'), 'ISO' => 'MLT', 'ISO2' => 'MT', 'prefix' => '356');
	$country_info['MTQ'] = array('name' => __('Martinique','wppizza-admin'), 'ISO' => 'MTQ', 'ISO2' => 'MQ', 'prefix' => '596');
	$country_info['MRT'] = array('name' => __('Mauritania','wppizza-admin'), 'ISO' => 'MRT', 'ISO2' => 'MR', 'prefix' => '222');
	$country_info['MUS'] = array('name' => __('Mauritius','wppizza-admin'), 'ISO' => 'MUS', 'ISO2' => 'MU', 'prefix' => '230');
	$country_info['MEX'] = array('name' => __('Mexico','wppizza-admin'), 'ISO' => 'MEX', 'ISO2' => 'MX', 'prefix' => '52');
	$country_info['FSM'] = array('name' => __('Micronesia','wppizza-admin'), 'ISO' => 'FSM', 'ISO2' => 'FM', 'prefix' => '691');
	$country_info['MDA'] = array('name' => __('Moldova','wppizza-admin'), 'ISO' => 'MDA', 'ISO2' => 'MD', 'prefix' => '373');
	$country_info['MCO'] = array('name' => __('Monaco','wppizza-admin'), 'ISO' => 'MCO', 'ISO2' => 'MC', 'prefix' => '377');
	$country_info['MNG'] = array('name' => __('Mongolia','wppizza-admin'), 'ISO' => 'MNG', 'ISO2' => 'MN', 'prefix' => '976');
	$country_info['MNE'] = array('name' => __('Montenegro','wppizza-admin'), 'ISO' => 'MNE', 'ISO2' => 'ME', 'prefix' => '382');
	$country_info['MSR'] = array('name' => __('Montserrat','wppizza-admin'), 'ISO' => 'MSR', 'ISO2' => 'MS', 'prefix' => '1664');
	$country_info['MAR'] = array('name' => __('Morocco','wppizza-admin'), 'ISO' => 'MAR', 'ISO2' => 'MA', 'prefix' => '212');
	$country_info['MOZ'] = array('name' => __('Mozambique','wppizza-admin'), 'ISO' => 'MOZ', 'ISO2' => 'MZ', 'prefix' => '258');
	$country_info['NAM'] = array('name' => __('Namibia','wppizza-admin'), 'ISO' => 'NAM', 'ISO2' => 'NA', 'prefix' => '264');
	$country_info['NRU'] = array('name' => __('Nauru','wppizza-admin'), 'ISO' => 'NRU', 'ISO2' => 'NR', 'prefix' => '674');
	$country_info['NPL'] = array('name' => __('Nepal','wppizza-admin'), 'ISO' => 'NPL', 'ISO2' => 'NP', 'prefix' => '977');
	$country_info['NLD'] = array('name' => __('Netherlands','wppizza-admin'), 'ISO' => 'NLD', 'ISO2' => 'NL', 'prefix' => '31');
	$country_info['ANT'] = array('name' => __('Netherlands Antilles','wppizza-admin'), 'ISO' => 'ANT', 'ISO2' => 'AN', 'prefix' => '599');
	$country_info['NCL'] = array('name' => __('New Caledonia','wppizza-admin'), 'ISO' => 'NCL', 'ISO2' => 'NC', 'prefix' => '687');
	$country_info['NZL'] = array('name' => __('New Zealand','wppizza-admin'), 'ISO' => 'NZL', 'ISO2' => 'NZ', 'prefix' => '64');
	$country_info['NIC'] = array('name' => __('Nicaragua','wppizza-admin'), 'ISO' => 'NIC', 'ISO2' => 'NI', 'prefix' => '505');
	$country_info['NER'] = array('name' => __('Niger','wppizza-admin'), 'ISO' => 'NER', 'ISO2' => 'NE', 'prefix' => '227');
	$country_info['NGA'] = array('name' => __('Nigeria','wppizza-admin'), 'ISO' => 'NGA', 'ISO2' => 'NG', 'prefix' => '234');
	$country_info['NFK'] = array('name' => __('Norfolk Island','wppizza-admin'), 'ISO' => 'NFK', 'ISO2' => 'NF', 'prefix' => '672');
	$country_info['MNP'] = array('name' => __('Northern Mariana Islands','wppizza-admin'), 'ISO' => 'MNP', 'ISO2' => 'MP', 'prefix' => '1670');
	$country_info['NOR'] = array('name' => __('Norway','wppizza-admin'), 'ISO' => 'NOR', 'ISO2' => 'NO', 'prefix' => '47');
	$country_info['OMN'] = array('name' => __('Oman','wppizza-admin'), 'ISO' => 'OMN', 'ISO2' => 'OM', 'prefix' => '968');
	$country_info['PAK'] = array('name' => __('Pakistan','wppizza-admin'), 'ISO' => 'PAK', 'ISO2' => 'PK', 'prefix' => '92');
	$country_info['PLW'] = array('name' => __('Palau','wppizza-admin'), 'ISO' => 'PLW', 'ISO2' => 'PW', 'prefix' => '680');
	$country_info['PSE'] = array('name' => __('Palestine','wppizza-admin'), 'ISO' => 'PSE', 'ISO2' => 'PS', 'prefix' => '970');
	$country_info['PAN'] = array('name' => __('Panama','wppizza-admin'), 'ISO' => 'PAN', 'ISO2' => 'PA', 'prefix' => '507');
	$country_info['PNG'] = array('name' => __('Papua New Guinea','wppizza-admin'), 'ISO' => 'PNG', 'ISO2' => 'PG', 'prefix' => '675');
	$country_info['PRY'] = array('name' => __('Paraguay','wppizza-admin'), 'ISO' => 'PRY', 'ISO2' => 'PY', 'prefix' => '595');
	$country_info['PER'] = array('name' => __('Peru','wppizza-admin'), 'ISO' => 'PER', 'ISO2' => 'PE', 'prefix' => '51');
	$country_info['PHL'] = array('name' => __('Philippines','wppizza-admin'), 'ISO' => 'PHL', 'ISO2' => 'PH', 'prefix' => '63');
	$country_info['POL'] = array('name' => __('Poland','wppizza-admin'), 'ISO' => 'POL', 'ISO2' => 'PL', 'prefix' => '48');
	$country_info['PRT'] = array('name' => __('Portugal','wppizza-admin'), 'ISO' => 'PRT', 'ISO2' => 'PT', 'prefix' => '351');
	$country_info['PRI'] = array('name' => __('Puerto Rico','wppizza-admin'), 'ISO' => 'PRI', 'ISO2' => 'PR', 'prefix' => '1939');
	$country_info['QAT'] = array('name' => __('Qatar','wppizza-admin'), 'ISO' => 'QAT', 'ISO2' => 'QA', 'prefix' => '974');
	$country_info['REU'] = array('name' => __('Reunion','wppizza-admin'), 'ISO' => 'REU', 'ISO2' => 'RE', 'prefix' => '262');
	$country_info['ROU'] = array('name' => __('Romania','wppizza-admin'), 'ISO' => 'ROU', 'ISO2' => 'RO', 'prefix' => '40');
	$country_info['RUS'] = array('name' => __('Russia','wppizza-admin'), 'ISO' => 'RUS', 'ISO2' => 'RU', 'prefix' => '7');
	$country_info['RWA'] = array('name' => __('Rwanda','wppizza-admin'), 'ISO' => 'RWA', 'ISO2' => 'RW', 'prefix' => '250');
	$country_info['KNA'] = array('name' => __('Saint Kitts and Nevis','wppizza-admin'), 'ISO' => 'KNA', 'ISO2' => 'KN', 'prefix' => '1869');
	$country_info['LCA'] = array('name' => __('Saint Lucia','wppizza-admin'), 'ISO' => 'LCA', 'ISO2' => 'LC', 'prefix' => '1758');
	$country_info['SPM'] = array('name' => __('Saint Pierre and Miquelon','wppizza-admin'), 'ISO' => 'SPM', 'ISO2' => 'PM', 'prefix' => '508');
	$country_info['VCT'] = array('name' => __('Saint Vincent and the Grenadines','wppizza-admin'), 'ISO' => 'VCT', 'ISO2' => 'VC', 'prefix' => '1784');
	$country_info['WSM'] = array('name' => __('Samoa','wppizza-admin'), 'ISO' => 'WSM', 'ISO2' => 'WS', 'prefix' => '685');
	$country_info['SMR'] = array('name' => __('San Marino','wppizza-admin'), 'ISO' => 'SMR', 'ISO2' => 'SM', 'prefix' => '378');
	$country_info['STP'] = array('name' => __('Sao Tome and Principe','wppizza-admin'), 'ISO' => 'STP', 'ISO2' => 'ST', 'prefix' => '239');
	$country_info['SAU'] = array('name' => __('Saudi Arabia','wppizza-admin'), 'ISO' => 'SAU', 'ISO2' => 'SA', 'prefix' => '966');
	$country_info['SEN'] = array('name' => __('Senegal','wppizza-admin'), 'ISO' => 'SEN', 'ISO2' => 'SN', 'prefix' => '221');
	$country_info['SRB'] = array('name' => __('Serbia','wppizza-admin'), 'ISO' => 'SRB', 'ISO2' => 'RS', 'prefix' => '381');
	$country_info['SYC'] = array('name' => __('Seychelles','wppizza-admin'), 'ISO' => 'SYC', 'ISO2' => 'SC', 'prefix' => '248');
	$country_info['SLE'] = array('name' => __('Sierra Leone','wppizza-admin'), 'ISO' => 'SLE', 'ISO2' => 'SL', 'prefix' => '232');
	$country_info['SGP'] = array('name' => __('Singapore','wppizza-admin'), 'ISO' => 'SGP', 'ISO2' => 'SG', 'prefix' => '65');
	$country_info['SVK'] = array('name' => __('Slovakia','wppizza-admin'), 'ISO' => 'SVK', 'ISO2' => 'SK', 'prefix' => '421');
	$country_info['SVN'] = array('name' => __('Slovenia','wppizza-admin'), 'ISO' => 'SVN', 'ISO2' => 'SI', 'prefix' => '386');
	$country_info['SLB'] = array('name' => __('Solomon Islands','wppizza-admin'), 'ISO' => 'SLB', 'ISO2' => 'SB', 'prefix' => '677');
	$country_info['SOM'] = array('name' => __('Somalia','wppizza-admin'), 'ISO' => 'SOM', 'ISO2' => 'SO', 'prefix' => '252');
	$country_info['ZAF'] = array('name' => __('South Africa','wppizza-admin'), 'ISO' => 'ZAF', 'ISO2' => 'ZA', 'prefix' => '27');
	$country_info['ESP'] = array('name' => __('Spain','wppizza-admin'), 'ISO' => 'ESP', 'ISO2' => 'ES', 'prefix' => '34');
	$country_info['LKA'] = array('name' => __('Sri Lanka','wppizza-admin'), 'ISO' => 'LKA', 'ISO2' => 'LK', 'prefix' => '94');
	$country_info['SDN'] = array('name' => __('Sudan','wppizza-admin'), 'ISO' => 'SDN', 'ISO2' => 'SD', 'prefix' => '249');
	$country_info['SUR'] = array('name' => __('Suriname','wppizza-admin'), 'ISO' => 'SUR', 'ISO2' => 'SR', 'prefix' => '597');
	$country_info['SWZ'] = array('name' => __('Swaziland','wppizza-admin'), 'ISO' => 'SWZ', 'ISO2' => 'SZ', 'prefix' => '268');
	$country_info['SWE'] = array('name' => __('Sweden','wppizza-admin'), 'ISO' => 'SWE', 'ISO2' => 'SE', 'prefix' => '46');
	$country_info['CHE'] = array('name' => __('Switzerland','wppizza-admin'), 'ISO' => 'CHE', 'ISO2' => 'CH', 'prefix' => '41');
	$country_info['SYR'] = array('name' => __('Syria','wppizza-admin'), 'ISO' => 'SYR', 'ISO2' => 'SY', 'prefix' => '963');
	$country_info['TWN'] = array('name' => __('Taiwan','wppizza-admin'), 'ISO' => 'TWN', 'ISO2' => 'TW', 'prefix' => '886');
	$country_info['TJK'] = array('name' => __('Tajikistan','wppizza-admin'), 'ISO' => 'TJK', 'ISO2' => 'TJ', 'prefix' => '992');
	$country_info['TZA'] = array('name' => __('Tanzania','wppizza-admin'), 'ISO' => 'TZA', 'ISO2' => 'TZ', 'prefix' => '255');
	$country_info['THA'] = array('name' => __('Thailand','wppizza-admin'), 'ISO' => 'THA', 'ISO2' => 'TH', 'prefix' => '66');
	$country_info['TLS'] = array('name' => __('Timor-Leste','wppizza-admin'), 'ISO' => 'TLS', 'ISO2' => 'TL', 'prefix' => '670');
	$country_info['TGO'] = array('name' => __('Togo','wppizza-admin'), 'ISO' => 'TGO', 'ISO2' => 'TG', 'prefix' => '228');
	$country_info['TON'] = array('name' => __('Tonga','wppizza-admin'), 'ISO' => 'TON', 'ISO2' => 'TO', 'prefix' => '676');
	$country_info['TTO'] = array('name' => __('Trinidad and Tobago','wppizza-admin'), 'ISO' => 'TTO', 'ISO2' => 'TT', 'prefix' => '1868');
	$country_info['TUN'] = array('name' => __('Tunisia','wppizza-admin'), 'ISO' => 'TUN', 'ISO2' => 'TN', 'prefix' => '216');
	$country_info['TUR'] = array('name' => __('Turkey','wppizza-admin'), 'ISO' => 'TUR', 'ISO2' => 'TR', 'prefix' => '90');
	$country_info['TKM'] = array('name' => __('Turkmenistan','wppizza-admin'), 'ISO' => 'TKM', 'ISO2' => 'TM', 'prefix' => '993');
	$country_info['TCA'] = array('name' => __('Turks and Caicos Islands','wppizza-admin'), 'ISO' => 'TCA', 'ISO2' => 'TC', 'prefix' => '1649');
	$country_info['UGA'] = array('name' => __('Uganda','wppizza-admin'), 'ISO' => 'UGA', 'ISO2' => 'UG', 'prefix' => '256');
	$country_info['UKR'] = array('name' => __('Ukraine','wppizza-admin'), 'ISO' => 'UKR', 'ISO2' => 'UA', 'prefix' => '380');
	$country_info['ARE'] = array('name' => __('United Arab Emirates','wppizza-admin'), 'ISO' => 'ARE', 'ISO2' => 'AE', 'prefix' => '971');
	$country_info['GBR'] = array('name' => __('United Kingdom','wppizza-admin'), 'ISO' => 'GBR', 'ISO2' => 'GB', 'prefix' => '44');
	$country_info['USA'] = array('name' => __('United States','wppizza-admin'), 'ISO' => 'USA', 'ISO2' => 'US', 'prefix' => '1');
	$country_info['URY'] = array('name' => __('Uruguay','wppizza-admin'), 'ISO' => 'URY', 'ISO2' => 'UY', 'prefix' => '598');
	$country_info['UZB'] = array('name' => __('Uzbekistan','wppizza-admin'), 'ISO' => 'UZB', 'ISO2' => 'UZ', 'prefix' => '998');
	$country_info['VUT'] = array('name' => __('Vanuatu','wppizza-admin'), 'ISO' => 'VUT', 'ISO2' => 'VU', 'prefix' => '678');
	$country_info['VEN'] = array('name' => __('Venezuela','wppizza-admin'), 'ISO' => 'VEN', 'ISO2' => 'VE', 'prefix' => '58');
	$country_info['VNM'] = array('name' => __('Vietnam','wppizza-admin'), 'ISO' => 'VNM', 'ISO2' => 'VN', 'prefix' => '84');
	$country_info['VGB'] = array('name' => __('Virgin Islands, British','wppizza-admin'), 'ISO' => 'VGB', 'ISO2' => 'VG', 'prefix' => '1284');
	$country_info['VIR'] = array('name' => __('Virgin Islands, US','wppizza-admin'), 'ISO' => 'VIR', 'ISO2' => 'VI', 'prefix' => '1340');
	$country_info['YEM'] = array('name' => __('Yemen','wppizza-admin'), 'ISO' => 'YEM', 'ISO2' => 'YE', 'prefix' => '967');
	$country_info['ZMB'] = array('name' => __('Zambia','wppizza-admin'), 'ISO' => 'ZMB', 'ISO2' => 'ZM', 'prefix' => '260');
	$country_info['ZWE'] = array('name' => __('Zimbabwe','wppizza-admin'), 'ISO' => 'ZWE', 'ISO2' => 'ZW', 'prefix' => '263');

	/*
		exclude some keys if set
	*/
	if(!empty($exclude)){
		foreach($exclude as $iso3){
			unset($country_info[$iso3]);
		}
	}

	/*
		simple sort by name
	*/
	asort($country_info);

	/*
		ini return
	*/
	$res = array();

	/*
		no key set
		simply return array as is
	*/
	if(empty($key)){
		return $country_info;
	}

	/*
		if key set
	*/
	if(!empty($key)){
		foreach($country_info as $k=>$val){

			/*
				if value set
			*/
			if(!empty($value)){
			$res[$val[$key]] = '' ;

				$country_values = array();

				foreach($value as $valKey){
					if(!empty($val[$valKey])){
						/* add + if prefix - because we can */
						$display_value = ($valKey =='prefix') ? '+'.$val[$valKey].'' : ''.$val[$valKey].'';
						/* add [] around it if not name - because we can */
						$display_value = ($valKey =='name') ? $display_value : '['.$display_value.']';
						/* create value */
						$country_values[] = $display_value;
					}
				}
				/* implode with spaces */
				$res[$val[$key]] = implode(' ', $country_values);

			}

			/*
				if no value set
				return array
			*/
			if(empty($value)){
				$res[$val[$key]] = $val ;
			}
		}
	}
	/* simple sort */
	asort($res);

return $res;
}


/*******************************************************
*
*	match a string between tags
*	usage
*	wppizza_get_string_between_tags($string, "[tag]", "[/tag]");
******************************************************/
function wppizza_get_string_between_tags($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

/*************************************************************
	convert bytes to something more readable
*************************************************************/
function wppizza_convert_bytes($number){
    $len = strlen($number);
    if($len < 4){
        return sprintf("%d b", $number);
    }
    if($len >= 4 && $len <=6){
        return sprintf("%0.2f Kb", $number/1024);
    }
    if($len >= 7 && $len <=9){
        return sprintf("%0.2f Mb", $number/1024/1024);
    }
   return sprintf("%0.2f Gb", $number/1024/1024/1024);
}

/*****************************************************
* return new default options when updating plugin
* compares options in option table with default and returns array
* of options that are not yet in option table or are not used anymore
* used on plugin update
* @a1=>comparison array 1 , @a2=>comparison array 2
******************************************************/
function wppizza_recursive_compare_options($a1, $a2) {
    $r = array();
    if(is_array(($a1))){
        foreach($a1 as $k => $v){
            if(isset($a2[$k])){
                $diff = wppizza_recursive_compare_options($a1[$k], $a2[$k]);
                if (!empty($diff)){
                    $r[$k] = $diff;
                }
            }else{
                $r[$k] = $v;
            }
        }
    }
    return $r;
}
/******************************************************
* @arr1=>comparison array 1 , @arr2=>comparison array 2
* intersect - used for removing obsolete options on
* plugin update
******************************************************/
function wppizza_array_intersect_assoc_recursive($arr1, $arr2) {
    if (!is_array($arr1) || !is_array($arr2)) {
		return $arr1;/* arr1 being the current value */
        //return (string) $arr1 == (string) $arr2;
    }
    $commonkeys = array_intersect(array_keys($arr1), array_keys($arr2));
    $ret = array();
    foreach ($commonkeys as $key) {
        $ret[$key] = wppizza_array_intersect_assoc_recursive($arr1[$key], $arr2[$key]);
    }
    return $ret;
}


/*************************************************************
	return required mysql version
*************************************************************/
function wppizza_required_mysql_version($mysql_version_required = '5.5'){
	return $mysql_version_required;
}
/*************************************************************
	get mysql version if we can
*************************************************************/
function wppizza_get_mysql_version(){
	$mysql_info=array();
	$mysql_info['version']=false;
	$mysql_info['info']='';
	$mysql_info['extension']='unable to determine mysql extension';

	if(!function_exists('mysqli_connect')){
		$mysql_info['info']='mysqli is not available - it is highly recommended to enable it';
	}

	if(function_exists('mysqli_connect')){

		$mysql_info['extension']='mysqli';

		$host_port=explode(':',DB_HOST);
		if(count($host_port)==2){
			$wppizza_test_mysql=mysqli_connect($host_port[0], DB_USER, DB_PASSWORD, DB_NAME, $host_port[1]);
		}else{
			$wppizza_test_mysql=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		}
		// Check connection
		if (mysqli_connect_errno()){
			$wppizza_test_mysql_error= mysqli_connect_error();
	 		$mysql_info['info']="Failed to connect to MySQL: " . print_r($wppizza_test_mysql_error,true);
		}else{
			$mysql_info['version']=mysqli_get_server_info($wppizza_test_mysql);
			$mysql_info['info']=mysqli_get_server_info($wppizza_test_mysql);
		}
		mysqli_close($wppizza_test_mysql);
	}

	/**try normal sql connection if we do not have mysqli**/
	if(!function_exists('mysqli_connect') && function_exists('mysql_connect') ){

		$mysql_info['extension']='mysql';

		$host_port=explode(':',DB_HOST);
		if(count($host_port)==2){
			$wppizza_test_mysql=mysql_connect($host_port[0], DB_USER, DB_PASSWORD, DB_NAME, $host_port[1]);
		}else{
			$wppizza_test_mysql=mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		}
		// Check connection
		if (!$wppizza_test_mysql) {
			$wppizza_test_mysql_error= mysql_error();
	 		$mysql_info['info']="Failed to connect to MySQL: " . print_r($wppizza_test_mysql_error,true);
		}else{
			$mysql_info['version']=mysql_get_server_info($wppizza_test_mysql);
			$mysql_info['info']=mysql_get_server_info($wppizza_test_mysql);

		}
		mysql_close($wppizza_test_mysql);
	}

	return $mysql_info;
}
?>