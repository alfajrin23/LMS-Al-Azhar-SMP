<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LMS Al Azhar Jaya Indonesia')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="dashboard-layout" x-data="{ tab: '{{ session('active_tab', request('tab', 'dashboard')) }}', detailSiswaHtml: '', tipeSoal: 'pg', selectedSiswa: null, selectedGuru: null, selectedOrtu: null, selectedKelas: null, filterKelasVal: '', filterMapelVal: '', filterStatusVal: '', filterTipeVal: '', filterPeriodeAwal: '', filterPeriodeAkhir: '', selectedWorkbook: null, selectedWorkbookTab: 'soal', childId: null, showTulisPesan: false, showReplyForm: false, replyTo: '', replyId: null, selectedExam: null }">
            <div class="overlay"></div>
            @if(session('success'))
                <div id="toast-success" style="position:fixed; top:20px; right:20px; background:var(--green); color:white; padding:15px 25px; border-radius:var(--radius-sm); z-index:9999; box-shadow:var(--shadow-lg); display:flex; align-items:center; gap:10px; font-weight:600;">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" style="background:none; border:none; color:white; cursor:pointer; margin-left:10px;"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div id="toast-error" style="position:fixed; top:20px; right:20px; background:var(--red); color:white; padding:15px 25px; border-radius:var(--radius-sm); z-index:9999; box-shadow:var(--shadow-lg); display:flex; align-items:center; gap:10px; font-weight:600;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" style="background:none; border:none; color:white; cursor:pointer; margin-left:10px;"><i class="fas fa-times"></i></button>
                </div>
            @endif
            <script>
                setTimeout(() => {
                    const ts = document.getElementById('toast-success');
                    if(ts) ts.remove();
                    const te = document.getElementById('toast-error');
                    if(te) te.remove();
                }, 5000);
            </script>
            <aside class="sidebar">
                <div style="display:flex;align-items:center;padding:0 20px 12px;border-bottom:1px solid var(--border-light);margin-bottom:8px">
                    <button class="hamburger d-md-none" id="sidebarToggle" type="button" style="display:none">
                        <span></span><span></span><span></span>
                    </button>
                </div>
                <div class="brand">
                    <h2><i class="fas fa-graduation-cap"></i>LMS Al Azhar Jaya</h2>
                    <p>SMPIT Al Azhar Jaya Indonesia</p>
                </div>
                <div style="padding:0 16px 10px">
                    <form class="d-flex" style="position:relative" onsubmit="return false">
                        <input type="text" id="globalSearch" class="form-control" placeholder="Cari..." style="width:100%;padding:8px 14px;border:1px solid var(--border);border-radius:20px;font-size:13px;font-family:var(--font);outline:none;background:var(--gray-50)">
                        <div id="searchResults" style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--border-light);border-radius:8px;display:none;z-index:9999;max-height:300px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,0.1);margin-top:4px"></div>
                    </form>
                </div>
                <ul class="sidebar-menu">
                    @yield('sidebar')
                </ul>
                <div class="sidebar-logout">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="background:none;border:none;width:100%;cursor:pointer;display:flex;align-items:center;gap:10px;padding:12px 20px;color:var(--gray-500);font-size:14px;font-family:var(--font)"><i class="fas fa-sign-out-alt"></i> Keluar</button>
                    </form>
                </div>
            </aside>
            <main class="main-area">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.overlay');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-mobile');
                if (overlay) overlay.classList.toggle('show');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('sidebar-mobile');
                this.classList.remove('show');
            });
        }

        // Global search
        let searchTimeout;
        const searchInput = document.getElementById('globalSearch');
        const searchResults = document.getElementById('searchResults');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const q = this.value.trim();
                if (q.length < 2) {
                    if (searchResults) searchResults.style.display = 'none';
                    return;
                }
                searchTimeout = setTimeout(() => {
                    fetch(`/search?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            if (!searchResults) return;
                            if (data.length === 0) {
                                searchResults.innerHTML = '<div class="p-2" style="padding:10px;text-align:center;color:var(--gray-400);font-size:13px">Tidak ada hasil</div>';
                            } else {
                                searchResults.innerHTML = data.map(item =>
                                    `<a href="${item.url}" class="d-block" style="display:block;padding:10px 14px;text-decoration:none;border-bottom:1px solid var(--border-light);color:var(--text);font-size:13px">
                                        <small style="color:var(--gray-400)">[${item.type}]</small> ${item.label}
                                    </a>`
                                ).join('');
                            }
                            searchResults.style.display = 'block';
                        })
                        .catch(() => {});
                }, 300);
            });
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.d-flex') && searchResults) {
                    searchResults.style.display = 'none';
                }
            });
        }
    });
    </script>
    @stack('scripts')
</body>
</html>
