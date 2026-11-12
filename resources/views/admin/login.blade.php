<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-brand flex items-center justify-center p-4">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="w-full max-w-sm rounded-2xl bg-white p-8 shadow-2xl space-y-4">
        @csrf
        <h1 class="text-4xl text-center">Admin login</h1>
        <x-form-field name="email" type="email" label="Email" required autocomplete="username" id="login" />
        <x-form-field name="password" type="password" label="Password" required autocomplete="current-password" id="login" />
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1" class="accent-brand"> Keep me signed in</label>
        <x-button type="submit" class="w-full" variant="secondary">Sign in</x-button>
    </form>
</body>
</html>
