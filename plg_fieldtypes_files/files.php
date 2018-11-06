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

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Response\JsonResponse;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JLoader::register('FieldTypesFilesHelper', JPATH_PLUGINS . '/fieldtypes/files/helper.php');

class PlgFieldTypesFiles extends CMSPlugin
{
	/**
	 * Ajax for plugin
	 *
	 * @return mixed object / bool
	 *
	 * @since  1.2.0
	 */
	public function onAjaxFiles()
	{
		$task = Factory::getApplication()->input->get('task', '', 'raw');
		if (!empty($task))
		{
			return $this->$task();
		}

		return $this->setResponse(false);
	}

	/**
	 * Method to get File
	 *
	 * @since  1.2.0
	 *
	 * @return bool
	 */
	public function getFile()
	{
		$app      = Factory::getApplication();
		$filename = $app->input->get('filename', '', 'raw');
		$folder   = $app->input->get('folder', '', 'raw');
		$type     = $app->input->get('type', 'image');
		$response = false;
		$helper   = new FieldTypesFilesHelper();

		if ($type == 'image')
		{
			$response = $helper->getImage($filename, $folder,
				$app->input->get('noimage', 'media/plg_fieldtypes_files/images/noimage.jpg', 'raw'));
		}

		return $this->setResponse($response);
	}

	/**
	 * Method to delete File
	 *
	 * @since  1.2.0
	 *
	 * @return bool
	 */
	public function deleteFile()
	{
		$app      = Factory::getApplication();
		$filename = $app->input->get('filename', '', 'raw');
		$folder   = $app->input->get('folder', '', 'raw');
		$type     = $app->input->get('type', 'image');
		$response = false;
		$helper   = new FieldTypesFilesHelper();

		if ($type == 'image')
		{
			$response = $helper->deleteImage($filename, $folder,
				$app->input->get('noimage', 'media/plg_fieldtypes_files/images/noimage.jpg', 'raw'));
		}

		return $this->setResponse($response);
	}

	/**
	 * Method to upload
	 *
	 * @since  1.2.0
	 *
	 * @return bool
	 */
	public function uploadFile()
	{
		$app      = Factory::getApplication();
		$filename = $app->input->get('filename', '', 'raw');
		$folder   = $app->input->get('folder', '', 'raw');
		$type     = $app->input->get('type', 'image');
		$files    = $app->input->files->get('files', array(), 'array');
		$response = false;
		$helper   = new FieldTypesFilesHelper();

		if ($type == 'image')
		{
			$response = $helper->uploadImage($filename, $folder, $files,
				$app->input->get('noimage', 'media/plg_fieldtypes_files/images/noimage.jpg', 'raw'));
		}

		return $this->setResponse($response);
	}

	/**
	 * Method to get Files
	 *
	 * @since  1.2.0
	 *
	 * @return bool
	 */
	public function getFiles()
	{
		$app         = Factory::getApplication();
		$folder      = $app->input->get('folder', '', 'raw');
		$root_folder = $app->input->get('root_folder', '', 'raw');
		$type        = $app->input->get('type', 'images');
		$response    = false;
		$helper      = new FieldTypesFilesHelper();

		if ($type == 'images')
		{
			$value    = $app->input->get('value', array(), 'array');
			$params   = array(
				'text'       => ($app->input->get('text', 'false') == 'true'),
				'filed_name' => $app->input->get('filed_name', 'jform[images_default]', 'raw')
			);
			$response = $helper->getImages($folder, $root_folder, $value, $params);
		}

		return $this->setResponse($response);
	}

	/**
	 * Method to upload files
	 *
	 * @since  1.2.0
	 *
	 * @return bool
	 */
	public function uploadFiles()
	{
		$app         = Factory::getApplication();
		$folder      = $app->input->get('folder', '', 'raw');
		$root_folder = $app->input->get('root_folder', '', 'raw');
		$type        = $app->input->get('type', 'images');
		$files       = $app->input->files->get('files', array(), 'array');
		$response    = false;
		$helper      = new FieldTypesFilesHelper();
		if ($type == 'images')
		{
			$params   = array(
				'unique' => ($app->input->get('unique', 'false') == 'true'),
				'limit'  => $app->input->get('limit', 0, 'int'),
				'exist'  => $app->input->get('exist', 0, 'int'),
			);

			$response = $helper->uploadImages($folder, $root_folder, $files, $params);
		}

		return $this->setResponse($response);
	}

	/**
	 * Method to delete File in multiple fields
	 *
	 * @since  1.2.0
	 *
	 * @return bool
	 */
	public function deleteFiles()
	{
		$file   = Factory::getApplication()->input->get('file', '', 'raw');
		$helper = new FieldTypesFilesHelper();

		return $this->setResponse($helper->deleteFile($file));
	}

	/**
	 * Method to set response
	 *
	 * @param $response
	 *
	 * @since  1.2.0
	 *
	 * @return bool
	 */
	protected function setResponse($response)
	{
		echo new JsonResponse($response, '', !($response));

		Factory::getApplication()->close();

		return true;
	}
}