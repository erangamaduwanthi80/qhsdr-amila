<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — QHS Defect Rate Dashboard</title>
    <link href="/pbpictures/qhsdr/public/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/pbpictures/qhsdr/public/assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 40px 36px;
            width: 100%;
            max-width: 400px;
        }

        .login-card .brand {
            font-size: 22px;
            font-weight: 700;
            color: #1e2a38;
            margin-bottom: 4px;
        }

        .login-card .brand span {
            color: #4ea8de;
        }

        .login-card .subtitle {
            font-size: 13px;
            color: #8a9bb0;
            margin-bottom: 28px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #1e2a38;
        }

        .form-control {
            border-radius: 8px;
            font-size: 14px;
            padding: 10px 14px;
            border: 1px solid #dde3ea;
        }

        .form-control:focus {
            border-color: #4ea8de;
            box-shadow: 0 0 0 3px rgba(78,168,222,0.15);
        }

        .btn-login {
            background-color: #4ea8de;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            padding: 11px;
            font-size: 14px;
            width: 100%;
            margin-top: 8px;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background-color: #3a95cb;
        }

        .alert-error {
            background-color: #fdecea;
            color: #c0392b;
            border: 1px solid #f5c6c2;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #aab4bf;
            font-size: 15px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand">QHS <span>DR</span></div>
    <div class="subtitle">Defect Rate Dashboard — Sign in to continue</div>

    <?php if (!empty($error)): ?>
        <div class="alert-error">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/pbpictures/qhsdr/public/auth/login">
        <?= Csrf::field() ?>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-icon">
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Enter your username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    autocomplete="username"
                    required
                >
                <i class="bi bi-person"></i>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-icon">
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
                <i class="bi bi-lock"></i>
            </div>
        </div>

        <button type="submit" class="btn-login">Sign In</button>
    </form>
</div>

<script src="/pbpictures/qhsdr/public/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
