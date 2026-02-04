$(function () {
	// Initialize Bootstrap
	// $('[data-bs-toggle="dropdown"]').dropdown();
	// $('[data-bs-toggle="tooltip"]').tooltip();

	// Sidebar
	$("#btnSidebar").on("click", function () {
		$("#sidebar").toggleClass("d-md-block");
		$("#layoutSidebar").toggleClass("d-md-block");
		$("#layoutContent").toggleClass("col-md-10");
	});

	// Notification
	$("#btnNotification").click(function (e) {
		e.stopPropagation();
		var dropdownNotification = $("#dropdownNotification").hasClass("show");

		$("#iconNotification")
			.toggleClass("bi-bell", !dropdownNotification)
			.toggleClass("bi-bell-fill", dropdownNotification);

		$("#dropdownProfile").removeClass("show");
	});

	// Close Notification
	$(document).click(function () {
		$("#iconNotification").removeClass("bi-bell-fill").addClass("bi-bell");
	});

	// Upload Image
	$("#picture").on("change", function () {
		var files = $(this)[0].files; // Mendapatkan semua file yang dipilih
		var fileInfo = "";

		// Loop untuk mendapatkan detail setiap file
		for (var i = 0; i < files.length; i++) {
			var fileName = files[i].name; // Nama file
			var fileSize = (files[i].size / 1024).toFixed(2) + " KB"; // Ukuran file dalam KB

			// Menambahkan informasi file ke variabel fileInfo
			fileInfo += fileName + " <br> " + fileSize;
		}

		// Menampilkan informasi file di div dengan id #fileInfo
		$("#fileInfo").html(fileInfo);
	});

	$("#picture").on("change", function () {
		var file = $(this)[0].files; // Mendapatkan semua file yang dipilih

		if (file) {
			$("#picturePreview").attr("src", URL.createObjectURL(file[0]));
		}
	});

	// Form Input
	$("form").submit(function (e) {
		const form = $(this);

		// Skip forms that have their own custom handlers
		const formsWithCustomHandlers = ['formCreateCustomer', 'formOrder', 'formOrderAgain'];
		if (formsWithCustomHandlers.includes(form.attr('id'))) {
			return; // Let the custom handler in the page handle this
		}

		// Skip AJAX handling for GET forms (allow native browser submission)
		if (form.attr("method")?.toLowerCase() === "get") {
			return true;
		}

		e.preventDefault();
		const inputType =
			form.attr("enctype") === "multipart/form-data"
				? new FormData(this)
				: form.serialize();

		$.ajax({
			url: form.attr("action"),
			method: form.attr("method"),
			data: inputType,
			contentType: inputType instanceof FormData ? false : undefined,
			processData: inputType instanceof FormData ? false : undefined,
			success: function (response) {
				$("form .is-invalid").removeClass("is-invalid");
				if (response.success) {
					Swal.fire({
						icon: "success",
						title: "Success",
						confirmButtonText: "OK",
						text: response.message,
						timer: 2000,
					}).then(() => {
						if (response.url) {
							window.location.href = response.url;
						} else {
							window.location.reload();
						}
					});
				} else {
					let errorMessage = "";

					if (Array.isArray(response.message)) {
						// Handling array of messages
						errorMessage = response.message.map((msg) => `<li>${msg}</li>`).join("");
					} else if (typeof response.message === "string") {
						// Handling single string message
						errorMessage = `<li>${response.message}</li>`;
					} else if (response.message && typeof response.message === "object") {
						// Handling object of field-specific messages
						errorMessage = Object.entries(response.message)
							.map(([field, msg]) => {
								// Add 'is-invalid' class to the incorrect field
								$(`[name="${field}"]`).addClass("is-invalid");
								return `<li>${msg}</li>`;
							})
							.join("");
					} else {
						// Default fallback message
						errorMessage = "<li>Terjadi kesalahan yang tidak diketahui</li>";
					}

					Swal.fire({
						icon: "error",
						title: "Error",
						html: `<ul class="list-unstyled">${errorMessage}</ul>`,
						confirmButtonText: "OK",
						timer: 2000,
					});
				}
			},
			error: function (xhr, status, error) {
				let response = {};
				try {
					response = JSON.parse(xhr.responseText);
				} catch (e) {
					response = { message: xhr.responseText };
				}
				console.error('AJAX error:', response, status, error);
				// Silent fail - no alert shown
			},
		});
	});

	// Button Delete Data
	$(".btn-delete").click((e) => {
		e.preventDefault();

		Swal.fire({
			title: "Yakin data ini dihapus?",
			text: "Anda tidak dapat mengembalikan data ini!",
			icon: "warning",
			showCancelButton: true,
			reverseButtons: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Ya, hapus!",
			cancelButtonText: "Batal",
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: $(e.currentTarget).data("href"),
					method: "POST",
					success: function () {
						Swal.fire({
							title: "Deleted!",
							text: "Data berhasil dihapus.",
							icon: "success",

							timer: 1500,
						}).then(() => window.location.reload());
					},
					error: function (xhr, status, error) {
						Swal.fire({
							title: "Error!",
							text: "Data gagal dihapus.",
							icon: "error",
						});

						console.error("AJAX Error:", error);
						console.log("Status:", status);
						console.log("Response:", xhr.responseText);
					},
				});
			}
		});
	});
});

// Function InitAutoComplete
function initAutoComplete(idSelector, nameSelector, listSelector) {
	const idInput = $("#" + idSelector);
	const nameInput = $("#" + nameSelector);
	const list = "#" + listSelector;

	nameInput.on("focus", function () {
		const query = $(this).val().toLowerCase(); // Ambil value saat ini dari input
		const items = $(list + " .list-group-item");
		let hasVisibleItems = false;

		// Jika ada value, filter daftar berdasarkan value tersebut
		if (query) {
			items.each(function () {
				const itemText = $(this).text().toLowerCase();
				if (itemText.includes(query)) {
					$(this).show();
					hasVisibleItems = true;
				} else {
					$(this).hide();
				}
			});
		} else {
			// Jika tidak ada value, tampilkan semua item
			items.show();
			hasVisibleItems = true;
		}

		// Tampilkan daftar jika ada item yang terlihat
		if (hasVisibleItems) {
			$(list).removeClass("d-none");
		} else {
			$(list).addClass("d-none");
		}
	});

	nameInput.on("focusout", function () {
		setTimeout(function () {
			$(list).addClass("d-none");
		}, 200);
	});

	nameInput.on("input", function () {
		const query = $(this).val().toLowerCase();
		const items = $(list + " .list-group-item");
		let hasVisibleItems = false;

		items.each(function () {
			const itemText = $(this).text().toLowerCase();
			if (itemText.includes(query)) {
				$(this).show();
				hasVisibleItems = true;
			} else {
				$(this).hide();
			}
		});

		if (hasVisibleItems) {
			$(list).removeClass("d-none");
		} else {
			$(list).addClass("d-none");
		}
	});

	$(document).on("click", list + " .list-group-item", function () {
		const id = $(this).data("id");
		const nama = $(this).data("nama");
		idInput.val(id);
		nameInput.val(nama || $(this).text().trim());
		$(list).addClass("d-none");
		// Trigger change event untuk filter atau logic lain
		idInput.trigger('change');
	});

	// Iterate through each list item to determine the maximum width
	var maxWidth = 0;
	$(list + " .list-group-item").each(function () {
		var itemWidth = $(this).outerWidth();
		if (itemWidth > maxWidth) {
			maxWidth = itemWidth;
		}
	});
}

// Function Signature
function signature(canvasId, inputName) {
	var canvas = document.querySelector("#" + canvasId);
	var container = canvas.parentElement;

	// Set canvas size dynamically
	canvas.width = container.offsetWidth;
	canvas.height = container.offsetHeight;

	// Initialize Signature Pad
	var signaturePad = new SignaturePad(canvas, {});

	// Update hidden input when the signature is created
	function updateSignature() {
		if (!signaturePad.isEmpty()) {
			$("#" + inputName).val(signaturePad.toDataURL("image/svg+xml"));
		} else {
			$("#" + inputName).val(""); // Clear input if signature is empty
		}
	}

	// Clear signature and input value on button click
	$("#clear").on("click", function () {
		signaturePad.clear();
		updateSignature(); // Ensure input is cleared too
	});

	// Update hidden input whenever the user draws
	canvas.addEventListener("mouseup", updateSignature);
	canvas.addEventListener("touchend", updateSignature);

	// Return the signaturePad instance for further control if needed
	return signaturePad;
}
