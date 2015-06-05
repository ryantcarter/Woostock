<?php

/*
Plugin Name: WooStock for WooCommerce Inventory
Plugin URI: http://www.woostock.com/
Description: This plugin displays your stock list on a page of your website; perfect for dropshippers to give access to upto date stock lists.
Version: 0.1
Author: WOO
Author URI: http://www.woostock.com

*/

// Check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	// Localization
	load_plugin_textdomain( 'woostock', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	// Class
	if ( ! class_exists( 'WC_woostock' ) ) {
		class WC_woostock {
			public function __construct() {
				
				$this->woostock_enabled = get_option( 'woostock_enable' ) == 'yes' ? true : false;
				
				// Let woocommerce finish loading
				add_action( 'init', array( $this, 'plugin_init' ) );
				
				// Init settings
				$this->settings = array(
					array(
						'name' => __( 'WooStock Options', 'woostock' ),
						'type' => 'title',
						'id' => 'woostock_options'
					),
					array(
						'name' => __( 'Enable', 'woostock' ),
						'desc' => __( 'Enable Frontend Stocklist', 'woostock' ),
						'id' => 'woostock_enable',
						'type' => 'checkbox'
					),
					array(
						'name'     => __( 'Error Message', 'woostock' ),
						'desc_tip' => __( 'Insert error message for non-logged in users', 'woostock' ),
						'id'       => 'woostock_error_message',
						'type'     => 'textarea',
						'css'      => 'min-width:500px;',
						'desc'     => __( 'Message:', 'woostock' ),
					),
					array(
						'type' => 'sectionend',
						'id' => 'woostock_options'
					),
				);
				
				// Default options
				add_option( 'woostock_enable', 'yes' );
				add_option( 'woostock_error_message', 'Sorry, you do not have sufficient access to this page. ' );
				
				// My Filter
	
				// Admin
				add_action( 'woocommerce_settings_image_options_after', array( $this, 'admin_settings' ), 20);
				add_action( 'woocommerce_update_woostock_options', array( $this, 'update_woocommerce_term_meta' ) );
				
			}			function plugin_init() {
				if ( $this->woostock_enabled ) {
					function get_inventory() {
						$options = get_option('woostock_options');
						$out = (!isset($options['errormessage_template']) || $options['errormessage_template']=="") ? 'Sorry, you do not have sufficient access to this page.' : $options['errormessage_template'];
						$out = get_option('woostock_error_message', 'Sorry, you do not have sufficient access to this page.' );
						$user = wp_get_current_user();
						if ( empty( $user->ID ) ) {
								echo $out;
						}
						else {
							global $woocommerce;
							
							

?> 


							<h2>Variance Products</h2>
							<table width="100%" style="border: 1px solid #000; width: 100%;" cellspacing="0" cellpadding="2">
								<thead ><th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('Image', 'woothemes'); ?></th>
										<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('Variant', 'woothemes'); ?></th>
										<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('Product Description', 'woothemes'); ?></th>
										<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('SKU', 'woothemes'); ?></th>
                                        <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('RRP', 'woothemes'); ?></th>
                                         <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('Sale Price', 'woothemes'); ?></th>
										<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('STOCK', 'woothemes'); ?></th>
									</tr>
								</thead>
								<tbody>
							
	<?php
                                $args = array(
                                           'post_type'         => 'product_variation',
                                           'post_status'       => 'publish',
                                           'posts_per_page'    => -1,
                                           'orderby'           => '_sku',
                                           'order'             => 'DESC',
                                           'meta_query'        => array(
                                           array(
                                           'key'   => '_stock',
                                           'value' => array('', false, null),
                                           'compare' => 'NOT IN'
                                                                       )
                                                       )
                                            );
                  //	Loop Product Variation 
								
								
								$loop = new WP_Query( $args);
								while ( $loop->have_posts() ) : $loop->the_post();
									$product = new WC_Product_Variation( $loop->post->ID );
									
				


			
?>
									<tr>
                               			<td  class="thumb column-thumb" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->get_image( $size ='shop_thumbnail' ); ?></td>

										<td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->get_title(); ?></td>
					<td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo get_the_title( $loop->post->post_parent ); ?></td>
										<td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->sku; ?></td>
                                        <td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->regular_price; ?></td>
                                        <td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->sale_price; ?></td>

						<td style="text-align:left; border: 1px solid #000; padding: 6px; "><?php echo $product->stock; ?></td>
									</tr>
                                   
								<?php
								endwhile;								

								?>
								</tbody>
							</table>
                            <style > 
							.hentry img { height: auto; max-width: 35%;}
                             </style>
                            
                            <h2>Simple Products</h2>
                            
                            <table width="100%" style="border: 1px solid #000; width: 100%;" cellspacing="0" cellpadding="2">
								<thead>
									<tr>
                                    <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('Image', 'woothemes'); ?></th>
							<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('Product Description', 'woothemes'); ?></th>
										<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('SKU', 'woothemes'); ?></th>
                                       
<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('RRP', 'woothemes'); ?></th>

 <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('Sale Price', 'woothemes'); ?></th>


<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('Stock', 'woothemes'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$args = array(
									'post_type'         => 'product',
									'post_status'       => 'publish',
									'posts_per_page'    => -1,
									'orderby'           => 'title',
									'order'             => 'ASC',
									'meta_query'        => array(
																array(
																	'key'   => '_manage_stock',
																	'value' => 'yes'
																)
															),
									'tax_query'         => array(
																array(
																	'taxonomy'  => 'product_type',
																	'field'     => 'slug',
																	'terms'     => array('simple'),
																	'operator'  => 'IN'
																)
															)
									);
									$loop = new WP_Query( $args );
									while ( $loop->have_posts() ) : $loop->the_post();
									global $product;
									?>
										<tr>
                                        <td  class="thumb column-thumb" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->get_image( $size ='shop_thumbnail' ); ?></td>
											<td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->get_title(); ?></td>
											<td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->sku; ?></td>											<td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->price; ?></td>
										
<td style="text-align:left; border: 1px solid #000; padding: 6px;"></td>	

<td style="text-align:left; border: 1px solid #000; padding: 6px;"><?php echo $product->stock; ?></td>
										</tr>
									<?php
									endwhile;
									?>
								</tbody>
							</table>
					<?php	
						   }
					}
					add_shortcode( 'woostock', 'get_inventory','manage_inventory' );
				}
			}
			
			// Load the settings
			function admin_settings() {
				woocommerce_admin_fields( $this->settings );
			}

			// Save the settings
			function woocommerce_update_option() {
				woocommerce_update_options( $this->settings );
			}
						
			//Add settings link to plugin page
			public function add_settings_link( $links ) {
				$settings = sprintf( '<a href="%s" title="%s">%s</a>' , admin_url( 'admin.php?page=woocommerce&tab=' . $this->settings->tab_name ) , __( 'Go to the settings page', 'woocommerce-delivery-notes' ) , __( 'Settings', 'woocommerce-delivery-notes' ) );
				array_unshift( $links, $settings );
				return $links;	
			}
			
		}
		// Instantiate our plugin class and add it to the set of globals
		$GLOBALS['wc_woostock'] = new WC_woostock();
	}
	
} else {
	function check_woo_notices() {
		if (!is_plugin_active('woocommerce/woocommerce.php')) {
			ob_start();
			?><div class="error">
			<p><strong>Sorry!</strong>: You do not have access to this page.</p>
			</div><?php
			echo ob_get_clean();
		}
	}
	add_action('admin_notices', 'check_woo_notices');
}

?>
