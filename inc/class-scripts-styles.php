<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if(!class_exists('FPD_Plus_Scripts_Styles')) {

	class FPD_Plus_Scripts_Styles {

		public static $add_script = false;

		public function __construct() {

			add_action( 'init', array( &$this, 'register'), 10 );

		}

		public function register() {

			$plus_js_url = Fancy_Product_Designer_Plus::LOCAL ? 'http://radykal.dep/fpd/dist/addons/plus/FancyProductDesignerPlus.js' : plugins_url( '/js/FancyProductDesignerPlus.min.js', dirname(__FILE__) );

			wp_register_script( 'fpd-plus', $plus_js_url, false, Fancy_Product_Designer_Plus::VERSION );

		}

	}

}

new FPD_Plus_Scripts_Styles();

?>