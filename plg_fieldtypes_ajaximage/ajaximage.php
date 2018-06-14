<?php
/**
 * @package    Field Types - Ajax Image Plugin
 * @version    1.0.6
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Filter\OutputFilter;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class PlgFieldTypesAjaxImage extends CMSPlugin
{
	/**
	 * Ajax for plugin
	 *
	 * @return mixed object / bool
	 *
	 * @since  1.0.0
	 */
	public function onAjaxAjaxImage()
	{

		$task = Factory::getApplication()->input->get('task', '', 'raw');
		if (!empty($task))
		{
			if ($task == 'upload')
			{
				return $this->uploadImage();
			}
			if ($task == 'remove')
			{
				return $this->removeImage();
			}
		}

		return false;
	}


	/**
	 * Upload image
	 *
	 * @return bool
	 *
	 * @since  1.0.0
	 */
	protected function removeImage()
	{
		$src = Factory::getApplication()->input->get('src', '', 'raw');
		if (!empty($src))
		{
			$src = JPATH_ROOT . '/' . trim($src, '/');
			if (!JFile::exists($src) || JFile::delete($src))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Upload image
	 *
	 * @return mixed object / bool
	 *
	 * @since  1.0.0
	 */
	protected function uploadImage()
	{
		$app       = Factory::getApplication();
		$root      = $app->input->get('folder', '', 'raw');
		$folder    = trim($root, '/');
		$directory = JPATH_ROOT . '/' . $folder;
		if (empty($root) || !JFolder::exists($directory))
		{
			return false;
		}

		$response       = new stdClass();
		$response->type = 'error';

		$fieldname = $app->input->get('fieldname', '', 'raw');
		$fieldid   = $app->input->get('fieldid', '', 'raw');

		$multiple = $app->input->get('multiple', '', 'raw');
		$multiple = (!empty($multiple) && $multiple == 'true');
		if ($multiple)
		{
			$key    = $app->input->get('key', 'image_X', 'raw');
			$text   = $app->input->get('text', '', 'raw');
			$text   = (!empty($text) && $text == 'true');
			$unique = $app->input->get('unique', '', 'raw');
			$unique = (!empty($unique) && $unique == 'true');
			$prefix = $app->input->get('prefix', '', 'raw');
		}
		else
		{
			$current  = $app->input->get('current', '', 'raw');
			$filename = $app->input->get('filename', '', 'raw');
		}

		$subfolder = $app->input->get('subfolder', '', 'raw');
		if (!empty($subfolder) && $subfolder !== 'null')
		{
			$folder    = $folder . '/' . trim($subfolder, '/');
			$directory = $directory . '/' . trim($subfolder, '/');
		}

		$files = $app->input->files->get('files', array(), 'array');
		if (count($files) > 0)
		{
			if (!JFolder::exists($directory))
			{
				JFolder::create($directory);
				JFile::write($directory . '/index.html', '<!DOCTYPE html><title></title>');
			}
			$file        = $files[0];
			$mediaHelper = new JHelperMedia;
			$isImage     = $mediaHelper->isImage($file['name']);
			if ($isImage)
			{
				$language = Factory::getLanguage();
				$language->load('plg_fieldtypes_ajaximage', JPATH_ADMINISTRATOR, $language->getTag(), true);

				$name = JFile::stripExt($file['name']);
				$name = OutputFilter::stringURLSafe($name);
				$ext  = JFile::getExt($file['name']);

				if ($multiple)
				{
					$prefix = (!empty($prefix)) ? $prefix . '_' : '';
					$name   = $prefix . $name;
					if ($unique)
					{
						$name = $prefix . uniqid();
						while (JFile::exists($directory . '/' . $name . '.' . $ext))
						{
							$name = $prefix . uniqid();
						}
					}
					else
					{
						$ik        = 1;
						$checkName = $name;
						while (JFile::exists($directory . '/' . $checkName . '.' . $ext))
						{
							$checkName = $name . '_' . $ik;
							$ik++;
						}
						$name = $checkName;
					}
				}
				else
				{
					if (!empty($current) && $current !== 'null')
					{
						JFile::delete(JPATH_ROOT . '/' . trim($current, '/'));
					}
					if (!empty($filename) && $filename !== 'null')
					{
						$name = OutputFilter::stringURLSafe($filename);
					}
				}

				$dest = $directory . '/' . $name . '.' . $ext;
				if (JFile::upload($file['tmp_name'], $dest, false, true))
				{
					$response->type = 'success';
					if ($multiple)
					{
						$data          = array();
						$data['name']  = $fieldname;
						$data['id']    = $fieldid;
						$data['text']  = $text;
						$data['key']   = $key;
						$data['value'] = array();

						$data['value']['file'] = $name . '.' . $ext;
						$data['value']['src']  = $folder . '/' . $name . '.' . $ext;
						$data['value']['text'] = '';

						$response->html = LayoutHelper::render('joomla.form.field.ajaximage.item', $data);
					}
					else
					{
						$response->value = $folder . '/' . $name . '.' . $ext;
						$response->image = '/' . $folder . '/' . $name . '.' . $ext . '?v=' . uniqid();
					}
				}
			}

		}

		return $response;
	}
}