{{-- ===== MODAL HAPUS PERANGKAT ===== --}}
<div class="ka-modal-backdrop" id="modalHapusPerangkat" aria-hidden="true">
    <div class="ka-modal" style="max-width: 420px; width: 100%; text-align: center;" role="dialog" aria-modal="true" aria-labelledby="modalHapusTitle">
        <div class="ka-modal-icon" style="background: #fef2f2; color: #dc2626; border-color: #fecaca;">
            <i class="bi bi-trash3-fill"></i>
        </div>
        <h3 id="modalHapusTitle" style="margin-bottom: 8px;">Hapus Perangkat Tambak?</h3>
        <p style="margin-bottom: 24px; font-size: 13.5px; color: var(--muted); line-height: 1.6;">
            Apakah Anda yakin ingin menghapus perangkat <strong id="hapusNamaPerangkat" style="color: var(--ink);"></strong>? Data sensor yang tersimpan tidak akan dapat dipantau lagi.
        </p>

        <form id="formHapusPerangkat" method="POST" action="">
            @csrf
            <div class="ka-modal-actions">
                <button type="button" class="ka-btn-cancel" id="btnBatalHapus">Batal</button>
                <button type="submit" class="ka-btn-confirm" style="background: #dc2626;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

