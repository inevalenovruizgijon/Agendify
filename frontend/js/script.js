function configurarModal(queremosLogin) {
    const authForm = document.getElementById('authForm');
    const modalTitle = document.getElementById('modalTitle');
    const btnSubmit = document.getElementById('btnSubmit');
    const toggleBtn = document.getElementById('toggleAuth');
    const groupName = document.getElementById('groupName');
    const alertBox = document.getElementById('authAlert');

    if (alertBox) alertBox.classList.add('d-none');

    if (queremosLogin) {
        authForm.action = '../../backend/auth/login.php';
        modalTitle.innerHTML = 'Bienvenido de <span>nuevo</span>';
        btnSubmit.innerText = 'Entrar a Agendify';
        toggleBtn.innerText = 'Regístrate';
        if (groupName) groupName.classList.add('d-none');
    } else {
        authForm.action = '../../backend/auth/registro.php';
        modalTitle.innerHTML = 'Crea tu <span>cuenta</span>';
        btnSubmit.innerText = 'Crear cuenta gratis';
        toggleBtn.innerText = 'Inicia sesión';
        if (groupName) groupName.classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('authModal');
    const alertBox = document.getElementById('authAlert');
    const alertText = document.getElementById('alertText');

    document.getElementById('navLogin')?.addEventListener('click', () => {
        configurarModal(true);
        modal.classList.add('active');
    });

    document.querySelectorAll('.btn-nav, .btn-primary-pro').forEach(btn => {
        btn.addEventListener('click', () => {
            configurarModal(false);
            modal.classList.add('active');
        });
    });

    document.getElementById('closeAuth')?.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');

    if (error && modal && alertBox && alertText) {
        setTimeout(() => {
            modal.classList.add('active');
            alertBox.classList.remove('d-none');

            if (error === 'auth') {
                configurarModal(true);
                alertText.innerText = "Email o contraseña incorrectos.";
            } else if (error === 'exists') {
                configurarModal(false);
                alertText.innerText = "Este correo ya está registrado.";
            }
        }, 100);
    }
});