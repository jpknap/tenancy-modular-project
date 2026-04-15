<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('auth.title') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow" style="width: 22rem;">
    <h3 class="text-center mb-3">{{ __('auth.title') }}</h3>
    <form method="POST" action="{{ url('activities-board/auth/login') }}">
        @csrf
        <div class="mb-3">
            <label>{{ __('auth.email') }}</label>
            <input name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label>{{ __('auth.password_label') }}</label>
            <input name="password" type="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">{{ __('auth.submit') }}</button>
    </form>
    @if($errors->any())
        <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
    @endif
</div>
</body>
</html>
