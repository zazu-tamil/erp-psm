<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ERP Login — Zazu Technologies</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <style>
        /* ── Brand tokens ── */
        :root {
            --cyan:   #00BFFF;
            --orange: #F5A623;
            --navy:   #1B3A6B;
            --navy-d: #0f2347;
            --navy-m: #213f78;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--navy-d);
            position: relative;
            overflow: hidden;
        }

        /* ── Animated background ── */
        .bg-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: .18;
            pointer-events: none;
        }
        .bg-blob-1 {
            width: 600px; height: 600px;
            background: var(--cyan);
            top: -200px; left: -200px;
            animation: blobDrift 18s ease-in-out infinite alternate;
        }
        .bg-blob-2 {
            width: 450px; height: 450px;
            background: var(--orange);
            bottom: -150px; right: -150px;
            animation: blobDrift 22s ease-in-out infinite alternate-reverse;
        }
        .bg-blob-3 {
            width: 280px; height: 280px;
            background: var(--navy-m);
            top: 45%; left: 45%;
            animation: blobDrift 15s ease-in-out infinite alternate;
        }
        @keyframes blobDrift {
            0%   { transform: translate(0,0) scale(1); }
            100% { transform: translate(60px,40px) scale(1.15); }
        }

        /* dot grid */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(rgba(255,255,255,.07) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
        }

        /* ── Card wrapper ── */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 450px;
            padding: 1rem;
        }

        .login-card {
            background: rgba(255,255,255,0.97);
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.1),
                0 40px 90px rgba(0,0,0,.5),
                0 8px 30px rgba(0,191,255,.15);
            animation: cardIn .65s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* shimmer accent bar */
        .card-accent {
            height: 5px;
            background: linear-gradient(90deg, var(--cyan), var(--orange), var(--cyan));
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ── Logo section ── */
        .logo-section {
            padding: 2rem 2.5rem 1.4rem;
            text-align: center;
            border-bottom: 1px solid #f0f4f8;
        }
        .logo-img {
            width: min(255px, 85%);
            height: auto;
            filter: drop-shadow(0 2px 14px rgba(0,191,255,.22));
            animation: logoFloat 5s ease-in-out infinite;
        }
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-6px); }
        }

        .system-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            margin-top: 1.1rem;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-m) 100%);
            color: #fff;
            border-radius: 50px;
            padding: .32rem 1rem;
            font-size: .74rem;
            font-weight: 600;
            letter-spacing: .4px;
        }
        .system-badge i { color: var(--cyan); }

        /* ── Form section ── */
        .form-section {
            padding: 1.6rem 2.5rem 2rem;
        }

        .form-heading { font-size: 1.3rem; font-weight: 700; color: var(--navy); margin-bottom: .15rem; }
        .form-subtext { font-size: .83rem; color: #94a3b8; margin-bottom: 1.5rem; }

        /* field */
        .field-wrap {
            position: relative;
            margin-bottom: 1rem;
        }
        .field-label {
            display: block;
            font-size: .72rem;
            font-weight: 700;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: .38rem;
        }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap .fi {
            position: absolute;
            left: .95rem;
            top: 50%;
            transform: translateY(-50%);
            color: #b0bec5;
            font-size: 1rem;
            pointer-events: none;
            transition: color .2s;
        }
        .form-control {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: .76rem 1rem .76rem 2.7rem;
            font-size: .94rem;
            font-family: 'Inter', sans-serif;
            color: #1a202c;
            background: #f8fafc;
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--cyan);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0,191,255,.12);
        }
        .input-icon-wrap:focus-within .fi { color: var(--cyan); }
        .form-control::placeholder { color: #c8d6e0; }

        /* password eye toggle */
        .pwd-wrap { position: relative; }
        .pwd-wrap .form-control { padding-right: 3rem; }
        .pwd-eye {
            position: absolute;
            right: .9rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 1rem;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
            transition: color .2s;
        }
        .pwd-eye:hover { color: var(--navy); }

        /* submit button */
        .btn-login {
            width: 100%;
            padding: .84rem;
            border: none;
            border-radius: 12px;
            font-size: .97rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            letter-spacing: .25px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-m) 100%);
            color: #fff;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform .18s, box-shadow .18s;
            box-shadow: 0 5px 20px rgba(27,58,107,.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-top: 1.3rem;
        }
        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.1) 0%, transparent 60%);
        }
        .btn-login i { color: var(--cyan); font-size: 1.05rem; }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(27,58,107,.5);
        }
        .btn-login:active { transform: translateY(0); }

        /* ── Footer ── */
        .card-foot {
            text-align: center;
            padding: .9rem 2.5rem 1.4rem;
            border-top: 1px solid #f0f4f8;
            font-size: .74rem;
            color: #94a3b8;
        }
        .card-foot .brand { color: var(--orange); font-weight: 700; }

        /* ── Flash alerts ── */
        .alert-success { background-color: #28a745 !important; color: #fff !important; border: none !important; }
        .alert-danger  { background-color: #dc3545 !important; color: #fff !important; border: none !important; }
        .alert-warning { background-color: #ffc107 !important; color: #000 !important; border: none !important; }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .logo-section, .form-section { padding-left: 1.4rem; padding-right: 1.4rem; }
            .card-foot { padding-left: 1.4rem; padding-right: 1.4rem; }
        }
    </style>
</head>

<body>

    <!-- Animated background layers -->
    <div class="bg-grid"></div>
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <!-- Flash messages -->
    <div style="position:fixed;top:20px;right:20px;z-index:2000;width:auto;max-width:360px;">
        <?php
        if ($login === false)
            echo '<div class="alert alert-danger alert-dismissible fade show shadow" role="alert">'
                . $msg .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

        echo validation_errors(
            '<div class="alert alert-danger alert-dismissible fade show shadow" role="alert">',
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
        );
        ?>
    </div>

    <!-- Card -->
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Shimmer accent -->
            <div class="card-accent"></div>

            <!-- Logo -->
            <div class="logo-section">
                <img src="<?php echo base_url('asset/images/zazulogo.png'); ?>"
                     alt="Zazu Technologies"
                     class="logo-img" />
                <div>
                    <span class="system-badge">
                        <i class="bi bi-building-lock"></i>
                        Enterprise Resource Planning
                    </span>
                </div>
            </div>

            <!-- Form -->
            <div class="form-section">
                <div class="form-heading">Welcome back</div>
                <div class="form-subtext">Sign in to access your ERP account</div>

                <form id="loginForm" action="<?php echo site_url('login'); ?>" method="post" novalidate>

                    <!-- Username -->
                    <div class="field-wrap">
                        <label class="field-label" for="user_name">Username or Email</label>
                        <div class="input-icon-wrap">
                            <input type="text"
                                   name="user_name"
                                   class="form-control"
                                   id="user_name"
                                   placeholder="Enter username or email"
                                   required
                                   autocomplete="username" />
                            <i class="bi bi-person fi"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="field-wrap">
                        <label class="field-label" for="user_pwd">Password</label>
                        <div class="input-icon-wrap pwd-wrap">
                            <input type="password"
                                   name="user_pwd"
                                   class="form-control"
                                   id="user_pwd"
                                   placeholder="Enter your password"
                                   required
                                   autocomplete="current-password" />
                            <i class="bi bi-lock fi"></i>
                            <button type="button" class="pwd-eye" id="togglePassword" aria-label="Toggle password">
                                <i class="bi bi-eye-slash" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Sign In to ERP
                    </button>

                </form>
            </div>

            <!-- Footer -->
            <div class="card-foot">
                Powered by <span class="brand">Zazu Technologies</span> &bull; We Innovate Technology
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pwdField  = document.getElementById('user_pwd');
            const toggleBtn = document.getElementById('togglePassword');
            const eyeIcon   = document.getElementById('eyeIcon');

            function togglePwd() {
                const isPass = pwdField.type === 'password';
                pwdField.type = isPass ? 'text' : 'password';
                eyeIcon.className = isPass ? 'bi bi-eye' : 'bi bi-eye-slash';
            }

            toggleBtn.addEventListener('click', togglePwd);
            toggleBtn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); togglePwd(); }
            });
        });
    </script>

</body>
</html>