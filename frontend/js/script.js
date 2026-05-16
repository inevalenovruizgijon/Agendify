function configurarModal(queremosLogin) {
    const authForm = document.getElementById('authForm');
    const modalTitle = document.getElementById('modalTitle');
    const btnSubmit = document.getElementById('btnSubmit');
    const toggleBtn = document.getElementById('toggleAuth');
    const groupName = document.getElementById('groupName');
    const alertBox = document.getElementById('authAlert');

    const footerQuestion = document.getElementById('footerQuestion');

    if (queremosLogin) {
        authForm.action = '../../backend/auth/login.php';
        modalTitle.innerHTML = 'Bienvenido de <span>nuevo</span>';
        btnSubmit.innerText = 'Entrar a Agendify';
        toggleBtn.innerText = 'Regístrate';

        if (footerQuestion) footerQuestion.innerText = '¿No tienes cuenta?';

        if (groupName) groupName.classList.add('d-none');
    } else {
        authForm.action = '../../backend/auth/registro.php';
        modalTitle.innerHTML = 'Crea tu <span>cuenta</span>';
        btnSubmit.innerText = 'Crear cuenta gratis';
        toggleBtn.innerText = 'Inicia sesión';

        if (footerQuestion) footerQuestion.innerText = '¿Ya tienes cuenta?';

        if (groupName) groupName.classList.remove('d-none');
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

    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');

    console.log("El error detectado en la URL es:", error);

    if (error && modal && alertBox && alertText) {
        setTimeout(() => {
            // configuramos primero el tipo de modal
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

            // mostramos el modal y quitamos el d-none
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