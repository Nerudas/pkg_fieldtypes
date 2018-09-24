/*
 * @package    Field Types - Files Plugin
 * @version    1.1.3
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		$('[data-input-images]').each(function () {
			var field = $(this),
				id = field.attr('id'),
				filed_name = field.attr('data-name'),
				form = field.find('.form'),
				input = form.find('input[type="file"]'),
				loading = form.find('.loading'),
				result = field.find('.result');

			var params = Joomla.getOptions(id, ''),
				folder_field = $('#' + params.folder_field),
				root_folder = $(folder_field).val(),
				default_value = params.value,
				text = params.text;

			if (params.folder_field == '' || folder_field.length == 0 || root_folder == '') {
				$(field).remove();
			}
			else {
				setInterval(function () {
					$(form).height(Math.round(($(field).width() / 10) * 2));
				}, 10);
				getImages();
			}

			// Upload
			input.on('change', function (e) {
				if (!form.hasClass('disable')) {
					uploadImages(e.target.files);
				}
			});

			field
				.on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
					e.preventDefault();
					e.stopPropagation();
				})
				.on('dragover dragenter', function () {
					if (!form.hasClass('disable')) {
						form.addClass('dragend');
					}
				})
				.on('dragleave dragend drop', function () {
					if (!form.hasClass('disable')) {
						form.removeClass('dragend');
					}
				})
				.on('drop', function (e) {
					if (!form.hasClass('disable')) {
						console.log('bbb');
						uploadImages(e.originalEvent.dataTransfer.files);
					}

				});

			// Upload image function
			function uploadImages(files) {
				var ajaxData = new FormData();
				ajaxData.append('type', 'images');
				ajaxData.append('root_folder', root_folder);
				ajaxData.append('folder', params.folder);
				$(files).each(function (i, file) {
					ajaxData.append('files[]', file);
				});

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: params.site_root + 'index.php?option=com_ajax&plugin=files&group=fieldtypes&format=json&task=uploadFiles',
					processData: false,
					contentType: false,
					cache: false,
					global: false,
					async: false,
					data: ajaxData,
					beforeSend: function () {
						$(result).html('');
						$(loading).show();
					},
					complete: function () {
						getImages();
					},
					error: function (response) {
						console.error(response.status + ': ' + response.statusText);
					}
				});
			}

			// Get images function
			function getImages() {
				var value = default_value,
					items = $(result).find('.item');
				$(items).each(function (i, item) {
					var key = $(item).attr('data-key'),
						val = {};
					if (text) {
						val.text = $(item).find('[name*="[text]"]').val();
					}
					val.ordering = $(item).find('[name*="[ordering]"]').val();
					value[key] = val;

				});

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: params.site_root + 'index.php?option=com_ajax&plugin=files&group=fieldtypes&format=json&task=getFiles',
					cache: false,
					global: false,
					async: false,
					data: {
						type: 'images',
						root_folder: root_folder,
						folder: params.folder,
						noimage: params.noimage,
						value: value,
						text: text,
						filed_name: filed_name
					},
					beforeSend: function () {
						$(result).html('');
						$(loading).show();
					},
					complete: function () {
						$(loading).hide();
					},
					success: function (response) {
						if (response.success) {
							$(result).html(response.data);
						}
						else {
							$(result).html('');
						}
					},
					error: function (response) {
						$(result).html('');
						console.error(response.status + ': ' + response.statusText);
					}
				});
			}

			// Delete images function
			$('body').on('click', result.selector + ' .item .actions .remove', function () {
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: params.site_root + 'index.php?option=com_ajax&plugin=files&group=fieldtypes&format=json&task=deleteFiles',
					cache: false,
					global: false,
					async: false,
					data: {
						file: $(this).data('file')
					},
					beforeSend: function () {
						$(result).html('');
						$(loading).show();
					},
					complete: function () {
						getImages();
					},
					error: function (response) {
						console.error(response.status + ': ' + response.statusText);
					}
				});
			});

			// Move images function
			$(result.selector + ' > ul').sortable({
				handle: ".actions .move",
				start: function (event, ui) {
					result.addClass('sortable');
				},
				stop: function (event, ui) {
					$(result).find('.item').each(function (i, item) {
						$(item).find('[name*="[ordering]"]').val(i + 1);
					});
					result.removeClass('sortable');
				}
			});
		});
	});
})(jQuery);