document.addEventListener("DOMContentLoaded", function () {
	LaporanSampah();
	KategoriSampah();
	TopPelapor();

	function LaporanSampah() {
		var bulan = [];
		var total = [];

		var tahunIni = new Date().getFullYear();

		var namaBulan = [
			"Jan",
			"Feb",
			"Mar",
			"Apr",
			"Mei",
			"Jun",
			"Jul",
			"Agst",
			"Sep",
			"Okt",
			"Nov",
			"Des",
		];

		$.ajax({
			url: "/laporan-sampah",
			type: "GET",
			success: function (data) {
				for (var i = 0; i < data.length; i++) {
					bulan.push(namaBulan[data[i].bulan] + " " + tahunIni);
					total.push(data[i].total);
				}

				var options = {
					chart: {
						type: "line",
					},
					series: [
						{
							name: "Jumlah",
							data: total,
						},
					],
					xaxis: {
						categories: bulan,
					},
				};

				var chart = new ApexCharts(document.querySelector("#chart"), options);

				chart.render();
			},
			error: function (data) {
				console.log(data);
			},
		});
	}

	function KategoriSampah() {
		var options = {
			chart: {
				type: "donut",
			},
			series: [44, 55, 41, 17, 15, 15, 21],
			labels: [
				"Organik",
				"Anorganik",
				"B3",
				"Daur Ulang",
				"Medis",
				"Elektronik",
				"Konstruksi",
			],
		};

		var chart = new ApexCharts(
			document.querySelector("#chartKategori"),
			options
		);

		chart.render();
	}

	function TopPelapor() {
		$.ajax({
			url: "/top-pelapor",
			type: "GET",
			success: function (data) {
				console.log(data);
			},
			error: function (data) {
				console.log(data);
			},
		});
		var options = {
			chart: {
				type: "bar",
			},
			series: [
				{
					name: "series-1",
					data: [30, 40, 35, 50, 49, 60, 70, 91, 125],
				},
			],
			xaxis: {
				categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999],
			},
		};

		var chart = new ApexCharts(
			document.querySelector("#chartPelapor"),
			options
		);

		chart.render();
	}
});
