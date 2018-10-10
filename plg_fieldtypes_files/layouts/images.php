<?php
/**
 * @package    Field Types - Files Plugin
 * @version    1.1.4
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

HTMLHelper::_('jquery.framework');
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_files/css/images.min.css', array('version' => 'auto'));
HTMLHelper::_('script', 'media/plg_fieldtypes_files/js/images.js', array('version' => 'auto'));
HTMLHelper::_('jquery.ui');
HTMLHelper::_('jquery.ui', array('sortable'));
?>
<div id="<?php echo $id; ?>" class="<?php echo $class; ?>" data-input-images="<?php echo $id; ?>"
	 data-name="<?php echo $name; ?>">
	<div class="form">
		<input id="<?php echo $id; ?>_field" class="file" type="file" accept="image/*" multiple/>
		<div class="loading"></div>
		<label for="<?php echo $id; ?>_field"></label>
		<div class="text">
			<div><i id="upload-icon" class="icon-upload"></i></div>
			<div class="button">
				<span class="btn btn-success"><?php echo Text::_('JGLOBAL_FIELD_IMAGES_CHOOSE_BUTTON'); ?></span>
			</div>
		</div>
	</div>
	<div class="limit-error alert alert-danger">
		<?php echo Text::sprintf('JGLOBAL_FIELD_IMAGES_LIMIT_ERROR', $limit); ?>
	</div>
	<div class="result"></div>
</div>