</div>
</main>

<script>
	function pupwindow(url) {
		window.open(url, 'popupWindow', 'width=850,height=550,left=50,top=50,scrollbars=yes');
	};

	function pupwindow1(url) {
		window.open(url, 'popupWindow', 'width=500,height=400, left=100, top=100, location=no');
	};


	$('.sortable').DataTable({
		paging: false,
		info: false,
		searching: false
	});
	$('.sortable2').DataTable({
		lengthMenu: [
			[25, 50, 100, 200],
			[25, 50, 100, 200]
		],
	});
	$(document).ready(function() {
		// Call to_get() function
		to_get();

		$('[data-toggle="tooltip"]').tooltip();

		setTimeout(function() {
			$('.soro').DataTable({
				lengthMenu: [
					[25, 50, 100, 200],
					[25, 50, 100, 200]
				],
			});
		}, 3000);
	});
</script>
</body>

</html>
