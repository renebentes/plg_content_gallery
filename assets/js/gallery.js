/*!
 * Gallery v1.0.0
 * Copyright 2014 Rene Bentes Pinto
 * Licensed under GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */

if (typeof jQuery === 'undefined') throw new Error('Gallery\'s Javascript requires jQuery!');

+ function($) {
  'use strict';

  // GALLERY CLASS DEFINITION
  // ========================

  var Gallery = function(element, options) {
    this.$body     = $(document.body)
    this.$element  = $(element);
    this.options   = $.extend({}, Gallery.DEFAULTS, options);
    this.$parent   = this.$element.parents().find('.gallery');
    this.count     = this.$parent.children().length;
    this.index     = this.$element.parent().index();
    this.container = this.options.container;

    this.template = {
      dialog: '<div class="gallery-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">' +
        '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-body">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>',
      header: '<div class="modal-header">' +
        '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">' +
        '<span class="fa fa-times" aria-hidden="true"></span>' +
        '<span class="sr-only">Close</span>' +
        '</button>' +
        '</div>',
      image: '<img src="" class="img-responsive center-block" alt="" title="" />',
      footer: '<div class="modal-footer"></div>',
      legend: '<div class="caption"></div>',
      control: '<div class="btn-group control">' +
        '<button type="button" class="btn btn-default prev"><i class="fa fa-chevron-left"></i></button>' +
        '<button type="button" class="btn btn-default next"><i class="fa fa-chevron-right"></i></button>' +
        '</div>'
    };
  };

  Gallery.VERSION = '1.0.3';

  Gallery.DEFAULTS = {
    container: '.gallery-modal'
  };

  Gallery.prototype.show = function() {
    $('div').remove(this.container);
    this.$body.append(this.template.dialog);

    var galleryTitle = this.$parent.attr('data-title');
    var title = this.$element.attr('data-title');
    var description = this.$element.attr('data-description');

    $(this.container).on('keydown.bs.gallery', $.proxy(this.keydown, this));

    $(this.container + ' .modal-content').prepend(this.template.header);

    if (galleryTitle !== 'undefined')
      $(this.container + ' .modal-header').append('<h4 class="modal-title">' + this.$parent.data('title') + '</h4>');

    $(this.container + ' .modal-body').append(this.template.image);
    $(this.container + ' img').attr('src', this.$element.attr('href'));


    if (this.count > 1 || (title !== 'undefined' || description !== 'undefined')) {
      $(this.container + ' .modal-content').append(this.template.footer);

      if (title !== 'undefined' || description !== 'undefined') {
        $(this.container + ' .modal-body').append(this.template.legend);

        if (title !== 'undefined')
          $(this.container + ' .modal-body .caption').append('<h4>' + title + '</h4>');

        if (description !== 'undefined')
          $(this.container + ' .modal-body .caption').append('<p>' + description + '</p>');
      }

      if (this.count > 1) {
        $(this.container + ' .modal-footer').append(this.template.control);
        $(this.container + ' .control .prev').on('click.prev.bs.gallery', $.proxy(this.prev, this));
        $(this.container + ' .control .next').on('click.next.bs.gallery', $.proxy(this.next, this));
      }
    }

    $(this.container).modal();
  };

  Gallery.prototype.keydown = function(e) {
    switch (e.which) {
      case 37:
        this.prev();
        break;
      case 39:
        this.next();
        break;
      case 27:
        $(this.container).modal('hide');
        break;
      default:
        return;
    }
    e.preventDefault();
  };

  Gallery.prototype.prev = function() {
    this.index--;
    $('div').remove('.modal-body .caption');
    if (this.index < 0) {
      this.index = this.count - 1;
    }

    $(this.container + ' img').attr('src', this.$parent.find('a').get(this.index).getAttribute('href'));
    var title = this.$parent.find('a').get(this.index).getAttribute('data-title');
    var description = this.$parent.find('a').get(this.index).getAttribute('data-description');

    if (title !== 'undefined' || description !== 'undefined') {
      $(this.container + ' .modal-body').append(this.template.legend);
      $(this.container + ' .caption').append('<h4>' + title + '</h4>');
      $(this.container + ' .caption').append('<p>' + description + '</p>');
    }

    return false;
  };

  Gallery.prototype.next = function() {
    this.index++;
    $('div').remove('.modal-body .caption');
    if (this.index >= this.count) {
      this.index = 0;
    }

    $(this.container + ' img').attr('src', this.$parent.find('a').get(this.index).getAttribute('href'));
    var title = this.$parent.find('a').get(this.index).getAttribute('data-title');
    var description = this.$parent.find('a').get(this.index).getAttribute('data-description');

    if (title !== 'undefined' || description !== 'undefined') {
      $(this.container + ' .modal-body').append(this.template.legend);
      $(this.container + ' .caption').append('<h4>' + title + '</h4>');
      $(this.container + ' .caption').append('<p>' + description + '</p>');
    }

    return false;
  };

  // GALLERY PLUGIN DEFINITION
  // ==================================

  function Plugin(option) {
    return this.each(function() {
      var $this = $(this);
      var data = $this.data('bs.gallery');
      var options = typeof option == 'object' && option;

      if (!data) $(this).data('bs.gallery', (data = new Gallery(this, options)));
      if (typeof option == 'string') data[option]();
    });
  };

  var old = $.fn.gallery;

  $.fn.gallery = Plugin;
  $.fn.gallery.Constructor = Gallery;

  // GALLERY NOCONFLICT

  $.fn.gallery.noConflict = function() {
    $.fn.gallery = old;
    return this;
  }

  // GALLERY DATA-API
  // ==============

  $(document).on('click.bs.gallery.data-api', '[data-toggle="gallery"]', function(e) {
    e.preventDefault();

    Plugin.call($(this), 'show');
  });

}(jQuery);