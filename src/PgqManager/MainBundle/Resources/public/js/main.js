/**
 * Created by kmarques on 04/04/14.
 */
$(document).ready(function () {

  $('div.sidebar-right > span[name="close"]').on('click', function () {
    $(this).parents('.sidebar-right').animate({width: "0"}, 300);
  });

  $('table.datatable:not(.ajax)').dataTable();

  var flashmessage = function (sMessage, sClass) {
    $('div[name=flashmessage]').attr('class', 'alert alert-' + sClass).text(sMessage).fadeIn();
  }

});