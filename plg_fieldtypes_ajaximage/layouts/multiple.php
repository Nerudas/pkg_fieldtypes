<?php
/**
 * @package    Field Type - Ajax Image Plugin
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

HTMLHelper::_('jquery.framework');
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_ajaximage/css/ajaximage.min.css', array('version' => 'auto'));
HTMLHelper::_('script', 'media/plg_fieldtypes_ajaximage/js/ajaximage.min.js', array('version' => 'auto'));
HTMLHelper::_('jquery.ui');
HTMLHelper::_('jquery.ui', array('sortable'));
?>
<div id="<?php echo $id; ?>" class="<?php echo $class; ?>" data-input-ajaximage="multiple">
	<div class="form">
		<input id="<?php echo $id; ?>_file" class="file" type="file" accept="image/*" multiple/>
		<label for="<?php echo $id; ?>_file">
			<i class="icon-upload"></i> <?php echo Text::_('PLG_FIELDTYPES_AJAXIMAGE_CHOOSE_LABEL'); ?>
		</label>
	</div>
	<?php if (!empty($limit)): ?>
		<div class="limit error alert alert-danger">
			<?php echo Text::sprintf('PLG_FIELDTYPES_AJAXIMAGE_LIMIT_ERROR', $limit); ?>
		</div>
	<?php endif; ?>
	<div id="<?php echo $id; ?>_progress" class="progress progress-success active">
		<div class="text"></div>
		<div class="bar"></div>
	</div>

	<div id="<?php echo $id; ?>_result" class="result clearfix">
		<?php if ($value && is_array($value)): ?>
			<?php foreach ($value as $key => $val)
			{

				$val['src']  = (!empty($val['src'])) ? $val['src'] : '';
				$val['file'] = (!empty($val['file'])) ? $val['file'] : '';
				$val['text'] = (!empty($val['text'])) ? $val['text'] : '';

				$item          = array();
				$item['id']    = $id . '_' . $key;
				$item['text']  = $text;
				$item['name']  = str_replace('[]', '[' . $key . ']', $name);
				$item['value'] = $val;
				$item['key']   = $key;

				echo LayoutHelper::render('joomla.form.field.ajaximage.item', $item);
			} ?>
		<?php endif; ?>
	</div>
</div>
