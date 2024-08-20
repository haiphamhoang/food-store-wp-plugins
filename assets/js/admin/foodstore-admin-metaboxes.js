jQuery(function ($) {
  ("use strict");

  let selected_addon_category = [];

  /**
   * Admin addon actions
   */
  var wfs_metaboxes_admin_addons = {
    /**
     * Initialize addon actions
     */
    init: function () {
      this.load_selected_addon_category();

      //Add addon category
      $("#addon_options .add-new-addon-category").on(
        "click",
        this.add_new_addon_category
      );

      //Add addon row
      $("#addon_options .add-new-addon").on("click", this.add_new_addon_row);

      //Remove Row
      $("body").on("click", ".remove_row.delete", this.delete_addon_row);

      //Add addon multiple item
      $("body").on(
        "click",
        ".add-addon-multiple-item",
        this.add_addon_multiple_item
      );

      //Load addon category
      $("body").on("click", ".button.load-wfs-addon", this.load_addon_category);

      //Remove addon item
      $("body").on("click", ".remove.wfs-addon-cat", this.remove_addon_row);

      //Live update addon name
      $("body").on(
        "input keypress",
        "input.wfs-input.addon-category-name",
        this.live_update_addon_name
      );

      //Select/unselect all addon items
      $("body").on(
        "click",
        ".wfs-select-all",
        this.select_unselect_all_addon_items
      );

      //Enable sortable
      $("#addon_options")
        .find(".wfs-addons")
        .sortable({
          cursor: "move",
          tolerance: "pointer",
          forcePlaceholderSize: true,
          opacity: 0.65,
          start: function () {
            $("body").addClass("wfs-is-dragging-metaboxes");
          },
          stop: function () {
            var $el = $(this);
            $("body").removeClass("wfs-is-dragging-metaboxes");
          },
        });

      //Apply Select2 to addon category selection
      $("._wfs_addon_select").select2();
    },

    /**
     * Add Addon Category
     */
    add_new_addon_category: function (e) {
      let addons_wrapper = $("#addon_options").find(".wfs-addons");
      wfs_metaboxes_admin_addons.block();

      $.ajax({
        url: wfs_admin_addon_params.ajax_url,
        data: {
          action: "wfs_add_addon_category",
          product_id: wfs_admin_addon_params.post_id,
          uuid:
            Date.now().toString(36) + Math.random().toString(36).substring(2),
        },
        type: "POST",
        success: function (response) {
          addons_wrapper.append(response);

          wfs_metaboxes_admin_addons.unblock();

          wfs_scroll_to_addon_category();
        },
      });
    },

    /**
     * Add Addon Row
     */
    add_new_addon_row: function (e) {
      e.preventDefault();
      let addons_wrapper = $("#addon_options").find(".wfs-addons");
      wfs_metaboxes_admin_addons.block();
      $.ajax({
        url: wfs_admin_addon_params.ajax_url,
        data: {
          action: "wfs_add_addon_row",
          product_id: wfs_admin_addon_params.post_id,
        },
        type: "POST",
        success: function (response) {
          addons_wrapper.append(response);

          wfs_metaboxes_admin_addons.unblock();
          // addons_wrapper.append(response);
          // wfs_metaboxes_admin_addons.unblock();
        },
      });
    },

    /**
     * Delete Addon Row
     */
    delete_addon_row: function (e) {
      e.preventDefault();
      if (window.confirm(wfs_admin_addon_params.remove_row_confirmation)) {
        $(this).parents(".wfs-addon").remove();
      }
    },

    /**
     * Block edit screen
     */
    block: function () {
      $("#woocommerce-product-data").block({
        message: null,
        overlayCSS: {
          background: "#fff",
          opacity: 0.6,
        },
      });
    },

    /**
     * Unblock edit screen
     */
    unblock: function () {
      $("#woocommerce-product-data").unblock();
    },

    /**
     * Add addon multiple items
     *
     */
    add_addon_multiple_item: function (e) {
      e.preventDefault();

      var selected = $(this);

      var SeletedRow = selected
        .parents(".wfs-metabox-content")
        .find("tr.addon-items-row");
      var ParentRow = SeletedRow.first().clone(true);

      ParentRow.find("input").each(function () {
        $(this).val("");
      });
      var LastRow = SeletedRow.last();
      $(ParentRow).insertAfter(LastRow);
    },

    /**
     * Remove Addon Row
     *
     */
    remove_addon_row: function (e) {
      e.preventDefault();

      var selected = $(this);

      if (window.confirm(wfs_admin_addon_params.remove_row_confirmation)) {
        selected.parents(".wfs-metabox.create-new-addon").remove();
      }
    },

    /**
     * Live Update Addon Name
     *
     */
    live_update_addon_name: function (e) {
      let self = $(this);
      let category_name = self.val();

      if (!category_name.trim()) {
        self
          .parents(".create-new-addon")
          .find(".addon_category_name")
          .html(wfs_admin_addon_params.create_addon_category_text);
      } else {
        self
          .parents(".create-new-addon")
          .find(".addon_category_name")
          .html(category_name);
      }
    },

    /**
     * Get selected addon categories
     *
     */
    load_selected_addon_category: function () {
      $(".wfs-addon-category select").each(function () {
        selected_addon_category.push($(this).find(":selected").val());
      });
    },
    /**
     * Load Addon Category
     *
     */
    load_addon_category: function (e) {
      e.preventDefault();

      var selected = $(this);

      let parent_addon_id = selected
        .parent(".wfs-addons-selection")
        .find("select")
        .val();

      if (parent_addon_id == "") {
        alert(wfs_admin_addon_params.parent_addon_selection_error);
        return false;
      }

      if (selected_addon_category.includes(parent_addon_id)) {
        //Bail if addon category is already selected
        alert(wfs_admin_addon_params.category_already_selected);
        return false;
      } else {
        selected_addon_category.push(parent_addon_id);
      }

      let wrapper = selected
        .parents(".wfs-addon-metabox")
        .find(".wfs-addon-items-wrapper");

      if (parent_addon_id === "") {
        alert(wfs_admin_addon_params.parent_addon_selection_error);
        return false;
      }

      let parent_addon_name = selected
        .parent(".wfs-addons-selection")
        .find("select option:selected")
        .text();

      let uuid = selected.attr("data-uuid");

      selected.html(wfs_admin_addon_params.fetching_addon_msg);

      wfs_metaboxes_admin_addons.block();

      $.ajax({
        url: wfs_admin_addon_params.ajax_url,
        data: {
          action: "wfs_load_addon_child",
          product_id: wfs_admin_addon_params.post_id,
          parent_addon_id: parent_addon_id,
          uuid: uuid,
        },
        type: "POST",
        success: function (response) {
          selected
            .parents(".wfs-addon")
            .find("h3 strong")
            .html(parent_addon_name);
          //addons_wrapper.append(response);
          wrapper.html(response);

          $(".wfs-addon-msg").hide();

          selected.html(wfs_admin_addon_params.add_addon_text);
          wfs_metaboxes_admin_addons.unblock();
        },
      });

      return false;
    },

    /**
     * Select unselect all addons
     */
    select_unselect_all_addon_items(e) {
      let selected = $(this);
      let is_checked = selected.is(":checked");
      selected
        .parents("table.wfs-addon-items")
        .find('input[type="checkbox"].wfs-addon-item')
        .prop("checked", is_checked);
    },
  };

  /**
   * Scroll to create new addon category section
   */
  function wfs_scroll_to_addon_category() {
    let container = $("html,body");
    let scrollTo = $(".wfs-addon.wfs-metabox.create-new-addon");

    container.animate(
      {
        scrollTop:
          scrollTo.offset().top -
          container.offset().top +
          container.scrollTop(),
        scrollLeft: 0,
      },
      300
    );
  }

  /**
   * Init admin addon actions
   */
  wfs_metaboxes_admin_addons.init();
});
