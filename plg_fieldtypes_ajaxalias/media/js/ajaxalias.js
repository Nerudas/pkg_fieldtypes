/*
 * @package    Field Types - Ajax Alias Plugin
 * @version    1.0.6
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */
(function ($) {
	$(document).ready(function () {
		$('[data-input-ajaxalias]').each(function () {

			// Elements
			var block = $(this),
				field = $(block).find('input'),
				status = $(block).find('.status'),
				error = $(block).find('.error'),
				errorDescription = $(block).find('.error.description'),
				success = $(block).find('.success'),
				loading = $(block).find('.loading'),
				form = $(this).closest('form');

			var params = Joomla.getOptions($(this).attr('id'), ''),
				checkurl = params.checkurl;

			$(status).hide();

			$(field).on('keyup', function () {
				$(status).hide();
			});

			$(field).on('change', function () {
				var data = $(form).serializeArray();
				$(data).each(function (key, value) {
					if (value.name == 'task') {
						data.splice(key, 1);
					}
				});
				$(status).hide();
				$(errorDescription).text('');

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: checkurl,
					data: data,
					beforeSend: function () {
						$(loading).show();
					},
					complete: function () {
						$(loading).hide();
					},
					success: function (response) {
						if (response.success) {
							$(success).show();
							$(field).val(response.data);
							if (response.message !== '') {
								$(errorDescription).show();
								$(errorDescription).text(response.message);
							}
						}
						else {
							$(error).show();
							$(errorDescription).text(response.message);
						}
					},
					error: function () {
						$(error).show();
					}
				});
			});
		});
	});
})(jQuery);
