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
    this.options  = $.extend({}, Gallery.DEFAULTS, options)
    this.count    = this.$element.children().length;
    this.parent   = this.$element.parents().find('.gallery');
    this.index    = 0;
    this.template = '<div class="gallery-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">';
    this.template += '<div class="modal-dialog"><div class="modal-content"><div class="modal-body">';
    this.template += '<img src="" class="img-responsive" alt="" title="" />' + this.parent.attr('class') + ' </div></div></div></div>';

  };

  Gallery.VERSION = '1.0.0';

  Gallery.DEFAULTS = {

  };

  Gallery.prototype.show = function () {
    //var src = this.$element.children().get(this.index);

    $('body').append(this.template);
    $('.gallery-modal').modal();
    $('.gallery-modal').on('shown.bs.modal', function () {
      $('.gallery-modal img').attr('title', 'Alguma coisa');
    });
  };
  /*

  Gallery.prototype.toggle = function () {
    $('body').append(this.options.template);
  };

  Gallery.prototype.keydown = function () {
    this.options.template.off('keydown').on('keydown', function (e) {
      switch (e.keyCode) {
        case 37:
          this.prev();
          break;
        case 39:
          this.next();
          break;
        default:
          return;
      }
    });


    e.preventDefault();
  };*/

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
  // ===========================

  $.fn.gallery.noConflict = function () {
    $.fn.gallery = old;
    return this;
  }

  // GALLERY DATA-API
  // ==============

  $(document).on('click.bs.gallery.data-api', '[data-toggle="gallery"]', function (e) {
    var $this = $(this);
    var href  = $this.attr('href');

    e.preventDefault();

    Plugin.call($(this), 'show');
  });

}(jQuery);