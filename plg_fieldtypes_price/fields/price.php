<?php
/**
 * @package    Field Types - Price Plugin
 * @version    1.1.2
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

class JFormFieldPrice extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'price';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $layout = 'joomla.form.field.price.default';

	/**
	 * Show contract price checkbox
	 *
	 * @var    bool
	 *
	 * @since  1.0.0
	 */
	protected $contract_price = false;

	/**
	 * Show between layout
	 *
	 * @var    bool
	 *
	 * @since  1.0.0
	 */
	protected $between = false;

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
			$this->contract_price = (!empty($this->element['contract_price'])
				&& (string) $this->element['contract_price'] == 'true');

			$this->between = (!empty($this->element['between'])
				&& (string) $this->element['between'] == 'true');

			$this->value = (!empty($this->value)) ? $this->value : '';

			if ($this->between)
			{
				$this->layout = 'joomla.form.field.price.between';
				$this->value  = (!empty($this->value)) ? $this->value : array();
			}
		}

		return $return;
	}

	/**
	 * Method to get the field input markup for a price field.
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
		$data             = parent::getLayoutData();
		$data['value']    = $this->value;
		$data['checkbox'] = $this->contract_price;

		if (!$this->between)
		{
			if ($this->contract_price)
			{
				$data['value']   = ($this->value !== '-0') ? $this->value : '';
				$data['checked'] = ($this->value == '-0') ? ' checked' : '';
			}
		}

		return $data;
	}
}