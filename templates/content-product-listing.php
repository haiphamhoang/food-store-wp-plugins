<?php
/**
 * The template for displaying product listings
 *
 * This template can be overridden by copying it to yourtheme/food-store
 *
 * @package FoodStore/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


$exclude_categories = wfs_get_exclude_categories();

$category_args = array(
  'taxonomy'    => 'product_cat',
  'hide_empty'  => true,
  'include'     => $category_ids,
  'exclude'     => $exclude_categories,
);

$category_args = apply_filters( 'wfs_categories',  $category_args );
$get_all_categories = get_terms( $category_args );
$posts_per_page = isset( $shortcode_args['items_per_category'] ) ? $shortcode_args['items_per_category'] : -1;

$orderby = isset( $shortcode_args['orderby'] ) ? $shortcode_args['orderby'] : 'id';
$order = isset( $shortcode_args['order'] ) ? $shortcode_args['order'] : 'ASC';
$product_service_type = isset( $shortcode_args['product_service_type'] ) ? $shortcode_args['product_service_type'] : '';


switch ( $orderby ) {
  
  case 'id':
	  $orderby = 'ID';
		break;

	case 'menu_order':
	  $orderby = 'menu_order title';
		break;
  
	case 'title':
	  $orderby = 'title';
		break;

	case 'relevance':
	  $orderby = 'relevance';
		break;

	case 'date':
	  $orderby = 'date ID';
		break;

	}

if ( isset( $shortcode_args['items_per_category'] ) ) {
  $items = $shortcode_args['items_per_category'];
}


if ( is_array( $get_all_categories ) ) {

  foreach( $get_all_categories as $wfs_category ) {
    
    $term_id = $wfs_category->term_id;

    $args = array(
      'post_type'       => 'product',
      'post_status'     => 'publish',
      'posts_per_page'  => $posts_per_page,
      'orderby'         => $orderby,
      'order'           => $order,
    );

    $args['tax_query'] = array(
      'relation'    => 'AND',
      array(
        'taxonomy'  => 'product_visibility',
        'terms'     => array('exclude-from-catalog'),
        'field'     => 'name',
        'operator'  => 'NOT IN',
      ),
      array(
        'taxonomy'  => 'product_cat',
        'field'     => 'term_id',
        'terms'     => array( $term_id ) ,
      ),
    );

    if ( !empty( $product_service_type ) ) {
      $args['meta_query'] = array(
        'relation'  => 'AND',
        array(
          'key'   => '_wfs_product_service_type',
          'value' => $product_service_type,
        ),
      );
    }
      
    $query = apply_filters( 'wfs_get_products_args', $args, $term_id );
    
    $wfs_products = new WP_Query( $query );

    if ( $wfs_products->have_posts() ) :

      wfs_listing_start( $echo = true, $term_id );

      // Get template column options 
      $columns = get_option( '_wfs_listing_column_count', '1' );
      switch ($columns) {
        case '2':
          $template_classes = 'fs-col-lg-6 fs-col-md-6 fs-col-sm-12 fs-col-xs-12 fs-2-columns';
          break;

        default:
          $template_classes = 'fs-col-lg-12 fs-col-md-12 fs-col-sm-12 fs-col-xs-12';
          break;
      }

      echo '<div class="fs-row">';

      $element = 1;

      while ( $wfs_products->have_posts() ) : $wfs_products->the_post();

        $product = wc_get_product( get_the_ID() );

        echo '<div class="'.apply_filters( 'wfs_template_columns', $template_classes, $columns ).'">';

        wfs_get_template(
          'content-listing-details.php',
          array(
            'product' => $product,
            'term_id' => $term_id,
          )
        );

        echo '</div>';

        if( $element % $columns  == 0 ) {
          echo '</div>';
          echo '<div class="fs-row">';
        }

        $element++;

      endwhile;

      echo '</div>';

      wfs_listing_end( $echo = true, $term_id );

      wp_reset_postdata();

    endif;
  }
}