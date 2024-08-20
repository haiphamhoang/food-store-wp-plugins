jQuery(function ($) {
  jQuery(".wc-order-status")
    .find("label")
    .text(wfs_admin_order_params.payment_status);
});
