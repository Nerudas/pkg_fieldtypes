<?php
/**
 * @package    Field Types - Ajax Image Plugin
 * @version    1.0.7
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

class PlgFieldTypesAjaxImageInstallerScript
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
		$plugin  = JPATH_PLUGINS . '/fieldtypes/ajaximage/layouts';
		$layouts = JPATH_ROOT . '/layouts/joomla/form/field/ajaximage';
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
		JFolder::delete(JPATH_ROOT . '/layouts/joomla/form/field/ajaximage');
	}
}