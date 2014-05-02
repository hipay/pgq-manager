/**
 * Created by kmarques on 04/04/14.
 */
$(document).ready(function () {

  var toInput = function (obj) {
    var html = '';
    $.each(obj, function (index, value) {
      html +=
        '<div class="form-group">'
          + '<label class="control-label col-md-6" for="form_text">' + index + '</label>'
          + '<div class="col-md-6">'
          + '<input type="text" id="form_text" name="' + index + "\" class=\"form-control\" value='" + value + "'>"
          + '</div>'
          + '</div>';
    });

    return html;
  };

  var options = [];

  var parseQueryString = function (queryString) {
    var params = {}, queries, temp, i, l;

    // Split into key/value pairs
    queries = queryString.split("&");

    // Convert the array of strings into an object
    for (i = 0, l = queries.length; i < l; i++) {
      temp = queries[i].split('=');
      params[decodeURIComponent(temp[0])] = (temp[1] ? decodeURIComponent(temp[1].replace(/\+/g, '%20')) : '');
    }

    return params;
  };
  var flashmessage = function (sMessage, sClass) {

    var div = $('<div class="row table alert alert-' + sClass + '">'
      + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
      + '<strong>' + sClass.toUpperCase() + '!</strong> ' + sMessage
      + '</div>');

    $(div).prependTo('div.main-content').delay(2000)
      .queue(function (next) {
        if (!$(div).hasClass('alert-error')) {
          $(div).fadeOut(400, function () {
            $(this).remove();
          })
        }
        next();
      });
  };

  function fnFormatDetails(nTr) {
    var aData = datatable.fnGetData(nTr);
    var sOut = '<table style="padding-left:50px; word-break: break-all">';
    sOut += '<tr name="data" data-clickable="false" class="alert-info"><td>' + aData['ev_data'] + '</td></tr>';
    if (aData['ev_failed_reason'] && aData['ev_failed_reason'] != '')
      sOut += '<tr name="reason" data-clickable="false" class="alert-danger"><td>' + aData['ev_failed_reason'] + '</td></tr>';
    sOut += '</table>';

    return sOut;
  }

  var eventAction = function (evId, action, data) {
    customSearchData.splice(3, Number.MAX_VALUE);
    customSearchData.push({name: 'event', value: evId});
    customSearchData.push({name: 'action', value: action});

    if (data != null) {
      customSearchData.push({name: 'data', value: data});
    }

    $.ajax({
      url: Routing.generate('_main_failedevent_action'),
      type: 'POST',
      dataType: 'json',
      data: customSearchData,
      success: function (json) {
        if (json.flashmessage) {
          flashmessage(json.flashmessage.message, json.flashmessage.class);
        }
        datatable.fnPageChange(0);
      }
    });
  };

  var datatable = $('table.ajax.datatable').dataTable({
    "bProcessing": true,
    "bServerSide": true,
    "iDeferLoading": 0,
    "sAjaxSource": $(this).data('uri'),
    "sServerMethod": "POST",
    "fnServerData": function (sSource, aoData, fnCallback) {
      var $this = $(this);

      $.each(customSearchData, function (index, item) {
        aoData.push({ "name": item.name, "value": item.value });
        if (item.name == 'eventid')
          customSearchData.splice(index, 1);
      });

      $.ajax({
        url: $(this).data('uri'),
        type: 'POST',
        dataType: 'json',
        data: aoData,
        success: function (json) {
          $this.parents('div[name^="table-event-"]').fadeIn();

          fnCallback(json);
        }
      });

    },
    "aoColumns": [
      { "mData": "ev_id" },
      { "mData": "ev_failed_time" },
      { "mData": "ev_time" },
      { "mData": "ev_retry", "bSortable": false },
      { "mData": "ev_type", "bSortable": false },
      { "mData": "action", "bSortable": false }
    ],
    "aaSorting": [
      [ 1, "desc" ]
    ],
    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
      /* Append the grade to the default row class name */
      var td =
        '<a class="table-event-action" href="#" data-action="retry">'
          + '  <span title="Retry" class="glyphicon glyphicon-refresh"></span>'
          + '</a>'
          + '<a class="table-event-action" href="#" data-action="edit">'
          + '  <span title="Edit" class="glyphicon glyphicon-pencil"></span>'
          + '</a>'
          + '<a class="table-event-action" href="#" data-action="delete">'
          + '  <span title="Delete" class="glyphicon glyphicon-remove"></span>'
          + '</a>';

      $(nRow).attr('data-clickable', 'true').attr('name', 'event-' + $(nRow).find('td:first-child').text());
      $(nRow).find('td:last-child').html($(td));
      $(nRow).find('td:last-child span').tooltip({
        placement: 'top'
      });
    }
  })
    .on('click', 'tbody > tr[data-clickable=true]', function (e) {
      if (e.originalEvent.srcElement.nodeName.toLowerCase() != 'td') {
        return;
      }
      var nTr = this;
      $subtable = $(this).find('table');
      if ($subtable.length) {
        return;
      }
      if (datatable.fnIsOpen(nTr)) {
        datatable.fnClose(nTr);
      }
      else {
        datatable.fnOpen(nTr, fnFormatDetails(nTr), 'details');
      }
    })
    .on('click', 'a.table-event-action', function (e) {
      e.stopPropagation();
      var $this = $(this);

      if ($this.data('action') == 'edit') {
        var tr = $this.parents('tr')[0],
          data = datatable.fnGetData(tr);

        $('#event-modal div[name=data]').html(toInput(parseQueryString(data.ev_data)));

        $('#event-modal')
          .modal('show')
          .on('click', 'button[name=save]', function (e) {
            eventAction(
              $this.parents('tr').find('td').eq(0).text(),
              $this.data('action'),
              $('#event-modal form').serialize()
            );
            $('#event-modal').modal('hide');
          });

        return false;
      }

      eventAction($this.parents('tr').find('td').eq(0).text(), $this.data('action'), null);

      return false;
    });

  $('.datatable-action').on('click', function () {
    customSearchData = [];
    customSearchData.push({name: 'database', value: $('[name^=table-event-]').attr('name').replace('table-event-', '')});
    customSearchData.push({name: 'queue', value: $(this).parents('tr').find('td').eq(0).text()});
    customSearchData.push({name: 'consumer', value: $(this).parents('tr').find('td').eq(1).text()});
    $(this).parents('tbody').find('tr').removeClass('active');
    $(this).parents('tr').addClass('active');
    window.clearInterval(refresh);
    datatable.fnFilter('');
    startRefresh();

    return false;
  });

  var refresh;
  var startRefresh = function () {
    refresh = setInterval(function () {
      datatable.fnDraw();
    }, 30000);
  };

  if (window.customSearchData == undefined) {
    var customSearchData = new Array();

  } else {
    var customSearchData = window.customSearchData;
    var eventFilter = '';

    $.each(customSearchData, function (index, item) {
      if (item.name == 'eventid')
        eventFilter = item.value;
    });
    datatable.fnFilter(eventFilter);
  }

})
;