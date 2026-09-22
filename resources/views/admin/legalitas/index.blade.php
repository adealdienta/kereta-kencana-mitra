@extends('layouts.admin')

@section('title', 'Dokumen Legalitas & Perizinan Cukai')
@section('header_title', 'Kelola Legalitas, NPPBKC & Izin Usaha Pabrik')

@section('content')
<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px;">
    <!-- Tabel Dokumen -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
        <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 16px;">
            <i class="fa-solid fa-stamp"></i> Daftar Dokumen Cukai & Izin Berusaha
        </h3>

        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                    <th style="padding: 10px;">Nama Izin / Dokumen</th>
                    <th style="padding: 10px;">Nomor Dokumen</th>
                    <th style="padding: 10px;">Penerbit</th>
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
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: #ecfdf5; color: #059669; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                                {{ $doc->status }}
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <form action="{{ route('admin.legalitas.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus dokumen {{ $doc->nama_dokumen }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
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

        <form action="{{ route('admin.legalitas.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Nama Dokumen / Izin <span style="color: #dc2626;">*</span></label>
                <input type="text" name="nama_dokumen" required placeholder="Contoh: NPPBKC Pita Cukai"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Nomor SK / Izin</label>
                <input type="text" name="nomor" placeholder="Contoh: 0821.1.2.XXXXX"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Instansi Penerbit</label>
                <input type="text" name="penerbit" placeholder="Contoh: KPPBC Tipe Madya Blitar"
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

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Catatan / Keterangan</label>
                <textarea name="catatan" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;"></textarea>
            </div>

            <button type="submit" style="background: var(--admin-gold); color: #fff; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 700; width: 100%; cursor: pointer;">
                <i class="fa-solid fa-save"></i> Simpan Dokumen
            </button>
        </form>
    </div>
</div>
@endsection
