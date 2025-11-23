$(function () {
	const stockTable = $("#table-stock").DataTable({
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
						},
						{
							extend: "csv",
							text: "CSV",
						},
						{
							extend: "excel",
							text: "Excel",
						},
						{
							extend: "pdf",
							text: "PDF",
						},
						{
							extend: "print",
							text: "Print",
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
							text: "Adjustment",
							className: "btn btn-sm",
							attr: {
								style: "background-color: #0d6efd; border-color: #0d6efd;",
							},
							action: function (dt) {
								window.location.href = "/stock/adjust";
							},
						},
					],
				},
			],
		},
		columnDefs: [
			{
				targets: 2,
				className: "text-center",
			},
		],
	});
});
