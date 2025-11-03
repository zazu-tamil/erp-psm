<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ERP Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .1);
            max-width: 420px;
            width: 100%;
        }

        .card-header {
            background: #0d6efd;
            color: #fff;
            padding: 1.5rem;
            text-align: center;
            border: none;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .card-header p {
            margin: 0.5rem 0 0;
            opacity: .9;
            font-size: .95rem;
        }

        .form-control {
            border-radius: 8px;
            padding: .65rem 1rem;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .25);
        }

        .btn-primary {
            border-radius: 8px;
            padding: .75rem;
            font-weight: 600;
            font-size: 1.05rem;
            transition: .2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, .3);
        }

        .password-toggle {
            cursor: pointer;
            color: #6c757d;
        }

        .password-toggle:hover {
            color: #0d6efd;
        }

        .text-muted a {
            color: #0d6efd !important;
        }

        .text-muted a:hover {
            text-decoration: underline !important;
        }
    </style>
</head>

<body>
    <!-- ==== FLASH MESSAGES ==== -->
    <style>
        /* Custom Flash Message Colors */
        .alert-success {
            background-color: #28a745 !important;
            /* Green */
            color: #fff !important;
            border: none !important;
        }

        .alert-danger {
            background-color: #dc3545 !important;
            /* Red */
            color: #fff !important;
            border: none !important;
        }

        .alert-warning {
            background-color: #ffc107 !important;
            /* Amber/Warning */
            color: #000 !important;
            border: none !important;
        }
    </style>

    <div style="position: fixed; top: 20px; right: 20px; z-index: 1050; width: auto; max-width: 350px;">
        <!-- <?php if ($this->session->flashdata('session_expired')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
                <?= $this->session->flashdata('session_expired'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($this->session->flashdata('logout_msg')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                <?= $this->session->flashdata('logout_msg'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?> -->

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


    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="login-card">

                    <!-- Header -->
                    <div class="card-header">
                        <h3>ERP Login</h3>
                        <p>Sign in to access your account</p>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-4">


                        <!-- ==== END FLASH MESSAGES ==== -->

                        <form id="loginForm" action="<?php echo site_url('login'); ?>" method="post" novalidate>
                            <div class="mb-3">
                                <label for="inputEmail" class="form-label">Username or Email</label>
                                <input type="text" name="user_name" class="form-control" id="user_name"
                                    placeholder="Enter username or email" required autocomplete="username" />
                            </div>

                            <div class="mb-3">
                                <label for="user_pwd" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="user_pwd" class="form-control" id="user_pwd"
                                        placeholder="Enter password" required autocomplete="current-password" />
                                    <span class="input-group-text password-toggle" id="togglePassword" role="button"
                                        tabindex="0" aria-label="Toggle password visibility">
                                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                    </span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

 

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.getElementById('user_pwd'); // ✅ corrected ID
            const toggleBtn = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            function toggle() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                eyeIcon.classList.toggle('bi-eye');
                eyeIcon.classList.toggle('bi-eye-slash');
            }

            toggleBtn.addEventListener('click', toggle);
            toggleBtn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggle();
                }
            });
        });
    </script>
</body>

</html>