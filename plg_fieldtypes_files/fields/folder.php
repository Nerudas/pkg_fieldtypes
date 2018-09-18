<?php
/**
 * @package    Field Types - Files Plugin
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

Joomla\CMS\Form\FormHelper::loadFieldClass('text');

class JFormFieldFolder extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  0.7.0
	 */
	protected $type = 'folder';

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement $element   The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed            $value     The form field value to validate.
	 * @param   string           $group     The field name group control value. This acts as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.2.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		// Set readonly
		$element['class']    = (!empty($element['class'])) ? $element['class'] . ' readonly' : ' readonly';
		$element['readonly'] = 'true';

		// Set value
		if (empty($value))
		{
			JLoader::register('FieldTypesFilesHelper', JPATH_PLUGINS . '/fieldtypes/files/helper.php');

			$helper = new FieldTypesFilesHelper();
			$root   = $element['root'];
			$pk     = $this->form->getValue('id');

			$value = $helper->getItemFolder($pk, $root);
		}

		return parent::setup($element, $value, $group);
	}
}