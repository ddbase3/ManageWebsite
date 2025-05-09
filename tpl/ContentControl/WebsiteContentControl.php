<div id="WebsiteContentControl" class="contentcontrol">

	<div id="wrap">

		<div id="websiteDatatable"></div>

	</div>

</div>

<script>
  var columns = [
    { key: 'group', label: 'Gruppe' },
    { key: 'url', label: 'URL' },
    { key: 'http_status', label: 'HTTP Status' },
    { key: 'title', label: 'Title' },
    { key: 'meta_description', label: 'Meta Description' },
    // { key: 'last_access', label: 'Letzter Zugriff' },
    { key: 'load_time_ms', label: 'Ladezeit (ms)' },
    { key: 'has_robots_txt', label: 'robots.txt' },
    { key: 'has_favicon_ico', label: 'favicon.ico' }
  ];

  $('#websiteDatatable').jqueryDataTable({
    url: '?name=websiteconnector&out=json',
    columns: columns,
    sortColumn: 'url',
    sortDirection: 'asc',
    pageSize: 20,
    onRowClick: function(row) {
      console.log('Zeile geklickt:', row);
    }
  });
</script>

