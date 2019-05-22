<?php

if(!class_exists('FmInitWidget')):

	/**
	*
	*/
	class FmInitWidget
	{

		function __construct()
		{
			add_action( 'widgets_init', array($this, 'initWidget'));
		}


		function initWidget(){
			global $TLPfoodmenu;
			$TLPfoodmenu->loadWidget( $TLPfoodmenu->widgetsPath );
		}
	}


endif;
