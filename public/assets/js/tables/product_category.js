$(function () {
	const categoryTable = $("#table-category").DataTable({
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
				{
					search: {
						placeholder: "Cari disini",
					},
				},
				{
					buttons: [
						{
							text: "Tambah",
							className: "btn btn-sm",
							attr: {
								style: "background-color: #0d6efd; border-color: #0d6efd;",
							},
							action: function (dt) {
								window.location.href = "/product-category/create";
							},
						},
					],
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
});
