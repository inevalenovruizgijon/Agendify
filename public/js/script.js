function configurarModal(queremosLogin) {
    const authForm = document.getElementById('authForm');
    const modalTitle = document.getElementById('modalTitle');
    const btnSubmit = document.getElementById('btnSubmit');
    const toggleBtn = document.getElementById('toggleAuth');
    const groupName = document.getElementById('groupName');
    const footerQuestion = document.getElementById('footerQuestion');

    if (queremosLogin) {
        authForm.action = '../../backend/auth/login.php';
        modalTitle.innerHTML = 'Bienvenido de <span>nuevo</span>';
        btnSubmit.innerText = 'Entrar a Agendify';
        toggleBtn.innerText = 'Regístrate';
        if (footerQuestion) footerQuestion.innerText = '¿No tienes cuenta?';
        if (groupName) {
            groupName.classList.add('d-none');
            groupName.querySelector('input').removeAttribute('required');
        }
    } else {
        authForm.action = '../../backend/auth/registro.php';
        modalTitle.innerHTML = 'Crea tu <span>cuenta</span>';
        btnSubmit.innerText = 'Crear cuenta gratis';
        toggleBtn.innerText = 'Inicia sesión';
        if (footerQuestion) footerQuestion.innerText = '¿Ya tienes cuenta?';
        if (groupName) {
            groupName.classList.remove('d-none');
            groupName.querySelector('input').setAttribute('required', '');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('authModal');
    const alertBox = document.getElementById('authAlert');
    const alertText = document.getElementById('alertText');
    const btnSubmit = document.getElementById('btnSubmit');

    document.getElementById('navLogin')?.addEventListener('click', () => {
        if (alertBox) alertBox.classList.add('d-none');
        configurarModal(true);
        modal.classList.add('active');
    });

    document.querySelectorAll('.btn-nav, .btn-primary-pro').forEach(btn => {
        btn.addEventListener('click', () => {
            if (alertBox) alertBox.classList.add('d-none');
            configurarModal(false);
            modal.classList.add('active');
        });
    });

    document.getElementById('closeAuth')?.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Validación del formulario
    document.getElementById('authForm')?.addEventListener('submit', (e) => {
        const esLogin = btnSubmit.innerText === 'Entrar a Agendify';
        const password = document.querySelector('input[name="password"]').value;
        const nombre = document.querySelector('input[name="nombre"]').value;
        const email = document.querySelector('input[name="email"]').value;

        alertBox.classList.add('d-none');

        // Validar email
        if (!email) {
            e.preventDefault();
            alertText.innerText = "El email es obligatorio.";
            alertBox.classList.remove('d-none');
            return;
        }

        // Validar contraseña
        if (password.length < 5) {
            e.preventDefault();
            alertText.innerText = "La contraseña debe tener mínimo 5 caracteres.";
            alertBox.classList.remove('d-none');
            return;
        }

        if (!/[a-zA-Z]/.test(password)) {
            e.preventDefault();
            alertText.innerText = "La contraseña debe contener al menos una letra.";
            alertBox.classList.remove('d-none');
            return;
        }

        // Validar nombre solo en registro
        if (!esLogin) {
            if (!nombre) {
                e.preventDefault();
                alertText.innerText = "El nombre es obligatorio.";
                alertBox.classList.remove('d-none');
                return;
            }
            if (nombre.length > 50) {
                e.preventDefault();
                alertText.innerText = "El nombre no puede tener más de 50 caracteres.";
                alertBox.classList.remove('d-none');
                return;
            }
        }
    });

    // Errores desde la URL
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');

    console.log("El error detectado en la URL es:", error);

    if (error && modal && alertBox && alertText) {
        setTimeout(() => {
            if (error === 'auth') {
                configurarModal(true);
                alertText.innerText = "El usuario no existe.";
            } else if (error === 'password') {
                configurarModal(true);
                alertText.innerText = "La contraseña introducida es incorrecta.";
            } else if (error === 'exists') {
                configurarModal(false);
                alertText.innerText = "Este correo ya está registrado.";
            }

            modal.classList.add('active');
            alertBox.classList.remove('d-none');
        }, 100);
    }

    document.getElementById('toggleAuth')?.addEventListener('click', (e) => {
        e.preventDefault();
        if (alertBox) alertBox.classList.add('d-none');
        const esLoginActual = btnSubmit.innerText === 'Entrar a Agendify';
        configurarModal(!esLoginActual);
    });
});