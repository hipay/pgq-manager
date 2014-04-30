/**
 * Created by kmarques on 09/04/14.
 */



(function ($) {


  $.listDraggable = function (element, options) {

    // private property
    //
    // plugin's default options
    //
    var defaults = {
      "multiSelect": false,
      "label": "Original",
      trash: false
    };

    // to avoid confusions, use "plugin" to reference the
    // current instance of the object
    var plugin = this;

    var listElements = [];

    plugin.settings = {};


    var $element = $(element), // reference to the jQuery version of DOM element
      element = element;    // reference to the actual DOM element

    // Constructor
    //
    // Constructor
    //
    plugin.init = function () {
      // Merge settings with defaults
      plugin.settings = $.extend({}, defaults, options);

      if (checkRequirements()) {
        initList();
        refreshList();
        bindEvents();
      }
    };

    // public method
    //
    // Refresh listing manually
    //
    plugin.refresh = function () {
      refreshList();
    };

    // public method
    //
    //
    //
    plugin.getData = function () {
      return plugin.settings.data;
    }

    // private method
    //
    // Change a string to an human readable string
    //
    var humanize = function (string) {

      string = string.replace(/([0-9])([a-zA-Z])/g, '$1 $2');
      string = string.replace(/([a-zA-Z])([0-9])/g, '$1 $2');
      string = string.replace(/([a-z])([A-Z])/g, '$1 $2');
      string = string.replace(/([A-Z])([a-z])/g, '$1 $2');

      return string;
    };

    // private method
    //
    // Capitalize string
    //
    var capitalize = function (string) {
      return string[0].toUpperCase() + string.slice(1);
    };

    // private method
    //
    // Check requirements
    //
    var checkRequirements = function () {

      var result = true;

      if (!plugin.settings.data || plugin.settings.data.length < 1) {
        $element.append('<p class="alert alert-error">data option not specified or is empty</p>');
        result = false;
      }

      if (!plugin.settings.lists || plugin.settings.lists.length < 1) {
        $element.append(
          '<p class="alert alert-error">'
            + 'lists option not specified or is empty<br>'
            + '<small>lists:[<br>'
            + '  {name: "mylistname", label: "my list label", class: "span2"}<br>'
            + ' ,...<br>'
            + ']'
            + '</small>'
            + '</p>'
        );

        result = false;
      }

      return result;
    };

    // private method
    //
    // Initialise DOM
    //
    var initList = function () {

      var $divPlugin = $('<div class="row-fluid draggable-list"><div class="draggable-original-list span3"></div><div class="draggable-filters-list'
          + (plugin.settings.trash ? ' span8' : ' span9') + '"></div></div>'),
        $divOriginal = $($divPlugin.find('div.draggable-original-list').eq(0)),
        $divFilters = $($divPlugin.find('div.draggable-filters-list').eq(0));

      //create original list
      $divOriginal.append(
        $('<div>'
          + '<h1 class="baloon-title">' + plugin.settings.label + ' List</h1>'
          + '<ul name="originList"></ul>'
          + '</div>'
        )
      );

      //create filter list
      $.each(plugin.settings.lists, function (index, item) {

        if (!this.label)
          this.label = humanize(this.name);

        listElements[this.name] = $('<div class="' + (this.class ? this.class : '' ) + '">'
          + '<h1 class="baloon-title">' + this.label + '</h1>'
          + '<ul name="' + this.name + 'List"></ul>'
          + '</div>'
        );

        $divFilters.append(listElements[this.name]);

      });


      if (plugin.settings.trash) {
        $trashDiv = $('<div class="span1">'
          + '  <ul name="noneList" class="baloon-title trash">'
          + '    <li>'
          + '      <h1 style="text-align: center;"><i class="icon icon-trash"></i>&nbsp;Trash</h1>'
          + '    </li>'
          + '  </ul>'
          + '</div>'
        );

        $divPlugin.append($trashDiv);
      }

      $element.html($divPlugin);

    };

    // private method
    //
    // refresh list from data
    //
    var refreshList = function () {

      $element.find('ul:not(.trash)').html('');

      var $originList = $element.find('ul[name=originList]');

      $.each(plugin.settings.data, function (index, group) {

        //create group item
        var groupLi = '<li class="group" draggable="true"><span class="text" name="' + group.id + '">' + group.name + '</span></li>',
          risks = [],
          lis = [];

        //append group to all list
        for (var key in listElements) {
          if (listElements.hasOwnProperty(key)) {
            $(listElements[key]).find('ul').append(groupLi);
          }
        }


        //compute items
        $.each(group.items, function (index, item) {

            var itemLi = '<li class="item" draggable="true"><span class="text" name="' + item.id + '">' + item.name + '</span></li>',
              list = '';

            if (listElements[item.list]) {
              listElements[item.list].find('ul').append(itemLi);
              list = capitalize(item.list);
            }

            if (list != '')
              risks.indexOf(list) < 0 ? risks.push(list) : true;

            lis.push($(itemLi).append('<span class="pull-right list">' + list + '</span>'));

          }
        );

        //append all li to originList

        $originList.append($(groupLi).append('<span class="pull-right list">' + risks.join('/') + '</span>'));

        $(lis).each(function () {
          $originList.append(this)
        });
      });
    };

    // private method
    //
    // Bind events (drag, drop, click
    //
    var bindEvents = function () {

      $element
        .on('click', 'li', function (e) {

          if (!plugin.settings.multiSelect)
            return false;

          $ulName = $('li.alert-info[draggable=true]').parents('ul').attr('name');

          if ($ulName && $ulName != $(this).parents('ul').attr('name')) {
            alert('Multi-select only available on the same list');

            return false;
          }

          if ($(this).hasClass('group')) {
            $(this).nextUntil('li.group').toggleClass('alert-info');
          } else {
            $(this).toggleClass('alert-info');
          }

        })
        .on('dragstart', 'li[draggable=true]', function (e) {

          var names = [],
          //get multi-select
            selectedNames = $('li.alert-info[draggable=true]');

          //push selected items on transfer list
          selectedNames.each(function () {
            names.push($(this).find('span.text').attr('name'))
          });

          $(e.target).find('span.list').hide();

          if ($(e.target).hasClass('item')) {
            names.push($(e.target).find('span.text').attr('name'));
            e.originalEvent.dataTransfer.setData("group", $(e.target).prevAll('li.group').first().find('span.text').attr('name'));
          } else {

            e.originalEvent.dataTransfer.setData("group", $(e.target).find('span.text').attr('name'));

            $.each($(e.target).nextUntil('li.group'), function () {
              names.push($(this).find('span.text').attr('name'));
            });
          }

          e.originalEvent.dataTransfer.setData("item", names.join('/'));

          e.originalEvent.dataTransfer.setData("fromlist", $(e.target).parents('ul').attr('name'));

        })
        .on('drop', 'ul', function (e) {
          e.preventDefault();

          var toList = $(this).attr('name'),
            itemValue = e.originalEvent.dataTransfer.getData("item").split('/'),
            groupValue = e.originalEvent.dataTransfer.getData("group"),
            fromList = e.originalEvent.dataTransfer.getData("fromlist");

          if (e.srcElement.nodeName.toUpperCase() == 'SPAN') {
            $(e.srcElement).siblings('span.list').show();
          } else {
            $(e.srcElement).find('span.list').show();
          }

          if (toList == 'originList' || toList == fromList)
            return;

          var groupResult = $.grep(plugin.settings.data, function (item, index) {
              return item.id == groupValue;
            }),
            itemResult = $.grep(groupResult[0].items, function (item, index) {
              return (itemValue ? $.inArray(item.id, itemValue) != -1 : true);
            });

          $.each(itemResult, function () {
            this.list = toList.replace('List', '');
          });

          refreshList();
        })
        .on('dragover', 'ul', function (e) {
          e.preventDefault();
        });

    };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object

  $.fn.listDraggable = function (options) {

    var args = Array.prototype.slice.call(arguments, 1);

    var returns;

    // iterate through the DOM elements we are attaching the plugin to
    this.each(function () {

      // if plugin has not already been attached to the element
      if (undefined == $(this).data('plugin_listDraggable') || typeof options === 'object') {

        // create a new instance of the plugin
        // pass the DOM element and the user-provided options as arguments
        var plugin = new $.listDraggable(this, options);

        // store a reference to the plugin object
        $(this).data('plugin_listDraggable', plugin);

        console.log("list draggable initialized");
      } else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {

        var instance = $(this).data('plugin_listDraggable');

        if (instance instanceof $.listDraggable && typeof instance[options] === 'function') {
          returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
        }

        // Allow instances to be destroyed via the 'destroy' method
        if (options === 'destroy') {
          $(this).data('plugin_listDraggable', null);
        }
      }
    });

    return returns !== undefined ? returns : this;
  };

  // Fix problems with console object when browser debug mode not activated
  if (!window.console) window.console = {log: function () {
  }};

})(jQuery);