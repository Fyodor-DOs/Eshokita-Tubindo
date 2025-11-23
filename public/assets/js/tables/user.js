$(function () {
	const user = $("#user").DataTable({
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
				function () {
					let element = document.createElement("div");

					element.innerHTML = `
					<div class="row align-items-center">

						<div class="col-md-auto col-12">Filter by:</div>

						<div class="col-md-auto col-12">
							<select class="form-select form-select-sm w-100" aria-label="Role" id="roleFilter">
								<option value="">Role</option>
								<option value="super-admin">Super Admin</option>
								<option value="admin">Admin</option>
								<option value="produksi">Produksi</option>
								<option value="distributor">Distributor</option>
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
				{
					buttons: [
						{
							text: "Tambah",
							className: "btn btn-sm",
							attr: {
								style: "background-color: #0d6efd; border-color: #0d6efd;",
							},
							action: function (dt) {
								window.location.href = "/user/create";
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

	$("#roleFilter").on("change", function () {
		var selectedValue = $(this).val();
		user.column(-2).search(selectedValue).draw();
	});
});
