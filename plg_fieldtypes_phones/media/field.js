/*
 * @package    Field Types - Phones Plugin
 * @version    1.1.4
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		$('[data-input-phones]').each(function () {
			// Elements
			var field = $(this),
				id = field.attr('id'),
				blank = field.find('.item[data-key="phone_X"]'),
				result = field.find('#' + id + '_result'),
				form = field.closest('form');

			// Fix selector
			if (!field.selector) {
				field.selector = '#' + field.attr('id');
			}

			// Params
			var params = Joomla.getOptions(id, ''),
				limit = params.limit;

			// Add phone
			addPhone();
			$('body').on('click', field.selector + ' .actions .add', function () {
				addPhone();
			});

			// Remove phone
			$('body').on('click', field.selector + ' .actions .remove', function () {
				$(this).closest('.item').remove();
				if (result.find('.item').length == 0) {
					addPhone();
				}
				reIndex();
				checkLimit();
			});

			// Move phone
			$(result.selector).sortable({
				handle: ".actions .move",
				start: function (event, ui) {
					result.addClass('sortable');
				},
				stop: function (event, ui) {
					reIndex();
					result.removeClass('sortable');
				}
			});

			// Number keyup
			$('body').on('keyup', '#' + id + ' [name*="number"]', function () {
				var value = $(this).val().replace(/[^.\d]+/g, '').replace(/^([^.]*\.)|\./g, '$1');
				$(this).val(value);
				var code = $(this).closest('.item').find('[name*="code"]').val();
				var phone = $(this).closest('.item').find('[name*="display"]');
				$(phone).val(code + value);
			});

			// Number change
			$('body').on('change', '#' + id + ' [name*="number"]', function () {
				var value = $(this).val().replace(/[^.\d]+/g, '').replace(/^([^.]*\.)|\./g, '$1');
				$(this).val(value);
				var code = $(this).closest('.item').find('[name*="code"]').val();
				var phone = $(this).closest('.item').find('[name*="display"]');
				$(phone).val(code + value);
			});

			// Remove empty phones
			$(form).on('submit', function () {
				result.find('[name*="number"]').each(function (i, input) {
					if ($(input).val() == '') {
						$(input).closest('.item').remove();
						reIndex();
					}
					var value = $(input).val().replace(/[^.\d]+/g, '').replace(/^([^.]*\.)|\./g, '$1');
					$(input).val(value);
					var code = $(input).closest('.item').find('[name*="code"]').val();
					var phone = $(input).closest('.item').find('[name*="display"]');
					$(phone).val(code + value);
				});
			});

			// Add phone function
			function addPhone() {
				var check = checkLimit();
				if (check) {
					var newRow = blank.clone();
					$(newRow).find('input').each(function (i, input) {
						$(input).attr('id', $(input).data('id'));
						$(input).attr('name', $(input).data('name'));
						$(input).removeAttr('data-id');
						$(input).removeAttr('data-name');
					});
					$(newRow).appendTo(result);
					reIndex();
					checkLimit();
				}
			}

			// Check limit function
			function checkLimit() {
				var check = false;
				if (limit == 0) {
					check = true;
				}
				var count = result.find('.item').length;
				if (!check && count < limit) {
					check = true;
				}
				if (!check) {
					$(field.selector + ' .actions .add').attr('disabled', 'true');
				}
				else {
					$(field.selector + ' .actions .add').removeAttr('disabled');
				}
				return check;
			}

			// Reindex function
			function reIndex() {
				result.find('.item').each(function (i, item) {
					var i_key = $(item).attr('data-key');
					var i_prefix = 'phone';
					var i_pattern = new RegExp(i_key, 'g');
					$(item).find('[id*="' + i_prefix + '_"]').each(function (a, input) {
						var a_id = $(input).attr('id').replace(i_pattern, i_prefix + '_' + (i + 1));
						$(input).attr('id', a_id);
					});
					$(item).find('[name*="' + i_prefix + '_"]').each(function (a, input) {
						var a_name = $(input).attr('name').replace(i_pattern, i_prefix + '_' + (i + 1));
						$(input).attr('name', a_name);
					});
					$(item).attr('data-key', i_prefix + '_' + (i + 1));
				});
			}
		});
	});
})(jQuery);