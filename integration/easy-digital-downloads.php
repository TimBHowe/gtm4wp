<?php
/**
 * GTM4WP Easy Digital Downloads integration.
 *
 * @package GTM4WP
 * @author Thomas Geiger
 * @copyright 2013- Geiger Tamás e.v. (Thomas Geiger s.e.)
 * @license GNU General Public License, version 3
 */

/**
 * Get the information when a product is added to the cart
 * https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtag#add_or_remove_an_item_from_a_shopping_cart
 *
 * TODO: Add affiliation. Tells any affiliate with the product.
 * TODO: Add index. Tells the position of the item in the cart.
 * TODO: Add item_list_id and item_list_name. Tells there the item was displayed and the ad d to cart.
 * TODO: Add location_id. Tells the location where this was purchased from. Might be unnecessary for online retail, but could be the company headquarters.
 *
 * @param int   $download_id The download ID being added to the cart.
 * @param array $options The options for the item being added including but not limited to quantity.
 * @param array $items The items added to the cart.
 */
function gtm4wp_edd_post_add_to_cart( $download_id, $options, $items ) {

	$coupons = edd_get_cart_discounts();

	// Loop through the items and build the items array for the event.
	$event_items = array();
	$event_value = 0;
	foreach ( $items as $item ) {

		// Get the items basic information.
		$item_id       = $item['id'];
		$item_options  = $item['options'];
		$item_price_id = edd_get_cart_item_price_id( $item );
		$event_item    = array(
			'item_id'      => edd_get_download_sku( $item_id ),
			'item_name'    => edd_get_download_name( $item_id, $item_price_id ),
			'item_variant' => edd_get_cart_item_price_name( $item ),
			'price'        => edd_get_cart_item_price( $item_id, $item_options ),
			'quantity'     => edd_get_cart_item_quantity( $item_id, $item_options ),
			'coupon'       => $coupons ? implode( ',', $coupons ) : '',
			'discount'     => $coupons ? edd_get_item_discount_amount( $item, $items, $coupons ) : '',
		);

		// Collect and calculate the total event value.
		$event_value = $event_value + edd_get_cart_item_price( $item_id, $item_options ) - edd_get_item_discount_amount( $item, $items, $coupons );

		// Get the items category information.
		$item_cats  = get_the_terms( $download_id, 'download_category' );
		$item_cats  = array_slice( wp_list_pluck( $item_cats, 'name' ), 0, 5 );
		$item_cats  = array(
			'item_category'  => $item_cats[0],
			'item_category2' => $item_cats[1],
			'item_category3' => $item_cats[2],
			'item_category4' => $item_cats[3],
			'item_category5' => $item_cats[4],
		);
		$item_cats  = array_filter( $item_cats );
		$event_item = array_merge( $event_item, $item_cats );

		$event_items[] = $event_item;
	}

	// Finish prepping the event.
	// https://developers.google.com/analytics/devguides/collection/ga4/reference/events?client_type=gtm#add_to_cart.
	$event = array(
		'event'     => 'add_to_cart',
		'ecommerce' =>
		array(
			'currency' => edd_get_currency(),
			'value'    => $event_value,
			'items'    => $event_items,
		),
	);

	if ( ! edd_is_ajax_disabled() ) {

	}

	// TODO: pass to the datalayer.
	//gtm4wp_edd_send_event( $event );

	// TODO: Remove after testing.
	edd_empty_cart();
	// exit;
}
add_action( 'edd_post_add_to_cart', 'gtm4wp_edd_post_add_to_cart', 10, 3 );


/**
 * Adds item to the cart via AJAX.
 *
 * @since 1.0
 * @return void
 */
// function edd_ajax_add_to_cart() {}

// function gtm4wp_edd_send_event( $event ) {}



// echo '<pre>';
// var_dump( $download_id );
// var_dump( $options );
// var_dump( $items );
// var_dump( $event );

// edd_get_cart_item_discount_amount();
// edd_get_item_position_in_cart( $download_id, $options );
// edd_get_cart_item_price( $download_id, $options );
// edd_get_price_name( $download_id, $options );

// edd_empty_cart();
// exit;
