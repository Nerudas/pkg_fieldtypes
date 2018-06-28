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

use Joomla\CMS\Language\Text;

extract($displayData);

?>
<div id="<?php echo $id; ?>" class="item well" data-key="<?php echo $key; ?>">
	<div class="actions clearfix">
		<a class="remove btn btn-small btn-danger pull-right"><i class="icon-remove"></i></a>
		<a class="move btn btn-small btn-primary pull-left"><i class="icon-move"></i></a>
	</div>
	<div class="image">
		<img src="/<?php echo $value['src']; ?>">
		<input id="<?php echo $id; ?>_file" type="text" class="link" readonly name="<?php echo $name; ?>[file]"
			   value="<?php echo $value['file']; ?>">
	</div>
	<input id="<?php echo $id; ?>_src" type="hidden" name="<?php echo $name; ?>[src]"
		   value="<?php echo $value['src']; ?>">
	<?php if ($text): ?>
		<textarea id="<?php echo $id; ?>_description" name="<?php echo $name; ?>[text]"
				  placeholder="<?php echo Text::_('PLG_FIELDTYPES_AJAXIMAGE_TEXT_PLACEHOLDER'); ?>"
				  class="description"><?php echo $value['text']; ?></textarea>
	<?php endif; ?>

</div>
