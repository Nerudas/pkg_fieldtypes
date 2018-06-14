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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

HTMLHelper::_('jquery.framework');
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_ajaximage/css/ajaximage.min.css', array('version' => 'auto'));
HTMLHelper::_('script', 'media/plg_fieldtypes_ajaximage/js/ajaximage.min.js', array('version' => 'auto'));
?>
<div id="<?php echo $id; ?>" class="<?php echo $class; ?>" data-input-ajaximage="simple">
	<div class="form">
		<img src=""/>
		<input id="<?php echo $id; ?>_file" class="file" type="file" accept="image/*"/>
		<label for="<?php echo $id; ?>_file">
			<a class="btn btn-large btn-info">
				<?php echo Text::_('PLG_FIELDTYPES_AJAXIMAGE_CHOOSE_BUTTON'); ?>
			</a>
		</label>
	</div>
	<a class="remove btn btn-small btn-danger"><i class="icon-remove"></i></a>
	<div id="<?php echo $id; ?>_progress" class="progress progress-success active">
		<div class="text"></div>
		<div class="bar"></div>
	</div>
	<input id="<?php echo $id; ?>_result" type="hidden" class="result" name="<?php echo $name; ?>"
		   value="<?php echo $value; ?>">
</div>
