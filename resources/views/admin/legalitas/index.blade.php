@extends('layouts.admin')

@section('title', 'Dokumen Legalitas & Perizinan Cukai')
@section('header_title', 'Kelola Legalitas, NPPBKC & Izin Usaha Pabrik')

@section('content')
<div class="admin-grid-2col">
    <!-- Tabel Dokumen -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; overflow-x: auto;">
        <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 16px;">
            <i class="fa-solid fa-stamp"></i> Daftar Dokumen Cukai &amp; Izin Berusaha
        </h3>

        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                    <th style="padding: 10px;">Nama Izin / Dokumen</th>
                    <th style="padding: 10px;">Nomor Dokumen</th>
                    <th style="padding: 10px;">Penerbit</th>
                    <th style="padding: 10px;">Lampiran</th>
                    <th style="padding: 10px; text-align: center;">Status</th>
                    <th style="padding: 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px;">
                            <strong>{{ $doc->nama_dokumen }}</strong>
                            <div style="font-size: 11px; color: #64748b;">
                                Berlaku: {{ $doc->berlaku_sampai ? $doc->berlaku_sampai->format('d/m/Y') : 'Selama Operasional' }}
                            </div>
                        </td>
                        <td style="padding: 10px; font-family: monospace; color: #b45309;">
                            {{ $doc->nomor ?? '-' }}
                        </td>
                        <td style="padding: 10px; color: #475569;">{{ $doc->penerbit ?? '-' }}</td>
                        <td style="padding: 10px; font-size: 12px;">
                            @if($doc->file_dokumen)
                                <a href="{{ asset('storage/' . $doc->file_dokumen) }}" target="_blank" style="color: #0369a1; font-weight: 600; text-decoration: underline;">
                                    Lihat File
                                </a>
                            @else
                                <span style="color: #94a3b8;">Arsip fisik pabrik</span>
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: #ecfdf5; color: #059669; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                                {{ $doc->status }}
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <div style="display: inline-flex; gap: 6px; align-items: center;">
                                <button type="button" onclick='openEditLegalitasModal(@json($doc->id), @json($doc->nama_dokumen), @json($doc->nomor), @json($doc->penerbit), @json($doc->berlaku_sampai ? $doc->berlaku_sampai->format("Y-m-d") : ""), @json($doc->status), @json($doc->catatan), @json($doc->urutan ?? 1))'
                                        style="background: none; border: none; color: #0284c7; cursor: pointer; padding: 4px;" title="Edit Dokumen">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.legalitas.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus dokumen {{ $doc->nama_dokumen }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer; padding: 4px;" title="Hapus Dokumen">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Form Tambah Dokumen -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
        <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 16px;">
            <i class="fa-solid fa-plus"></i> Tambah Dokumen Baru
        </h3>

        <form action="{{ route('admin.legalitas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Nama Dokumen / Izin <span style="color: #dc2626;">*</span></label>
                <input type="text" name="nama_dokumen" required placeholder="Contoh: NPPBKC Pita Cukai"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Nomor SK / Izin</label>
                <input type="text" name="nomor" placeholder="Contoh: 750******-070700-221221*******"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Instansi Penerbit</label>
                <input type="text" name="penerbit" placeholder="Contoh: KPPBC Tipe Madya Pabean C Blitar"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Berlaku Sampai Tanggal</label>
                <input type="date" name="berlaku_sampai"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Status</label>
                <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    <option value="Aktif">Aktif</option>
                    <option value="Dalam Proses Perpanjangan">Dalam Proses Perpanjangan</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Catatan / Keterangan</label>
                <textarea name="catatan" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Scan / PDF Dokumen (Opsional)</label>
                <input type="file" name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png"
                       style="width: 100%; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; background: #f8fafc;">
                <small style="color: #64748b; font-size: 11px;">Format: PDF, JPG, atau PNG (maks. 4 MB). Kosongkan jika masih berupa arsip fisik.</small>
            </div>

            <button type="submit" style="background: var(--admin-gold); color: #fff; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 700; width: 100%; cursor: pointer;">
                <i class="fa-solid fa-save"></i> Simpan Dokumen
            </button>
        </form>
    </div>
</div>

<!-- Modal Edit Dokumen Legalitas -->
<div id="editLegalitasModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; border-radius: 8px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--admin-gold); margin-right: 6px;"></i> Edit Dokumen Legalitas
            </h3>
            <button type="button" onclick="closeEditLegalitasModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="formEditLegalitas" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="urutan" id="editLegalitasUrutan" value="1">

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Nama Dokumen <span style="color: #dc2626;">*</span></label>
                <input type="text" name="nama_dokumen" id="editLegalitasNama" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Nomor SK / Izin</label>
                <input type="text" name="nomor" id="editLegalitasNomor" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Instansi Penerbit</label>
                <input type="text" name="penerbit" id="editLegalitasPenerbit" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Berlaku Sampai</label>
                    <input type="date" name="berlaku_sampai" id="editLegalitasBerlaku" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Status</label>
                    <select name="status" id="editLegalitasStatus" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        <option value="Aktif">Aktif</option>
                        <option value="Dalam Proses Perpanjangan">Dalam Proses Perpanjangan</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Catatan</label>
                <textarea name="catatan" id="editLegalitasCatatan" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;"></textarea>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Ganti / Unggah Lampiran (Opsional)</label>
                <input type="file" name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                <small style="color: #64748b; font-size: 11px;">Biarkan kosong jika tidak mengubah lampiran.</small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeEditLegalitasModal()" style="padding: 9px 16px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 9px 18px; border: none; background: var(--admin-gold); border-radius: 6px; font-size: 13px; font-weight: 700; color: #fff; cursor: pointer;">
                    Simpan Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditLegalitasModal(id, nama, nomor, penerbit, berlaku, status, catatan, urutan) {
    document.getElementById('formEditLegalitas').action = '/admin/legalitas/' + id;
    document.getElementById('editLegalitasNama').value = nama || '';
    document.getElementById('editLegalitasNomor').value = nomor || '';
    document.getElementById('editLegalitasPenerbit').value = penerbit || '';
    document.getElementById('editLegalitasBerlaku').value = berlaku || '';
    document.getElementById('editLegalitasStatus').value = status || 'Aktif';
    document.getElementById('editLegalitasCatatan').value = catatan || '';
    document.getElementById('editLegalitasUrutan').value = urutan || 1;
    document.getElementById('editLegalitasModal').style.display = 'flex';
}
function closeEditLegalitasModal() {
    document.getElementById('editLegalitasModal').style.display = 'none';
}
</script>
@endpush
@endsection
