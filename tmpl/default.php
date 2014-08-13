<?php
/**
 * @package     Gallery
 * @subpackage  plg_gallery
 * @copyright   Copyright (C) 2014 Rene Bentes Pinto, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access!');

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
$script[] = "    $('.previous').on('click', function () {";
$script[] = "      return false;";
$script[] = "    });";
$script[] = "  });";
$script[] = "}(jQuery);";

JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

?>
<?php foreach ($row->gallery as $key => $photo) :
  if ($key == 0) : ?>
    <div class="row">
  <?php endif; ?>
      <div class="col-md-3">
        <a href="<?php echo $photo->image; ?>" class="thumbnail">
          <img class="img-gallery img-responsive" src="<?php echo $photo->thumbnail; ?>" alt="<?php echo $key;?>" />
        </a>
      </div>
  <?php if ($key % 3 == 0 && $key != 0) :?>
    </div>
    <?php if ($key < count($row->gallery) - 1) : ?>
    <div class="row">
    <?php endif; ?>
  <?php else : ?>
    <?php if ($key == count($row->gallery) - 1) : ?>
    </div>
    <?php endif; ?>
  <?php endif; ?>
<?php endforeach; ?>

<div class="modal fade" id="gallery" tabindex="-1" role="dialog" aria-labelledby="galleryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close hasTooltip" data-original-title="<?php echo JText::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
        <h4 class="modal-title" id="galleryLabel"><?php echo $row->title; ?></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <ul class="pagination pagination-sm pull-right">
          <li>
            <a class="previous hasTooltip" href="#" data-original-title="<?php echo JText::_('JPREVIOUS');?>" role="button"><i class="fa fa-long-arrow-left"></i></a>
          </li>
          <li>
            <a class="next hasTooltip" href="#" data-original-title="<?php echo JText::_('JNEXT');?>" role="button"><i class="fa fa-long-arrow-right"></i></a>
          </li>
        </ul>
      </div>
    </div>
  </div>
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