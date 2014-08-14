<?php
/**
 * @package     Gallery
 * @subpackage  plg_gallery
 * @copyright   Copyright (C) 2014 Rene Bentes Pinto, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access!');

/*
$script   = array();
$script[] = "if (typeof jQuery === 'undefined') { throw new Error('Gallery\\'s Javascript requires jQuery!'); }\n";
$script[] = "jQuery.noConflict();\n";
$script[] = "+function ($) {";
$script[] = "  $(function () {";
$script[] = "    $('.thumbnail img').on('click', function () {";
$script[] = "      var img = '<img src=\"' + $(this).parent('a').attr('href') + '\" class=\"img-responsive\" />';";
$script[] = "      var index = $(this).parent('a').index();";
$script[] = "      $('#gallery').modal();";
$script[] = "      $('#gallery').on('shown.bs.modal', function () {";
$script[] = "        $('#gallery .modal-body').html(img);";
$script[] = "        $('#gallery .modal-footer a.previous').attr('href', index);";
$script[] = "      });";
$script[] = "      $('#gallery').on('hidden.bs.modal', function () {";
$script[] = "        $('#gallery .modal-body').html('');";
$script[] = "      });";
$script[] = "      return false;";
$script[] = "    });\n";
$script[] = "    $('.modal-footer .control').on('click', function () {";
$script[] = "      var index = $(this).attr('href');";
$script[] = "      var src = $('div.row div:nth-child('+ index +') a.thumbnail').attr('href');";
$script[] = "      $('.modal-body img').attr('src', src);\n";
$script[] = "      var newPrevIndex = parseInt(index) - 1;";
$script[] = "      var newNextIndex = parseInt(newPrevIndex) + 1;\n";
$script[] = "      if ($(this).hasClass('prev')) {";
$script[] = "        $(this).attr('href', newPrevIndex);";
$script[] = "        $('a.next').attr('href', newNextIndex);";
$script[] = "      } else {";
$script[] = "        $(this).attr('href', newNextIndex);";
$script[] = "        $('a.prev').attr('href', newPrevIndex);";
$script[] = "      }";
$script[] = "      return false;";
$script[] = "    });\n";
//$script[] = "    $('.gallery').gallery();";
$script[] = "  });";
$script[] = "}(jQuery);";

JFactory::getDocument()->addScriptDeclaration(implode("\n", $script)); */?>

<script src="<?php echo JUri::root(true); ?>/plugins/content/gallery/assets/js/gallery.js" type="text/javascript"></script>

<div class="row gallery">
<?php foreach ($row->gallery as $key => $photo) : ?>
  <div class="col-md-3">
    <a href="<?php echo $photo->image; ?>" class="thumbnail" data-toggle="gallery">
      <img class="img-gallery img-responsive" src="<?php echo $photo->thumbnail; ?>" alt="<?php echo $key;?>" />
    </a>
  </div>
<?php endforeach; ?>
</div>

<?php /*
$(document).on('click', 'a.controls', function(){
            var index = $(this).attr('href');
            var src = $('ul.row li:nth-child('+ index +') img').attr('src');

            $('.modal-body img').attr('src', src);

            var newPrevIndex = parseInt(index) - 1;
            var newNextIndex = parseInt(newPrevIndex) + 2;

            if($(this).hasClass('previous')){
                $(this).attr('href', newPrevIndex);
                $('a.next').attr('href', newNextIndex);
            }else{
                $(this).attr('href', newNextIndex);
                $('a.previous').attr('href', newPrevIndex);
            }

            var total = $('ul.row li').length + 1;
            //hide next button
            if(total === newNextIndex){
                $('a.next').hide();
            }else{
                $('a.next').show()
            }
            //hide previous button
            if(newPrevIndex === 0){
                $('a.previous').hide();
            }else{
                $('a.previous').show()
            }


            return false;
        });

 */?>