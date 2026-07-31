@extends('layouts.app')

@section('title', 'Acceso para prestadores')

@section('content')
<section class="min-h-screen bg-[#f8f3ec] pb-16 pt-32">
    <div class="container-custom">
        <div class="mx-auto max-w-xl rounded-3xl bg-white p-7 shadow-xl sm:p-10">
            <div class="text-center">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-red-950 text-2xl text-white"><i class="fa-solid fa-store"></i></span>
                <h1 class="mt-5 text-3xl font-black text-gray-950">Portal del prestador</h1>
                <p class="mx-auto mt-3 max-w-md leading-6 text-gray-600">Administra la información pública de tu servicio y solicita aparecer en el mapa turístico.</p>
            </div>
            @if(session('success'))<div class="mt-6 rounded-xl bg-green-50 p-4 text-sm font-bold text-green-800">{{ session('success') }}</div>@endif
            @if(session('session_expired'))<div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800"><i class="fa-solid fa-clock mr-2"></i>{{ session('session_expired') }}</div>@endif
            <form class="mx-auto mt-8 max-w-md space-y-5" method="POST" action="{{ route('prestador.authenticate') }}">
                @csrf
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-gray-700">Correo electrónico</span>
                    <input class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 outline-none transition focus:border-red-800 focus:ring-4 focus:ring-red-800/10" type="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" required autofocus>
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-gray-700">Contraseña</span>
                    <input class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 outline-none transition focus:border-red-800 focus:ring-4 focus:ring-red-800/10" type="password" name="password" placeholder="Ingresa tu contraseña" required>
                </label>
                @error('email')<p class="provider-error">{{ $message }}</p>@enderror
                <label class="flex cursor-pointer items-center gap-3 text-sm text-gray-600"><input class="h-4 w-4 rounded border-gray-300 text-red-800 focus:ring-red-800/20" type="checkbox" name="remember"> Mantener sesión iniciada</label>
                <button class="btn-primary w-full py-3.5" type="submit"><i class="fa-solid fa-right-to-bracket mr-2"></i>Ingresar a mi página</button>
            </form>
            <p class="mt-7 text-center text-sm text-gray-600">¿Todavía no tienes cuenta? <a class="font-bold text-red-800 hover:underline" href="{{ route('prestadores.create') }}">Registra tu servicio</a></p>
        </div>
    </div>
</section>
@endsection
