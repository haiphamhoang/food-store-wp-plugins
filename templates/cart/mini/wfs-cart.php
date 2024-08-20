<?php
/**
 * The template for displaying product cart
 *
 * This template can be overridden by copying it to yourtheme/food-store
 *
 * @package FoodStore/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

global $woocommerce;

$items = $woocommerce->cart->get_cart();

$cart_count = count( $items );
$cart_class = $cart_count ? 'content' : 'empty';

// Cart display option
$hide_cart = get_option( '_wfs_listing_hide_cart_area', 'no' );

//Render cart style
$cart_style = wfs_get_cart_style();

?>

<?php if( 'no' == $hide_cart ) : ?>

  <!-- Fade body when the cart is expanded -->
  <div class="wfs-body-fade"></div>

  <!-- cart icon starts-->
  <a href="#" class="wfs-mini-cart-icon">    
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-fill" viewBox="0 0 16 16">
      <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5z"/>
    </svg>
    <span class="wfs-mini-cart-count"><?php echo $cart_count; ?></span>
  </a>
  <!-- cart icon ends-->
  
  <!-- Complete Cart View -->
  <div class="wfs-cart-expanded active <?php echo $cart_class; ?>">
    <div class="fs-container">

      <?php if ( $cart_count ) : ?>
        
        <div class="wfs-cart-content-area">
          
          <div class="cart-content-left">
            <?php wfs_get_template( "cart/{$cart_style}/cart-contents.php" ); //cart with items ?>
          </div>
          
          <div class="cart-content-right">
            <?php wfs_get_template( "cart/{$cart_style}/cart-totals.php" ); ?>
          </div>

        </div>

      <?php else: ?>

        <?php wfs_get_template( "cart/{$cart_style}/empty-cart.php" ); //empty cart ?>

      <?php endif; ?>

    </div>

    <!-- Cart Overview Area Starts-->
  <div class="wfs-cart-overview">
    <div class="fs-container">
      <div class="wfs-cart-overview-row">
        <?php if ( wfs_is_service_enabled() ) : ?>
          <?php echo wfs_service_time(); ?>
        <?php endif; ?>

        <div class="fs-text-left wfs-cart-purchase-actions">
          <span class="wfs-cart-subtotal"><?php echo __( 'Total:&nbsp;', 'food-store' ); ?><?php wc_cart_totals_order_total_html(); ?></span>
          
          <?php if( $cart_count > 0 ) : ?>
            <button class="fs-btn-md fs-btn-secondary wfs-clear-cart">
              <?php echo wfs_empty_cart(); ?>
            </button>
          <?php endif; ?>
          
          <?php if( apply_filters( 'wfs_allow_proceed_to_checkout', true ) ) : ?>
            <button class="fs-btn-md fs-btn-primary wfs-proceed-to-checkout">
              <?php echo __( 'Continue' , 'food-store' ); ?>
            </button>
          <?php endif; ?>
          <?php apply_filters( 'wfs_after_proceed_to_checkout', '' ); ?>
        </div>
      </div>
    </div>
  </div>
  <!-- Cart Overview Area Ends-->

  </div>

  

<?php endif; ?>