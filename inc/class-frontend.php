<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if(!class_exists('FPD_Plus_Frontend')) {

	class FPD_Plus_Frontend {

		private $color_selection_custom_placement = null;
		private $bulk_add_form_placement = '';

		public function __construct() {

			add_action( 'fpd_post_fpd_enabled', array( &$this, 'post_fpd_enabled' ), 20, 2 );
			add_action( 'fpd_before_js_fpd_init', array( &$this, 'before_js_fpd_init' ) );

			//CART
			add_filter( 'woocommerce_get_item_data', array(&$this, 'get_item_data'), 10, 2 );
			add_action( 'woocommerce_after_cart_item_name', array(&$this, 'after_cart_item_name'), 10, 2 );
			//bulk variations
			add_filter( 'woocommerce_is_sold_individually', array(&$this, 'disable_all_quantity_inputs'), 100, 2 );

			//ORDER
			add_action( 'woocommerce_order_item_meta_end', array(&$this, 'display_order_item_meta') , 10, 4 );


		}

		public function post_fpd_enabled( $post, $product_settings ) {

			$color_selection_placement = $product_settings->get_option('plus_color_selection_placement');
			if( $color_selection_placement === 'after-short-desc' ) {

				add_action( 'woocommerce_single_product_summary', array(&$this, 'cs_placement_output'), 25 );
				$this->color_selection_custom_placement = '#fpd-color-selection-placement';

			}
			else if( $color_selection_placement === 'shortcode' ) {

				add_shortcode( 'fpd_cs', array(&$this, 'cs_shortcode') );
				$this->color_selection_custom_placement = '#fpd-color-selection-placement';

			}
			else if( $color_selection_placement === 'none' ) {
				$this->color_selection_custom_placement = '';
			}

			$bulk_add_form_placement = $product_settings->get_option('plus_bulk_add_form_placement');
			if( $bulk_add_form_placement === 'after-short-desc' ) {

				add_action( 'woocommerce_single_product_summary', array(&$this, 'bulkd_add_form_placement_output'), 26 );
				$this->bulk_add_form_placement = '#fpd-bulk-add-form-placement';

			}
			else if( $bulk_add_form_placement === 'shortcode' ) {

				add_shortcode( 'fpd_bulk_add_form', array(&$this, 'bulk_add_form_shortcode') );
				$this->bulk_add_form_placement = '#fpd-bulk-add-form-placement';

			}

			if( !empty($this->bulk_add_form_placement) ) {
				add_action( 'fpd_product_designer_form_end', array( &$this, 'add_product_designer_form_fields' ) );
			}

		}

		public function cs_shortcode() {

			return $this->cs_placement_output(false);

		}

		public function bulk_add_form_shortcode() {

			return $this->bulkd_add_form_placement_output(false);

		}

		public function cs_placement_output( $echo=true ) {

			if( $echo || $echo === '' )
				echo '<div id="fpd-color-selection-placement"></div>';
			else
				return '<div id="fpd-color-selection-placement"></div>';

		}

		public function bulkd_add_form_placement_output( $echo=true ) {

			if( $echo || $echo === '' )
				echo '<div id="fpd-bulk-add-form-placement"></div>';
			else
				return '<div id="fpd-bulk-add-form-placement"></div>';

		}

		public function before_js_fpd_init( $product_settings ) {


			$namesNumbersDropdownAttr = preg_replace('/\s+/', '', $product_settings->get_option('plus_names_numbers_dropdown'));

			if(	empty($namesNumbersDropdownAttr) ) {
				$namesNumbersDropdownAttr = "[]";
			}
			else {
				$namesNumbersDropdownAttr = explode('|', $namesNumbersDropdownAttr);
				$namesNumbersDropdownAttr = json_encode($namesNumbersDropdownAttr);
			}

			$this->color_selection_custom_placement = is_null($this->color_selection_custom_placement) ? $product_settings->get_option('plus_color_selection_placement') : $this->color_selection_custom_placement;

			$variations = '';
			$variations_written = $product_settings->get_option('plus_bulk_add_variations_written');
			if( !empty($variations_written) ) {

				$bulkVariationsWritten = $product_settings->get_option('plus_bulk_add_variations_written');
				$attributes = explode(';', $bulkVariationsWritten);
				foreach($attributes as $attribute) {

					$attr_props = explode('=', $attribute);
					$variations .= '"'.$attr_props[0].'":';
					$terms = explode('|', $attr_props[1]);
					$variations .= json_encode($terms).',';

				}

				$variations = trim($variations, ',');

			}

			//dynamic views: formats
			$final_formats = array();
			$formats = $product_settings->get_option('plus_dynamic_views_formats');
			if( !empty($formats) ) {
				$formats = explode(',', $formats);
				foreach($formats as $format) {
					$final_formats[] = array_map('intval', explode(':', $format) );
				}

				$formats = is_array($final_formats) ? $final_formats : array();
			}

			?>

			var plusOptions = {
				namesNumbersDropdown: <?php echo $namesNumbersDropdownAttr; ?>,
				namesNumbersEntryPrice: <?php echo $product_settings->get_option('plus_names_numbers_entry_price'); ?>,
				colorSelectionPlacement: "<?php echo $this->color_selection_custom_placement; ?>",
				colorSelectionDisplayType: "<?php echo $product_settings->get_option('plus_color_selection_display_type'); ?>",
				bulkVariationsPlacement: "<?php echo $this->bulk_add_form_placement; ?>",
				bulkVariations: {<?php echo $variations; ?>},
				dynamicViewsOptions: {
					unit: "<?php echo $product_settings->get_option('plus_dynamic_views_unit'); ?>",
					formats: <?php echo json_encode($final_formats); ?>
				}
			};

			pluginOptions = jQuery.extend({}, pluginOptions, plusOptions);

			<?php

		}

		public function add_product_designer_form_fields() {

			?>
			<input type="hidden" value="" name="fpd_bulk_variations_order" />
			<input type="hidden" value="" name="fpd_quantity" />
			<?php

		}

		//meta data displayed after the title, key: value
	    public function get_item_data( $other_data, $cart_item ) {

		    if ( isset($cart_item['fpd_data']) && fpd_get_option('fpd_cart_show_element_props') !== 'none' ) {

				//get fpd data
				$fpd_data = $cart_item['fpd_data'];

				if( isset($fpd_data['fpd_bulk_variations_order']) && $fpd_data['fpd_bulk_variations_order'] ) {

					$bulk_variations = json_decode(stripslashes($fpd_data['fpd_bulk_variations_order']), true);
					if( !empty($bulk_variations) ) {

						foreach($bulk_variations as $variation) {

							array_push($other_data, array(
								'name' => $this->create_variation_string($variation['variation']),
								'value' => $variation['quantity']
							));

						}

					}
				}

			}

		    return $other_data;

	    }

	    public function after_cart_item_name( $cart_item, $cart_item_key ) {

			if ( isset($cart_item['fpd_data']) ) {

				$fpd_data = $cart_item['fpd_data'];

				if( isset($fpd_data['fpd_product']) ) {

					$fpd_product = json_decode(stripslashes($fpd_data['fpd_product']), true);
					$views = $fpd_product['product'];

					self::display_names_numbers_items($views);

				}

			}

	    }

		//hide wc quantity on single product pages, if bulk form is added
	    public function disable_all_quantity_inputs( $return, $product ) {

		    if( is_product() && is_fancy_product($product->get_ID()) ) {

			    $product_settings = new FPD_Product_Settings( $product->get_id() );
			    $bulk_form_placement = $product_settings->get_option('plus_bulk_add_form_placement');

			    if( $bulk_form_placement === 'after-short-desc' || $bulk_form_placement === 'shortcode' )
			    	return true;

		    }

		    return $return;

		}

		public function display_order_item_meta( $item_id, $item, $order, $plain_text=false ) {

			if( $plain_text )
				return;

			$product = $item->get_product();

			if( is_bool($product) )
				return;

			//show element props
			if( isset($item['_fpd_data']) ) {

				$order = json_decode(stripslashes($item['_fpd_data']), true);

				if( isset($order['bulkVariations']) && sizeof($order['bulkVariations']) > 0 ) {

					$bulk_variations = $order['bulkVariations'];

					echo '<div style="margin: 10px 0;" class=""fpd-plus-order-bulk-variations>';

					foreach($bulk_variations as $variation) {

						echo '<span style="margin-right: 10px;">'.$this->create_variation_string($variation['variation']).': <strong>'.$variation['quantity'].'</strong></span>';


					}

					echo '</div>';

				}

				self::display_names_numbers_items($order['product']);

			}

		}

		public static function display_names_numbers_items( $views ) {

			echo '<div class="fpd-table-item-names-numbers">';

			foreach($views as $view) {

				if( isset($view['names_numbers']) ) {

					$names_numbers = $view['names_numbers'];
					foreach($names_numbers as $name_number) {

						$nn_line = array();

						if( isset($name_number['name']) )
							$nn_line[] = $name_number['name'];

						if( isset($name_number['number']) )
							$nn_line[] = $name_number['number'];

						if( isset($name_number['select']) )
							$nn_line[] = $name_number['select'];

						echo '<div>'. implode(' / ', $nn_line) .'</div>';

		    		}
		    	}
		    }

		    echo '</div>';
		    ?>
		    <style type="text/css">
			    .fpd-table-item-names-numbers {
				    font-size: 12px;
			    }

		    </style>
		    <?php

		}

		private function create_variation_string( $variations ) {

			$final_str = '';

			if( is_array($variations) ) {

				foreach($variations as $key => $value) {
					$final_str .= $key . '=' . $value . ', ';
				}

				$final_str = substr_replace($final_str, '', -2); //remove last 2 chars

			}

			return $final_str;

		}

	}

}

new FPD_Plus_Frontend();

?>