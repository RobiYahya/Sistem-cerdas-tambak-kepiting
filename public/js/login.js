(function () {
    'use strict';

    const input  = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    if (!input || !toggle) return;

    toggle.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        toggle.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        input.focus();
    });
})();