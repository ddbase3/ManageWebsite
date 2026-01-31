<div id="WebsiteContentControl" class="contentcontrol">
	<div id="wrap">
		<div id="websiteDatatable"></div>
	</div>
</div>

<script>
        (async function () {
                await AssetLoader.loadCssAsync('<?php echo $this->_['resolve']('plugin/ClientStack/assets/jquerydatatable/jquery.datatable.min.css'); ?>');
                await AssetLoader.loadScriptAsync('<?php echo $this->_['resolve']('plugin/ClientStack/assets/jquerydatatable/jquery.datatable.min.js'); ?>');
	
		var columns = [
			{ key: 'group', label: 'Gruppe' },
			{ key: 'url', label: 'URL' },
			{ key: 'http_status', label: 'HTTP Status' },
			{ key: 'title', label: 'Title' },
			{ key: 'meta_description', label: 'Meta Description', visible: false },
			{ key: 'last_access', label: 'Letzter Zugriff', visible: false },
			{ key: 'load_time_ms', label: 'Ladezeit' },
			{ key: 'has_robots_txt', label: 'robots.txt' },
			{ key: 'has_favicon_ico', label: 'favicon.ico', options: [
				{ value: 'false', label: '❌ nein' },
				{ value: 'true', label: '✅ ja' }
			] }
		];

		var infoRenderer = function($el, { page, pageSize, total }) {
			const from = (page - 1) * pageSize + 1;
			const to = Math.min(page * pageSize, total);
			return `Zeige ${from} - ${to} von ${total}`;
		};

		var filterRenderer = function (col, settings, $el) {
			return col.key == 'has_favicon_ico'
				? $.fn.jqueryDataTable.renderers.filterCellSelect(col, settings, $el)
				: $.fn.jqueryDataTable.renderers.filterCell(col, settings, $el);
		};

	        var cellRenderer = function (row, column, value, type) {
			if (type == 'value' && column.key == 'load_time_ms') {
				var c = 'transparent';
				if (value >= 500) c = 'yellow';
				if (value >= 1000) c = 'orange';
				if (value >= 2000) c = 'red';
				return $('<td style="background:' + c + ';"></td>');
			}
			return $.fn.jqueryDataTable.renderers.cell(row, column, value, type);
		};

		var valueRenderer = function(row, column, value) {
			switch (column.key) {
				case 'url':
					var link = $('<a href="' + value + '" target="_blank">' + value + '</a>');
					link.css({
						'display': 'inline-block',
						'padding-left': '24px',
						'height': '16px',
						'background-repeat': 'no-repeat',
						'background-position': '0 0',
						'background-size': 'contain',
						'line-height': '16px',
						'vertical-align': 'middle'
					});
					if (row['has_favicon_ico'] == 'true') link.css('background-image', 'url(' + row['url'] + '/favicon.ico)');
					return link;
				case 'http_status':
					return $('<span' + ( value == 200 ? '' : ' style="color:red; font-weight:bold;"' ) + '>' + value + '<span>');
				case 'load_time_ms':
					return '<span style="display:inline-block; width:100%; text-align:right;">' + value + ' ms</span>';
				case 'has_robots_txt':
					return $('<span style="display:inline-block; width:100%; text-align:center;">' + (value == 'false' ? '❌' : '✅') + '</span>');
				case 'has_favicon_ico':
					var str = value == 'false' ? '❌' : '<img src="' + row['url'] + '/favicon.ico" style="width:16px;">';
					return $('<span style="display:inline-block; width:100%; text-align:center;">' + str + '</span>');
			}
			return $.fn.jqueryDataTable.renderers.valueCell(row, column, value);
		};

		$('#websiteDatatable').jqueryDataTable({
			dataSource: '?name=websiteconnector&out=json',
			columns: columns,
			sortColumn: 'url',
			sortDirection: 'asc',
			pageSizeOptions: [3, 5, 10, 20, 50],
			pageSize: 5,
			layoutTargets: {
				'.header-left': ['columnSelector'],
				'.header-right': ['pager'],
				'.footer-left': ['resetButton'],
				'.footer-center': ['info'],
				'.footer-right': ['pageSizeSelector']
			},
			renderers: {
				pager: $.fn.jqueryDataTable.renderers.compactPager,
				info: infoRenderer,
				filterCell: filterRenderer,
				valueCell: valueRenderer,
				cell: cellRenderer
			},
			onRowClick: function(row) {
				console.log('Zeile geklickt:', row);
			}
		});
	})();
</script>

