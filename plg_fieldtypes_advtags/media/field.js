/*
 * @package    Field Types - Advanced Tags Plugin
 * @version    1.0.2
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {

		$('[data-input-advtags]').each(function () {
			var field = $(this),
				inputs = $(field).find('input[type="checkbox"]');

			$(inputs).on('change', function () {
				var input = this;
				var childs = $(input).closest('.item').find('.childs input[type="checkbox"]');
				childs.prop('checked', input.checked);
				parentChecked($(input).data('parent'), $(this).prop('checked'));

			});

			function parentChecked(id, checked) {
				if (id !== 1) {
					var input = $(field).find('input[type="checkbox"][value="' + id + '"]');

					if (!checked) {
						var childs = $(input).closest('.item').find('.childs input[type="checkbox"]');
						$(childs).each(function () {
							if (!checked && $(this).prop('checked') == true) {
								checked = true;
							}
						});
					}
					$(input).prop('checked', checked);

					// if (!checked && $(input).data('parent') == 1) {
					// 	var childs = $(input).closest('.item').find('.childs input[type="checkbox"]');
					// 	$(childs).each(function () {
					// 		if (!checked && $(this).prop('checked')) {
					// 			checked = true;
					// 		}
					// 	});
					// 	$(input).prop('checked', checked);
					// }
					// if (checked) {
					// 	$(input).prop('checked', checked);
					// }
					parentChecked($(input).data('parent'), checked);
				}
			}
		});
	});
})(jQuery);
