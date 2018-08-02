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

use Joomla\Registry\Registry;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

class JFormFieldPhones extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'phones';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $layout = 'joomla.form.field.phones';

	/**
	 * Phones limit
	 *
	 * @var   int
	 *
	 * @since  1.0.0
	 */
	protected $limit;

	/**
	 * Show description field
	 *
	 * @var    bool
	 *
	 * @since  1.0.0
	 */
	protected $text;

	/**
	 * Description placeholder
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $text_placeholder;

	/**
	 * Description Icon
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $text_icon;

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
		$return = parent::setup($element, $value, $group);
		if ($return)
		{
			$this->limit = (!empty($this->element['limit'])) ? (int) $this->element['limit'] : 0;

			$this->text = (!empty($this->element['text']) &&
				((string) $this->element['text'] == 'true' || (string) $this->element['text'] == 1));

			$this->text_placeholder = (!empty($this->element['text_placeholder'])) ?
				(string) $this->element['text_placeholder'] : '';

			$this->text_icon = (!empty($this->element['text_icon'])) ?
				(string) $this->element['text_icon'] : '';

			if (Factory::getApplication()->isAdmin())
			{
				$this->text_icon = (!empty($this->element['text_icon_admin'])) ?
					(string) $this->element['text_icon_admin'] : '';
			}
			if (Factory::getApplication()->isSite())
			{
				$this->text_icon = (!empty($this->element['text_icon_site'])) ?
					(string) $this->element['text_icon_site'] : '';
			}
		}

		$registry    = new Registry($this->value);
		$this->value = $registry->toArray();

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

		$data['text']             = $this->text;
		$data['text_placeholder'] = $this->text_placeholder;
		$data['text_icon']        = $this->text_icon;
		$data['limit']            = $this->limit;
		$params                   = array();
		$params['limit']          = $this->limit;
		Factory::getDocument()->addScriptOptions($this->id, $params);

		return $data;
	}
}

