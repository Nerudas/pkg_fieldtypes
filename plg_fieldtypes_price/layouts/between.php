<?php
/**
 * @package    Field Types - Price Plugin
 * @version    1.0.8
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   array  $value    Value attribute of the field.
 * @var   bool   $checkbox show contract price checkbox
 * @var   string $checked  checkbox checked
 * @var   string $id       DOM id of the field.
 * @var   string $name     Name of the input field.
 * @var   string $class    Classes for the input.
 */


HTMLHelper::_('jquery.framework');
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_price/field.min.css', array('version' => 'auto'));
HTMLHelper::_('script', 'media/plg_fieldtypes_price/field.min.js', array('version' => 'auto'));

?>

<div id="<?php echo $id; ?>" data-input-price="between" class="<?php echo $class; ?>">
	<div class="text-field">
		<span><?php echo Text::_('JGLOBAL_FIELD_PRICE_FROM'); ?> </span>
		<div class="input-append">
			<input id="<?php echo $id; ?>_text_from" type="text" name="<?php echo $name; ?>[from]"
				   value="<?php echo (!empty($value['from'])) ? $value['from'] : ''; ?>">
			<span class="add-on"><?php echo Text::_('JGLOBAL_FIELD_PRICE_CURRENCY_RUB'); ?></span>
		</div>
		<span>	<?php echo Text::_('JGLOBAL_FIELD_PRICE_TO'); ?>	</span>
		<div class="input-append">
			<input id="<?php echo $id; ?>_text_to" type="text" name="<?php echo $name; ?>[to]"
				   value="<?php echo (!empty($value['to'])) ? $value['to'] : ''; ?>">
			<span class="add-on"><?php echo Text::_('JGLOBAL_FIELD_PRICE_CURRENCY_RUB'); ?></span>
		</div>
	</div>
	<?php if ($checkbox) : ?>
		<div class="checkbox-field">
			<label for="<?php echo $id; ?>_checkbox">
				<input id="<?php echo $id; ?>_checkbox" type="checkbox" name="<?php echo $name; ?>[contract]" value="-0"
					<?php echo (!empty($value['contract'])) ? 'checked' : ''; ?>>
				<?php echo Text::_('JGLOBAL_FIELD_PRICE_CONTRACT_PRICE'); ?>
			</label>
		</div>
	<?php endif; ?>
</div>
