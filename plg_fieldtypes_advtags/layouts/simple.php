<?php
/**
 * @package    Field Types - Advanced Tags Plugin
 * @version    1.0.4
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   array  $options  Options available for this field.
 * @var   array  $children Childrens options available for this field.
 * @var   array  $root     Roots ptions available for this field.
 * @var   array  $value    Value attribute of the field.
 * @var   string $id       DOM id of the field.
 * @var   string $name     Name of the input field.
 * @var   string $class    Classes for the input.
 */

HTMLHelper::_('jquery.framework');
HTMLHelper::_('stylesheet', 'media/plg_fieldtypes_advtags/field.min.css', array('version' => 'auto'));
if (Factory::getApplication()->isSite())
{
	HTMLHelper::_('script', 'media/plg_fieldtypes_advtags/field.min.js', array('version' => 'auto'));
}
?>
<div id="<?php echo $id; ?>" data-input-advtags="simple" class="<?php echo $class; ?>">
	<?php $data      = $displayData;
	$data['options'] = $root;
	echo LayoutHelper::render('joomla.form.field.advtags.options.default', $data); ?>
</div>
