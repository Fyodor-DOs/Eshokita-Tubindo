<form action="<?= esc($segment) ?>" method="get" class="w-100 my-0">
    <div class="input-group">
        <input type="text" id="searchInput" class="form-control bg-white" name="search"
            placeholder="<?= $placeholder ?>" value="<?= $search ?? '' ?>">
        <button class="btn btn-link position-absolute py-0 d-none" id="resetButton" type="button"
            style="right: 60px; top: 50%; transform: translateY(-50%);" data-bs-toggle="tooltip" data-bs-title="Reset"
            data-bs-placement="bottom">
            <i class="bi bi-x-circle-fill text-danger bg-white"></i>
        </button>
        <button class="btn btn-primary" id="submitButton" type="submit">Cari</button>
    </div>
</form>

<style>


</style>

<script>
    const searchInput = document.getElementById('searchInput');
    const resetButton = document.getElementById('resetButton');

    // Menampilkan tombol reset jika ada input
    searchInput.addEventListener('input', function() {
        resetButton.classList.toggle('d-none', !this.value);
    });

    if (searchInput.value) {
        resetButton.classList.remove('d-none');
    }

    // Fungsi untuk mereset input
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        resetButton.classList.add('d-none');
        searchInput.focus();
    });
</script>