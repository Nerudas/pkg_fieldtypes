<?php
/**
 * @package    Field Types - Advanced Tags Plugin
 * @version    1.1.6
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);
/**
 * Layout variables
 * -----------------
 * @var   array  $options  Options available for this field.
 * @var   array  $children Childrens options available for this field.
 * @var   array  $root     Roots options available for this field.
 * @var   array  $value    Value attribute of the field.
 * @var   string $id       DOM id of the field.
 * @var   string $name     Name of the input field.
 * @var   string $class    Classes for the input.
 */

foreach ($options as $key => $option)
{
	$options[$key] = HTMLHelper::_('select.option', $option->value, $option->treename, 'value', 'text');
}
$attributes = array();
if ($onchange)
{
	$attributes[] = 'onchange="' . $onchange . '"';
}
if ($class)
{
	$attributes[] = 'class="' . $class . '"';
}
if ($multiple)
{
	$attributes[] = 'multiple';
}
$attributes = implode(' ', $attributes);

echo HTMLHelper::_('select.genericlist', $options, $name, $attributes, 'value', 'text', $value);
