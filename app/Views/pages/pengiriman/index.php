<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'pengiriman']) ?>

<div class="card mb-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="pengiriman">
                <thead>
                    <tr>
                        <th scope="col" class="col-1 text-center">No.</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col" class="text-start">No. BON</th>
                        <th scope="col">Rute</th>
                        <th scope="col">Supir</th>
                        <th scope="col">Plat Kendaraan</th>
                        <th scope="col">Status</th>
                        <th scope="col">Operasional</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengiriman as $key => $value) : ?>
                    <tr>
                        <td class="text-center"><?= $key + 1 ?></td>
                        <td><?= date('d M Y', strtotime($value['tanggal'])) ?></td>
                        <td class="text-start"><strong><?= $value['no_bon'] ?></strong></td>
                        <td class="text-start"><?= $value['nama_wilayah'] ?? '-' ?></td>
                        <td><?= esc($value['supir']) ?></td>
                        <td><?= esc($value['plat_kendaraan']) ?></td>
                        <td id="status-cell-<?= $value['id_pengiriman'] ?>">
                            <?php
                            $status = $value['status'] ?? 'siap';
                            $map = [
                                'siap' => ['warning','Siap'],
                                'mengirim' => ['primary','Mengirim'],
                                'diterima' => ['success','Diterima'],
                                'gagal' => ['danger','Gagal']
                            ];
                            [$cls,$lbl] = $map[$status] ?? ['secondary', ucfirst($status)];
                            echo '<span id="status-badge-' . $value['id_pengiriman'] . '" class="badge bg-' . $cls . '">' . $lbl . '</span>';
                            ?>
                        </td>
                        <td>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-modal-upload" data-id="<?= $value['id_pengiriman'] ?>">
                                    <i class="bi bi-upload"></i> Kelola Upload (per Invoice)
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('pengiriman/detail/' . $value['id_pengiriman']) ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Detail</a>
                                <a href="<?= base_url('pengiriman/edit/' . $value['id_pengiriman']) ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                                <button type="button" data-href="<?= base_url('pengiriman/delete/' . $value['id_pengiriman']) ?>" class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash"></i> Delete</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Kelola Upload per Invoice -->
<div class="modal fade" id="modalKelolaUpload" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelola Upload per Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Customer</th>
                                <th class="text-end">Jumlah</th>
                                <th>Status</th>
                                <th>Surat Jalan</th>
                                <th>Bukti Diterima</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyInv"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

<script>
// Ensure SweetAlert2 is available
(function ensureSwal(){
    if (typeof Swal !== 'undefined') return;
    var s=document.createElement('script'); s.src='https://cdn.jsdelivr.net/npm/sweetalert2@11'; document.head.appendChild(s);
})();

function notifyAlert(success, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: success ? 'success' : 'error',
            title: success ? 'Sukses' : 'Gagal',
            text: message
        });
    } else {
        alert((success ? '' : 'Gagal: ') + message);
    }
}

function computeStatusFromInvoices(invoices){
    if(!invoices || invoices.length===0) return 'siap';
    const allSJ = invoices.every(x => !!x.foto_surat_jalan);
    const allTerima = invoices.every(x => !!x.foto_penerimaan);
    if (allTerima) return 'diterima';
    if (allSJ) return 'mengirim';
    return 'siap';
}

function renderStatusBadge(status){
    const map = { siap:['warning','Siap'], mengirim:['primary','Mengirim'], diterima:['success','Diterima'], gagal:['danger','Gagal'] };
    const m = map[status] || ['secondary', status];
    return `<span class="badge bg-${m[0]}">${m[1]}</span>`;
}
// Upload buttons open modals and set target id
const modalEl = document.getElementById('modalKelolaUpload');
async function loadInvoicesIntoModal(idPengiriman){
    modalEl.dataset.idPengiriman = idPengiriman;
    const r = await fetch('<?= base_url('pengiriman/invoices') ?>/'+idPengiriman);
    const j = await r.json();
    const tbody = document.getElementById('tbodyInv');
    tbody.innerHTML = '';
    (j.data||[]).forEach(inv => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${inv.invoice_no || inv.id_invoice}</td>
            <td>${inv.customer_name || '-'}</td>
            <td class="text-end">${Number(inv.amount||0).toLocaleString('id-ID')}</td>
            <td><span class="badge bg-${(inv.status||'')==='paid'?'success':'secondary'}">${(inv.status||'-')}</span></td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <span class="small ${inv.foto_surat_jalan? 'text-success':'text-muted'}">${inv.foto_surat_jalan? 'Sudah':'Belum'}</span>
                    <button class="btn btn-sm btn-outline-primary" data-action="sj" data-id="${inv.id_invoice}" ${inv.foto_surat_jalan? 'disabled':''}><i class="bi bi-upload"></i> ${inv.foto_surat_jalan? 'Sudah Upload':'Upload'}</button>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <span class="small ${inv.foto_penerimaan? 'text-success':'text-muted'}">${inv.foto_penerimaan? 'Sudah':'Belum'}</span>
                    <button class="btn btn-sm btn-success" data-action="terima" data-id="${inv.id_invoice}" ${inv.foto_penerimaan? 'disabled':(inv.foto_surat_jalan? '':'disabled title=\"Upload Surat Jalan dahulu\"')}><i class="bi bi-check2-circle"></i> ${inv.foto_penerimaan? 'Sudah Upload':'Upload'}</button>
                </div>
            </td>`;
        tbody.appendChild(tr);
    });

    // Update status badge on main table from current invoices state
    const status = computeStatusFromInvoices(j.data||[]);
    const badgeContainer = document.getElementById('status-badge-'+idPengiriman);
    if (badgeContainer) {
        const cell = document.getElementById('status-cell-'+idPengiriman);
        if (cell) cell.innerHTML = renderStatusBadge(status);
    }
}

document.querySelectorAll('.btn-modal-upload').forEach(btn => {
    btn.addEventListener('click', async function(){
        await loadInvoicesIntoModal(this.dataset.id);
        const m = new bootstrap.Modal(modalEl);
        m.show();
    });
});

// Submit handlers
// Delegate upload buttons inside modal
document.getElementById('tbodyInv').addEventListener('click', async function(e){
    const btn = e.target.closest('button');
    if(!btn) return;
    const action = btn.dataset.action;
    const idInv = btn.dataset.id;
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = async () => {
        const file = input.files[0];
        if(!file) return;
        const fd = new FormData();
        fd.append('foto', file);
        const url = action==='sj' ? '<?= base_url('pengiriman/uploadSuratJalanInvoice') ?>/'+idInv : '<?= base_url('pengiriman/uploadPenerimaanInvoice') ?>/'+idInv;
        try{
            const r = await fetch(url, { method:'POST', body: fd });
            const j = await r.json();
            if(j.success){
                if(typeof Swal !== 'undefined'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: 'Berhasil diunggah',
                    }).then(() => {
                        window.location.reload();
                    });
                }else{
                    alert('Berhasil diunggah');
                    window.location.reload();
                }
            }else{
                notifyAlert(false, j.message||'Gagal mengunggah');
            }
        }catch(err){ alert('Gagal mengunggah'); }
    };
    input.click();
});
</script>
<?= $this->endSection() ?>