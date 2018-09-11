<?php
/**
 * @package    Field Types - Files Plugin
 * @version    1.1.0
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
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_files/css/image.min.css', array('version' => 'auto'));
HTMLHelper::_('script', 'media/plg_fieldtypes_files/js/image.min.js', array('version' => 'auto'));
?>
<div id="<?php echo $id; ?>" class="<?php echo $class; ?>" data-input-image="<?php echo $id; ?>">
	<div class="form">
		<img src=""/>
		<input id="<?php echo $id; ?>_field" class="file" type="file" accept="image/*"/>
		<div class="loading"></div>
		<div class="actions">
			<label for="<?php echo $id; ?>_field" class="hasTooltip icon-upload btn-link"
			       title="<?php echo Text::_('JGLOBAL_FIELD_IMAGE_CHOOSE_BUTTON'); ?>"></label>
			<a class="remove text-error icon-remove"></a>
		</div>
		<label for="<?php echo $id; ?>_field"></label>
	</div>
</div>