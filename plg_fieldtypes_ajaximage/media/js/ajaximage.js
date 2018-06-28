/*
 * @package    Field Types - Ajax Image Plugin
 * @version    1.0.6
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		$('[data-input-ajaximage]').each(function () {
			// Elements
			var field = $(this),
				id = field.attr('id'),
				form = field.find('.form'),
				input = form.find('input[type="file"]'),
				limit_error = field.find('.error.limit'),
				progress = field.find('#' + id + '_progress'),
				progressbar = progress.find('.bar'),
				progresstext = progress.find('.text'),
				result = field.find('#' + id + '_result');

			// Fix selectors
			if (!result.selector) {
				result.selector = '#' + result.attr('id');
			}
			if (!field.selector) {
				field.selector = '#' + field.attr('id');
			}

			// Params
			var params = Joomla.getOptions(id, ''),
				name = params.name,
				multiple = params.multiple,
				subfolder = params.subfolder,
				saveurl = params.saveurl,
				uploadurl = params.uploadurl,
				removeurl = params.removeurl;
			if (multiple) {
				var unique = params.unique,
					text = params.text,
					prefix = params.prefix,
					limit = params.limit;
			}
			else {
				var noimage = params.noimage,
					image = params.image,
					filename = params.filename;
			}

			// Hide progressbar
			progress.hide();
			progressbar.width('0%');
			progresstext.text('0/0');

			// Simple background
			if (!multiple) {
				var img = '';
				if (image !== '') {
					img = image;
				}
				if (img == '' && noimage !== '') {
					img = noimage;
				}
				if (img !== '') {
					img = img + '?v=' + Math.random();
					$(field).find('img').attr('src', img);
				}
			}

			// Multiple actions
			if (multiple) {
				// Check on page load
				checkLimit();

				// Save text
				if (text) {
					$('body').on('change', result.selector + ' .item [name*="[text]"]', function () {
						saveField();
					});
				}

				// Multiple remove
				$('body').on('click', result.selector + ' .item .actions .remove', function () {
					form.addClass('disable');
					var item = $(this).parents('.item');
					var src = item.find('input[name*="[src]"]').val();
					$.ajax({
						type: 'POST',
						dataType: 'json',
						global: false,
						async: false,
						url: removeurl,
						cache: false,
						data: {'src': src},
						success: function (response) {
							var data = response.data[0];
							if (data) {
								item.remove();
								reIndex();
								saveField();
								checkLimit();
							}
						}
					});
					form.removeClass('disable');
				});

				// Multiple sort
				$(result.selector).sortable({
					handle: ".actions .move",
					start: function (event, ui) {
						result.addClass('sortable');
					},
					stop: function (event, ui) {
						reIndex();
						saveField();
						result.removeClass('sortable');
					}
				});

				// Multiple Reindex data
				function reIndex() {
					result.find('.item').each(function (i, item) {
						var i_key = $(item).data('key');
						var i_prefix = 'image';
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

			}
			// Simple Actions
			else {
				$('body').on('click', field.selector + ' .remove', function () {
					form.addClass('disable');
					var item = $(this).parents('.item');
					var src = result.val();
					$.ajax({
						type: 'POST',
						dataType: 'json',
						global: false,
						async: false,
						url: removeurl,
						cache: false,
						data: {'src': src},
						success: function (response) {
							var data = response.data[0];
							if (data) {
								result.val('');
								saveField();
								if (noimage !== '') {
									$(field).find('img').attr('src', noimage);
								}
								else {
									$(field).find('img').attr('src', '');
								}
							}
						}
					});
					form.removeClass('disable');
				});
			}

			// Input upload
			input.on('change', function (e) {
				if (!form.hasClass('disable')) {
					uploadImages(e.target.files);
				}
			});
			// Drag and Drop upload
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
						uploadImages(e.originalEvent.dataTransfer.files);
					}

				});

			// Upload images
			function uploadImages(files) {
				if (files) {
					if (!multiple) {
						files = [files[0]];
					}
					var folder = field.parents('form').find('[name*="imagefolder"]').val(),
						current = 0,
						total = files.length,
						loading = 0,
						fieldname = name,
						fieldid = id,
						canUpload = true;

					//Block UI
					var blockUI = '<div id="blockUI" ' +
						'style="display: block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; z-index: 999999;"></div>';
					$('body').append($(blockUI));

					// Show progressbar
					progressbar.width('0%');
					progresstext.text('0/0');
					progress.show();

					// Recursive upload
					var i = 0;

					function upload() {
						current = current + 1;
						loading = current / total * 100;

						if (current <= total) {
							if (checkLimit()) {
								// Prepare ajax data
								var ajaxData = new FormData(),
									file = files[i];
								ajaxData.append('folder', folder);
								ajaxData.append('files[]', file);
								ajaxData.append('multiple', multiple);
								ajaxData.append('subfolder', subfolder);
								if (multiple) {
									var number = result.find('.item').length + 1;
									canUpload = checkLimit();
									ajaxData.append('text', text);
									ajaxData.append('unique', unique);
									ajaxData.append('prefix', prefix);
									ajaxData.append('key', 'image_' + number);
									fieldid = id + '_image_' + number;
									fieldname = name.replace('[]', '[image_' + number + ']');
									ajaxData.append('fieldname', fieldname);
									ajaxData.append('fieldid', fieldid);
								}
								else {
									ajaxData.append('current', result.val());
									ajaxData.append('filename', filename);
								}

								$.ajax({
									type: 'POST',
									dataType: 'json',
									processData: false,
									contentType: false,
									url: uploadurl,
									cache: false,
									data: ajaxData,
									success: function (response) {
										var data = response.data[0];
										if (data) {
											if (data.type == 'success') {
												if (multiple) {
													$(data.html).appendTo(result);
												}
												else {
													result.val(data.value);
													$(field).find('img').attr('src', data.image);
												}
												//saveField();
											}
										}

										// Progressbar in progress
										progresstext.text(current + '/' + total);
										progressbar.width(loading + '%');
										i++;
										upload();
									}
								});
							}
							else {
								// Progressbar in progress
								progresstext.text(current + '/' + total);
								progressbar.width(loading + '%');
								i++;
								upload();
							}
						}
						else {
							// Hide progressbar, save field, unblock ui
							saveField();
							$('#blockUI').remove();
							progress.hide();
							progressbar.width('0%');
							progresstext.text('0/0');
						}
					}

					upload();
				}
			}

			// Check limit
			function checkLimit() {
				var canUpload = false;
				if (multiple) {
					if (limit == 0) {
						canUpload = true;
					}
					else {
						var count = result.find('.item').length;
						if (count < limit) {
							canUpload = true;
						}
					}

					if (canUpload) {
						form.show();
						limit_error.hide();
					}
					else {
						form.hide();
						limit_error.show();
					}
				}
				else {
					canUpload = true;
				}

				return canUpload;
			}

			// Save field value
			function saveField() {
				var ajaxData = new FormData();
				ajaxData.append('multiple', multiple);
				if (multiple) {
					reIndex();
					result.find('.item').each(function (i, item) {
						var f_name = 'value[' + $(item).data('key') + ']';
						ajaxData.append(f_name + '[src]', $(item).find('[name*="[src]"]').val());
						ajaxData.append(f_name + '[file]', $(item).find('[name*="[file]"]').val());
						if (text) {
							ajaxData.append(f_name + '[text]', $(item).find('[name*="[text]"]').val());
						}
					});
				}
				else {
					ajaxData.append('value', result.val());
				}
				$.ajax({
					type: 'POST',
					dataType: 'json',
					processData: false,
					contentType: false,
					global: false,
					async: false,
					url: saveurl,
					cache: false,
					data: ajaxData,
					success: function (response) {
						if (multiple) {
							checkLimit();
						}
					}

				});
			}
		});
	});
})
(jQuery);

