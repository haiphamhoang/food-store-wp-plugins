<?php
/**
 * FoodStore Order Functions
 *
 * Functions for order specific things.
 *
 * @package FoodStore\Functions
 * @version 1.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get all order statuses.
 *
 * @since 2.2
 * @return array
 */
function wfs_get_order_statuses() {
	$order_statuses = array(
    'pending'     => __( 'Pending', 'food-store' ),
    'accepted'    => __( 'Accepted', 'food-store' ),
    'processing'  => __( 'Processing', 'food-store' ),
    'ready' 	  => __( 'Ready', 'food-store' ),
    'transit' 	  => __( 'In Transit', 'food-store' ),
    'cancelled'   => __( 'Cancelled', 'food-store' ),
    'completed'   => __( 'Completed', 'food-store' ),
	);
	return apply_filters( 'wfs_order_statuses', $order_statuses );
}


