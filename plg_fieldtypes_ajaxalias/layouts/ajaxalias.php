<?php
/**
 * @package    Field Types - Ajax Alias Plugin
 * @version    1.1.7
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_ajaxalias/css/ajaxalias.min.css', array('version' => 'auto'));
HTMLHelper::_('script', 'media/plg_fieldtypes_ajaxalias/js/ajaxalias.min.js', array('version' => 'auto'));

extract($displayData);

$value = (!empty($value)) ? $value : '';

?>
<div id="<?php echo $id; ?>" data-input-ajaxalias>
	<div class="input-append">
		<input id="<?php echo $id; ?>_field" type="text" name="<?php echo $name; ?>" value="<?php echo $value; ?>"
			   class="<?php echo $class; ?>"
			   placeholder="<?php echo Text::_($hint); ?>">
		<span class="add-on status loading"><i class="icon-loop"></i></span>
		<span class="add-on status success"><i class="icon-ok text-success"></i></span>
		<span class="add-on status error"><i class="icon-cancel-2 text-error"></i></span>
	</div>
	<div class="error description  alert alert-danger status help-inline"></div>
</div>
