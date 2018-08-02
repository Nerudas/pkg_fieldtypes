<?php
/**
 * @package    Field Types - Phones Plugin
 * @version    1.0.8
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

class PlgFieldTypesPhonesInstallerScript
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
	 *
	 * @since  1.0.0
	 */
	function postflight($type, $parent)
	{
		$file    = '/phones.php';
		$plugin  = JPATH_PLUGINS . '/fieldtypes/phones/layouts';
		$layouts = JPATH_ROOT . '/layouts/joomla/form/field';
		JFile::copy($plugin . $file, $layouts . $file);
		JFolder::delete($plugin);

		return true;
	}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @since  1.0.0
	 */
	public function uninstall($adapter)
	{
		JFile::delete(JPATH_ROOT . '/layouts/joomla/form/field/phones.php');
	}
}