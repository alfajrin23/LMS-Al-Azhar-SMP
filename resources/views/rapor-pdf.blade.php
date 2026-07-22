<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor {{ ucfirst($jenis) }} - {{ $siswa->nama }}</title>
    <style>
        @page { margin: 22mm 16mm; }
        body { font-family: DejaVu Sans, sans-serif; color:#1f2937; font-size:11px; }
        .rapor-header { text-align:center; border-bottom:3px double #1d4ed8; padding-bottom:10px; margin-bottom:14px; }
        .rapor-header h1 { margin:0; font-size:18px; color:#1d4ed8; }
        .rapor-header p { margin:3px 0 0; font-size:10px; color:#4b5563; }
        .rapor-section { margin-bottom:12px; page-break-inside:avoid; }
        .rapor-section h2 { font-size:12px; color:#1d4ed8; margin:0 0 6px; border-bottom:1px solid #d1d5db; padding-bottom:4px; }
        .rapor-grid { width:100%; display:table; table-layout:fixed; border-spacing:6px; }
        .rapor-grid > div { display:table-cell; width:50%; border:1px solid #e5e7eb; padding:6px; }
        .rapor-grid.compact > div { width:16.66%; }
        .rapor-grid span, .signature-grid span { display:block; color:#6b7280; font-size:9px; }
        .rapor-grid strong { display:block; color:#111827; font-size:10px; margin-top:2px; }
        table { width:100%; border-collapse:collapse; font-size:9px; }
        th, td { border:1px solid #d1d5db; padding:4px 5px; vertical-align:top; }
        th { background:#f3f4f6; color:#374151; }
        .num { text-align:center; font-weight:bold; }
        .empty { text-align:center; color:#6b7280; padding:10px; }
        .note { line-height:1.5; font-style:italic; margin:6px 0 0; }
        .signature-grid { width:100%; display:table; table-layout:fixed; border-spacing:12px; margin-top:22px; text-align:center; }
        .signature-grid > div { display:table-cell; width:33%; }
        .signature-grid strong { display:block; padding-top:42px; border-bottom:1px solid #9ca3af; min-height:16px; }
    </style>
</head>
<body>
    @include('partials.rapor-content')
</body>
</html>
