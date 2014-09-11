<?php
/**
 * @package     Gallery
 * @subpackage  plg_gallery
 * @copyright   Copyright (C) 2014 Rene Bentes Pinto, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access!');

/**
 * Gallery Content plugin
 *
 * @package     Gallery
 * @subpackage  plg_content_gallery
 * @since       2.5
 */
class plgContentGallery extends JPlugin
{
  /**
   * Affects constructor behavior. If true, language files will be loaded automatically.
   *
   * @var    boolean
   * @since  3.1
   */
  protected $autoloadLanguage = true;

  /**
   * Constructor
   *
   * @param   object  &$subject  The object to observe
   * @param   array   $config    An array that holds the plugin configuration
   *
   * @since   2.5
   */
  public function __construct(&$subject, $config)
  {
    parent::__construct($subject, $config);

    // Load the language files if needed.
    if ($this->autoloadLanguage)
    {
      $this->loadLanguage();
    }
  }

  /**
   * Method called to prepare content before html output
   *
   * @param string  $context    The context of the content being passed to the plugin.
   * @param object  &$item      The content object.  Note $item->text is also available
   * @param object  &$params    The content params.
   * @param integer $page       The 'page' number.
   *
   * @return mixed Returns void on success or false otherwise
   *
   * @since 2.5
   */
  public function onContentPrepare($context, &$row, &$params, $page = 0)
  {
    require_once dirname(__FILE__) . '/helper.php';

    // Simple performance check to determine whether bot should process further.
    if (JString::strpos($row->text, '{gallery}') === false)
    {
      return;
    }

    $regex = "/{gallery}(.+?){\/gallery}/is";
    preg_match_all($regex, $row->text, $matches);

    if (!count($matches))
    {
      return;
    }

    // Get the path for the layout file
    $version = new JVersion;
    if ($version->isCompatible(3.0)) {
      $path = JPluginHelper::getLayoutPath('content', 'gallery');
    }
    else {
      $path = GalleryHelper::getLayoutPath('content', 'gallery');
    }

    foreach ($matches[1] as $source)
    {
      $row->gallery = GalleryHelper::getGallery($source,'200x160', true, 90, 86400, $row->id);
      ob_start();
      include $path;
      $html = ob_get_contents();
      ob_end_clean();

      $regex = '/{gallery}' . str_replace('.', '\.', str_replace('/', '\/', $source)) . '{\/gallery}/is';

      $row->text = preg_replace($regex, $html, $row->text);
    }


    return true;
  }

  /**
   * Method is called by the view and the results are imploded and displayed in a placeholder
   *
   * @param string  $context    The context of the content passed to the plugin.
   * @param object  &$item      The content object.  Note $item->text is also available
   * @param object  &$params    The content params
   * @param integer $page       The 'page' number
   *
   * @return string
   *
   * @since 2.5
   */
  public function onContentBeforeDisplay($context, &$item, &$params, $page = 0)
  {
    JFactory::getDocument()->addStylesheet(JUri::root(true) . '/plugins/content/gallery/assets/css/gallery.css');
    echo '<script src="' . JUri::root(true) . '/plugins/content/gallery/assets/js/gallery.js" type="text/javascript"></script>';

    return '';
  }
}