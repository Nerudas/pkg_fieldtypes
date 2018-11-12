<?php
/**
 * @package    System - Field Types Plugin
 * @version    1.1.7
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

jimport('joomla.filesystem.folder');

class PlgSystemFieldTypes extends CMSPlugin
{
	/**
	 * Adds additional languages files
	 *
	 * @since  1.0.0
	 */
	public function onAfterInitialise()
	{
		$plugins  = PluginHelper::getPlugin('fieldtypes');
		$language = Factory::getLanguage();

		foreach ($plugins as $plugin)
		{
			$language->load('plg_fieldtypes_' . $plugin->name, JPATH_ADMINISTRATOR, $language->getTag(), true);
		}
	}

	/**
	 * Adds additional fields & rules to From
	 *
	 * @param   Form  $form The form to be altered.
	 * @param   mixed $data The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since  1.0.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		$plugins = PluginHelper::getPlugin('fieldtypes');
		foreach ($plugins as $plugin)
		{
			$folder = JPATH_PLUGINS . '/fieldtypes/' . $plugin->name;

			// Add field types
			if (JFolder::exists($folder . '/fields'))
			{
				$form->addFieldPath($folder . '/fields');
			}

			// Add Rules
			if (JFolder::exists($folder . '/rules'))
			{
				$form->addRulePath($folder . '/rules');
			}
		}

		return true;
	}
}