{{-- Modal Info Node Telemetri --}}
<div class="ka-modal-backdrop" id="modalInfoNode" aria-hidden="true">
    <div class="ka-modal" style="max-width: 480px;">
        <div class="ka-modal-icon" style="background: #e0f2fe; color: #0284c7;">
            <i class="bi bi-hdd-network"></i>
        </div>
        <h3 id="infoNodeTitle">Informasi Node Telemetri</h3>
        <p style="margin-bottom: 16px;">Konfigurasi dan kredensial node sensor ESP32 untuk wadah budidaya ini.</p>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 14px 16px; margin-bottom: 16px; text-align: left;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; color: #64748b; font-weight: 500;">ID Node:</span>
                <strong id="infoNodeId" style="font-size: 13px; color: #0f172a; font-family: monospace;">-</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; color: #64748b; font-weight: 500;">Lokasi Pemasangan:</span>
                <span id="infoNodeLokasi" style="font-size: 13px; color: #0f172a; font-weight: 600;">-</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <span style="font-size: 12px; color: #64748b; font-weight: 500;">Status Telemetri:</span>
                <span id="infoNodeStatus" style="font-size: 12px; font-weight: 700; color: #16a34a;">🟢 ONLINE</span>
            </div>

            <div style="border-top: 1px dashed #cbd5e1; padding-top: 10px;">
                <span style="display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">API KEY ESP32</span>
                <div style="display: flex; align-items: center; justify-content: space-between; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 10px;">
                    <code id="infoNodeToken" style="font-size: 12.5px; font-weight: 600; color: #0f172a;">-</code>
                    <button type="button" class="btn-copy-token" id="btnCopyInfoToken" data-token="" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer; color: #334155;">Salin</button>
                </div>
            </div>
        </div>

        <div class="ka-modal-actions" style="display: flex; gap: 8px; justify-content: flex-end;">
            @if (session('user_role', 'teknisi') === 'teknisi')
                <button type="button" class="btn-edit-device btn-action-icon" id="btnInfoEdit" data-id="" data-nama="" data-lokasi="" title="Edit Wadah" style="height: 40px; padding: 0 14px; border-radius: 10px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #cbd5e1; color: #334155;">
                    <i class="bi bi-pencil"></i> <span>Edit</span>
                </button>
                <button type="button" class="btn-delete-device btn-action-icon btn-delete" id="btnInfoDelete" data-id="" data-nama="" title="Hapus Wadah" style="height: 40px; padding: 0 14px; border-radius: 10px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-trash3"></i> <span>Hapus</span>
                </button>
            @endif
            <button type="button" class="ka-btn-cancel" id="btnTutupInfoNode" style="height: 40px;">Tutup</button>
        </div>
    </div>
</div>

