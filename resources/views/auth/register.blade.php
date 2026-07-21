<x-guest-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&family=Patrick+Hand&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100vh !important;
            overflow: hidden !important;
        }
        .uni-input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .uni-label {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            padding: 0;
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
            z-index: 2;
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .uni-input-field {
            width: 100%;
            padding: 13px 40px 13px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            color: #1e293b;
            background: #f8fafc;
            font-family: inherit;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .uni-input-field:hover {
            border-color: #FF7A60;
            background: #ffffff;
        }
        .uni-input-field:focus {
            border-color: #FF7A60;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(255, 122, 96, 0.08), 0 0 0 4px rgba(255, 122, 96, 0.12);
            transform: translateY(-1px);
        }
        .uni-input-field:focus ~ .uni-label,
        .uni-input-field:not(:placeholder-shown) ~ .uni-label {
            top: -9px;
            transform: translateY(0);
            background: #ffffff;
            padding: 0 6px;
            font-size: 11px;
            font-weight: 600;
            color: #FF7A60;
        }
        .uni-input-field.input-error {
            border-color: #ef4444 !important;
            background: #fffef2 !important;
        }
        .uni-input-field.input-error:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12) !important;
        }
        .uni-input-field.input-error ~ .uni-label {
            color: #ef4444 !important;
        }
        .tester-tip-badge {
            background: rgba(38, 186, 155, 0.08);
            border: 1px dashed rgba(38, 186, 155, 0.3);
            color: #26BA9B;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 16px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            line-height: 1.4;
        }
        .uni-header-block {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 15px;
            width: 100%;
        }
        .uni-header-title {
            font-family: 'Fredoka', sans-serif;
            font-size: 38px;
            font-weight: 700;
            color: #26BA9B;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }
        .uni-header-subtitle {
            font-family: 'Patrick Hand', cursive;
            font-size: 28px;
            font-weight: 400;
            color: #FF7A60;
            margin: 0;
            padding: 0;
            margin-top: -2px;
            line-height: 1.1;
            transition: opacity 0.25s ease;
        }
        .uni-logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-shrink: 0;
        }
        .uni-logo-wrapper img {
            height: 75px;
            width: auto;
            object-fit: contain;
            mix-blend-mode: multiply;
        }
        .uni-submit-btn {
            width: 100%;
            padding: 13px;
            background: #FF7A60;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 12px;
            font-family: inherit;
            box-shadow: 0 4px 12px rgba(255, 122, 96, 0.15);
            outline: none;
        }
        .uni-submit-btn:hover {
            background: #f96347;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 122, 96, 0.3);
        }
        .uni-submit-btn:active {
            transform: translateY(0);
        }
        .uni-google-btn {
            width: 100%;
            padding: 13px;
            background: #ffffff;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 24px;
            font-family: inherit;
            outline: none;
        }
        .uni-google-btn:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .uni-google-btn:active {
            transform: translateY(0);
        }
        .uni-role-btn {
            padding: 10px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
            text-align: center;
            outline: none;
        }
        .uni-role-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }
        .uni-role-btn:active {
            transform: translateY(0);
        }
        .uni-checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #94a3b8;
            cursor: pointer;
            user-select: none;
            position: relative;
            font-size: 12.5px;
        }
        .uni-checkbox-input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        .uni-checkbox-checkmark {
            height: 16px;
            width: 16px;
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 4px;
            transition: all 0.2s ease;
            display: inline-block;
            position: relative;
        }
        .uni-checkbox-container:hover .uni-checkbox-input ~ .uni-checkbox-checkmark {
            border-color: #FF7A60;
        }
        .uni-checkbox-input:checked ~ .uni-checkbox-checkmark {
            background-color: #FF7A60;
            border-color: #FF7A60;
        }
        .uni-checkbox-checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        .uni-checkbox-input:checked ~ .uni-checkbox-checkmark:after {
            display: block;
        }
        .uni-checkbox-container .uni-checkbox-checkmark:after {
            left: 4.5px;
            top: 1.5px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .form-fade-container {
            position: relative;
            width: 100%;
        }
        .form-view {
            transition: opacity 0.3s ease, transform 0.3s ease;
            width: 100%;
        }
        @media (max-width: 1024px) {
            html, body {
                overflow: hidden !important;
                height: 100vh !important;
                height: 100dvh !important;
            }
            .uni-login-page {
                height: 100vh !important;
                height: 100dvh !important;
                max-height: 100vh !important;
                max-height: 100dvh !important;
                overflow: hidden !important;
                background: #2ebe9f;
            }
            .uni-bg-curve {
                display: none !important;
            }
            .uni-container {
                flex-direction: column !important;
                height: 100vh !important;
                height: 100dvh !important;
                min-height: 100vh !important;
                min-height: 100dvh !important;
                max-height: 100vh !important;
                max-height: 100dvh !important;
                align-items: stretch !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            .uni-left {
                flex: none !important;
                height: 125px !important;
                padding-left: 0 !important;
                justify-content: center !important;
                align-items: flex-end !important;
                background: #2ebe9f;
                overflow: visible !important;
            }
            .uni-left img {
                display: block !important;
                width: auto !important;
                height: 105px !important;
                margin-left: 0 !important;
                transform: translateY(20px) !important;
                z-index: 10 !important;
            }
            .uni-right {
                flex: 1 !important;
                background: #ffffff !important;
                border-top-left-radius: 35px !important;
                border-top-right-radius: 35px !important;
                padding: 28px 24px 24px 24px !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
                align-items: stretch !important;
                box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.08) !important;
                z-index: 5 !important;
            }
            .uni-form-wrapper {
                margin-top: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .form-fade-container {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .form-view {
                display: none !important;
            }
            .form-view.active-view {
                display: flex !important;
                flex: 1 !important;
                flex-direction: column !important;
            }
            .uni-footer-link {
                margin-top: auto !important;
                padding-top: 15px !important;
                margin-bottom: 0 !important;
            }
            .uni-header-block {
                margin-bottom: 8px !important;
                gap: 12px !important;
            }
            .uni-logo-wrapper img {
                height: 48px !important;
            }
            .uni-header-title {
                font-size: 24px !important;
            }
            .uni-header-subtitle {
                font-size: 15px !important;
            }
            .uni-input-group {
                margin-bottom: 8px !important;
            }
            .uni-label {
                position: static !important;
                padding: 0 !important;
                font-size: 11px !important;
                color: #94a3b8 !important;
                font-weight: 500 !important;
                display: block !important;
                margin-bottom: 2px !important;
                transform: none !important;
            }
            .uni-input-field {
                border: none !important;
                border-bottom: 1.5px solid #e2e8f0 !important;
                border-radius: 0 !important;
                padding: 4px 30px 4px 0 !important;
                background: transparent !important;
                font-size: 14px !important;
            }
            .uni-input-field:focus {
                border-bottom-color: #FF7A60 !important;
                box-shadow: none !important;
                transform: none !important;
            }
            .uni-input-field-active {
                border: none !important;
                border-bottom: 1.5px solid #FF7A60 !important;
                background: transparent !important;
                box-shadow: none !important;
            }
            .uni-input-icon {
                right: 0 !important;
            }
            .uni-form-wrapper form button[type="submit"] {
                padding: 8px !important;
                margin-bottom: 6px !important;
                font-size: 14px !important;
            }
            .uni-form-wrapper form button[type="button"] {
                padding: 8px !important;
                margin-bottom: 6px !important;
                font-size: 13px !important;
            }
            .tester-tip-badge {
                padding: 4px 8px !important;
                margin-bottom: 6px !important;
                font-size: 9px !important;
            }
            .uni-remember-row {
                margin-bottom: 8px !important;
            }
            .uni-role-selector {
                margin-bottom: 8px !important;
            }
            .uni-role-btn {
                padding: 4px !important;
                font-size: 10px !important;
            }
            .uni-google-btn {
                margin-bottom: 10px !important;
                padding: 8px !important;
                font-size: 13px !important;
            }
            .uni-header-text {
                padding-left: 6px !important;
            }
        }
    </style>
    <div class="uni-login-page" style="overflow: hidden; height: 100vh; max-height: 100vh; position: relative;">
        <div class="uni-bg-curve" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;">
            <svg id="visual" viewBox="0 0 900 600" width="100%" height="100%" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1">
                <rect x="0" y="0" width="900" height="600" fill="#ffffff"></rect>
                <path d="M443 0L457.7 14.3C472.3 28.7 501.7 57.3 512 85.8C522.3 114.3 513.7 142.7 492.5 171.2C471.3 199.7 437.7 228.3 414.5 257C391.3 285.7 378.7 314.3 395.7 343C412.7 371.7 459.3 400.3 474.3 428.8C489.3 457.3 472.7 485.7 460.7 514.2C448.7 542.7 441.3 571.3 437.7 585.7L434 600L0 600L0 585.7C0 571.3 0 542.7 0 514.2C0 485.7 0 457.3 0 428.8C0 400.3 0 371.7 0 343C0 314.3 0 285.7 0 257C0 228.3 0 199.7 0 171.2C0 142.7 0 114.3 0 85.8C0 57.3 0 28.7 0 14.3L0 0Z" fill="#2ebe9f" stroke-linecap="round" stroke-linejoin="miter"></path>
            </svg>
        </div>
        <div class="uni-container" style="font-family: 'Plus Jakarta Sans', sans-serif; height: 100vh; display: flex; align-items: center;">
            <div class="uni-left" style="flex: 1.4; display: flex; align-items: center; justify-content: flex-start; padding-left: 10px; height: 100%; z-index: 5;">
                <img src="{{ asset('college entrance exam-amico.svg') }}" alt="Illustration" style="width: 100%; max-width: 640px; height: auto; object-fit: contain; margin-left: -35px;">
            </div>
            <div class="uni-right" style="flex: 0.6; display: flex; align-items: center; justify-content: flex-end; padding-right: 40px; z-index: 5;">
                <div class="uni-form-wrapper" style="width: 100%; max-width: 350px; display: flex; flex-direction: column; margin-top: 100px;">
                    <div class="uni-header-block">
                        <div class="uni-header-text" style="text-align: left; line-height: 1.1; flex: 1;">
                            <div class="uni-header-title">Welcome</div>
                            <div class="uni-header-subtitle" id="uni-subtitle">Let's get started!</div>
                        </div>
                        <div class="uni-logo-wrapper">
                            <img src="{{ asset('lms_logo.png') }}" alt="Al-Azhar Logo">
                        </div>
                    </div>
                    <div class="form-fade-container">
                        <div id="login-view" class="form-view active-view">
                            <div class="tester-tip-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18zM12 18V9.75M12 9.75a3.75 3.75 0 110-7.5 3.75 3.75 0 010 7.5z" />
                                </svg>
                                Tip: Pilih role di bawah untuk mengisi data demo!
                            </div>
                            @if (session('status'))
                                <div style="color: #10b981; font-size: 12px; margin-bottom: 12px; text-align: center; font-weight: 500;">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form method="POST" action="{{ route('login') }}" id="login-form">
                                @csrf
                                <div class="uni-input-group">
                                    <input type="email" id="email" name="email" placeholder=" " value="{{ old('email') }}" required autofocus class="uni-input-field @error('email') input-error @enderror">
                                    <label class="uni-label">Email</label>
                                    <span class="uni-input-icon" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #FF7A60; font-size: 16px; font-weight: 600; pointer-events: none; user-select: none;">@</span>
                                    @error('email')
                                        <div style="color: #ef4444; font-size: 11px; margin-top: 5px; font-weight: 500;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="uni-input-group" style="position: relative;">
                                    <input id="password" type="password" name="password" placeholder=" " required class="uni-input-field @error('password') input-error @enderror">
                                    <label class="uni-label">Password</label>
                                    <button type="button" onclick="togglePasswordVisibility('password', 'eye-icon')" class="uni-input-icon" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; outline: none; transition: color 0.2s;">
                                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 18px; height: 18px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </button>
                                    @error('password')
                                        <div style="color: #ef4444; font-size: 11px; margin-top: 5px; font-weight: 500;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="uni-remember-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                    <label class="uni-checkbox-container">
                                        <input type="checkbox" name="remember" class="uni-checkbox-input">
                                        <span class="uni-checkbox-checkmark"></span>
                                        Remember Me
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" style="color: #FF7A60; text-decoration: none; font-size: 12.5px; font-weight: 600;">Recovery Password</a>
                                    @endif
                                </div>
                                <div class="uni-role-selector" style="margin-bottom: 20px;">
                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                                        <button type="button" onclick="selectRole('admin')" id="role-admin" class="uni-role-btn">
                                            Admin
                                        </button>
                                        <button type="button" onclick="selectRole('kepala_sekolah')" id="role-kepala_sekolah" class="uni-role-btn">
                                            Kepala Sekolah
                                        </button>
                                        <button type="button" onclick="selectRole('guru')" id="role-guru" class="uni-role-btn">
                                            Guru
                                        </button>
                                        <button type="button" onclick="selectRole('siswa')" id="role-siswa" class="uni-role-btn" style="border: 1.5px solid #FF7A60; background: #fff5f3; color: #FF7A60;">
                                            Siswa
                                        </button>
                                    </div>
                                    <input type="hidden" name="role" id="selected-role" value="siswa">
                                </div>
                                <button type="submit" id="login-submit-btn" class="uni-submit-btn">
                                    Login
                                </button>
                                <button type="button" class="uni-google-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="width: 18px; height: 18px;">
                                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                        <path fill="#4285F4" d="M46.5 24c0-1.55-.15-3.24-.47-4.77H24v9.03h12.75c-.55 2.92-2.2 5.4-4.69 7.07l7.29 5.65C43.6 36.42 46.5 30.73 46.5 24z"/>
                                        <path fill="#FBBC05" d="M10.54 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.98-6.19z"/>
                                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.29-5.65c-2.02 1.35-4.61 2.16-8.6 2.16-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                    </svg>
                                    Sign in with Google
                                </button>
                            </form>
                            <div class="uni-footer-link" style="text-align: center; font-size: 13px; color: #94a3b8;">
                                Don't have an account yet? <a href="{{ route('register') }}" onclick="toggleAuthPage(event, 'register')" style="color: #FF7A60; text-decoration: none; font-weight: 700; margin-left: 4px;">Sign Up</a>
                            </div>
                        </div>
                        <div id="register-view" class="form-view" style="display: none; opacity: 0;">
                            <form method="POST" action="{{ route('register') }}" id="register-form">
                                @csrf
                                <div class="uni-input-group">
                                    <input type="text" id="name" name="name" placeholder=" " value="{{ old('name') }}" required class="uni-input-field @error('name') input-error @enderror">
                                    <label class="uni-label">Name</label>
                                    <span class="uni-input-icon" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #FF7A60; font-size: 16px; font-weight: 600; pointer-events: none; user-select: none;">👤</span>
                                    @error('name')
                                        <div style="color: #ef4444; font-size: 11px; margin-top: 5px; font-weight: 500;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="uni-input-group">
                                    <input type="email" id="register_email" name="email" placeholder=" " value="{{ old('email') }}" required class="uni-input-field @error('email') input-error @enderror">
                                    <label class="uni-label">Email</label>
                                    <span class="uni-input-icon" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #FF7A60; font-size: 16px; font-weight: 600; pointer-events: none; user-select: none;">@</span>
                                    @error('email')
                                        <div style="color: #ef4444; font-size: 11px; margin-top: 5px; font-weight: 500;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="uni-input-group">
                                    <select id="kelas_id" name="kelas_id" required class="uni-input-field @error('kelas_id') input-error @enderror" style="appearance: auto; padding-right: 16px; padding-top: 15px; padding-bottom: 11px; color: #1e293b; background: transparent;">
                                        <option value="" disabled selected></option>
                                        @foreach($kelases as $kelas)
                                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                    <label class="uni-label">Pilih Kelas</label>
                                    @error('kelas_id')
                                        <div style="color: #ef4444; font-size: 11px; margin-top: 5px; font-weight: 500;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="uni-input-group" style="position: relative;">
                                    <input id="register_password" type="password" name="password" placeholder=" " required class="uni-input-field @error('password') input-error @enderror">
                                    <label class="uni-label">Password</label>
                                    <button type="button" onclick="togglePasswordVisibility('register_password', 'eye-icon-pw')" class="uni-input-icon" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; outline: none; transition: color 0.2s;">
                                        <svg id="eye-icon-pw" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 18px; height: 18px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </button>
                                    @error('password')
                                        <div style="color: #ef4444; font-size: 11px; margin-top: 5px; font-weight: 500;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="uni-input-group" style="position: relative;">
                                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder=" " required class="uni-input-field">
                                    <label class="uni-label">Confirm Password</label>
                                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eye-icon-confirm')" class="uni-input-icon" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; outline: none; transition: color 0.2s;">
                                        <svg id="eye-icon-confirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 18px; height: 18px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </button>
                                </div>
                                <button type="submit" id="register-submit-btn" class="uni-submit-btn" style="margin-top: 8px;">
                                    Register
                                </button>
                                <button type="button" class="uni-google-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="width: 18px; height: 18px;">
                                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                        <path fill="#4285F4" d="M46.5 24c0-1.55-.15-3.24-.47-4.77H24v9.03h12.75c-.55 2.92-2.2 5.4-4.69 7.07l7.29 5.65C43.6 36.42 46.5 30.73 46.5 24z"/>
                                        <path fill="#FBBC05" d="M10.54 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.98-6.19z"/>
                                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.29-5.65c-2.02 1.35-4.61 2.16-8.6 2.16-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                    </svg>
                                    Sign up with Google
                                </button>
                            </form>
                            <div class="uni-footer-link" style="text-align: center; font-size: 13px; color: #94a3b8; margin-bottom: 20px;">
                                Already registered? <a href="{{ route('login') }}" onclick="toggleAuthPage(event, 'login')" style="color: #FF7A60; text-decoration: none; font-weight: 700; margin-left: 4px;">Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h16.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                `;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                `;
            }
        }
        const demoCredentials = {
            'admin': { 'email': 'admin@alazharjayaindonesia.sch.id', 'password': 'password123' },
            'kepala_sekolah': { 'email': 'admin@alazharjayaindonesia.sch.id', 'password': 'password123' },
            'guru': { 'email': 'dewi.sartika@alazharjayaindonesia.sch.id', 'password': 'password123' },
            'siswa': { 'email': 'ahmad.rizky@alazharjayaindonesia.sch.id', 'password': 'password123' }
        };
        function selectRole(role) {
            document.getElementById('selected-role').value = role;
            const roles = ['admin', 'kepala_sekolah', 'guru', 'siswa'];
            roles.forEach(r => {
                const btn = document.getElementById('role-' + r);
                if (btn) {
                    if (r === role) {
                        btn.style.border = '1.5px solid #FF7A60';
                        btn.style.background = '#fff5f3';
                        btn.style.color = '#FF7A60';
                    } else {
                        btn.style.border = '1.5px solid #e2e8f0';
                        btn.style.background = '#ffffff';
                        btn.style.color = '#64748b';
                    }
                }
            });
            if (demoCredentials[role]) {
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                emailInput.value = demoCredentials[role].email;
                passwordInput.value = demoCredentials[role].password;
                emailInput.dispatchEvent(new Event('input'));
                passwordInput.dispatchEvent(new Event('input'));
            }
        }
        function toggleAuthPage(event, viewName) {
            if (event) event.preventDefault();
            const loginView = document.getElementById('login-view');
            const registerView = document.getElementById('register-view');
            const subtitle = document.getElementById('uni-subtitle');
            const newPath = viewName === 'register' ? '/register' : '/login';
            window.history.pushState({}, '', newPath);
            if (viewName === 'register') {
                subtitle.style.opacity = '0';
                loginView.style.opacity = '0';
                setTimeout(() => {
                    loginView.style.display = 'none';
                    loginView.classList.remove('active-view');
                    registerView.style.display = 'block';
                    registerView.classList.add('active-view');
                    subtitle.innerText = "Sign up to start!";
                    subtitle.style.opacity = '1';
                    setTimeout(() => {
                        registerView.style.opacity = '1';
                    }, 50);
                }, 200);
            } else {
                subtitle.style.opacity = '0';
                registerView.style.opacity = '0';
                setTimeout(() => {
                    registerView.style.display = 'none';
                    registerView.classList.remove('active-view');
                    loginView.style.display = 'block';
                    loginView.classList.add('active-view');
                    subtitle.innerText = "Let's get started!";
                    subtitle.style.opacity = '1';
                    setTimeout(() => {
                        loginView.style.opacity = '1';
                    }, 50);
                }, 200);
            }
        }
        document.getElementById('login-form').addEventListener('submit', function() {
            const submitBtn = document.getElementById('login-submit-btn');
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.innerText = 'Logging in...';
        });
        document.getElementById('register-form').addEventListener('submit', function() {
            const submitBtn = document.getElementById('register-submit-btn');
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.innerText = 'Registering...';
        });
        window.addEventListener('DOMContentLoaded', () => {
            const path = window.location.pathname;
            if (path.includes('register')) {
                toggleAuthPage(null, 'register');
            } else {
                toggleAuthPage(null, 'login');
                selectRole('siswa');
            }
        });
        window.addEventListener('popstate', () => {
            const path = window.location.pathname;
            if (path.includes('register')) {
                toggleAuthPage(null, 'register');
            } else {
                toggleAuthPage(null, 'login');
            }
        });
    </script>
</x-guest-layout>
