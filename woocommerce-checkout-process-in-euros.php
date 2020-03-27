<?php
/**
 * Plugin Name: Woocommerce Checkout Process in Euros
 * Plugin URI: http://woodemia.com
 * Description: This is a WooCommerce Multi Currency Premium addon that shows the total equivalent price of order in euros on the checkout page.
 * Version: 1.0.0
 * Author: Woodemia
 * Author URI: http://woodemia.com
 * Text-domain: wccpe
 * Tested up to: 5.3
 * WC requires at least: 3.2.0
 * WC tested up to: 4.0.1
 */
if ( in_array( 'woocommerce-multi-currency/woocommerce-multi-currency.php', get_option( 'active_plugins' ) ) ) {
	add_action( 'woocommerce_review_order_after_order_total', 'show_order_price_in_the_selected_currency' );
	function show_order_price_in_the_selected_currency() {
		$wcmc_data = WOOMULTI_CURRENCY_Data::get_ins();

		if ( 'EUR' == $wcmc_data->get_current_currency() ) return;
		
		?>
		<tr class="order-current-currency-total" style="background-color: #F7A8A8; color: #F05E60;">
			<th style="padding-left: 10px;">
				<?php
				esc_html_e( 'Total en €', 'woocommerce' ); 
				?>
			</th>
			<td><?php echo do_shortcode( '[woo_multi_currency_exchange price='. get_equivalent_price_in_euros( WC()->cart->get_total( 'float' ) ) .' currency="EUR"]' ); ?></td>
		</tr>
		<?php
	}

	add_action( 'woocommerce_checkout_process', 'set_currency_to_default_shop_currency', 99 );
	function set_currency_to_default_shop_currency() {
		$wcmc_data = WOOMULTI_CURRENCY_Data::get_ins();
		$wcmc_data->set_current_currency( 'EUR' );
	}

	function get_equivalent_price_in_euros( $price ) {
		$setting        = WOOMULTI_CURRENCY_Data::get_ins();
		$currency_code 	= 'EUR'; 

		if ( isset( $price ) ) {
			$price = str_replace( ',', '.', $price );
		}

		/*Check currency*/
		$selected_currencies = $setting->get_list_currencies();
		$current_currency    = $setting->get_current_currency();

		if ( ! $current_currency || $current_currency == $currency_code ) {
			return $price;
		}

		if ( $price && $currency_code && isset( $selected_currencies[ $currency_code ] ) ) {
			$price = $price / $selected_currencies[ $current_currency ]['rate'];
		}

		return $price;
	}
}