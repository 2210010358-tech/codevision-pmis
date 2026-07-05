<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CodeVision PMIS</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-card {
            background-color: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }

        .login-logo {
            font-size: 2.5rem;
            color: #4f46e5;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
        }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            padding: 0.75rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('images/logo.png') }}" alt="CodeVision Logo" style="height: 64px; width: 64px; object-fit: contain; border-radius: 12px;">
        </div>
        <h3 class="text-center fw-bold mb-1 text-dark">CodeVision PMIS</h3>
        <p class="text-center text-secondary small mb-4">Project Management Information System</p>

        @if ($errors->any())
            <div class="alert alert-danger border-0 small py-2 mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-bold">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@company.com">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label small fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small text-secondary" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-3">
                Sign In <i class="bi bi-arrow-right-short fs-5 align-middle"></i>
            </button>
        </form>

        <div class="mt-4 pt-3 border-top border-light text-center">
            <p class="text-muted small mb-0">Demo Accounts (Password: <code>password</code>):</p>
            <div class="text-secondary small mt-1" style="font-size: 0.75rem;">
                Admin: <code>admin@codevision.com</code><br>
                Leader: <code>leader@codevision.com</code><br>
                Dev: <code>dev1@codevision.com</code> | Client: <code>client1@codevision.com</code>
            </div>
        </div>
    </div>
</body>
</html>
