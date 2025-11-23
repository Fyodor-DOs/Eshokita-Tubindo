$(function () {
	$("#table-transaction").DataTable({
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
		order: [[2, 'desc']]
	});
});
