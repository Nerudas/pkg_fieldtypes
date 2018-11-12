/*
 * @package    Field Types - Price Plugin
 * @version    1.1.7
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		$('[data-input-price]').each(function () {
			var field = $(this),
				text = $(field).find('input[type="text"]'),
				checkbox = $(field).find('input[type="checkbox"]'),
				data = $(field).data('input-price');

			if (data !== 'between') {
				function textDisabled() {
					if ($(checkbox).prop('checked')) {
						$(text).attr('disabled', 'true');
					}
					else {
						$(text).removeAttr('disabled');
					}
				}

				$(checkbox).on('change', function () {
					textDisabled();
				});
				textDisabled();
			}
		});
	});
})(jQuery);
