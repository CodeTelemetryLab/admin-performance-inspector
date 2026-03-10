(function ($) {
	'use strict';

	$(document).ready(function () {
		var $tbody = $('#api-query-tbody');
		if (!$tbody.length) return;

		var perPage = parseInt($tbody.data('per-page'), 10) || 20;
		var currentPage = 1;
		var currentFilter = 'all';

		function renderTable() {
			var $rows = $tbody.find('.api-query-row');
			var $visibleRows = $rows;

			if (currentFilter === 'slow') {
				$visibleRows = $rows.filter('.is-slow');
				$rows.not('.is-slow').hide();
			} else {
				$rows.show();
			}

			var totalItems = $visibleRows.length;
			var totalPages = Math.ceil(totalItems / perPage) || 1;

			if (currentPage > totalPages) {
				currentPage = totalPages;
			}

			$('.api-displaying-num-val').text(totalItems);

			var startIndex = (currentPage - 1) * perPage;
			var endIndex = startIndex + perPage;

			$visibleRows.each(function (index) {
				if (index >= startIndex && index < endIndex) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});

			$('.current-page').val(currentPage);
			$('.current-page-display').text(currentPage);
			$('.total-pages').text(totalPages);

			$('.tablenav-pages').toggleClass('one-page', totalPages <= 1);

			$('.first-page, .prev-page').toggleClass('disabled', currentPage <= 1);
			$('.last-page, .next-page').toggleClass('disabled', currentPage >= totalPages);
		}

		renderTable();

		$('.api-query-filters a').on('click', function (e) {
			e.preventDefault();
			var filter = $(this).data('filter');
			if (filter) {
				$('.api-query-filters a').removeClass('current');
				$(this).addClass('current');
				currentFilter = filter;
				currentPage = 1;
				renderTable();
			}
		});

		$('.tablenav-pages .button').on('click', function (e) {
			e.preventDefault();
			if ($(this).hasClass('disabled')) return;

			var totalPages = parseInt($('.total-pages').first().text(), 10);

			if ($(this).hasClass('next-page')) {
				currentPage++;
			} else if ($(this).hasClass('prev-page')) {
				currentPage--;
			} else if ($(this).hasClass('first-page')) {
				currentPage = 1;
			} else if ($(this).hasClass('last-page')) {
				currentPage = totalPages;
			}

			renderTable();
		});

		$('.current-page').on('change', function () {
			var totalPages = parseInt($('.total-pages').first().text(), 10);
			var requestedPage = parseInt($(this).val(), 10);

			if (!isNaN(requestedPage)) {
				currentPage = Math.max(1, Math.min(requestedPage, totalPages));
			}
			renderTable();
		});
	});

})(jQuery);
