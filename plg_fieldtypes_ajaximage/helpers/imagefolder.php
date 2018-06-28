<?php
/**
 * @package    Field Types - Ajax Image Plugin
 * @version    1.0.7
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class imageFolderHelper
{
	/**
	 * Image folders path
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	public $path = null;

	/**
	 * Method to instantiate the imagefolder helper
	 *
	 * @param string $path to image folders
	 *
	 * @since  1.0.0
	 */
	public function __construct($path = null)
	{
		$this->path = (!empty($path)) ? trim($path, '/') : 'images/others';

		$this->checkFolder();
	}

	/**
	 * Check if folder exist if not exist create
	 *
	 * @param string $path Simple path to folder (etc images/others)
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function checkFolder($path = null)
	{
		$path = (!(empty($path))) ? trim($path, '/') : $this->path;

		$folder = JPATH_ROOT . '/' . $path;
		if (!JFolder::exists($folder))
		{
			JFolder::create($folder);
			JFile::write($folder . '/index.html', '<!DOCTYPE html><title></title>');
		}

		return $path;
	}

	/**
	 * Create Temporary image folder
	 *
	 * @param int    $pk   Item id;
	 * @param string $path Simple path to folder (etc images/others)
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function getItemImageFolder($pk = null, $path = null)
	{
		$path = (!(empty($path))) ? trim($path, '/') : $this->path;

		$folder = (!empty($pk)) ? $this->checkFolder($path . '/' . $pk) : $this->createTemporaryFolder($path);

		return $folder;
	}

	/**
	 * Create Temporary image folder
	 *
	 * @param int    $pk   Item id;
	 * @param string $path Simple path to folder (etc images/others)
	 *
	 * @return bool
	 *
	 * @since  1.0.0
	 */
	public function deleteItemImageFolder($pk, $path = null)
	{
		if (empty($pk))
		{
			return false;
		}

		$path   = (!(empty($path))) ? trim($path, '/') : $this->path;
		$folder = JPATH_ROOT . '/' . $path . '/' . $pk;

		return (JFolder::exists($folder)) ? JFolder::delete($folder) : true;
	}

	/**
	 * Create Temporary image folder
	 *
	 * @param string $path Simple path to folder (etc images/others)
	 *
	 * @return string
	 *
	 * @since  1.0.0
	 */
	public function createTemporaryFolder($path = null)
	{
		$path = (!(empty($path))) ? trim($path, '/') : $this->path;

		$result = $path . '/tmp_' . uniqid();
		$folder = JPATH_ROOT . '/' . $result;
		while (JFolder::exists($folder))
		{
			$result = $path . '/tmp_' . uniqid();
			$folder = JPATH_ROOT . '/' . $path;
		}

		JFolder::create($folder);
		JFile::write($folder . '/index.html', '<!DOCTYPE html><title></title>');

		return $result;
	}

	/**
	 * Save item images
	 *
	 * @param  int          $pk     Item id
	 * @param  string       $folder Image folder
	 * @param  string       $table  Table name
	 * @param  string       $column Table column name
	 * @param  string|array $value  Images value
	 *
	 * @return bool
	 *
	 * @since  1.0.0
	 */
	public function saveItemImages($pk, $folder = null, $table, $column, $value = null)
	{
		if (empty($pk))
		{
			return false;
		}

		if (empty($folder))
		{
			$folder = $this->getItemImageFolder($pk);
		}
		$update = (preg_match('/tmp_/', $folder)) ? $this->getItemImageFolder($pk) : false;

		if ($update)
		{
			if (is_string($value))
			{
				$old   = JPATH_ROOT . '/' . $value;
				$value = str_replace($folder, $update, $value);
				$new   = JPATH_ROOT . '/' . $value;
				JFile::move($old, $new);
			}
			elseif (is_array($value))
			{
				foreach ($value as &$image)
				{
					$old          = JPATH_ROOT . '/' . $image['src'];
					$image['src'] = str_replace($folder, $update, $image['src']);
					$new          = JPATH_ROOT . '/' . $image['src'];

					$this->checkFolder(pathinfo($image['src'])['dirname']);
					JFile::move($old, $new);
				}
			}
		}

		if ($update && JFolder::exists(JPATH_ROOT . '/' . $folder) &&
			count(JFolder::files(JPATH_ROOT . '/' . $folder, '', true, true, array('index.html'))) == 0)
		{
			JFolder::delete(JPATH_ROOT . '/' . $folder);
		}

		return $this->saveImagesValue($pk, $table, $column, $value);
	}

	/**
	 * Save item images values in database
	 *
	 * @param  int          $pk     Item id
	 * @param  string       $table  Table name
	 * @param  string       $column Table column name
	 * @param  string|array $value  Images value
	 *
	 * @return bool
	 *
	 * @since  1.0.0
	 */
	public function saveImagesValue($pk, $table, $column, $value = null)
	{
		if (!empty($pk) && !empty($table) && !empty($column))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('id')
				->from($table)
				->where('id = ' . $pk);
			$db->setQuery($query);
			$exist = (!empty($db->loadResult()));

			if (is_array($value))
			{
				$registry = new Registry($value);
				$value    = (string) $registry;
			}

			$update          = new stdClass();
			$update->id      = $pk;
			$update->$column = $value;

			return ($exist) ? $db->updateObject($table, $update, 'id') :
				$db->insertObject($table, $update);
		}

		return false;
	}
}