<?php
/**
 * @package     Gallery
 * @subpackage  plg_gallery
 * @copyright   Copyright (C) 2014 Rene Bentes Pinto, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access!');

?>

<div class="row gallery" data-title="<?php echo $row->title; ?>">
<?php foreach ($row->gallery as $key => $photo) : ?>
  <div class="col-md-3">
    <a href="<?php echo $photo->image; ?>" class="thumbnail" data-toggle="gallery" data-title="<?php echo $photo->title; ?>" data-description="<?php echo $photo->description; ?>">
      <img class="img-gallery img-responsive" src="<?php echo $photo->thumbnail; ?>" alt="<?php echo $photo->title;?>" title="<?php echo $photo->title;?>" />
    </a>
  </div>
<?php endforeach; ?>
</div>