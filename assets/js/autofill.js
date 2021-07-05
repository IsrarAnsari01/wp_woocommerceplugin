jQuery(document).ready(function ($) {
  var ajaxurl = BACKEND.site_url;
  /**
   * MULTISELECT IN PRODUCTS
   */

  /**
   * Retrive Data And display them in fields
   */
  let saveProducts = products.selectedProducts;
  if (saveProducts) {
    var productSelect = $("#products");
    saveProducts.forEach((element) => {
      let option = new Option(element.text, element.id, true, true);
      productSelect.append(option).trigger("change");
    });
  }
  /**
   * Save selected Products
   */
  $("#products").select2({
    ajax: {
      url: ajaxurl,
      dataType: "json",
      data: function (params) {
        return {
          q: params.term,
          action: "product_autoFill",
          page: params.page,
        };
      },

      processResults: function (data) {
        var options = [];
        options.push(data);
        return {
          results: options[0],
        };
      },
    },
    minimumInputLength: 3,
  });
  /**
   * MULTISELECT IN CETAGORY
   */

  /**
   * Retrive Data And display them in fields
   */
  let saveCetagories = cetagories.selectedCetagories;
  if (saveCetagories) {
    var cetagorySelect = $("#cetagory");
    saveCetagories.forEach((element) => {
      let option = new Option(element.text, element.id, true, true);
      cetagorySelect.append(option).trigger("change");
    });
  }
  /**
   * Save selected Cetagories
   */
  $("#cetagory").select2({
    ajax: {
      url: ajaxurl,
      dataType: "json",
      data: function (params) {
        return {
          q: params.term,
          action: "cetagory_autoFill",
          page: params.page,
        };
      },

      processResults: function (data) {
        console.log(data);
        var cetagories = [];
        cetagories.push(data);
        return {
          results: cetagories[0],
        };
      },
    },
    minimumInputLength: 3,
  });
});
