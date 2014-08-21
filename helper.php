<?php
/**
 * @package     Gallery
 * @subpackage  plg_gallery
 * @copyright   Copyright (C) 2014 Rene Bentes Pinto, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access!');

jimport('joomla.filesystem.folder');

/**
* Gallery plugin helper class
*
* @package    Gallery
* @subpackage plg_gallery
* @since      2.5
*/
abstract class GalleryHelper
{
  /**
   * Get items to Gallery
   *
   * @param  string  $folder      Path to image files
   * @param  string  $size        Thumbnail size (width x height)
   * @param  boolean $smartResize Type of resize
   * @param  integer $quality     JPEG quality
   * @param  integer $expireTime  Cache time
   * @param  integer $id          Gallery identifier
   *
   * @return ob                Gallery items
   */
  public static function getGallery($folder, $size = '100x60', $smartResize = true, $quality = 90, $expireTime = 86400, $id)
  {
    $folder  = JPATH_SITE . '/images/' . $folder;

    return self::_createThumbnail($folder, $size, $smartResize, $quality, $expireTime, $id);
  }

  /**
   * Gets captions for items
   *
   * @param string $folder    The gallery path
   * @param string $reference The item gallery filename
   *
   * @return object           Captions
   */
  private static function _getCaption($folder, $reference)
  {
    $file = $folder . '/gallery.txt';
    $caption              = new JObject;
    $caption->title       = '';
    $caption->description = '';

    if (JFile::exists($file))
    {
      $content = file($file);

      foreach ($content as $line)
      {
        $temp = explode(';', $line);
        if (strtolower($temp[0]) == strtolower($reference))
        {
          $caption->title       = $temp[1];
          $caption->description = $temp[2];
          break;
        }
      }
    }
    return $caption;
  }

  /**
   * Get the path to a layout from Gallery
   *
   * @param   string  $type    Plugin type
   * @param   string  $name    Plugin name
   * @param   string  $layout  Layout name
   *
   * @return  string  Layout path
   *
   * @since   2.5
   */
  public static function getLayoutPath($type, $name, $layout = 'default')
  {
    $template      = JFactory::getApplication()->getTemplate();
    $defaultLayout = $layout;

    if (strpos($layout, ':') !== false)
    {
      // Get the template and file name from the string
      $temp          = explode(':', $layout);
      $template      = ($temp[0] == '_') ? $template : $temp[0];
      $layout        = $temp[1];
      $defaultLayout = ($temp[1]) ? $temp[1] : 'default';
    }

    // Build the template and base path for the layout
    $tPath = JPATH_THEMES . '/' . $template . '/html/plg_' . $type . '_' . $name . '/' . $layout . '.php';
    $bPath = JPATH_BASE . '/plugins/' . $type . '/' . $name . '/tmpl/' . $defaultLayout . '.php';
    $dPath = JPATH_BASE . '/plugins/' . $type . '/' . $name . '/tmpl/default.php';

    // If the template has a layout override use it
    if (file_exists($tPath))
    {
      return $tPath;
    }
    elseif (file_exists($bPath))
    {
      return $bPath;
    }
    else
    {
      return $dPath;
    }
  }

  /**
   * Creates thumbnails for images
   *
   * @param  string  $folder      Path to image files
   * @param  string  $size        Thumbnail size (width x height)
   * @param  boolean $smartResize Type of resize
   * @param  integer $quality     JPEG quality
   * @param  integer $expireTime  Cache time
   * @param  integer $id          Gallery identifier
   *
   * @return array                Gallery thumbnails
   */
  private static function _createThumbnail($folder, $size, $smartResize, $quality, $expireTime, $id)
  {
    if (!JFolder::exists($folder))
    {
      JFactory::getApplication()->enqueueMessage(JText::_('PLG_CONTENT_GALLERY_ERROR_PATH_NOT_FOUND'), 'error');
      return;
    }

    $files = JFolder::files($folder);

    if (!$files)
    {
      JFactory::getApplication()->enqueueMessage(JText::_('PLG_CONTENT_GALLERY_ERROR_FILES_NOT_FOUND'), 'error');
      return;
    }

    $size = explode('x', strtolower($size));

    if (count($size) != 2)
    {
      JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_CONTENT_GALLERY_ERROR_ARGUMENT_SIZE_INVALID', $size), 'error');
      return;
    }

    if (!is_numeric($size[0]) || $size[0] < 0)
    {
      JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_CONTENT_GALLERY_ERROR_WIDTH_INVALID', $size[0]), 'error');
      return;
    }

    if (!is_numeric($size[1]) || $size[1] < 0)
    {
      JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_CONTENT_GALLERY_ERROR_HEIGHT_INVALID', $size[1]), 'error');
      return;
    }

    $cache = JPATH_SITE . '/cache/plg_content_gallery/' . $id;

    if (JFolder::exists($cache) || JFolder::create($cache))
    {
      $fileTypes = array('jpg', 'jpeg', 'gif', 'png');
      $found     = array();

      foreach ($files as $file)
      {
        $fileInfo = pathinfo($file);
        if (array_key_exists('extension', $fileInfo) && in_array(strtolower($fileInfo['extension']), $fileTypes))
        {
          $found[] = $file;
        }
      }

      // Bail out if there are no images found
      if (count($found) == 0)
      {
        JFactory::getApplication()->enqueueMessage(JText::_('PLG_CONTENT_GALLERY_ERROR_IMAGES_NOT_FOUND'), 'error');
        return;
      }

      sort($found);
      $gallery = array();

      foreach ($found as $key => $filename)
      {
        $gallery[$key] = new JObject;

        if (self::_getCaption($folder, $filename))
        {
          $gallery[$key]->title       = self::_getCaption($folder, $filename)->title;
          $gallery[$key]->description = self::_getCaption($folder, $filename)->description;
        }

        $original             = $folder . '/' . $filename;
        $thumbname            = $cache . '/' . strtolower($filename);

        // Check if thumbnail exists already
        if (!(JFile::exists($thumbname) && is_readable($thumbname) && (filemtime($thumbname) + $expireTime) > time()))
        {
          list($width, $height, $type) = getimagesize($original);

          switch($type)
          {
            case 1 :
              $source = @ imagecreatefromgif($original);
              break;
            case 2 :
              $source = imagecreatefromjpeg($original);
              break;
            case 3 :
              $source = imagecreatefrompng($original);
              break;
            default :
              $source = NULL;
          }

          if (!$source)
          {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_CONTENT_GALLERY_ERROR_PROCESS_SOURCE'), 'error');
            return;
          }

          $thumbnail = self::_getDimension($width, $height, $size[0], $size[1], $smartResize);

          $thumb = imagecreatetruecolor($size[0], $size[1]);
          imagecopyresampled($thumb, $source, 0, 0, 0, 0, $size[0], $size[1], $width, $height);

          switch($type)
          {
            case 1 :
              $success = imagegif($thumb, $thumbname);
              break;
            case 2 :
              $success = imagejpeg($thumb, $thumbname, $quality);
              break;
            case 3 :
              $success = imagepng($thumb, $thumbname);
              break;
            default :
              $success = NULL;
          }

          // Bail out if there is a problem in the GD conversion
          if (!$success)
          {
            return;
          }

          // remove the image resources from memory
          imagedestroy($source);
          imagedestroy($thumb);
        }

        $gallery[$key]->filename  = $filename;
        $gallery[$key]->image     = str_replace(JPATH_SITE, JUri::base(true), $original);
        $gallery[$key]->thumbnail = str_replace(JPATH_SITE, JUri::base(true), $thumbname);
      }
    }

    return $gallery;
  }

  /**
   * Calculate the dimensions of the thumbnails
   *
   * @param  integer $width       Source width
   * @param  integer $height      Source height
   * @param  integer $thb_width   Thumbnail width
   * @param  integer $thb_height  Thumbnail height
   * @param  boolean $smartResize Resize type
   *
   * @return array                The thumbnail dimensions
   */
  private static function _getDimension($width, $height, $thb_width, $thb_height, $smartResize)
  {
    if ($smartResize)
    {
      // thumb ratio bigger that container ratio
      if ($width / $height > $thb_width / $thb_height)
      {
        // wide containers
        if ($thb_width >= $thb_height)
        {
          // wide thumbs
          if ($width > $height)
          {
            $thumb_width  = $thb_height * $width / $height;
            $thumb_height = $thb_height;
          }
          // high thumbs
          else
          {
            $thumb_width  = $thb_height * $width / $height;
            $thumb_height = $thb_height;
          }
          // high containers
        }
        else
        {
          // wide thumbs
          if ($width > $height)
          {
            $thumb_width  = $thb_height * $width / $height;
            $thumb_height = $thb_height;
          }
          // high thumbs
          else
          {
            $thumb_width  = $thb_height * $width / $height;
            $thumb_height = $thb_height;
          }
        }
      }
      else
      {
        // wide containers
        if ($thb_width >= $thb_height)
        {
          // wide thumbs
          if ($width > $height)
          {
            $thumb_width  = $thb_width;
            $thumb_height = $thb_width * $height / $width;
          }
          // high thumbs
          else
          {
            $thumb_width  = $thb_width;
            $thumb_height = $thb_width * $height / $width;
          }
          // high containers
        }
        else
        {
          // wide thumbs
          if ($width > $height)
          {
            $thumb_width  = $thb_height * $width / $height;
            $thumb_height = $thb_height;
          }
          // high thumbs
          else
          {
            $thumb_width  = $thb_width;
            $thumb_height = $thb_width * $height / $width;
          }
        }
      }
    }
    else
    {
      if ($width > $height)
      {
        $thumb_width  = $thb_width;
        $thumb_height = $thb_width * $height / $width;
      }
      elseif ($width < $height)
      {
        $thumb_width  = $thb_height * $width / $height;
        $thumb_height = $thb_height;
      }
      else
      {
        $thumb_width  = $thb_width;
        $thumb_height = $thb_height;
      }
    }

    $thumbnail           = array();
    $thumbnail['width']  = round($thumb_width);
    $thumbnail['height'] = round($thumb_height);

    return $thumbnail;

  }
}