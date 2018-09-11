<?php
/**
 * @package    Field Types - Ajax Image Plugin
 * @version    1.1.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

class JFormFieldImageFolder extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'ImageFolder';

	/**
	 * Image folder
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $folder = null;

	/**
	 * This helper
	 *
	 * @var    new imageFolderHelper
	 *
	 * @since  1.0.0
	 */
	protected $helper = null;

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
	 * @see     JFormField::setup()
	 *
	 * @since   1.0.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$this->element['readonly'] = 'true';
		if ($return = parent::setup($element, $value, $group))
		{
			if (empty($this->value))
			{
				JLoader::register('imageFolderHelper', JPATH_PLUGINS . '/fieldtypes/ajaximage/helpers/imagefolder.php');
				$this->helper = new imageFolderHelper((!empty($this->element['path'])) ? (string) $this->element['path'] : '');

				$this->value = $this->helper->getItemImageFolder($this->form->getValue('id'));
			}
		}

		if (Factory::getApplication()->isSite())
		{
			$this->hidden = true;
			$this->layout = 'joomla.form.field.hidden';
		}
		else
		{
			$this->element['label'] = 'PLG_FIELDTYPES_IMAGEFOLDER_LABEL';
			$this->layout           = 'joomla.form.field.text';
		}

		return $return;
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since  1.0.0
	 */
	protected function getLayoutData()
	{
		$data             = parent::getLayoutData();
		$data['options']  = array('read');
		$data['dirname']  = '';
		$data['readonly'] = true;

		return $data;
	}
}