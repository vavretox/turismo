<div id="provider-timeout-warning" class="fixed inset-x-4 bottom-5 z-[90] hidden max-w-md rounded-2xl border border-amber-200 bg-white p-4 shadow-2xl sm:left-auto sm:right-5">
    <div class="flex gap-3"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-amber-100 text-amber-700"><i class="fa-solid fa-clock"></i></span><div><strong class="text-gray-950">Tu sesión está por cerrarse</strong><p class="mt-1 text-sm text-gray-600">Interactúa con la página para continuar.</p></div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const timeout = 20 * 60 * 1000;
    const warningAt = 18 * 60 * 1000;
    const loginUrl = @js(route('prestador.login'));
    const expireUrl = @js(route('prestador.expire'));
    const pingUrl = @js(route('prestador.activity'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const warning = document.getElementById('provider-timeout-warning');
    let warningTimer;
    let logoutTimer;
    let lastPing = Date.now();

    const ping = function () {
        if (Date.now() - lastPing < 60000) return;
        lastPing = Date.now();
        fetch(pingUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (response) { if (response.status === 401 || response.redirected) window.location.href = loginUrl; })
            .catch(function () {});
    };
    const reset = function () {
        clearTimeout(warningTimer);
        clearTimeout(logoutTimer);
        warning.classList.add('hidden');
        warningTimer = setTimeout(function () { warning.classList.remove('hidden'); }, warningAt);
        logoutTimer = setTimeout(function () {
            fetch(expireUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'text/html' }, credentials: 'same-origin' })
                .finally(function () { window.location.href = loginUrl; });
        }, timeout);
        ping();
    };
    ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach(function (eventName) {
        window.addEventListener(eventName, reset, { passive: true });
    });
    reset();
});
</script>
