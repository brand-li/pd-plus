<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Plus_Settings') ) {

	class FPD_Plus_Settings {

		public function __construct() {

			//Global Settings
			add_filter( 'fpd_settings_tabs', array(&$this, 'add_plus_tab') );
			add_filter( 'fpd_settings_blocks', array(&$this, 'add_plus_tab_blocks') );
			add_action( 'fpd_block_options_end', array(&$this, 'add_block_options') );

			//IPS
			add_action( 'fpd_ips_tabs', array(&$this, 'add_ips_tab') );
			add_filter( 'fpd_ips_tabs_options', array(&$this, 'add_ips_tab_options') );

			//Products
			add_action( 'fpd_shortcode_module_options', array( &$this, 'add_shortcode_module_options') );

		}

		public function add_plus_tab( $tabs ) {

			$tabs['plus'] = __('Plus', 'radykal');

			return $tabs;

		}

		public function add_plus_tab_blocks( $blocks ) {

			$blocks['plus'] = array(
				'plus-modules' => __('Modules', 'radykal'),
				'plus-tools' => __('Tools', 'radykal'),
			);

			return $blocks;

		}

		public function add_block_options() {

			$plus_options = self::get_options();
			FPD_Settings::$radykal_settings->add_block_options( 'plus-modules', $plus_options['modules']);
			FPD_Settings::$radykal_settings->add_block_options( 'plus-tools', $plus_options['tools']);

		}

		public function add_ips_tab( $ips_tabs ) {

			$ips_tabs['plus-options'] = __('Plus', 'radykal');

			return $ips_tabs;

		}

		public function add_ips_tab_options( $options ) {

			$nn_module_placeholder = fpd_get_option('fpd_plus_names_numbers_dropdown');
			$nn_module_placeholder = empty( $nn_module_placeholder ) ? __('e.g. S | M | L', 'radykal') : $nn_module_placeholder;

			$bulk_add_placeholder = fpd_get_option('fpd_plus_bulk_add_variations_written');
			$bulk_add_placeholder = empty( $bulk_add_placeholder ) ? __('e.g. Size=M|L;Colors=Blue|Red', 'radykal') : $bulk_add_placeholder;

			$options['plus-options'] = array(

				array(
					'title' 			=> __( 'Names & Numbers Dropdown', 'radykal' ),
					'id' 				=> 'plus_names_numbers_dropdown',
					'placeholder'		=> $nn_module_placeholder,
					'type' 				=> 'text',
				),

				array(
					'title' 			=> __( 'Names & Numbers Entry Price', 'radykal' ),
					'id' 				=> 'plus_names_numbers_entry_price',
					'placeholder'		=> fpd_get_option('fpd_plus_names_numbers_entry_price'),
					'type' 				=> 'number',
					'custom_attributes' => array(
						'min' => 0,
						'step' => 0.01
					)
				),

				array(
					'title' 			=> __( 'Color Selection Placement', 'radykal' ),
					'id' 				=> 'plus_color_selection_placement',
					'default'			=> '',
					'type' 				=> 'select',
					'class'				=> 'semantic-select',
					'allowclear'		=> true,
					'options'   		=> self::get_color_selection_placements()
				),

				array(
					'title' 			=> __( 'Color Selection Display Type', 'radykal' ),
					'id' 				=> 'plus_color_selection_display_type',
					'default'			=> '',
					'type' 				=> 'select',
					'class'				=> 'semantic-select',
					'allowclear'		=> true,
					'options'   		=> self::get_color_selection_displays()
				),

				array(
					'title' 			=> __( 'Bulk-Add Form Placement', 'radykal' ),
					'id' 				=> 'plus_bulk_add_form_placement',
					'default'			=> '',
					'type' 				=> 'select',
					'class'				=> 'semantic-select',
					'allowclear'		=> true,
					'options'   		=> self::get_bulk_add_form_placements()
				),

				array(
					'title' 			=> __( 'Bulk-Add Variations', 'radykal' ),
					'id' 				=> 'plus_bulk_add_variations_written',
					'placeholder'		=> $bulk_add_placeholder,
					'type' 				=> 'text',
				),

			);

			return $options;

		}

		public function add_shortcode_module_options() {

			echo '<option value="names-numbers">'. __('Names & Numbers', 'radykal') .'</option>';
			echo '<option value="drawing">'. __('Drawing', 'radykal') .'</option>';
			echo '<option value="dynamic-views">'. __('Dynamic Views', 'radykal') .'</option>';

		}

		public static function get_options() {

			return apply_filters('fpd_plus_settings', array(

				'modules' => array(

					array(
						'title' => __('Names & Numbers', 'radykal'),
						'type' => 'section-title',
						'id' => 'names-numbers-section'
					),

					array(
						'title' 		=> __( 'Dropdown Attributes', 'radykal' ),
						'description' 	=> __( 'Enter some attributes by "|" separating values. These attributes will be used in the Names&Numbers module.', 'radykal' ),
						'id' 			=> 'fpd_plus_names_numbers_dropdown',
						'css' 			=> 'width:500px;',
						'default'		=> '',
						'type' 			=> 'text'
					),

					array(
						'title' 		=> __( 'Entry Price', 'radykal' ),
						'description' 	=> __( 'The additional price for every entry in the Names&Numbers module.', 'radykal' ),
						'id' 			=> 'fpd_plus_names_numbers_entry_price',
						'css' 			=> 'width:70px;',
						'default'		=> 0,
						'type' 			=> 'number'
					),

					array(
						'title' => __('Dynamic Views', 'radykal'),
						'type' => 'section',
						'id' => 'dynamic-views-section'
					),

					array(
						'title' 		=> __( 'Unit Of Length', 'radykal' ),
						'id' 			=> 'fpd_plus_dynamic_views_unit',
						'css' 			=> 'width: 300px;',
						'default'		=> '',
						'type' 			=> 'select',
						'options' 		=> self::get_dynamic_views_units()
					),

					array(
						'title' => __( 'Predefined Formats', 'radykal' ),
						'description' 		=> __( 'Display some predefined formats that your customers can choose from.', 'radykal' ),
						'id' 		=> 'fpd_plus_dynamic_views_formats',
						'css' 		=> 'width:500px;',
						'default'	=> '',
						'type' 		=> 'values-group',
						'options'   => array(
							'width' => 'Width',
							'height' => 'Height'
						),
						'regexs' => array(
							'width' => '^-?\d+\.?\d*$',
							'height' => '^-?\d+\.?\d*$'
						)
					),


				), //modules

				'tools' => array(

					array(
						'title' 		=> __( 'Color Selection Placement', 'radykal' ),
						'description' 	=> __( 'In order to show specific elements in the Color Selection panel, you need to enable the relevant option "Show in Color Selection" in the Product Builder under the General tab. The options that start with "Inside" are only working for a single element with a color palette.', 'radykal' ),
						'id' 			=> 'fpd_plus_color_selection_placement',
						'css' 			=> 'width: 300px;',
						'default'		=> '',
						'type' 			=> 'select',
						'options' 		=> self::get_color_selection_placements()
					),

					array(
						'title' 		=> __( 'Color Selection Display Type', 'radykal' ),
						'description' 	=> __( 'Display the color selection items in a grid or dropdown. <b>Dropdown is only working when the placement is outside</b>. ', 'radykal' ),
						'id' 			=> 'fpd_plus_color_selection_display_type',
						'css' 			=> 'width: 300px;',
						'default'		=> 'grid',
						'type' 			=> 'select',
						'options' 		=> self::get_color_selection_displays()
					),

					array(
						'title' 		=> __( 'Bulk-Add Form Placement', 'radykal' ),
						'id' 			=> 'fpd_plus_bulk_add_form_placement',
						'css' 			=> 'width: 300px;',
						'default'		=> '',
						'type' 			=> 'select',
						'options' 		=> self::get_bulk_add_form_placements()
					),

					array(
						'title' 		=> __( 'Bulk-Add Variations', 'radykal' ),
						'description' 	=> __( 'You can define variations like that: Size=M|L;Colors=Blue|Red.', 'radykal' ),
						'id' 			=> 'fpd_plus_bulk_add_variations_written',
						'css' 			=> 'width: 100%;',
						'default'		=> '',
						'type' 			=> 'text',
					),

				), //tools

			));
		}

		public static function get_color_selection_placements() {

			$options = array(
				'none' => __( 'None', 'radykal' ),
				'inside-tl'	 => __( 'Inside Top/Left', 'radykal' ),
				'inside-tc'	 => __( 'Inside Top/Center', 'radykal' ),
				'inside-tr'	 => __( 'Inside Top/Right', 'radykal' ),
				'inside-bl'	 => __( 'Inside Bottom/Left', 'radykal' ),
				'inside-bc'	 => __( 'Inside Bottom/Center', 'radykal' ),
				'inside-br'	 => __( 'Inside Bottom/Right', 'radykal' ),
				'shortcode' => __( 'Via Shortcode [fpd_cs]', 'radykal' )
			);

			if( function_exists('get_woocommerce_currency') ) {
				$options['after-short-desc'] = __( 'After Short Description (WooCommerce)', 'radykal' );
			}

			return $options;

		}

		public static function get_color_selection_displays() {

			$options = array(
				'grid' => __( 'Grid', 'radykal' ),
				'dropdown'	 => __( 'Dropdown', 'radykal' ),
			);

			return $options;

		}

		public static function get_bulk_add_form_placements() {

			$options = array(
				'none' => __( 'None', 'radykal' ),
				'after-short-desc'	 => __( 'After Short Description (WooCommerce)', 'radykal' ),
				'shortcode' => __( 'Via Shortcode [fpd_bulk_add_form]', 'radykal' ),
			);

			if( function_exists('get_woocommerce_currency') ) {
				$options['after-short-desc'] = __( 'After Short Description (WooCommerce)', 'radykal' );
			}

			return $options;

		}

		public static function get_dynamic_views_units() {

			return array(
				'mm' => 'MM (Millimetre)',
				'cm' => 'CM (Centimetre)',
				'inch' => 'INCH'
			);

		}

		public static function get_all_product_attributes() {

			$attribute_taxonomies = wc_get_attribute_taxonomies();

		}
	}
}

new FPD_Plus_Settings();
?>