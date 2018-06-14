/*
 * @package    Field Types - Social Plugin
 * @version    1.0.6
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		$('[data-input-social]').each(function () {
			// Elements
			var field = $(this),
				input = field.find('input'),
				id = input.attr('id'),
				form = field.parents('form');
			// Params
			var params = Joomla.getOptions(id, ''),
				network = params.network;

			// Run check
			$('body').on('keyup', input, function () {
				checkSocial(input);
			});

			$('body').on('change', input, function () {
				checkSocial(input);
			});

			$(form).on('submit', function () {
				checkSocial(input);
			});

			// Check function
			function checkSocial(element) {
				$(element).val($(element).val()
					.replace('http://', '')
					.replace('https://', '')
					.replace('/', '')
					.replace('www.', '')
					.replace(network, ''));
			}
		});
	});
})(jQuery);