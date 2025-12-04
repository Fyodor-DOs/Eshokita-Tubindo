$(function () {
  const nota = $('#nota').DataTable({
    responsive: true,
    fixedColumns: true,
    scrollCollapse: true,
    scrollX: true,
    scrollY: 450,
    language: {
      emptyTable: 'Tidak ada data',
      lengthMenu: '_MENU_',
      search: '_INPUT_',
    },
    layout: {
      topStart: [
        { pageLength: '_MENU_' },
        {
          buttons: [
            {
              extend: 'copy',
              text: 'Copy',
              exportOptions: {
                columns: ':not(:last-child)',
              },
            },
            {
              extend: 'csv',
              text: 'CSV',
              exportOptions: {
                columns: ':not(:last-child)',
              },
            },
            {
              extend: 'excel',
              text: 'Excel',
              exportOptions: {
                columns: ':not(:last-child)',
              },
            },
            {
              extend: 'pdf',
              text: 'PDF',
              exportOptions: {
                columns: ':not(:last-child)',
              },
            },
            {
              extend: 'print',
              text: 'Print',
              exportOptions: {
                columns: ':not(:last-child)',
              },
            },
          ],
        },
      ],
      topEnd: [
        function () {
          let element = document.createElement('div');

          element.innerHTML = `
						<div class="row align-items-center">
							<div class="col-md-auto col-12">Filter by:</div>
							<div class="col-md-auto col-12">
								<select class="form-select form-select-sm w-100" aria-label="Rute" id="ruteFilter">
									<option value="">Loading...</option>
								</select>
							</div>
						</div>`;

          // Tunggu hingga elemen terpasang di DOM
          setTimeout(() => {
            const ruteSelect = element.querySelector('#ruteFilter');
            $.ajax({
              url: '/rute/get-rute',
              type: 'GET',
              success: function (data) {
                ruteSelect.innerHTML = '<option value="">Rute</option>'; // Reset dropdown
                data.forEach(function (rute) {
                  ruteSelect.innerHTML += `<option value="${rute.nama_wilayah}">${rute.nama_wilayah}</option>`;
                });
              },
              error: function () {
                ruteSelect.innerHTML =
                  '<option value="">Gagal memuat rute</option>';
              },
            });
          }, 0); // Dijalankan segera setelah rendering selesai

          return element;
        },
        {
          search: {
            placeholder: 'Cari disini',
          },
        },
        {
          buttons: [
            {
              text: 'Tambah',
              className: 'btn btn-sm',
              attr: {
                style: 'background-color: #0d6efd; border-color: #0d6efd;',
              },
              action: function (dt) {
                window.location.href = '/nota/create';
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

  $('#ruteFilter').on('change', function () {
    var selectedValue = $(this).val();
    nota.column(4).search(selectedValue).draw();
  });

  $('#dt-export-0').on('change', function () {
    var selectedValue = $(this).val();
    if (selectedValue) {
      surat_jalan.buttons(`.${selectedValue}`).trigger();
      $(this).val('');
    }
  });
});
