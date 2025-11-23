$(function () {
	$("#table-rute").DataTable({
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
								window.location.href = "/rute/create";
							},
						},
					],
				},
			],
		},

		columnDefs: [
			{
				targets: [-1],
				orderable: false,
				searchable: false,
			},
			{
				targets: [0],
				searchable: false,
			},
		],
	});
});
