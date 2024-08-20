<?php
/**
 * FoodStore Order related functions and actions.
 *
 * @package FoodStore/Classes
 * @since   1.4.1
 */

defined( 'ABSPATH' ) || exit;

class WFS_Order {

  /**
	 * Stores data about status changes so relevant hooks can be fired.
	 *
	 * @var bool|array
	 */
	protected $status_transition = false;

    /** 
     * Constructor of Order Class
     */
    public function __construct() {

      add_filter( 'manage_edit-shop_order_columns', array( $this , 'wfs_order_status_column' ), 1 );
      add_action( 'manage_shop_order_posts_custom_column' , array( $this , 'wfs_food_status_column' ), 99, 2 );
      add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'wfs_editable_wfs_order_status' ), 99 );
      add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'wfs_search_order_column'), 10 );
      add_action( 'woocommerce_thankyou', array( $this, 'wfs_order_placed' ), 10, 1 );
      add_action( 'save_post', array( $this, 'wfs_update_order_status' ), 10, 1 );
      add_action( 'wfs_order_status_changed', array( $this, 'wfs_trigger_transaction_email'), 10, 3 );
      add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'wfs_order_placed_again'), 99, 3 );
      add_action( 'woocommerce_checkout_process', array( $this, 'wfs_before_checkout_process' ), 10 );
    }

    /**
     * Setup Order Status columns for Order Listing Page
     *
     * @author WP Scripts
     * @since 1.4
     * @param array $columns
     *
     * @return array $columns
     */
    public function wfs_order_status_column( $columns ) {

      $reordered_columns = array();

      foreach( $columns as $key => $column ){
        $reordered_columns[$key] = $column;
        if( $key == 'order_status' ){
          // Inserting after "Status" column
          $reordered_columns['order_status'] = __( 'Payment Status','food-store' );
          $reordered_columns['food_status'] =  __( 'Order Status','food-store');
        }
      }
      return $reordered_columns;
    }

    /**
     * Get food order status columns for Order Listing Page
     *
     * @author WP Scripts
     * @since 1.4
     * @param array $column
     * @param int $post_id
     *
     * @return array $columns
     */
    public function wfs_food_status_column( $column, $post_id ) {
      
      if( 'food_status' == $column ) {
        $wfs_order_status = get_post_meta( $post_id, '_wfs_order_status', true );
        $wfs_order_status = empty( $wfs_order_status ) ? __( 'pending','food-store' ) : $wfs_order_status;
        echo '<mark class="order-status food-order-status status-'.strtolower( $wfs_order_status ).' "><span>'.$this->wfs_get_order_prop( $wfs_order_status ).'</span></mark>';
      }
      
    }


    /**
     * Food Order Status editable in order edit section
     *
     * @author WP Scripts
     * @since 1.4
     * @param object $order
     *
     * @return array $mixed
     */
    public function wfs_editable_wfs_order_status( $order ) {
      ?>
      <p class="form-field form-field-wide wc-food-order-status">
        <label for="wfs_order_status">
        <?php _e( 'Order status', 'food-store' ); ?>:
      </label>
      <select id="wfs_order_status" name="wfs_order_status" class="wc-enhanced-select">
			<?php
			  $order_statuses = wfs_get_order_statuses();
        $wfs_order_status = get_post_meta( $order->get_id(), '_wfs_order_status', true );
        $wfs_order_status = empty( $wfs_order_status ) ? 'pending' : $wfs_order_status;
				foreach ( $order_statuses as $status => $status_name ) {
				  echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status, $wfs_order_status , false ) . '>' . esc_html( $status_name ) . '</option>';
				}
			?>
			</select>
      </p>
      <?php
    }


    /**
     * Get order status label based on order meta
     *
     * @author WP Scripts
     * @since 1.4
     * @param string $wfs_order_status
     *
     * @return string
     */
    protected function wfs_get_order_prop( $prop ) {
      
      $value = null;
      $order_statuses = wfs_get_order_statuses();

      if ( array_key_exists( $prop, $order_statuses ) ) {
        $value = $order_statuses[ $prop ];
		  }

		  return $value;
    }

    /**
     * Seach order based on food order status
     *
     * @author WP Scripts
     * @since 1.4
     *
     * @return array
     */
    public function wfs_search_order_column( $search_fields ) {
      
      $search_fields[] = '_wfs_order_status';
      return $search_fields;

    }

    /**
     * Update food order status on order placed
     *
     * @author WP Scripts
     * @since 1.4
     *
     * @return bool
     */
    public function wfs_order_placed( $order_id ) {

      if ( ! $order_id ) {
        return false;
      }

      update_post_meta( $order_id, '_wfs_order_status', 'pending' );
      
    }

    /**
     * Update food order status when admin changes order status
     *
     * @author WP Scripts
     * @since 1.4.1
     *
     * @return bool
     */
    public function wfs_update_order_status( $post_id ) {
      global $post;
      
      // Check if post is an order
      if ( isset( $post->post_type ) && $post->post_type != 'shop_order' ) {
        return;
      }
      
      $order_previous_status = get_post_meta( $post_id, '_wfs_order_status', true );
      $order_next_status     = isset( $_POST['wfs_order_status'] ) ? wc_clean( $_POST['wfs_order_status'] ) : '';
      
      do_action( 'wfs_order_status_' . $order_previous_status . '_to_' . $order_next_status,  $post_id );
      do_action( 'wfs_order_status_changed', $post_id, $order_previous_status, $order_next_status );

      update_post_meta( $post_id, '_wfs_order_status', $order_next_status );
    }

    /**
     * Trigger transaction email when order status changes
     *
     * @author WP Scripts
     * @since 1.4.1
     *
     * @return mixed
     */
    public function wfs_trigger_transaction_email( $order_id, $previous_status, $next_status ) {
      
      $send_email_on_order_updates = get_option( '_wfs_send_email_on_order_update', true );

      if ( $send_email_on_order_updates != 'yes' ) {
        return;
      }

      $current_status = get_post_meta( $order_id, '_wfs_order_status', true );
      
      //bail out if both the order status are same
      if ( $current_status == $next_status ) {
        return;
      }

      global $woocommerce;
      $order = new WC_Order( $order_id );
      $user_id = get_post_meta( $order_id, '_customer_user', true );

      // Get an instance of the WC_Customer Object from the user ID
      $customer = new WC_Customer( $user_id );
      $first_name = $customer->get_first_name();
      $last_name  = $customer->get_last_name();
      
      // load the mailer class.
			$mailer = WC()->mailer();
      
      $message_body = sprintf( 
        __( 'Hello %1$s %2$s ,
        Your order in our store having order id %3$s is %4$s. 
      ' ),  
      $first_name, $last_name, $order_id, $next_status  );

      $message = $mailer->wrap_message(
      // Message head and message body.
      sprintf( __( 'Your Order has been updated' ) ), $message_body );

      // Client email, email subject and message.
      $mailer->send( $order->billing_email, sprintf( __( 'Your Order has been updated' ) ), $message );
    }

    
    /**
     * Add addon item to order again cart item data
     *
     * @author WP Scripts
     * @since 1.4.5
     *
     * @return array
     */
    public function wfs_order_placed_again( $data, $item, $order ) {
      
      $item_id = !is_null( $item->get_id() ) ? $item->get_id() : '';

      if( !empty( $item_id ) ) {
        //Get order id from order item 
        $order_id = $item->get_order_id();

        //Check whether service is being enabled or not
        $is_service_enabled = wfs_is_service_enabled();

        if ( $is_service_enabled ) {
          //Get service type from order item meta
          $service_type = get_post_meta( $order_id, '_wfs_service_type', true );
          $service_type = !empty( $service_type ) ? $service_type : wfs_get_default_service_type();
          setcookie( 'service_type', $service_type, time() + 1800, COOKIEPATH, COOKIE_DOMAIN );
        }

        $order = new WC_Order( $order_id );
        $order_items = $order->get_items();

        if ( is_array( $order_items ) && !empty( $order_items ) ) {
          $addon_items = [];
          foreach( $order_items as $cart_key => $cart_item ) {
            if( $item_id == $cart_item->get_id() ) {
              if( !empty( $cart_item->get_meta( '_addon_items' ) ) ){
                $addon_items = $cart_item->get_meta( '_addon_items' );
                $data['addons'] = $addon_items;
              }
            }
          }
        }
      }

      return $data;

    }

    /**
     * Add notice error if service type or service time is not set
     *
     * @author WP Scripts
     * @since 1.4.6
     *
     * @return mixed
     */
    public function wfs_before_checkout_process() {
      
      //Check whether service is being enabled or not
      $is_service_enabled = wfs_is_service_enabled();

      if ( $is_service_enabled ) {

        $service_type = isset( $_COOKIE['service_type'] ) ? wc_clean( $_COOKIE['service_type'] ) : '';
        $service_time = isset( $_COOKIE['service_time'] ) ? wc_clean( $_COOKIE['service_time'] ) : '';

        //Get foodstore page details
        $get_foodstore_details = wfs_get_default_store_page();
        $page_title     = '';
        $page_permalink = '';
      
        if( !empty( $get_foodstore_details ) ) {
          $page_title     = isset( $get_foodstore_details['page_title'] ) ? $get_foodstore_details['page_title'] : '';
          $page_permalink = isset( $get_foodstore_details['page_permalink'] ) ? $get_foodstore_details['page_permalink'] : '';
        }

        //Add notice error if service type is not set
        if ( empty( $service_type ) ) {
          if( !empty( $page_title ) || !empty( $page_permalink ) ) {
            /* translators: %1$s: $page_permalink foodstore page url. */
            /* translators: %2$s: $page_title foodstore page title. */
            wc_add_notice( sprintf( __( 'Please select service type for your order from <a href="%1$s">%2$s</a> page.', 'food-store' ), $page_permalink, $page_title ), 'error' );
          }
          else {
            wc_add_notice( __( 'Please select service type for your order.', 'food-store' ), 'error' );
          }
        }
      
        //Add notice error if service time is not set
        if ( empty( $service_time ) ) {
          if( !empty( $page_title ) || !empty( $page_permalink ) ) {
            /* translators: %1$s: $page_permalink foodstore page url. */
            /* translators: %2$s: $page_title foodstore page title. */
            wc_add_notice( sprintf( __( 'Please choose a time for your order from <a href="%1$s">%2$s</a> page', 'food-store' ), $page_permalink, $page_title ), 'error' );
          }
          else {
            wc_add_notice( __( 'Please choose a time for your order.', 'food-store' ), 'error' );
          }
        }

      }
    }

}

new WFS_Order();