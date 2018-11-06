<?php
/**
 * @package    Field Types - Advanced Tags Plugin
 * @version    1.1.5
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

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
?>
<ul class="level-<?php echo $level; ?> childs">
	<?php foreach ($options as $option): ?>
		<li class="item option-<?php echo $option->key; ?> level-<?php echo $option->level; ?> ">
			<label for="<?php echo $option->id; ?>" class="checkbox">
				<?php if (!$option->only_title): ?>
					<input type="checkbox" name="<?php echo $option->name; ?>" id="<?php echo $option->id; ?>"
						   value="<?php echo $option->value; ?>"
						   data-parent="<?php echo $option->parent; ?>" <?php echo $option->checked; ?>>
				<?php endif; ?>
				<?php echo $option->text; ?>
			</label>
			<?php if (!empty($children[$option->key]))
			{
				$data            = $displayData;
				$data['options'] = $children[$option->key];
				$data['level']   = $option->level + 1;
				echo LayoutHelper::render('joomla.form.field.advtags.options.default', $data);
			}
			?>
		</li>
	<?php endforeach; ?>
</ul>

