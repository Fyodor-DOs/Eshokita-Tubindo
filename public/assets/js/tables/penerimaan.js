$(function () {
	const penerimaanTable = $("#table-penerimaan").DataTable({
		responsive: true,
		fixedColumns: true,
		scrollCollapse: true,
		scrollX: true,
		scrollY: 450,
		language: {
			emptyTable: "Tidak ada data",
			lengthMenu: "_MENU_",
			search: "_INPUT_",
		},
		layout: {
			topStart: [
				{ pageLength: "_MENU_" },
				{
					buttons: [
						{
							extend: "copy",
							text: "Copy",
							exportOptions: {
								columns: ":not(:last-child)",
							},
						},
						{
							extend: "csv",
							text: "CSV",
							exportOptions: {
								columns: ":not(:last-child)",
							},
						},
						{
							extend: "excel",
							text: "Excel",
							exportOptions: {
								columns: ":not(:last-child)",
							},
						},
						{
							extend: "pdf",
							text: "PDF",
							exportOptions: {
								columns: ":not(:last-child)",
							},
						},
						{
							extend: "print",
							text: "Print",
							exportOptions: {
								columns: ":not(:last-child)",
							},
						},
					],
				},
			],
			topEnd: [
				function () {
					let element = document.createElement("div");

					element.innerHTML = `
				<div class="row align-items-center">
					<div class="col-md-auto col-12">Filter by:</div>
					<div class="col-md-auto col-12">
						<select class="form-select form-select-sm w-100" aria-label="Status" id="statusFilter">
							<option value="">Status</option>
							<option value="received">Received</option>
							<option value="partial">Partial</option>
							<option value="pending">Pending</option>
						</select>
					</div>
					<div class="col-md-auto col-12">
						<select class="form-select form-select-sm w-100" aria-label="Verifikasi" id="verifikasiFilter">
							<option value="">Verifikasi</option>
							<option value="Verified">Verified</option>
							<option value="Pending">Pending</option>
						</select>
					</div>
				</div>`;
					return element;
				},
				{
					search: {
						placeholder: "Cari disini",
					},
				},
			],
		},
		columnDefs: [
			{
				targets: -1,
				orderable: false,
			},
		],
	});

	// Filter by status
	$("#statusFilter").on("change", function () {
		const selectedValue = this.value;
		penerimaanTable.column(2).search(selectedValue).draw();
	});

	// Filter by verifikasi
	$("#verifikasiFilter").on("change", function () {
		const selectedValue = this.value;
		penerimaanTable.column(4).search(selectedValue).draw();
	});
});
