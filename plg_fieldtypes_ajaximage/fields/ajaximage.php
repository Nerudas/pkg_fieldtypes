<?php
/**
 * @package    Field Types - Ajax Image Plugin
 * @version    1.0.5
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class JFormFieldAjaxImage extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'ajaximage';

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
	 * Save url
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $saveurl;

	/**
	 * Subfolder
	 *
	 * @var   string
	 *
	 * @since  1.0.0
	 */
	protected $subfolder;

	/**
	 * Subfolder
	 *
	 * @var   string
	 *
	 * @since  1.0.0
	 */
	protected $prefix;

	/**
	 * No image
	 *
	 * @var   string
	 *
	 * @since  1.0.0
	 */
	protected $noimage;

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $layout = 'joomla.form.field.ajaximage.simple';

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
		if ($return = parent::setup($element, $value, $group))
		{
			$this->saveurl   = (!empty($this->element['saveurl'])) ? (string) $this->element['saveurl'] : '';
			$this->subfolder = (!empty($this->element['subfolder'])) ? (string) $this->element['subfolder'] : '';
			if ($this->multiple)
			{
				$this->text   = (!empty($this->element['text']) &&
					((string) $this->element['text'] == 'true' || (string) $this->element['text'] == 1));
				$this->unique = (!empty($this->element['unique']) &&
					((string) $this->element['unique'] == 'true') || (string) $this->element['unique'] == 1);
				$this->prefix = (!empty($this->element['prefix'])) ? (string) $this->element['prefix'] : '';
				$this->limit  = (!empty($this->element['limit'])) ? (int) $this->element['limit'] : 0;
			}
			else
			{
				$this->noimage  = (!empty($this->element['noimage'])) ?
					(string) $this->element['noimage'] : '';
				$this->filename = (!empty($this->element['filename'])) ?
					(string) $this->element['filename'] : '';
			}
		}
		if ($this->multiple)
		{
			$this->layout = 'joomla.form.field.ajaximage.multiple';
			$registry     = new Registry($this->value);
			$this->value  = $registry->toArray();
		}


		return $return;
	}

	/**
	 * Method to get the field input markup for a image list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since  1.0.0
	 */
	protected function getInput()
	{
		$renderer = $this->getRenderer($this->layout);

		return $renderer->render($this->getLayoutData());

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
		$data = parent::getLayoutData();

		if ($this->multiple)
		{
			$data['text']  = $this->text;
			$data['limit'] = $this->limit;
		}

		$params              = array();
		$params['multiple']  = $this->multiple;
		$params['name']      = $this->name;
		$params['saveurl']   = $this->saveurl;
		$params['subfolder'] = $this->subfolder;
		$params['removeurl'] = Uri::base(true) . '/index.php?option=com_ajax&plugin=ajaximage&group=fieldtypes&task=remove&format=json';
		$params['uploadurl'] = Uri::base(true) . '/index.php?option=com_ajax&plugin=ajaximage&group=fieldtypes&task=upload&format=json';

		if ($this->multiple)
		{
			$params['text']   = $this->text;
			$params['unique'] = $this->unique;
			$params['prefix'] = $this->prefix;
			$params['limit']  = $this->limit;
		}
		else
		{
			$params['noimage']  = (!empty($this->noimage)) ? '/' . $this->noimage : '';
			$params['image']    = (!empty($this->value)) ? '/' . $this->value : '';
			$params['filename'] = $this->filename;
		}

		Factory::getDocument()->addScriptOptions($this->id, $params);

		return $data;

	}
}