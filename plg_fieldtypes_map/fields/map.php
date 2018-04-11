<?php
/**
 * @package    Field Types - Map Plugin
 * @version    1.0.4
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

class JFormFieldMap extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'map';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $layout = 'joomla.form.field.map.default';

	/**
	 * src to get placemark layout data
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $placemarkurl = '';


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
			$this->layout       = (!empty($this->element['layout'])) ? (string) $this->element['layout'] : $this->layout;
			$this->placemarkurl = (!empty($this->element['placemarkurl'])) ? (string) $this->element['placemarkurl'] : $this->layout;
		}

		if (empty($this->value))
		{
			$this->value = array();
		}
		if (empty($this->value['params']))
		{
			$this->value['params']              = array();
			$this->value['params']['center']    = '';
			$this->value['params']['latitude']  = '';
			$this->value['params']['longitude'] = '';
			$this->value['params']['zoom']      = '';
		}
		if (empty($this->value['placemark']))
		{
			$this->value['placemark']                = array();
			$this->value['placemark']['coordinates'] = '';
			$this->value['placemark']['latitude']    = '';
			$this->value['placemark']['longitude']   = '';
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
		$data          = parent::getLayoutData();
		$data['value'] = $this->value;

		$params = array();

		$region = $this->getRegionData();

		$params['latitude']     = ($region) ? $region->latitude : 60.58949999;
		$params['longitude']    = ($region) ? $region->longitude : 88.167;
		$params['center']       = array($params['latitude'], $params['longitude']);
		$params['zoom']         = ($region) ? $region->zoom : 6;
		$params['placemarkurl'] = (!empty($this->placemarkurl)) ? $this->placemarkurl : '';

		Factory::getDocument()->addScriptOptions($this->id, $params);

		return $data;
	}

	/**
	 * Method to get current region data
	 *
	 * @return mixed object| false
	 *
	 * @since  1.0.0
	 */
	protected function getRegionData()
	{
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_nerudas/models');
		$regionModel = JModelLegacy::getInstance('regions', 'NerudasModel');

		$region = $regionModel->getRegion(Factory::getApplication()->input->cookie->get('region'));

		return ($region) ? $region : false;
	}
}