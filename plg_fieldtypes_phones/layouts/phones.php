<?php
/**
 * @package    Field Types - Phones Plugin
 * @version    1.0.6
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
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_phones/field.min.css', array('version' => 'auto'));
HTMLHelper::_('script', 'media/plg_fieldtypes_phones/field.min.js', array('version' => 'auto'));
HTMLHelper::_('jquery.ui');
HTMLHelper::_('jquery.ui', array('sortable'));
?>

<div id="<?php echo $id; ?>" data-input-phones="" class="<?php echo $class; ?>">
	<div class="item" data-key="phone_X">
		<input type="hidden" data-name="<?php echo $name; ?>[phone_X][code]" data-id="<?php echo $id; ?>_phone_X_code"
			   value="+7" class="code">
		<div class="input-prepend">
			<span class="add-on">+7</span>
			<input type="text" data-name="<?php echo $name; ?>[phone_X][number]" placeholder="9213331758"
				   data-id="<?php echo $id; ?>_phone_X_number" maxlength="10" class="number">
		</div>
		<?php if ($text): ?>
			<div class="input-prepend">
				<?php if (!empty($text_icon)): ?>
					<span class="add-on"><i class="icon-<?php echo $text_icon; ?>"></i></span>
				<?php endif; ?>
				<input type="text" data-name="<?php echo $name; ?>[phone_X][text]" class="text"
					   placeholder="<?php echo Text::_($text_placeholder); ?>"
					   data-id="<?php echo $id; ?>_phone_X_text">
			</div>
		<?php endif; ?>
		<input type="hidden" data-name="<?php echo $name; ?>[phone_X][display]" class="display"
			   data-id="<?php echo $id; ?>_phone_X_display">
		<?php if ($limit !== 1): ?>
			<div class="actions btn-group">
				<a class="remove btn btn-small button btn-danger"><span class="icon-remove"></span></a>
				<a class="move btn btn-small button btn-primary"><span class="icon-move"></span></a>
				<a class="add btn btn-small button btn-success"><span class="icon-plus-2"></span></a>
			</div>
		<?php endif; ?>
	</div>
	<div id="<?php echo $id; ?>_result" class="result">
		<?php foreach ($value as $key => $val): ?>
			<div class="item" data-key="<?php echo $key; ?>">
				<input type="hidden" name="<?php echo $name; ?>[<?php echo $key; ?>][code]"
					   id="<?php echo $id; ?>_<?php echo $key; ?>_code"
					   value="<?php echo $val['code']; ?>" class="code">
				<div class="input-prepend">
					<span class="add-on">+7</span>
					<input type="text" name="<?php echo $name; ?>[<?php echo $key; ?>][number]"
						   placeholder="9213331758"
						   maxlength="10"
						   id="<?php echo $id; ?>_<?php echo $key; ?>_number" class="number"
						   value="<?php echo $val['number']; ?>">
				</div>
				<?php if ($text): ?>
					<div class="input-prepend">
						<?php if (!empty($text_icon)): ?>
							<span class="add-on"><i class="icon-<?php echo $text_icon; ?>"></i></span>
						<?php endif; ?>
						<input type="text" name="<?php echo $name; ?>[<?php echo $key; ?>][text]" class="text"
							   id="<?php echo $id; ?>_<?php echo $key; ?>_text"
							   value="<?php echo !(empty($val['text'])) ? $val['text'] : ''; ?>"
							   placeholder="<?php echo Text::_($text_placeholder); ?>">
					</div>
				<?php endif; ?>
				<input type="hidden" name="<?php echo $name; ?>[<?php echo $key; ?>][display]" class="display"
					   id="<?php echo $id; ?>_<?php echo $key; ?>_display"
					   value="<?php echo (!empty($val['display'])) ? $val['display'] : $val['code'] . $val['number']; ?>"
					   placeholder="<?php echo Text::_($text_placeholder); ?>">
				<?php if ($limit !== 1): ?>
					<div class="actions btn-group">
						<a class="remove btn btn-small button btn-danger"><span class="icon-remove"></span></a>
						<a class="move btn btn-small button btn-primary"><span class="icon-move"></span></a>
						<a class="add btn btn-small button btn-success"><span class="icon-plus-2"></span></a>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
