jQuery(document).ready(function ($) {
  var ajaxurl = hostedUrl.site_url;
  $("#addRestrictedRow").on("click", function () {
    var row = $(".empty-row.screen-reader-text").clone(true);
    row.removeClass("empty-row screen-reader-text");
    row.insertBefore("#repeatable-fieldset-one tbody>tr:last");
    row.find(".restrictedProducts").select2({
      ajax: {
        url: ajaxurl,
        dataType: "json",
        data: function (params) {
          return {
            q: params.term,
            action: "multi_product_autoFill",
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
    row.find(".restrictedCetagories").select2({
      ajax: {
        url: ajaxurl,
        dataType: "json",
        data: function (params) {
          return {
            q: params.term,
            action: "multi_cetagory_autoFill",
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
    return false;
  });

  $(".remove-row").on("click", function () {
    console.log("clicked for remove row");
    $(this).parents("tr").remove();
    return false;
  });
});
