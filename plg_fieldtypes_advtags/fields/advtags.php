<?php
/**
 * @package    Field Types - Advanced Tags Plugin
 * @version    1.0.5
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
	 * Root items whiteout checkbox title
	 *
	 * @var   boolean
	 * @since  1.0.0
	 */
	protected $root_titles = false;

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
	 *
	 * @since   1.0.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		if ($return)
		{
			$this->parents     = (!empty($this->element['parents'])) ? (string) $this->element['parents'] : '';
			$this->ids         = (!empty($this->element['ids'])) ? (string) $this->element['ids'] : '';
			$this->show_null   = (!empty($this->element['show_null']) && (string) $this->element['show_null'] == 'true');
			$this->root_titles = (!empty($this->element['root_titles']) && (string) $this->element['root_titles'] == 'true');
			$this->layout      = (!empty($this->element['layout'])) ? (string) $this->element['layout'] : $this->layout;
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

			// Published and Access
			if ($app->isSite())
			{
				$query->where($db->quoteName('published') . ' = ' . 1)
					->where('access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')');
			}

			$only_title = array();
			// Parents
			if (!empty($this->parents))
			{
				$tree = $this->getTagsTree();
				$query->where($db->quoteName('id') . ' IN (' . implode(',', $tree) . ')');
			}
			// Ids
			elseif (!empty($this->ids))
			{
				$include    = $this->getTagsInclude();
				$ids        = implode(',', $include->ids);
				$only_title = array_unique(array_merge($only_title, $include->only_title));
				$query->where($db->quoteName('id') . ' IN (' . $ids . ')');
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
				$option->images     = new Registry($option->images);
				$option->text       = $option->title;
				$option->key        = $option->id;
				$option->id         = $this->id . '_' . $option->id;
				$option->value      = $option->key;
				$option->name       = $this->name;
				$option->parent     = $option->parent_id;
				$option->only_title = (in_array($option->key, $only_title) ||
					($this->root_titles && $option->level == 1 && !empty($option->key)));
				$option->treename   = str_repeat('- ', ($option->level - 1)) . $option->text;
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
	 * Method to get tags
	 *
	 * @param array $pks tags ids
	 *
	 * @return object   * ids         all ids array
	 *                 * only_title  id tags to show without checkbox
	 *
	 * @since  1.0.0
	 */
	protected function getTagsInclude($pks = null)
	{
		$result             = new stdClass();
		$result->ids        = array();
		$result->only_title = array();

		$pks = (!empty($pks)) ? $pks : $this->ids;
		if (!empty($pks))
		{
			$ids   = (!is_array($pks)) ? explode(',', $pks) : $pks;
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select(array('id', 'parent_id', 'level'))
				->from($db->quoteName('#__tags'))
				->where($db->quoteName('alias') . ' <> ' . $db->quote('root'))
				->where('(' . $db->quoteName('id') . ' IN (' . implode(',', $ids) . ') OR '
					. '(' . $db->quoteName('level') . ' = ' . $db->quote(3) . ' AND ' .
					$db->quoteName('parent_id') . ' IN (' . implode(',', $ids) . ')))');
			$db->setQuery($query);
			$tags = $db->loadObjectList('id');

			foreach ($tags as $id => $tag)
			{
				$result->ids[] = $id;
				if ($tag->parent_id != 1 && !isset($tags[$tag->parent_id]) && !in_array($tag->parent_id, $result->ids))
				{
					$result->ids[]        = $tag->parent_id;
					$result->only_title[] = $tag->parent_id;
				}
			}
		}

		return $result;
	}

	/**
	 * Method to get tags tree array
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

		// Published and Access
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