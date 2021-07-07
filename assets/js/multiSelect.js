jQuery(document).ready(function ($) {
  /**
   * Hosted Url
   */
  var ajaxurl = hostedUrl.site_url;

  /**
   * Click to add new Row also change the value of name attribute dynamically
   * Run Select2 for new added row
   */

  $("#addRestrictedRow").on("click", function () {
    var row = $(".empty-row.screen-reader-text").clone(true);
    const randomNumber = Math.floor(Math.random() * 10) + 1;
    $(row)
      .find("select")
      .each(function () {
        let fieldName = $(this).attr("name");
        $(this).attr("name", fieldName + "[" + randomNumber + "][]");
      });
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

  /**
   * All Rules Come from multipleCountryBasedRestriction File
   */
  let allRules = rules.savedRules;

  /**
   * Retrive all saved Rules
   * Looping in tr
   */

  $(".forDataInsertion").each(function (index, el) {
    const randomNumber = Math.floor(Math.random() * 100) + 1;
    $(el)
      .find("select")
      .each(function () {
        let fieldName = $(this).attr("name");
        $(this).attr("name", fieldName + "[" + randomNumber + "][]");
      });
    $(el)
      .find(".restrictedProducts")
      .select2({
        ajax: {
          url: ajaxurl,
          dataType: "json",
          data: function (params) {
            console.log("inside data function");
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
    console.log("After Select2 Each function");
    $(el)
      .find(".restrictedCetagories")
      .select2({
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
  });

  /**
   * Populate selected product and cetagory
   */

  if (allRules) {
    allRules.forEach((element, index) => {
      var registerdRow = $($(".forDataInsertion")[index]);
      if (element.restrictedProducts.length) {
        element.restrictedProducts.forEach((element) => {
          let option = new Option(element.text, element.id, true, true);
          registerdRow
            .find(".restrictedProducts")
            .append(option)
            .trigger("change");
        });
      }
      if (element.restrictedCetagories.length) {
        element.restrictedCetagories.forEach((element) => {
          let option = new Option(element.text, element.id, true, true);
          registerdRow
            .find(".restrictedCetagories")
            .append(option)
            .trigger("change");
        });
      }
    });
  }

  /**
   * Remove new added row on click
   */
  $(".remove-row").on("click", function (e) {
    e.preventDefault();
    $(this).parents("tr").remove();
    return false;
  });
});
