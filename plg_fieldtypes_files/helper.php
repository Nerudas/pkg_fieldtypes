<?php
/**
 * @package    Field Types - Files Plugin
 * @version    1.1.5
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class FieldTypesFilesHelper
{
	/**
	 * Create Temporary image folder
	 *
	 * @param int    $pk   Item id;
	 * @param string $root Simple path to folder (etc images/others)
	 *
	 * @return string|bool
	 *
	 * @since  1.1.0
	 */
	public function getItemFolder($pk = null, $root = '')
	{
		if (empty($root))
		{
			return $root;
		}

		return (!empty($pk)) ? $this->checkFolder($root . '/' . $pk) : $this->createTemporaryFolder($root);
	}

	/**
	 * Delete item folder
	 *
	 * @param int    $pk   Item id;
	 * @param string $root Simple path to folder (etc images/others)
	 *
	 * @return bool
	 *
	 * @since  1.1.0
	 */
	public function deleteItemFolder($pk = null, $root = '')
	{
		$folder = JPATH_ROOT . '/' . $root . '/' . $pk;

		if (empty($pk) || empty($root) || !JFolder::exists($folder))
		{
			return false;
		}

		return JFolder::delete($folder);
	}


	/**
	 * Check if folder exist if not exist create
	 *
	 * @param string $path Simple path to folder (etc images/others)
	 *
	 * @return string
	 *
	 * @since  1.1.0
	 */
	public function checkFolder($path = '')
	{
		if (!empty($path))
		{
			$folder = JPATH_ROOT . '/' . $path;
			if (!JFolder::exists($folder))
			{
				JFolder::create($folder);
				JFile::write($folder . '/index.html', '<!DOCTYPE html><title></title>');
			}
		}

		return $path;
	}

	/**
	 * Create temporary folder
	 *
	 * @param string $root Simple path to folder (etc images/others)
	 *
	 * @return string
	 *
	 * @since  1.1.0
	 */
	public function createTemporaryFolder($root = '')
	{
		if (empty($root))
		{
			return $root;
		}

		$result = $root . '/tmp_' . uniqid();
		$folder = JPATH_ROOT . '/' . $result;
		while (JFolder::exists($folder))
		{
			$result = $root . '/tmp_' . uniqid();
			$folder = JPATH_ROOT . '/' . $root;
		}

		JFolder::create($folder);
		JFile::write($folder . '/index.html', '<!DOCTYPE html><title></title>');

		return $result;
	}

	/**
	 * Move temporary folder
	 *
	 * @param string $temporary Temporary Folder
	 * @param int    $pk        Item id;
	 * @param string $root      Simple path to folder (etc images/others)
	 *
	 * @return bool
	 *
	 * @since  1.1.0
	 */
	public function moveTemporaryFolder($temporary = null, $pk = null, $root = null)
	{
		if (empty($temporary) || empty($root) || empty($pk))
		{
			return false;
		}

		$old = JPATH_ROOT . '/' . $temporary;
		$new = JPATH_ROOT . '/' . $root . '/' . $pk;

		if (!JFolder::exists($old))
		{
			return false;
		}

		return JFolder::move($old, $new);
	}

	/**
	 * Get Image
	 *
	 * @param string $filename Filename
	 * @param string $folder   Simple path to file (etc images/others)
	 * @param string $noimage  Simple path to file (etc images/others)
	 * @param bool   $version  Add version to src
	 *
	 * @return bool|string
	 *
	 * @since  1.1.0
	 */
	public function getImage($filename = '', $folder = '', $noimage = null, $version = true)
	{
		if (empty($filename) || empty($folder))
		{
			return $noimage;
		}

		$this->checkFolder($folder);
		$path = JPATH_ROOT . '/' . $folder;

		$files = JFolder::files($path, $filename, false, false);
		if (!empty($files))
		{
			foreach ($files as $file)
			{
				if ($this->checkImage($file))
				{
					$src = $folder . '/' . $file;
					if ($version)
					{
						$src .= '?v=' . rand();
					}

					return $src;
				}
			}
		}

		return $noimage;
	}

	/**
	 * Get Image
	 *
	 * @param string $filename Filename
	 * @param string $folder   Simple path to file (etc images/others)
	 * @param string $noimage  Simple path to file (etc images/others)
	 *
	 * @return bool|string
	 *
	 * @since  1.1.0
	 */
	public function deleteImage($filename = '', $folder = '', $noimage = null)
	{
		if (empty($filename) || empty($folder))
		{
			return $this->getImage($filename, $folder, $noimage);
		}

		$path  = JPATH_ROOT . '/' . $folder;
		$files = JFolder::files($path, $filename, false, false);
		if (!empty($files))
		{
			foreach ($files as $file)
			{
				if ($this->checkImage($file))
				{
					JFile::delete($path . '/' . $file);
				}
			}
		}

		return $this->getImage($filename, $folder, $noimage);
	}

	/**
	 * Upload Image
	 *
	 * @param string $filename Filename
	 * @param string $folder   Simple path to file (etc images/others)
	 * @param array  $files    Files array
	 * @param string $noimage  Simple path to file (etc images/others)
	 *
	 * @return bool|string
	 *
	 * @since  1.1.0
	 */
	public function uploadImage($filename = '', $folder = '', $files = array(), $noimage = null)
	{

		if (empty($filename) || empty($folder))
		{
			return $this->getImage($filename, $folder, $noimage);
		}

		$file = array_shift($files);
		if (empty($file) || !$this->checkImage($file['name']))
		{
			return $this->getImage($filename, $folder, $noimage);
		}

		$this->checkFolder($folder);

		$this->deleteImage($filename, $folder, $noimage);

		$dest = JPATH_ROOT . '/' . $folder . '/' . $filename . '.' . JFile::getExt($file['name']);
		JFile::upload($file['tmp_name'], $dest, false, true);

		return $this->getImage($filename, $folder, $noimage);
	}

	/**
	 * Get Images
	 *
	 * @param string $folder      Images sub folder
	 * @param string $root_folder Simple path to files (etc images/others)
	 * @param array  $value       Exist images value
	 * @param array  $params      Parameters
	 *
	 * @return bool|array
	 *
	 * @since  1.1.0
	 */
	public function getImages($folder = '', $root_folder = '', $value = array(), $params = array())
	{
		$params = new Registry($params);

		if (empty($folder) || empty($root_folder))
		{
			return array();
		}

		$path = $root_folder . '/' . $folder;
		$this->checkFolder($path);

		$files = JFolder::files(JPATH_ROOT . '/' . $path, '', false, false);

		if (!empty($files))
		{
			$images = array();
			$count  = count($value);
			foreach ($files as $file)
			{
				if ($this->checkImage($file))
				{
					$val = (!empty($value[$file])) ? $value[$file] : false;

					$image             = new stdClass();
					$image->file       = $file;
					$image->src        = $path . '/' . $file;
					$image->text       = false;
					$image->filed_name = $params->get('filed_name', 'jform[images_default]') . '[' . $file . ']';

					if ($val && !empty($val['ordering']))
					{
						$image->ordering = $val['ordering'];
					}
					else
					{
						$image->ordering = $count + 1;
						$count++;
					}

					if ($params->get('text', false))
					{
						$image->text = ($val && !empty($val['text'])) ? $val['text'] : '';
					}

					$images[$file] = $image;
				}
			}
			if (!empty($images))
			{
				$images = ArrayHelper::sortObjects($images, 'ordering', 1);
			}

			return ($params->get('for_field', true)) ?
				LayoutHelper::render('joomla.form.field.files.images.items', $images) : $images;
		}

		return '';
	}


	/**
	 * Upload Images
	 *
	 * @param string $folder      Filename
	 * @param string $root_folder Simple path to file (etc images/others)
	 * @param array  $files       Files array
	 * @param array  $params      Parameters
	 *
	 * @return bool
	 *
	 * @since  1.1.0
	 */
	public function uploadImages($folder = '', $root_folder = '', $files = array(), $params = array())
	{
		$params = new Registry($params);
		$total  = $params->get('exist', 0);
		$limit  = $params->get('limit', 0);

		if (empty($folder) || empty($root_folder) || empty($files) || ($limit > 0 && $total >= $limit))
		{
			return false;
		}

		$path = $root_folder . '/' . $folder;
		$this->checkFolder($path);
		foreach ($files as $file)
		{
			if ($limit == 0 || $total < $limit)
			{
				if ($this->checkImage($file['name']))
				{
					$filename  = JFile::stripExt($file['name']);
					$filename  = OutputFilter::stringURLSafe($filename);
					$extension = JFile::getExt($file['name']);

					if ($params->get('unique', false))
					{
						$checkName = uniqid();
						while (JFile::exists(JPATH_ROOT . '/' . $path . '/' . $checkName . '.' . $extension))
						{
							$checkName = uniqid();
						}

						$filename = $checkName;
					}
					else
					{
						$ik        = 1;
						$checkName = $filename;
						while (JFile::exists(JPATH_ROOT . '/' . $path . '/' . $checkName . '.' . $extension))
						{
							$checkName = $filename . '_' . $ik;
							$ik++;
						}
						$filename = $checkName;
					}


					$dest = JPATH_ROOT . '/' . $path . '/' . $filename . '.' . $extension;
					if (JFile::upload($file['tmp_name'], $dest, false, true))
					{
						$total++;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Check if file is image
	 *
	 * @param string $image Simple path to image
	 *
	 * @return string
	 *
	 * @since  1.1.0
	 */
	public function checkImage($image = '')
	{
		$mediaHelper = new JHelperMedia;

		return $mediaHelper->isImage(JPATH_ROOT . '/' . $image);
	}

	/**
	 * Delete File in multiple fields
	 *
	 * @param string $file Path to file
	 *
	 * @return bool|string
	 *
	 * @since  1.1.0
	 */
	public function deleteFile($file)
	{
		if (empty($file))
		{
			return false;
		}

		$path = JPATH_ROOT . '/' . $file;
		if (JFile::exists($path))
		{
			return JFile::delete($path);
		}

		return false;
	}

}