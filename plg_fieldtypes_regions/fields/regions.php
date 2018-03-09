<?php
/**
 * @package    Field Types - Regions Plugin
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Access;

class JFormFieldRegions extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'regions';

	/**
	 * Show select all
	 *
	 * @var    bool
	 *
	 * @since  1.0.0
	 */
	protected $show_all = false;

	/**
	 * Show null option
	 *
	 * @var    bool
	 *
	 * @since  1.0.0
	 */
	protected $show_null = false;


	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $layout = 'joomla.form.field.regions.select';

	/**
	 * Current region id
	 *
	 * @var   int
	 *
	 * @since  1.0.0
	 */
	protected $current = null;


	/**
	 * Options array
	 *
	 * @var   array
	 *
	 * @since  1.0.0
	 */
	protected $_options = null;

	/**
	 * Root Options
	 *
	 * @var   array
	 *
	 * @since  1.0.0
	 */
	protected $_root = null;

	/**
	 * Tags parents array
	 *
	 * @var   array
	 *
	 * @since  1.0.0
	 */
	protected $_children = null;

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
			$this->show_all  = (!empty($this->element['show_all']) && (string) $this->element['show_all'] == 'true');
			$this->show_null = (!empty($this->element['show_null']) && (string) $this->element['show_null'] == 'true');
			$this->layout    = (!empty($this->element['layout'])) ? (string) $this->element['layout'] : $this->layout;
		}

		$app           = Factory::getApplication();
		$this->current = $app->input->cookie->get('region');

		if ($app->isSite() && empty($this->value) && $this->layout !== 'joomla.form.field.regions.current')
		{
			$current     = $app->input->cookie->get('region');
			$this->value = ($this->multiple) ? array($current) : $current;
		}

		return $return;
	}

	/**
	 * Method to get the field input markup for a tags list.
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
		$data['options']  = $this->getOptions();
		$data['children'] = $this->getChildren();
		$data['root']     = $this->getRoot();
		$data['level']    = 1;
		$data['current']  = $this->current;

		return $data;
	}

	/**
	 * Method to get options array
	 *
	 * @return  array.
	 *
	 * @since  1.0.0
	 */
	protected function getRoot()
	{
		if (!is_array($this->_root))
		{
			$children    = $this->getChildren();
			$this->_root = (!empty($children[1])) ? $children[1] : array();
		}

		return $this->_root;
	}

	/**
	 * Method to get options array
	 *
	 * @return  array.
	 *
	 * @since  1.0.0
	 */
	protected function getChildren()
	{
		if (!is_array($this->_children))
		{
			$options  = $this->getOptions();
			$children = array();
			foreach ($options as $option)
			{
				if (!isset($children[$option->key]))
				{
					$children[$option->key] = array();
				}
				if (!isset($children[$option->parent]))
				{
					$children[$option->parent] = array();
				}
				$children[$option->parent][$option->key] = $option;
			}
			$this->_children = $children;
		}

		return $this->_children;
	}

	/**
	 * Method to get options array
	 *
	 * @return  array.
	 *
	 * @since  1.0.0
	 */
	protected function getOptions()
	{
		if (!is_array($this->_options))
		{
			$app    = Factory::getApplication();
			$access = Access::getAuthorisedViewLevels(Factory::getUser()->id);

			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select(array('id', 'name', 'parent', 'false as disable'))
				->from($db->quoteName('#__regions'));

			// Published and Access
			if ($app->isSite())
			{
				$query->where($db->quoteName('published') . ' = ' . 1);
			}

			$query->order(array('parent', 'ordering'));
			$db->setQuery($query);
			$options = $db->loadObjectList('id');

			// show_all option
			if ($this->show_all)
			{
				$all          = new stdClass();
				$all->id      = '*';
				$all->name    = Text::_('JGLOBAL_FIELD_REGIONS_ALL');
				$all->parent  = 0;
				$all->disable = (!$app->isAdmin() && !in_array(3, $access));
				array_unshift($options, $all);
			}

			// null option
			if ($this->show_null)
			{
				$null          = new stdClass();
				$null->id      = '';
				$null->name    = Text::_('JGLOBAL_FIELD_REGIONS_NULL');
				$null->parent  = 0;
				$null->disable = false;
				array_unshift($options, $null);
			}

			foreach ($options as &$option)
			{
				$option->title     = $option->name;
				$option->parent_id = $option->parent;
				$option->text      = $option->title;
				$option->key       = $option->id;
				$option->id        = $this->id . '_' . $option->id;
				$option->value     = $option->key;
				$option->name      = $this->name;
				if ($this->multiple)
				{
					$option->checked  = (in_array($option->value, $this->value)) ? 'checked' : '';
					$option->selected = (in_array($option->value, $this->value)) ? 'selected' : '';
				}
				else
				{
					$option->checked  = ($option->value == $this->value) ? 'checked' : '';
					$option->selected = ($option->value == $this->value) ? 'selected' : '';
				}
				$option->parent_id = $option->parent;
			}
			$this->_options = $options;
		}

		return $this->_options;
	}
}