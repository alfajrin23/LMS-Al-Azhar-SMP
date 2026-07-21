<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Rapor - {{ $siswa->nama }}</title>
<style>
  body { font-family: sans-serif; font-size: 12px; color: #333; margin: 30px; }
  .header { text-align: center; margin-bottom: 24px; border-bottom: 3px double #1a73e8; padding-bottom: 16px; }
  .header h1 { margin: 0; font-size: 20px; color: #1a73e8; }
  .header p { margin: 4px 0 0; font-size: 13px; color: #666; }
  .section { margin-bottom: 20px; }
  .section h2 { font-size: 14px; color: #1a73e8; border-bottom: 2px solid #eee; padding-bottom: 6px; margin-bottom: 10px; }
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  table th, table td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
  table th { background: #f5f5f5; font-weight: 600; }
  .bio-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 12px; }
  .bio-grid .label { color: #888; }
  .rata { font-size: 16px; font-weight: 700; color: #1a73e8; text-align: center; margin-top: 12px; }
  .catatan { font-style: italic; line-height: 1.7; color: #555; }
  .kehadiran-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; }
  .footer { text-align: center; margin-top: 30px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 12px; }
</style>
</head>
<body>
<div class="header">
  <h1>{{ (isset($type) && $type === 'unggulan') ? 'RAPOR PROGRAM UNGGULAN' : 'RAPOR SEMESTER' }}</h1>
  <p>{{ setting('school_name') }} — Tahun Ajaran {{ setting('tahun_ajaran') }}</p>
</div>
<div class="section">
  <h2>Identitas Siswa</h2>
  <div class="bio-grid">
    <div><span class="label">Nama</span><br><strong>{{ $siswa->nama }}</strong></div>
    <div><span class="label">NIS</span><br><strong>{{ $siswa->nis }}</strong></div>
    <div><span class="label">Kelas</span><br><strong>{{ $kelas?->nama_kelas ?? '-' }}</strong></div>
    <div><span class="label">Semester</span><br><strong>{{ $catatanWali?->semester ?? setting('semester_aktif') }}</strong></div>
  </div>
</div>
<div class="section">
  <h2>{{ (isset($type) && $type === 'unggulan') ? 'Nilai Program Unggulan' : 'Nilai Akademik' }}</h2>
  <table>
    <thead><tr><th>No</th><th>Mata Pelajaran</th><th>KKM</th><th>Nilai</th><th>Status (Grade)</th></tr></thead>
    <tbody>
      @foreach($nilai as $n)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $n->mapel->nama_mapel }}</td>
        <td>{{ $kkm }}</td>
        <td style="font-weight:700;text-align:center">{{ $n->nilai }}</td>
        <td style="text-align:center;font-weight:600">
          @if($n->nilai >= 90) Bagus Banget (A)
          @elseif($n->nilai >= 85) Bagus (A-)
          @elseif($n->nilai >= 80) Bagus (B+)
          @elseif($n->nilai >= $kkm) Perlu Belajar Lagi (B)
          @elseif($n->nilai >= 70) Perlu Belajar Lagi (B-)
          @else Perlu Diulang (C)
          @endif
        </td>
      </tr>
      @endforeach
      <tr style="font-weight:700;background:#f9f9f9">
        <td colspan="2" style="text-align:right">Rata-rata</td>
        <td colspan="3" style="text-align:center">{{ $rata }}</td>
      </tr>
    </tbody>
  </table>
</div>
<div class="section">
  <h2>Kehadiran</h2>
  <div class="kehadiran-grid">
    <div>Hadir: <strong>{{ $totalHadir }}</strong> Hari</div>
    <div>Sakit: <strong>{{ $totalSakit }}</strong> Hari</div>
    <div>Izin: <strong>{{ $totalIzin }}</strong> Hari</div>
    <div>Alpha: <strong>{{ $totalAlpha }}</strong> Hari</div>
  </div>
</div>
<div class="section">
  <h2>Tahfidz</h2>
  <p>Total setoran tahfidz: <strong>{{ $tahfidz }} ayat</strong></p>
</div>
@if($catatanWali)
<div class="section">
  <h2>Catatan Wali Kelas</h2>
  <p class="catatan">&quot;{{ $catatanWali->catatan }}&quot;</p>
  <p style="font-size:11px;color:#888;margin-top:4px">— {{ $catatanWali->guru->nama ?? 'Wali Kelas' }}</p>
</div>
@endif
<div class="footer">
  Dicetak pada {{ now()->format('d M Y H:i') }} &mdash; {{ setting('school_name') }}
</div>
</body>
</html>
