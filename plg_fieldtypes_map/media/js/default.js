/*
 * @package    Field Types - Map Plugin
 * @version    1.0.3
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		$('[data-input-map]').each(function () {
			var field = $(this),
				form = $(this).closest('form'),
				id = field.attr('id'),
				map,
				mapShow = false,
				mapBlock = field.find('#' + id + '_map'),
				fieldParams = Joomla.getOptions(id, ''),
				placemarkURL = fieldParams.placemarkurl,
				storageParams = localStorage.getItem('map');


			if (storageParams) {
				storageParams = $.parseJSON(storageParams);
			}
			else {
				storageParams = {};
				storageParams.latitude = fieldParams.latitude;
				storageParams.longitude = fieldParams.longitude;
				storageParams.center = fieldParams.center;
				storageParams.zoom = fieldParams.zoom;
				localStorage.setItem('map', JSON.stringify(storageParams));

			}

			// set Map height & init
			var mapHeight = setInterval(setMapHeight, 3);

			function setMapHeight() {
				$(mapBlock).height(Math.round(($(mapBlock).width() / 4) * 3));
				if ($(mapBlock).width() > 0 && $(mapBlock).height() > 0) {
					clearInterval(mapHeight);
					ymaps.ready(initializeMap);
					mapShow = true;
				}
			}


			// Get map params
			var mapParams = {};
			if ($('#' + id + '_params_center').val() == '') {
				$('#' + id + '_params_center').val(JSON.stringify(storageParams.center));
			}
			mapParams.center = $.parseJSON($('#' + id + '_params_center').val());
			if ($('#' + id + '_params_latitude').val() == '') {
				$('#' + id + '_params_latitude').val(storageParams.latitude);
			}
			mapParams.latitude = $('#' + id + '_params_latitude').val();

			if ($('#' + id + '_params_longitude').val() == '') {
				$('#' + id + '_params_longitude').val(storageParams.longitude);
			}
			mapParams.longitude = $('#' + id + '_params_longitude').val();

			if ($('#' + id + '_params_zoom').val() == '') {
				$('#' + id + '_params_zoom').val(storageParams.zoom);
			}
			mapParams.zoom = $('#' + id + '_params_zoom').val();

			// Map initialization
			function initializeMap() {
				map = new ymaps.Map(id + '_map', {
					center: mapParams.center,
					zoom: mapParams.zoom,
					controls: ['zoomControl', 'fullscreenControl', 'geolocationControl']
				});

				// Disable scroll zoom
				map.behaviors.disable('scrollZoom');

				// Add search
				var search = new ymaps.control.SearchControl({
					options: {
						'float': 'left',
						'floatIndex': 100,
						'noPlacemark': true
					}
				});
				map.controls.add(search);

				// Placemark
				var placemark = new ymaps.Placemark([0, 0], {}, getplacemarkOptions());

				function getplacemarkOptions() {
					var options = {};
					options.draggable = true;
					if (placemarkURL !== '') {
						var data = $(form).serializeArray();
						$(data).each(function (key, value) {
							if (value.name == 'task') {
								data.splice(key, 1);
							}
						});
						$.ajax({
							type: 'POST',
							dataType: 'json',
							url: placemarkURL,
							data: data,
							global: false,
							async: false,
							success: function (response) {
								if (response.success) {
									var data = response.data;
									$.each(data, function (key, value) {
										if (key == 'customLayout') {
											key = 'iconLayout';
											value = ymaps.templateLayoutFactory.createClass(value);
										}
										options[key] = value;
									});
								}

							}
						});

					}
					else {
						options.iconLayout = 'default#image';
						options.iconImageHref = '/media/plg_fieldtypes_map/images/placemark.png';
						options.iconImageSize = [48, 48];
						options.iconImageOffset = [-24, -48];
					}

					return options;
				}

				// on change form
				$(form).on('change', function () {
					var options = getplacemarkOptions();
					placemark.options.set(options);
				});

				// If has palcemark value
				if ($('#' + id + '_placemark_coordinates').val() !== '') {
					var coordinates = $.parseJSON($('#' + id + '_placemark_coordinates').val());
					map.geoObjects.add(placemark);
					placemark.geometry.setCoordinates(coordinates);
				}

				// On click map
				map.events.add('click', function (e) {
					var coordinates = e.get('coords');
					if (map.geoObjects.getLength() == 0) {
						map.geoObjects.add(placemark);
					}
					placemark.geometry.setCoordinates(coordinates);
					setplacemarkValue(coordinates);
				});
				// On draged placemark
				placemark.events.add('dragend', function () {
					var coordinates = this.geometry.getCoordinates();
					setplacemarkValue(coordinates);
				}, placemark);

				// Delete mark
				placemark.events.add('contextmenu', function () {
					map.geoObjects.remove(placemark);
					$('#' + id + '_placemark_coordinates').val('');
					$('#' + id + '_placemark_latitude').val('');
					$('#' + id + '_placemark_longitude').val('');
				});

				// On search
				search.events.add('resultselect', function () {
					var coordinates = search.getResultsArray()[0].geometry.getCoordinates();
					if (map.geoObjects.getLength() == 0) {
						map.geoObjects.add(placemark);
					}
					placemark.geometry.setCoordinates(coordinates);
					setplacemarkValue(coordinates);
				});

				// Save placemark coordinates
				function setplacemarkValue(coordinates) {
					var latitude = coordinates[0].toFixed(6),
						longitude = coordinates[1].toFixed(6);

					$('#' + id + '_placemark_coordinates').val(JSON.stringify([latitude, longitude]));
					$('#' + id + '_placemark_latitude').val(latitude);
					$('#' + id + '_placemark_longitude').val(longitude);
				}

				// On change map bounds
				map.events.add('boundschange', function (event) {
					//  Change zoom
					if (event.get('newZoom') != event.get('oldZoom')) {
						var zoom = event.get('newZoom');

						mapParams.zoom = zoom;
						$('#' + id + '_params_zoom').val(zoom);
					}

					//  Change center
					if (event.get('newCenter') != event.get('oldCenter')) {
						var
							latitude = event.get('newCenter')[0].toFixed(6),
							longitude = event.get('newCenter')[1].toFixed(6),
							center = [latitude, longitude];

						mapParams.center = center;
						mapParams.latitude = latitude;
						mapParams.longitude = longitude;

						$('#' + id + '_params_center').val(JSON.stringify(center));
						$('#' + id + '_params_latitude').val(latitude);
						$('#' + id + '_params_longitude').val(longitude);
					}

					localStorage.setItem('map', JSON.stringify(mapParams));
				});

			}
		});
	});
})
(jQuery);