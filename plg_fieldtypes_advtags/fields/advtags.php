<?php
/**
 * @package    Field Types - Advanced Tags Plugin
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;

class JFormFieldAdvTags extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'advtags';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $layout = 'joomla.form.field.advtags.blocks';

	/**
	 * First level tags to show
	 *
	 * @var   string
	 * @since  1.0.0
	 */
	protected $parents;

	/**
	 * Ids tags to show
	 *
	 * @var   string
	 * @since  1.0.0
	 */
	protected $ids;

	/**
	 * Show null option
	 *
	 * @var    bool
	 * @since  1.0.0
	 */
	protected $show_null = false;

	/**
	 * Options array
	 *
	 * @var   array
	 * @since  1.0.0
	 */
	protected $_options = null;

	/**
	 * Root Options
	 *
	 * @var   array
	 * @since  1.0.0
	 */
	protected $_root = null;

	/**
	 * Tags parents array
	 *
	 * @var   array
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
	 * @since  1.0.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		if ($return)
		{
			$this->parents   = (!empty($this->element['parents'])) ? (string) $this->element['parents'] : '';
			$this->ids       = (!empty($this->element['ids'])) ? (string) $this->element['ids'] : '';
			$this->show_null = (!empty($this->element['show_null']) && (string) $this->element['show_null'] == 'true');
			$this->layout    = (!empty($this->element['layout'])) ? (string) $this->element['layout'] : $this->layout;
		}

		$value = $this->value;
		if (is_object($value))
		{
			$value = (!empty($value->tags)) ? $value->tags : '';
		}

		if ($this->multiple && is_string($value))
		{
			$value = explode(',', $value);
		}
		$value = (empty($value) && $this->multiple) ? array() : $value;

		$this->value = $value;


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
	 * @throws Exception
	 */
	protected function getLayoutData()
	{
		$data             = parent::getLayoutData();
		$data['options']  = $this->getOptions();
		$data['children'] = $this->getChildren();
		$data['root']     = $this->getRoot();
		$data['level']    = 1;
		$data['value']    = $this->value;

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
			$children = $this->getChildren();

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
			$app   = Factory::getApplication();
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select(array('id', 'title', 'images', 'parent_id', 'published', 'lft', 'level'))
				->from($db->quoteName('#__tags'))
				->where($db->quoteName('alias') . ' <> ' . $db->quote('root'));

			// Aublished and Access
			if ($app->isSite())
			{
				$query->where($db->quoteName('published') . ' = ' . 1)
					->where('access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')');
			}

			// Parents
			if (!empty($this->parents))
			{
				$tree = $this->getTagsTree();
				$query->where($db->quoteName('id') . ' IN (' . implode(',', $tree) . ')');
			}
			// Ids
			elseif (!empty($this->ids))
			{
				$query->where($db->quoteName('id') . ' IN (' . $this->ids . ')');
			}

			$query->order('lft ASC');
			$db->setQuery($query);
			$options = $db->loadObjectList('id');

			// null option
			if ($this->show_null)
			{
				$null            = new stdClass();
				$null->id        = '';
				$null->title     = Text::_('JOPTION_SELECT_TAG');
				$null->images    = '';
				$null->parent_id = 1;
				$null->published = 1;
				$null->lft       = -100500;
				$null->level     = 1;
				array_unshift($options, $null);
			}

			foreach ($options as &$option)
			{
				$option->images   = new Registry($option->images);
				$option->text     = $option->title;
				$option->key      = $option->id;
				$option->id       = $this->id . '_' . $option->id;
				$option->value    = $option->key;
				$option->name     = $this->name;
				$option->parent   = $option->parent_id;
				$option->treename = str_repeat('- ', ($option->level - 1)) . $option->text;
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

			}

			$this->_options = $options;
		}

		return $this->_options;
	}

	/**
	 * Method to get options array
	 *
	 * @return  array.
	 *
	 * @since  1.0.0
	 */
	protected function getTagsTree()
	{
		$app   = Factory::getApplication();
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('t.id')
			->from($db->quoteName('#__tags', 't'))
			->join('INNER', '#__tags as this ON t.lft > this.lft AND t.rgt < this.rgt')
			->where('this.id IN (' . $this->parents . ')');

		// Aublished and Access
		if ($app->isSite())
		{
			$query->where($db->quoteName('t.published') . ' = ' . 1)
				->where($db->quoteName('this.published') . ' = ' . 1)
				->where('t.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')')
				->where('this.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')');
		}

		$db->setQuery($query);
		$childs  = $db->loadColumn();
		$parents = explode(',', $this->parents);
		$array   = array_merge($parents, $childs);

		return array_unique($array);
	}

}