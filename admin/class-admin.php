<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Plus_Admin') ) {

	class FPD_Plus_Admin {

		public function __construct() {

			require_once( __DIR__.'/class-settings.php' );

		}
	}

}

new FPD_Plus_Admin();

?>