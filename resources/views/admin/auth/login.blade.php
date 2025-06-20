<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">

<div class="container">
    <div class="col-md-6 offset-md-3">
        <h3 class="mb-4 text-center">Admin Login</h3>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>

</body>
</html>
