<div class="content-header">
    <h1>Kelas Saya</h1>
    <div class="header-right">
        <label @click="tab='kelas-form'" class="header-btn primary" style="cursor:pointer"><i class="fas fa-plus"></i>
            Tambah Kelas</label>
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th>Mapel</th>
                    <th>Siswa</th>
                    <th>Rata-rata</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kelasYangDiajar as $k)
                    <tr>
                        <td><strong>{{ $k->nama_kelas }}</strong></td>
                        <td>{{ $guru->mapels->pluck('nama_mapel')->implode(', ') }}</td>
                        <td>{{ $k->siswa_count }}</td>
                        <td>{{ number_format($k->rataNilai, 1) }}</td>
                        <td><label @click="selectedKelas='{{ $k->id }}'; tab='kelas-detail'"
                                class="btn-small teal" style="cursor:pointer">Masuk</label></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
