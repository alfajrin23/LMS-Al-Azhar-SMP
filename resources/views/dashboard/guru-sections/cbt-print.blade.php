<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Ujian: {{ $cbtExam->judul }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        :root {
            --font: 'Outfit', sans-serif;
            --text-color: #1e293b;
            --border-color: #cbd5e1;
        }
        body {
            font-family: var(--font);
            color: var(--text-color);
            line-height: 1.5;
            background: #f8fafc;
            margin: 0;
            padding: 40px;
        }
        .no-print-bar {
            background: #ffffff;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-print {
            background: #0d9488;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-print:hover {
            background: #0f766e;
        }
        .exam-paper {
            background: #ffffff;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 4px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-logo {
            font-size: 40px;
            color: #0d9488;
            margin-right: 20px;
        }
        .kop-text {
            flex: 1;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #64748b;
        }
        .exam-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            font-size: 13px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }
        .info-item {
            display: flex;
        }
        .info-label {
            font-weight: 600;
            width: 140px;
        }
        .info-value {
            flex: 1;
        }
        .question-list {
            margin-top: 20px;
        }
        .question-item {
            margin-bottom: 24px;
            page-break-inside: avoid;
        }
        .question-text {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
            display: flex;
            gap: 8px;
        }
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 20px;
            margin-left: 24px;
        }
        .option-item {
            font-size: 13px;
        }
        .essay-space {
            border-bottom: 1px dashed #cbd5e1;
            height: 80px;
            margin-left: 24px;
            margin-top: 10px;
        }
        .key-paper {
            page-break-before: always;
            background: #ffffff;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 40px;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                color: #000000;
            }
            .no-print-bar {
                display: none;
            }
            .exam-paper, .key-paper {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .exam-info {
                background: none;
                border: 1px solid #000000;
            }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <div>
            <h4 style="margin:0; font-size:16px;">Pratinjau Cetak Ujian</h4>
            <p style="margin:4px 0 0 0; font-size:12px; color:#64748b;">Gunakan tombol cetak di sebelah kanan untuk mengeprint soal atau menyimpan sebagai PDF.</p>
        </div>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Cetak Soal Ujian
        </button>
    </div>
    <div class="exam-paper">
        <div class="kop-surat">
            <div class="kop-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="kop-text">
                <h2>Lembaga Pendidikan Al Azhar Jaya Indonesia</h2>
                <p>SDIT &amp; SMPIT Al Azhar Jaya Indonesia &bull; Akreditasi A &bull; Sistem Informasi Akademik</p>
            </div>
        </div>
        <h3 style="text-align:center; text-transform:uppercase; margin-bottom:20px; font-size:16px; font-weight:700;">
            Lembar Soal {{ $cbtExam->tipe === 'uts' ? 'Ujian Tengah Semester' : ($cbtExam->tipe === 'uas' ? 'Ujian Akhir Semester' : 'Ulangan Harian') }}
        </h3>
        <div class="exam-info">
            <div class="info-item">
                <div class="info-label">Mata Pelajaran</div>
                <div class="info-value">: {{ $cbtExam->mapel->nama_mapel ?? '—' }} ({{ $cbtExam->mapel->kode ?? '—' }})</div>
            </div>
            <div class="info-item">
                <div class="info-label">Kelas / Tingkat</div>
                <div class="info-value">: {{ $cbtExam->kelas->nama_kelas ?? 'Semua Kelas' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Durasi Waktu</div>
                <div class="info-value">: {{ $cbtExam->durasi }} Menit</div>
            </div>
            <div class="info-item">
                <div class="info-label">Jumlah Soal</div>
                <div class="info-value">: {{ $soals->count() }} Butir Soal</div>
            </div>
            <div class="info-item" style="grid-column: span 2;">
                <div class="info-label">Nama Siswa</div>
                <div class="info-value">: ________________________________________</div>
            </div>
        </div>
        @if($cbtExam->deskripsi)
        <div style="font-size:13px; font-style:italic; border-left:3px solid #0d9488; padding-left:10px; margin-bottom:25px; color:#475569;">
            <strong>Petunjuk Mengerjakan:</strong><br>
            {{ $cbtExam->deskripsi }}
        </div>
        @endif
        <div class="question-list">
            @foreach($soals as $s)
            <div class="question-item">
                <div class="question-text">
                    <span style="font-weight:700;">{{ $s->nomor }}.</span>
                    <span style="flex:1;">{!! nl2br(e($s->soal)) !!}</span>
                </div>
                @if($s->tipe === 'pg')
                <div class="options-grid">
                    <div class="option-item">A. {{ $s->pilihan_a }}</div>
                    <div class="option-item">B. {{ $s->pilihan_b }}</div>
                    <div class="option-item">C. {{ $s->pilihan_c }}</div>
                    <div class="option-item">D. {{ $s->pilihan_d }}</div>
                </div>
                @else
                <div class="essay-space"></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    <div class="key-paper">
        <h3 style="text-align:center; text-transform:uppercase; margin-bottom:25px; font-size:15px; font-weight:700; border-bottom:2px solid #000; padding-bottom:10px;">
            Lembar Kunci Jawaban &bull; Khusus Guru
        </h3>
        <div style="margin-bottom:20px; font-size:13px;">
            <strong>Nama Ujian:</strong> {{ $cbtExam->judul }}<br>
            <strong>Mata Pelajaran:</strong> {{ $cbtExam->mapel->nama_mapel ?? '—' }}
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:13px; margin-top:20px;">
            <thead>
                <tr style="background:#f1f5f9;">
                    <th style="border:1px solid #cbd5e1; padding:8px; text-align:center; width:10%;">No</th>
                    <th style="border:1px solid #cbd5e1; padding:8px; text-align:left; width:15%;">Tipe</th>
                    <th style="border:1px solid #cbd5e1; padding:8px; text-align:center; width:20%;">Kunci Jawaban</th>
                    <th style="border:1px solid #cbd5e1; padding:8px; text-align:left; width:15%;">Kesulitan</th>
                    <th style="border:1px solid #cbd5e1; padding:8px; text-align:left; width:40%;">Pratinjau Soal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($soals as $s)
                <tr>
                    <td style="border:1px solid #cbd5e1; padding:8px; text-align:center; font-weight:700;">{{ $s->nomor }}</td>
                    <td style="border:1px solid #cbd5e1; padding:8px; text-align:left; text-transform:uppercase;">{{ $s->tipe }}</td>
                    <td style="border:1px solid #cbd5e1; padding:8px; text-align:center; font-weight:700; text-transform:uppercase; color:#0d9488;">
                        {{ $s->tipe === 'pg' ? $s->jawaban_benar : '— Essay —' }}
                    </td>
                    <td style="border:1px solid #cbd5e1; padding:8px; text-align:left; text-transform:capitalize;">{{ $s->kesulitan ?? 'sedang' }}</td>
                    <td style="border:1px solid #cbd5e1; padding:8px; text-align:left; color:#64748b; font-size:12px;">{{ Str::limit($s->soal, 50) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
