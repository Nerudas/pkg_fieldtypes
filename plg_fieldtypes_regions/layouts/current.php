<?php
/**
 * @package    Field Types - Regions Plugin
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

extract($displayData);

$option = $options[$current];

?>
<label for="<?php echo $option->id; ?>" class="checkbox">
	<input type="checkbox" name="<?php echo $option->name; ?>" id="<?php echo $option->id; ?>"
		   value="<?php echo $option->value; ?>" <?php echo $option->checked; ?>>
	<?php echo Text::sprintf('JGLOBAL_FIELD_REGIONS_ONLY', $option->text); ?>
</label>