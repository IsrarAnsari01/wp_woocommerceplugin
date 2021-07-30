jQuery(document).ready(($) => {
  var a = "#ia_datepicker_for_dob";
  $(a).datepicker({
    dateFormat: "yy-mm-dd",
  });
  $("#ia_donate_box").click(function () {
    $("body").trigger("update_checkout");
  });
});
