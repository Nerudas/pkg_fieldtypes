<?php
/**
 * @package    Field Types - Map Plugin
 * @version    1.0.7
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

HTMLHelper::_('jquery.framework');
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_map/css/default.min.css', array('version' => 'auto'));
HTMLHelper::_('script', '//api-maps.yandex.ru/2.1/?lang=ru-RU', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'media/plg_fieldtypes_map/js/default.min.js', array('version' => 'auto'));

?>

<div id="<?php echo $id; ?>" data-input-map="default" class="<?php echo $class; ?>">
	<div id="<?php echo $id; ?>_map" class="map">
	</div>
	<div class="placemark">
		<input id="<?php echo $id; ?>_placemark_coordinates" name="<?php echo $name; ?>[placemark][coordinates]"
			   type="hidden" value='<?php echo $value['placemark']['coordinates']; ?>'>
		<input id="<?php echo $id; ?>_placemark_latitude" name="<?php echo $name; ?>[placemark][latitude]" type="hidden"
			   value='<?php echo $value['placemark']['latitude']; ?>'>
		<input id="<?php echo $id; ?>_placemark_longitude" name="<?php echo $name; ?>[placemark][longitude]"
			   type="hidden"
			   value='<?php echo $value['placemark']['longitude']; ?>'>
	</div>
	<div class="params">
		<input id="<?php echo $id; ?>_params_center" name="<?php echo $name; ?>[params][center]" type="hidden"
			   value='<?php echo $value['params']['center']; ?>'>
		<input id="<?php echo $id; ?>_params_latitude" name="<?php echo $name; ?>[params][latitude]" type="hidden"
			   value="<?php echo $value['params']['latitude']; ?>">
		<input id="<?php echo $id; ?>_params_longitude" name="<?php echo $name; ?>[params][longitude]" type="hidden"
			   value="<?php echo $value['params']['longitude']; ?>">
		<input id="<?php echo $id; ?>_params_zoom" name="<?php echo $name; ?>[params][zoom]" type="hidden"
			   value="<?php echo $value['params']['zoom']; ?>">
	</div>
</div>