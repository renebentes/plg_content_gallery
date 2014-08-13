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
    this.$gallery = $(element);
    this.options  = $.extend({}, Button.DEFAULTS, options)
    this.count    = this.$gallery.children().length;
    this.index    = 0;
  };

  Gallery.VERSION = '1.0.0';

  // GALLERY PLUGIN DEFINITION
  // ==================================

  var old = $.fn.gallery;
  $.fn.gallery = function (options) {
    return this.each(function () {
      var data = $(this).data('bs.Gallery');
      if (!data) {
        $(this).data('bs.Gallery', (data = new Gallery($(this), options)));
      }
    });
  };

  $.fn.gallery.Constructor = Gallery;

  // BOOTSTRAPGALLERY NOCONFLICT
  // ===========================

  $.fn.gallery.noConflict = function () {
    $.fn.gallery = old;
    return this;
  }
}(jQuery);