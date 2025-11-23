$(function () {
	// Check if payment history table exists
	if ($("#payment-history").length) {
		const paymentHistoryTable = $("#payment-history").DataTable({
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
							<select class="form-select form-select-sm w-100" aria-label="Status Pembayaran" id="statusPaymentFilter">
								<option value="">Status Pembayaran</option>
								<option value="Belum Bayar">Belum Bayar</option>
								<option value="Sebagian">Dibayar Sebagian</option>
								<option value="Lunas">Lunas</option>
							</select>
						</div>
						<div class="col-md-auto col-12">
							<select class="form-select form-select-sm w-100" aria-label="Status Pengiriman" id="statusShipmentFilter">
								<option value="">Status Pengiriman</option>
								<option value="Siap">Siap</option>
								<option value="Mengirim">Mengirim</option>
								<option value="Diterima">Diterima</option>
								<option value="Gagal">Gagal</option>
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

		// Filter by status pembayaran
		$("#statusPaymentFilter").on("change", function () {
			const selectedValue = this.value;
			paymentHistoryTable.column(7).search(selectedValue).draw();
		});

		// Filter by status pengiriman
		$("#statusShipmentFilter").on("change", function () {
			const selectedValue = this.value;
			paymentHistoryTable.column(8).search(selectedValue).draw();
		});
	}
});
