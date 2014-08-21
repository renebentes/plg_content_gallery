<?php
/**
 * @package     Gallery
 * @subpackage  plg_content_galler
 * @copyright   Copyright (C) 2014 Rene Bentes Pinto, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access!');

// Include dependencies
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Script file of Gallery Plugin
 *
 * @package     Gallery
 * @subpackage  plg_content_gallery
 *
 * @since       2.5
 */
class PlgContentGalleryInstallerScript
{
  /**
   * Extension name
   *
   * @var string
   */
  private $_extension = 'plg_content_gallery';

  /**
   * Version release
   *
   * @vare string
   */
  private $_release = '';

  /**
   * Array of sub extensions package
   *
   * @var array
   */
  private $_subextensions = array(
    'modules' => array(
    ),
    'plugins' => array(
    )
  );

  /**
   * Array of obsoletes files and folders
   *
   * @var array
   */
  private $_obsoletes = array(
    'files' => array(
      'plugins/content/gallery/assets/gallery.css',
      'plugins/content/gallery/assets/js/blueimp-gallery.js',
      'plugins/content/gallery/assets/js/blueimp-gallery.min.js',
    ),
    'folders' => array(
    )
  );

  /**
   * Method to install the plugin
   *
   * @param JInstaller $parent
   */
  function install($parent)
  {
    echo JText::sprintf('PLG_CONTENT_GALLERY_INSTALL_TEXT', $this->_extension, $this->_release);
  }

  /**
   * Method to uninstall the plugin
   *
   * @param JInstaller $parent
   */
  function uninstall($parent)
  {
    echo JText::sprintf('PLG_CONTENT_GALLERY_UNINSTALL_TEXT', $this->_extension, $this->_release);
  }

  /**
   * Method to update the plugin
   *
   * @param JInstaller $parent
   */
  function update($parent)
  {
    echo JText::sprintf('PLG_CONTENT_GALLERY_UPDATE_TEXT', $this->_extension, $this->_release);
  }

  /**
   * Method to run before an install/update/uninstall method
   *
   * @param string     $type Installation type (install, update, discover_install)
   * @param JInstaller $parent Parent object
   */
  function preflight($type, $parent)
  {
    $this->_checkCompatible($type, $parent);
  }

  /**
   * Method to run after an install/update/uninstall method
   *
   * @param string     $type install, update or discover_update
   * @param JInstaller $parent
   */
  function postflight($type, $parent)
  {
    $this->_removeObsoletes($this->_obsoletes);
  }

  /**
   * Method for checking compatibility installation environment
   *
   * @param  JInstaller   $parent Parent object
   *
   * @return bool         True if the installation environment is compatible
   */
  private function _checkCompatible($type, $parent)
  {
    // Get the application.
    $app            = JFactory::getApplication();
    $this->_release = (string) $parent->get('manifest')->version;
    $min_version    = (string) $parent->get('manifest')->attributes()->version;
    $jversion       = new JVersion;

    if (version_compare($jversion->getShortVersion(), $min_version, 'lt' ))
    {
      $app->enqueueMessage(JText::sprintf('PLG_CONTENT_GALLERY_VERSION_UNSUPPORTED', $this->_extension, $this->_release, $min_version), 'error');
      return false;
    }

    // Storing old release number for process in postflight.
    if ($type == 'update')
    {
      $oldRelease = $this->_getParam('version');

      if (version_compare($this->_release, $oldRelease, 'le'))
      {
        $app->enqueueMessage(JText::sprintf('PLG_CONTENT_GALLERY_UPDATE_UNSUPPORTED', $this->_extension, $oldRelease, $this->_release), 'error');
        return false;
      }
    }
  }

  /**
   * Removes obsolete files and folders
   *
   * @param array $obsoletes
   */
  private function _removeObsoletes($obsoletes = array())
  {
    // Remove files
    if(!empty($obsoletes['files']))
    {
      foreach($obsoletes['files'] as $file)
      {
        $f = JPATH_ROOT . '/' . $file;
        if(!JFile::exists($f))
        {
          continue;
        }
        JFile::delete($f);
      }
    }

    // Remove folders
    if(!empty($obsoletes['folders']))
    {
      foreach($obsoletes['folders'] as $folder)
      {
        $f = JPATH_ROOT . '/' . $folder;
        if(!JFolder::exists($f))
        {
          continue;
        }
        JFolder::delete($f);
      }
    }
  }

  /**
   * Get a variable from the manifest cache.
   *
   * @param string $name Column name
   *
   * @return string Value of column name
   */
  private function _getParam($name)
  {
    $db    = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select($db->quoteName('manifest_cache'));
    $query->from($db->quoteName('#__extensions'));
    $query->where($db->quoteName('name') . ' = ' . $db->quote($this->_extension));
    $db->setQuery($query);

    $manifest = json_decode($db->loadResult(), true);

    return $manifest[$name];
  }
}