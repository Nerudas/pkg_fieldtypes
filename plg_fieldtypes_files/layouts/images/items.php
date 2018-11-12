<?php
/**
 * @package    Field Types - Files Plugin
 * @version    1.1.7
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

jimport('joomla.filesystem.file');

$images = $displayData;
$root   = '/' . trim(Uri::root(true));
?>
<ul>
	<?php foreach ($images as $image): ?>
		<li class="item<?php echo ($image->text !== false) ? ' with-text' : '' ?>"
			data-key="<?php echo $image->file; ?>">
			<div class="wrapper">
				<a href="<?php echo $root . $image->src; ?>" target="_blank" class="icon">
					<img src="<?php echo $root . $image->src; ?>">
				</a>
				<div class="text">
					<?php if ($image->text === false) : ?>
						<div class="lead">
							<?php echo JFile::getName($image->src); ?>
						</div>
					<?php endif; ?>
					<div><code><?php echo $image->src; ?></code></div>
					<?php if ($image->text !== false) : ?>
						<div class="description">
							<textarea class="span12" rows="3" name="<?php echo $image->filed_name; ?>[text]"
							><?php echo $image->text; ?></textarea>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="actions">
				<a class="remove text-error icon-remove" data-file="<?php echo $image->src; ?>"></a>
				<a class="move text-primary icon-move"></a>
			</div>
			<input type="hidden" name="<?php echo $image->filed_name; ?>[ordering]"
				   value="<?php echo $image->ordering; ?>">
		</li>
	<?php endforeach; ?>
</ul>