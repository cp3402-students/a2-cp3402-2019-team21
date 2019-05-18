<?php if ( ! defined( 'ABSPATH' ) ) exit;/*Exit if accessed directly*/ ?>
<?php

	/*some lorem ipsum to insert as default description for items**/
	$loremIpsum[0]='Praesent ut massa dolor. Aenean pharetra quam at risus aliquet laoreet posuere ipsum porta.' ;
	$loremIpsum[1]='Integer id lacus sapien, eu porta lectus. Vestibulum justo elit, rutrum a pharetra id, ornare ac est. ' ;
	$loremIpsum[2]='Sed commodo scelerisque magna, eu tempus ante faucibus vitae. Nulla tempus varius ornare. ' ;
	$loremIpsum[3]='Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. ' ;
	$loremIpsum[4]='Praesent non pulvinar neque. Donec ut ante tortor. Fusce sit amet velit eget arcu lobortis imperdiet.' ;
	$loremIpsum[5]='Nunc odio libero, tempor quis mollis eu, gravida vel augue. Aliquam erat volutpat.' ;
	$loremIpsum[6]='Sed neque metus, tincidunt quis fermentum id, rhoncus ut neque. Fusce non metus enim.' ;
	$loremIpsum[7]='Aliquam nec turpis est, id consequat dolor. Etiam rhoncus elementum cursus.' ;
	$loremIpsum[8]='Etiam et dolor turpis, id gravida eros. Ut eu orci nulla. Fusce porta porttitor arcu sed sollicitudin.' ;
	$loremIpsum[9]='Quisque a augue dui, quis venenatis leo. Curabitur bibendum faucibus neque at vehicula. ' ;
	$loremIpsum[10]='Donec feugiat metus vel metus gravida et accumsan tellus pretium. Phasellus tortor sapien, aliquam convallis faucibus non.' ;
	$loremIpsum[11]='Suspendisse potenti. Sed feugiat lectus et odio dignissim at congue libero fermentum.' ;
	$loremIpsum[12]='Sed sodales felis lorem. Nullam eleifend magna eget turpis rutrum ac auctor mauris pharetra.' ;
	$loremIpsum[13]='Aliquam convallis lacinia suscipit. Mauris ac diam enim. Nullam quis lacus odio, et sagittis sem.' ;
	$loremIpsum[14]='Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.' ;
	$loremIpsum[15]='Suspendisse potenti. Pellentesque habitant morbi tristique senectus et netus.' ;
	$loremIpsum[16]='Aenean vitae est arcu, ut ullamcorper dolor.' ;
	$loremIpsum[17]='Sed at tellus quam, in vulputate sem. Ut eu orci nulla. Fusce porta porttitor arcu sed.';
	$loremIpsum[18]='Mauris gravida, nisl a mollis lobortis.';
	$loremIpsum[19]='Phasellus molestie mauris nec sem malesuada rhoncus. Donec volutpat interdum elit.';
	$loremIpsum[20]='Vivamus nisi enim, faucibus ut auctor nec, vulputate vitae nibh. Maecenas scelerisque malesuada risus, sit.';


	/*default sizeoptions/tiers and associated prices**/
	$default_sizes=array(
		0=>array(
			0=>array('lbl'=>esc_html__('regular', 'wppizza'),'price'=>'5.99')
		),
		1=>array(
			0=>array('lbl'=>esc_html__('small', 'wppizza'),'price'=>'4.95'),
			1=>array('lbl'=>esc_html__('large', 'wppizza'),'price'=>'9.95')
		),
		2=>array(
			0=>array('lbl'=>esc_html__('small', 'wppizza'),'price'=>'4.95'),
			1=>array('lbl'=>esc_html__('medium', 'wppizza'),'price'=>'7.45'),
			2=>array('lbl'=>esc_html__('large', 'wppizza'),'price'=>'9.95')
		),
		3=>array(
			0=>array('lbl'=>esc_html__('small', 'wppizza'),'price'=>'4.95'),
			1=>array('lbl'=>esc_html__('medium', 'wppizza'),'price'=>'7.45'),
			2=>array('lbl'=>esc_html__('large', 'wppizza'),'price'=>'9.95'),
			3=>array('lbl'=>esc_html__('xxl', 'wppizza'),'price'=>'14.99')
		),
		4=>array(
			0=>array('lbl'=>esc_html__('0.25l', 'wppizza'),'price'=>'0.99'),
			1=>array('lbl'=>esc_html__('0.33l', 'wppizza'),'price'=>'1.25'),
			2=>array('lbl'=>esc_html__('0.75l', 'wppizza'),'price'=>'1.99'),
			3=>array('lbl'=>esc_html__('1.00l', 'wppizza'),'price'=>'2.25'),
			4=>array('lbl'=>esc_html__('1.50l', 'wppizza'),'price'=>'2.99'),
		)
	);
	/* allow filtering */
	$default_sizes = apply_filters('wppizza_filter_install_default_sizes', $default_sizes);


	/*default prices**/
	$default_prices=array();
	foreach($default_sizes as $k=>$v){
		foreach($v as $l=>$m){
			$default_prices[$k][$l]=$m['price'];
		}
	}

	/*default additives**/
	$default_additives=array(
		0=>array('sort'=>1,'name'=>esc_html__('Food coloring', 'wppizza')),
		1=>array('sort'=>2,'name'=>esc_html__('Flavor enhancers', 'wppizza')),
		2=>array('sort'=>3,'name'=>esc_html__('Preservatives', 'wppizza')),
		3=>array('sort'=>4,'name'=>esc_html__('Stabilizers', 'wppizza')),
		4=>array('sort'=>5,'name'=>esc_html__('Sweeteners', 'wppizza'))
	);
	/* allow filtering */
	$default_additives = apply_filters('wppizza_filter_install_default_additives', $default_additives);

	/********************************************************************************************
	*
	*	[insert default categories and menu items]
	*
	*********************************************************************************************/
	/*************************************
		[categories]
	/*************************************/
	$default_categories = array(
		0=>esc_html__('Special Offers', 'wppizza'),
		1=>esc_html__('Pizza', 'wppizza'),
		2=>esc_html__('Pasta', 'wppizza'),
		3=>esc_html__('Salads', 'wppizza'),
		4=>esc_html__('Desserts', 'wppizza'),
		5=>esc_html__('Beverages', 'wppizza'),
		6=>esc_html__('Snacks', 'wppizza')
	);
	/* allow filtering */
	$default_categories = apply_filters('wppizza_filter_install_default_categories', $default_categories);


	/*array to cach/initialize sortorder of categories [inserted into default options below]**/
	$category_sort_hierarchy=array();

	/*************************************
		[add item to categories [linked by key]]
	/*************************************/
	$defaultItems=array();
	$defaultItems[0] = array(
		array('title'=>esc_html__('Special Pizza', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>3,'prices'=>$default_prices[3]),'featuredimage'=>'pizza-64.png'),
		array('title'=>esc_html__('Great Steak', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>3,'prices'=>$default_prices[3]),'featuredimage'=>'steak-64.png'),
		array('title'=>esc_html__('Yummy Pudding', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>3,'prices'=>$default_prices[3]),'featuredimage'=>'cake-64.png')
	);
	$defaultItems[1] = array(
		array('title'=>esc_html__('Pizza A', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>2,'prices'=>$default_prices[2]),'featuredimage'=>'pizza-64.png'),
		array('title'=>esc_html__('Pizza B', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>2,'prices'=>$default_prices[2]),'featuredimage'=>'pizza-64.png'),
		array('title'=>esc_html__('Pizza C', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>2,'prices'=>$default_prices[2]),'featuredimage'=>'pizza-64.png'),
		array('title'=>esc_html__('Pizza D', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1,2),'sizes'=>2,'prices'=>$default_prices[2]),'featuredimage'=>''),
		array('title'=>esc_html__('Pizza E', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>2,'prices'=>$default_prices[2]),'featuredimage'=>''),
		array('title'=>esc_html__('Pizza F', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(2,3,4),'sizes'=>2,'prices'=>$default_prices[2]),'featuredimage'=>'')
	);
	$defaultItems[2] = array(
		array('title'=>esc_html__('Pasta A', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Pasta B', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Pasta C', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Pasta D', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Pasta E', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Pasta F', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>'')
	);
	$defaultItems[3] = array(
		array('title'=>esc_html__('Salad A', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Salad B', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Salad C', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Salad D', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Salad E', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Salad F', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>'')
	);
	$defaultItems[4] = array(
		array('title'=>esc_html__('Dessert A', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1,2),'sizes'=>0,'prices'=>$default_prices[0]),'featuredimage'=>'cake-64.png'),
		array('title'=>esc_html__('Dessert B', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>0,'prices'=>$default_prices[0]),'featuredimage'=>''),
		array('title'=>esc_html__('Dessert C', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1,4),'sizes'=>0,'prices'=>$default_prices[0]),'featuredimage'=>''),
		array('title'=>esc_html__('Dessert D', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>0,'prices'=>$default_prices[0]),'featuredimage'=>''),
		array('title'=>esc_html__('Dessert E', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(2,3,4),'sizes'=>0,'prices'=>$default_prices[0]),'featuredimage'=>''),
		array('title'=>esc_html__('Dessert F', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>0,'prices'=>$default_prices[0]),'featuredimage'=>'')
	);
	$defaultItems[5] = array(
		array('title'=>esc_html__('Drink A', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$default_prices[4]),'featuredimage'=>''),
		array('title'=>esc_html__('Drink B', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$default_prices[4]),'featuredimage'=>''),
		array('title'=>esc_html__('Drink C', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$default_prices[4]),'featuredimage'=>''),
		array('title'=>esc_html__('Drink D', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$default_prices[4]),'featuredimage'=>''),
		array('title'=>esc_html__('Drink E', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$default_prices[4]),'featuredimage'=>''),
		array('title'=>esc_html__('Drink F', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$default_prices[4]),'featuredimage'=>'')
	);

	$defaultItems[6] = array(
		array('title'=>esc_html__('Snack A', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Snack B', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Snack C', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Snack D', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Snack E', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>''),
		array('title'=>esc_html__('Snack F', 'wppizza'),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$default_prices[1]),'featuredimage'=>'')
	);
	/* allow filtering */
	$defaultItems = apply_filters('wppizza_filter_install_default_items', $defaultItems, $default_prices, $loremIpsum);


	/**********************************************
	*
	*	[now insert categories and items]
	*
	**********************************************/
	$parent_term = term_exists(''.WPPIZZA_SLUG.''); // array is returned if taxonomy is given

	$parent_term_id = $parent_term['term_id']; // get numeric term id
	$upload_dir = wp_upload_dir();//err, upload dir . doh
	$i=0;
	foreach($default_categories as $k=>$v){
		/*insert category*/
		$term=wp_insert_term(
		  ''.$v.'',
		  ''.WPPIZZA_TAXONOMY.'',
		  array(
		    'description'=> ''.__('Description of', 'wppizza-admin').' '.$v.'',
		    'slug' => sanitize_title($v),
		    'parent'=> $parent_term_id
		  )
		);

		if ( is_wp_error($term) ) {
			echo $term->get_error_message();
		}else{
			/*insert item into category*/
			$j=0;
			foreach($defaultItems[$k] as $iKey=>$items){
				$item = array(
			  	'post_title'    	=> wp_strip_all_tags( $items['title'] ),
			  	'post_content'  	=> $loremIpsum[$items['descr']],
			  	'post_type'     	=> WPPIZZA_POST_TYPE,
			  	'post_status'   	=> 'publish',
			  	'menu_order'	  	=> $j,
			  	'comment_status'	=> 'closed',
			  	'ping_status'		=> 'closed',
			  	'post_author'  		=> get_current_user_id()
				);

				/** insert post */
				$post_id = wp_insert_post($item);

				/** separately add taxonomy id, as wppizza_cap_categories does not yet have permissions on install to add these */
				$term_taxonomy_ids = wp_set_object_terms( $post_id, array($term['term_id']), WPPIZZA_TAXONOMY, true );

				/**add meta boxes values**/
				$metaId=update_post_meta($post_id, ''.WPPIZZA_SLUG.'', $items['meta']) ;

				/*add thumbnail/featured image if set and available**/
				if($items['featuredimage']!='' && is_file(WPPIZZA_PATH.'assets/images/'.$items['featuredimage'].'')){

					$filename = basename($items['featuredimage']);
					if(wp_mkdir_p($upload_dir['path'])){
						$file = $upload_dir['path'] . '/' . $filename;
					}else{
						$file = $upload_dir['basedir'] . '/' . $filename;
					}
					/* copy image to upload directory */
					copy(WPPIZZA_PATH.'assets/images/'.$items['featuredimage'].'' , $file);

					$wp_filetype = wp_check_filetype($filename, null );
					$attachment = array(
					   	'post_mime_type' => $wp_filetype['type'],
				    	'guid' => $upload_dir['url'] . '/' .  $filename ,
				    	'post_title' => sanitize_file_name($filename),
				    	'post_content' => '',
				    	'post_status' => 'inherit'
					);
					$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					set_post_thumbnail( $post_id, $attach_id );
				}

			$j++;
			}
			/*add term id to category sort array to be inserted into options table below*/
			$category_sort_hierarchy[$term['term_id']]=$i;
		$i++;
	}}

	/**********************************************
	*
	*	[insert menu category pages]
	*
	**********************************************/
	$i=0;
	foreach($default_categories as $iKey=>$items){
		$item = array(
		  'post_title'    	=> wp_strip_all_tags( $items),
		  'post_content'  	=> '['.WPPIZZA_SLUG.' category="'.sanitize_title($items).'" noheader="1"]',
		  'post_name'  		=> sanitize_title_with_dashes($items),
		  'post_type'     	=> 'page',
		  'post_status'   	=> 'publish',
		  'menu_order'	  	=> $iKey,
		  'post_parent'	  	=> $pages_parent_id,
		  'comment_status'	=> 'closed',
		  'ping_status'		=> 'closed'
		);
		$post_id=wp_insert_post($item);
	}
?>