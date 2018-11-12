<?php
/**
 * @package    Field Types - Advanced Tags Plugin
 * @version    1.1.7
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

class PlgFieldTypesAdvTagsInstallerScript
{

	/**
	 * Runs right after any installation action is preformed on the component.
	 *
	 * @param  string $type      Type of PostFlight action. Possible values are:
	 *                           - * install
	 *                           - * update
	 *                           - * discover_install
	 * @param         $parent    Parent object calling object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function postflight($type, $parent)
	{

		$plugin  = JPATH_PLUGINS . '/fieldtypes/advtags/layouts';
		$layouts = JPATH_ROOT . '/layouts/joomla/form/field/advtags';
		if (JFolder::exists($layouts))
		{
			JFolder::delete($layouts);
		}
		JFolder::move($plugin, $layouts);

		return true;
	}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @since  1.0.0
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
		JFile::delete(JPATH_ROOT . '/layouts/joomla/form/field/advtags.php');
	}

	/**
	 * This method is called after a component is updated.
	 *
	 * @param  \stdClass $parent - Parent object calling object.
	 *
	 * @return void
	 *
	 * @since  1.0.4
	 */
	public function update($parent)
	{
		$path = JPATH_ROOT . '/media/plg_fieldtypes_advtags/';
		if (JFile::exists($path . 'field.js'))
		{
			JFile::delete($path . 'field.js');
		}
		if (JFile::exists($path . 'field.min.js'))
		{
			JFile::delete($path . 'field.min.js');
		}
	}
}