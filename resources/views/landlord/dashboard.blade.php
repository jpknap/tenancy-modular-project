<h1>Bienvenido, {{ auth('web')->user()->name }}</h1>
<form method="POST" action="{{ route('landlord.logout') }}">
    @csrf
    <button type="submit">Cerrar sesión</button>
</form>
