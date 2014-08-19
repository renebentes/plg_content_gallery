/*!
 * Gallery v1.0.0
 * Copyright 2014 Rene Bentes Pinto
 * Licensed under GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */

if (typeof jQuery === 'undefined') { throw new Error('Gallery\'s Javascript requires jQuery!'); }

+function ($) {
  'use strict';

  // GALLERY CLASS DEFINITION
  // ========================

  var Gallery = function (element, options) {
    this.$element = $(element);
    this.options  = $.extend({}, Gallery.DEFAULTS, options);
    this.$parent  = this.$element.parents().find('.gallery');
    this.count    = this.$parent.children().length;
    this.index    = 0;

    this.template = {
      dialog:
        '<div class="gallery-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">' +
          '<div class="modal-dialog">' +
            '<div class="modal-content">' +
              '<div class="modal-body">' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</div>',
      header:
        '<div class="modal-header">' +
          '<button type="button" class="close" data-dismiss="modal">' +
            '<span aria-hidden="true">&times;</span>' +
            '<span class="sr-only">Close</span>' +
          '</button>' +
        '</div>',
      footer:
        '<div class="modal-footer"></div>',
      image:
        '<img src="" class="img-responsive" alt="" title="" />',
      control:
        '<div class="btn-group control">' +
          '<button type="button" class="btn btn-default prev"><i class="fa fa-chevron-left"></i></button>' +
          '<button type="button" class="btn btn-default next"><i class="fa fa-chevron-right"></i></button>' +
        '</div>'
    };
  };

  Gallery.VERSION = '1.0.0';

  Gallery.DEFAULTS = {

  };

  Gallery.prototype.show = function () {
    $('div').remove('.gallery-modal');
    $('body').append(this.template.dialog);
    $('.gallery-modal .modal-content').prepend(this.template.header);
    $('.gallery-modal .modal-body').append(this.template.image);
    $('.gallery-modal img').attr('src', this.$element.attr('href'));

    if (this.count > 1) {
      $('.gallery-modal .modal-content').append(this.template.footer);
      $('.gallery-modal .modal-footer').append(this.template.control);

      $('gallery-modal').on('click.prev.bs.gallery', '[class="prev"]', $.proxy(this.prev(), this));
      $('gallery-modal').on('click.next.bs.gallery', '[class="next"]', $.proxy(this.next, this));
    }

    $('.gallery-modal').modal();
  };

  /*Gallery.prototype.keydown = function () {
    $('gallery-modal').off('keydown').on('keydown', function (e) {
      switch (e.keyCode) {
        case 37:
          this.prev();
          break;
        case 39:
          this.next();
          break;
        case 27:
          ('gallery-modal').modal('hide');
          break;
        default:
          return;
      }
    });
  };*/

  Gallery.prototype.prev = function () {
    $('.gallery-modal .control .prev').on('click', function (e) {
      e.preventDefault();
      alert(this.index);

    /*  this.index--;
      if (this.index < 0) this.index = this.count - 1;

      $('.gallery-modal img').attr('src', this.$parent.children().get(this.index).attr('href'));*/

      return false;
    });
  };

  Gallery.prototype.next = function () {
    $('.gallery-modal .control .next').off('click').on('click', function (e) {
      e.preventDefault();

      this.index++;
      if (this.index >= this.count) this.index = 0;

      $('.gallery-modal img').attr('src', this.$parent.children().get(this.index).attr('href'));

      return false;
    });
  };

  // GALLERY PLUGIN DEFINITION
  // ==================================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this);
      var data    = $this.data('bs.gallery');
      var options = typeof option == 'object' && option;

      if (!data) $(this).data('bs.gallery', (data = new Gallery(this, options)));
      if (typeof option == 'string') data[option]();
    });
  };

  var old = $.fn.gallery;

  $.fn.gallery             = Plugin;
  $.fn.gallery.Constructor = Gallery;

  // GALLERY NOCONFLICT

  $.fn.gallery.noConflict = function () {
    $.fn.gallery = old;
    return this;
  }

  // GALLERY DATA-API
  // ==============

  $(document).on('click.bs.gallery.data-api', '[data-toggle="gallery"]', function (e) {
    e.preventDefault();

    Plugin.call($(this), 'show');
  });

}(jQuery);
