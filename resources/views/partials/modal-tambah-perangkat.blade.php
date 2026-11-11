{{-- ===== MODAL TAMBAH PERANGKAT (MULTI-DEVICE REGISTRATION) ===== --}}
<div class="ka-modal-backdrop" id="modalTambahPerangkat" aria-hidden="true">
    <div class="ka-modal ka-modal-form" style="max-width: 480px; width: 100%; text-align: left;" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        
        <div style="text-align: center;">
            <div class="ka-modal-icon" style="background: #eaf7cf; color: #153024; border-color: #c5e87a;">
                <i class="bi bi-hdd-network"></i>
            </div>
            <h3 id="modalTitle" style="margin-bottom: 6px;">Tambah Perangkat Tambak</h3>
            <p style="margin-bottom: 22px; font-size: 13.5px; color: var(--muted);">Daftarkan modul sensor kolam untuk menghasilkan kunci koneksi (API Token).</p>
        </div>

        <form action="{{ route('perangkat.store') }}" method="POST" id="formDaftarPerangkat">
            @csrf
            <div class="modal-form-group">
                <label for="namaKolam">Nama Kolam / Perangkat</label>
                <input type="text" id="namaKolam" name="nama" class="modal-form-input" placeholder="Contoh: Tambak Pembesaran #02" required autofocus>
            </div>

            <div class="modal-form-group">
                <label for="lokasiTambak">Lokasi / Keterangan (Opsional)</label>
                <input type="text" id="lokasiTambak" name="lokasi" class="modal-form-input" placeholder="Contoh: Blok Timur No. 04">
            </div>

            <div class="token-result-box" style="margin-top: 14px; margin-bottom: 18px; padding: 12px 14px;">
                <span style="font-size: 11.5px; font-weight: 600; color: #166534; display: block;">Perangkat akan di-generate otomatis:</span>
                <span style="font-size: 12px; color: #15803d; display: block; margin-top: 4px;">
                    <i class="bi bi-key-fill"></i> API Token unik & Endpoint URL akan aktif seketika setelah disimpan.
                </span>
            </div>

            <div class="ka-modal-actions" style="margin-top: 20px;">
                <button type="button" class="ka-btn-cancel" id="btnTutupModal">Batal</button>
                <button type="submit" class="ka-btn-confirm">Daftarkan Perangkat</button>
            </div>
        </form>

    </div>
</div>
