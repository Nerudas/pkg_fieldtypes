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
use Joomla\Registry\Registry;

JLoader::register('FieldTypesFilesHelper', JPATH_PLUGINS . '/fieldtypes/files/helper.php');

class JFormFieldImages extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  0.7.0
	 */
	protected $type = 'images';

	/**
	 * Folder field name
	 *
	 * @var    string
	 *
	 * @since  0.7.0
	 */
	protected $folder_field = 'images_folder';

	/**
	 * Folder name
	 *
	 * @var    string
	 *
	 * @since  0.7.0
	 */
	protected $folder = 'content';

	/**
	 * Show description field
	 *
	 * @var    bool
	 *
	 * @since  1.0.0
	 */
	protected $text;

	/**
	 * unique name
	 *
	 * @var    bool
	 *
	 * @since  1.0.0
	 */
	protected $unique;

	/**
	 * Maximum images
	 *
	 * @var    int
	 *
	 * @since  1.0.0
	 */
	protected $limit;

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 *
	 * @since  0.7.0
	 */
	protected $layout = 'joomla.form.field.files.images';

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
	 * @since   0.7.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		if ($return = parent::setup($element, $value, $group))
		{
			$this->folder       = (!empty($this->element['folder'])) ? (string) $this->element['folder'] : '';
			$this->folder_field = (!empty($this->element['folder_field'])) ? (string) $this->element['folder_field'] : '';
			$this->text         = (!empty($this->element['text']) &&
				((string) $this->element['text'] == 'true' || (string) $this->element['text'] == 1));
			$this->unique       = (!empty($this->element['unique']) &&
				((string) $this->element['unique'] == 'true') || (string) $this->element['unique'] == 1);
			$this->limit        = (!empty($this->element['limit'])) ? (int) $this->element['limit'] : 0;
		}

		if (!is_array($this->value))
		{
			$registry    = new Registry($this->value);
			$this->value = $registry->toArray();
		}

		return $return;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   0.7.0
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
	 * @since  0.7.0
	 */
	protected function getLayoutData()
	{
		$data          = parent::getLayoutData();
		$data['text']  = $this->text;
		$data['limit'] = $this->limit;

		$params                 = array();
		$params['folder_field'] = (empty($this->folder_field)) ? false :
			$this->form->getField($this->folder_field)->id;
		$params['folder']       = $this->folder;
		$params['text']         = $this->text;
		$params['value']        = $this->value;
		$params['unique']       = $this->unique;
		$params['limit']        = $this->limit;
		$params['site_root']    = trim(Uri::root(true), '/') . '/';

		Factory::getDocument()->addScriptOptions($this->id, $params);

		return $data;
	}
}