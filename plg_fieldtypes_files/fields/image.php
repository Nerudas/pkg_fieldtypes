<?php
/**
 * @package    Field Types - Files Plugin
 * @version    1.1.3
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

class JFormFieldImage extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.2.0
	 */
	protected $type = 'image';

	/**
	 * Folder field name
	 *
	 * @var    string
	 *
	 * @since  1.2.0
	 */
	protected $folder_field = 'images_folder';

	/**
	 * File name
	 *
	 * @var    string
	 *
	 * @since  1.2.0
	 */
	protected $filename = 'image';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 *
	 * @since  1.2.0
	 */
	protected $layout = 'joomla.form.field.files.image';

	/**
	 * Noimage file
	 *
	 * @var    string
	 *
	 * @since  1.1.3
	 */
	protected $noimage = 'media/plg_fieldtypes_files/images/noimage.jpg';


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
	 * @see     FormField::setup()
	 *
	 * @since   1.2.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		if ($return = parent::setup($element, $value, $group))
		{
			$this->filename     = (!empty($this->element['filename'])) ? (string) $this->element['filename'] : '';
			$this->folder_field = (!empty($this->element['folder_field'])) ? (string) $this->element['folder_field'] : '';
			$this->noimage      = (!empty($this->element['noimage'])) ? (string) $this->element['noimage'] :
				'media/plg_fieldtypes_files/images/noimage.jpg';
		}

		return $return;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.2.0
	 */
	protected function getInput()
	{
		return (!empty($this->folder_field) && !empty($this->form->getField($this->folder_field)->id)) ?
			parent::getInput() : false;
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since  1.2.0
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$params                 = array();
		$params['folder_field'] = (empty($this->folder_field)) ? false :
			$this->form->getField($this->folder_field)->id;
		$params['noimage']      = (!empty($this->noimage)) ? $this->noimage : '';
		$params['filename']     = $this->filename;
		$params['site_root']    = trim(Uri::root(true), '/') . '/';

		Factory::getDocument()->addScriptOptions($this->id, $params);

		return $data;
	}
}