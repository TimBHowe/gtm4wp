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
 *
 * @param int   $download_id The download ID being added to the cart.
 * @param array $options The options for the item being added including but not limited to quantity.
 * @param array $items The items added to the cart.
 */
function gtm4wp_edd_post_add_to_cart( $download_id, $options, $items ) {

	// $coupon      = edd_get_cart_discounts();

	// Loop through the items and build the items array for the event.
	$event_items = array();
	foreach ( $items as $item ) {

		// Get the items basic information.
		$item_id       = $item['id'];
		$item_options  = $item['options'];
		$item_price_id = edd_get_cart_item_price_id( $item );
		$event_item    = array(
			'item_id'        => edd_get_download_sku( $item_id ),
			'item_name'      => edd_get_download_name( $item_id, $item_price_id ),
			'item_variant'   => edd_get_cart_item_price_name( $item ),
			'price'          => edd_get_cart_item_price( $item_id, $item_options ),
			'quantity'       => edd_get_cart_item_quantity( $item_id, $item_options ),
			'item_list_id'   => 'TOOD',
			'item_list_name' => 'TOOD',
		);

		// Get the items category information.
		$item_cats  = get_the_terms( $download_id, 'download_category' );
		$item_cats  = array_slice( wp_list_pluck( $item_cats, 'name' ), 0, 5 );
		$item_cats = array(
			'item_category'  => $item_cats[0],
			'item_category2' => $item_cats[1],
			'item_category3' => $item_cats[2],
			'item_category4' => $item_cats[3],
			'item_category5' => $item_cats[4],
		);
		$item_cats = array_filter($item_cats);
		$event_item = array_merge( $event_item, $item_cats);

		$event_items[] = $event_item;
	}

	// https://developers.google.com/analytics/devguides/collection/ga4/reference/events?client_type=gtm#add_to_cart.
	$event = array(
		'event'     => 'add_to_cart',
		'ecommerce' =>
		array(
			'currency' => edd_get_currency(),
			'value'    => 'TODO',
			'items'    => $event_items,
		),
	);
	echo '<pre>';
	var_dump( $item_cats );
	var_dump( $options );
	var_dump( $items );
	var_dump( $event );

	// edd_get_cart_item_discount_amount();
	// edd_get_item_position_in_cart( $download_id, $options );
	// edd_get_cart_item_price( $download_id, $options );
	// edd_get_price_name( $download_id, $options );

	edd_empty_cart();
	exit;
}
add_action( 'edd_post_add_to_cart', 'gtm4wp_edd_post_add_to_cart', 10, 3 );
