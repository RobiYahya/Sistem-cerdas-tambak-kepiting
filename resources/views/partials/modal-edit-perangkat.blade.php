{{-- ===== MODAL EDIT PERANGKAT ===== --}}
<div class="ka-modal-backdrop" id="modalEditPerangkat" aria-hidden="true">
    <div class="ka-modal ka-modal-form" style="max-width: 440px; width: 100%; text-align: left;" role="dialog" aria-modal="true" aria-labelledby="modalEditTitle">
        <div style="text-align: center;">
            <div class="ka-modal-icon" style="background: #eaf7cf; color: #153024; border-color: #c5e87a;">
                <i class="bi bi-pencil-square"></i>
            </div>
            <h3 id="modalEditTitle" style="margin-bottom: 6px;">Edit Informasi Perangkat</h3>
            <p style="margin-bottom: 22px;">Ubah nama kolam atau lokasi penempatan perangkat sensor.</p>
        </div>

        <form id="formEditPerangkat" method="POST" action="">
            @csrf
            <div class="modal-form-group">
                <label for="editNamaPerangkat">Nama Kolam / Perangkat</label>
                <input type="text" id="editNamaPerangkat" name="nama" class="modal-form-input" required>
            </div>

            <div class="modal-form-group">
                <label for="editLokasiPerangkat">Lokasi / Keterangan</label>
                <input type="text" id="editLokasiPerangkat" name="lokasi" class="modal-form-input">
            </div>

            <div class="ka-modal-actions" style="margin-top: 24px;">
                <button type="button" class="ka-btn-cancel" id="btnBatalEdit">Batal</button>
                <button type="submit" class="ka-btn-confirm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

