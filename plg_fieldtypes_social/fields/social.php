<?php
/**
 * @package    Field Types - Social Plugin
 * @version    1.0.8
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;


use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

class JFormFieldSocial extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'social';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $layout = 'joomla.form.field.social';

	/**
	 * Social Network
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $network;

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
			$this->network = (!empty($this->element['network'])) ?
				(string) $this->element['network'] : '';
		}

		$this->value = str_replace(array('http://', 'https://', '/', 'www.', $this->network), '', $this->value);

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
		$data            = parent::getLayoutData();
		$data['network'] = $this->network;

		$params            = array();
		$params['network'] = $this->network;

		Factory::getDocument()->addScriptOptions($this->id, $params);

		return $data;
	}
}