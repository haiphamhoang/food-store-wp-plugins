jQuery(function ($) {
  if (wfsCheckoutParams.enable_asap == "yes") {
    const getURLParams = window.location.search;
    var selectedServiceType = Cookies.get("service_type");

    if (
      selectedServiceType == "" ||
      selectedServiceType == null ||
      selectedServiceType == "undefined"
    ) {
      selectedServiceType = wfsCheckoutParams.default_service_type;
    }
    var serviceTime = Cookies.get("service_time");
    var asapLabel = wfsCheckoutParams.asap_label;
    //Append the service type to the URL
    if (
      getURLParams == "" ||
      getURLParams == null ||
      getURLParams == "undefined"
    ) {
      let checkoutUrl = wfsCheckoutParams.checkout_url;
      window.location = checkoutUrl + "?type=" + selectedServiceType;
    }

    if (serviceTime == asapLabel) {
      checkoutASAPOption();
    }

    //Set the selected service type in the option
    if (serviceTime !== asapLabel && $(".checkout-asap-block").length > 0) {
      $(".checkout-asap-block")
        .find('a[href="#' + selectedServiceType + '_asap"]')
        .removeClass("active");
      $(".checkout-asap-block")
        .find('a[href="#' + selectedServiceType + '_schedule"]')
        .addClass("active");
      $("#wfs_checkout_fields")
        .find("#" + selectedServiceType + "_asap")
        .removeClass("active");
      $("#wfs_checkout_fields")
        .find("#" + selectedServiceType + "_schedule")
        .addClass("active");
    }

    $("body").on("click", ".checkout-asap-block a", function (e) {
      e.preventDefault();
      let serviceTime = $(this).attr("aria-controls");
      let serviceASAP = serviceTime.includes("asap");
      if (serviceASAP) {
        Cookies.set("service_time", asapLabel);
        checkoutASAPOption();
      } else {
        jQuery("#wfs_service_time option[value='" + asapLabel + "']").remove();
      }
    });

    //Add ASAP option to the service time dropdown in the checkout page
    function checkoutASAPOption() {
      let checkASAPOptionExists = jQuery(
        "#wfs_service_time option[value='" + asapLabel + "']"
      ).length;

      if (checkASAPOptionExists == 0) {
        $("<option/>")
          .val(asapLabel)
          .text(asapLabel)
          .appendTo("#wfs_service_time");
      }
      $("#wfs_service_time").val(asapLabel);
    }
  }

  $("body").on("blur change", "#wfs_service_time", function () {
    let selected_time = $(this).val();
    let selected_service = Cookies.get("service_type");
    var data = {
      action: "update_service_time",
      selected_service: selected_service,
      selected_time: selected_time,
    };

    $.ajax({
      type: "POST",
      data: data,
      url: wfsCheckoutParams.ajaxurl,
      success: function (response) {},
    });
  });

  //Toggle service nav container
  $(document).on("click", "#wfs_checkout_fields .nav-link", function (e) {
    e.preventDefault();
    let selectedServiceTime = $(this).attr("aria-controls");
    $(this)
      .parents("#wfs_checkout_fields")
      .find(".tab-pane")
      .removeClass("active show");
    $(this)
      .parents("#wfs_checkout_fields")
      .find(".tab-pane#" + selectedServiceTime + "")
      .addClass("active show");
  });
});
