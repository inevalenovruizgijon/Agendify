const modal = document.getElementById('authModal');
const btnClose = document.getElementById('closeAuth');
const toggleBtn = document.getElementById('toggleAuth');

// Elementos que cambian dentro del modal
const modalTitle = document.getElementById('modalTitle');
const modalSubtitle = document.getElementById('modalSubtitle');
const groupName = document.getElementById('groupName');
const btnSubmit = document.getElementById('btnSubmit');
const footerQuestion = document.getElementById('footerQuestion');

let isLogin = true;

// Función única para cambiar la apariencia del modal
function configurarModal(queremosLogin) {
    isLogin = queremosLogin;
    
    if (isLogin) {
        modalTitle.innerHTML = 'Bienvenido de <span>nuevo</span>';
        modalSubtitle.innerText = 'Ingresa tus datos para continuar';
        btnSubmit.innerText = 'Entrar a Agendify';
        footerQuestion.innerText = '¿No tienes cuenta?';
        toggleBtn.innerText = 'Regístrate';
        groupName.classList.add('d-none');
    } else {
        modalTitle.innerHTML = 'Crea tu <span>cuenta</span>';
        modalSubtitle.innerText = 'Únete a la revolución de la productividad';
        btnSubmit.innerText = 'Crear cuenta gratis';
        footerQuestion.innerText = '¿Ya tienes cuenta?';
        toggleBtn.innerText = 'Inicia sesión';
        groupName.classList.remove('d-none');
    }
}

// BOTÓN LOGIN (Navbar)
document.querySelector('.link-login').addEventListener('click', (e) => {
    e.preventDefault();
    configurarModal(true); // Queremos modo login
    modal.classList.add('active');
});

// BOTONES REGISTRO (Navbar y Hero)
// Seleccionamos ambos: el de la nav y el grande del centro
document.querySelectorAll('.btn-nav, .btn-primary-pro').forEach(boton => {
    boton.addEventListener('click', (e) => {
        e.preventDefault();
        configurarModal(false); // Queremos modo registro
        modal.classList.add('active');
    });
});

// LINK DE CAMBIO (Dentro del modal)
toggleBtn.addEventListener('click', (e) => {
    e.preventDefault();
    configurarModal(!isLogin); // Cambia al estado contrario
});

// CERRAR MODAL
btnClose.addEventListener('click', () => modal.classList.remove('active'));

window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.remove('active');
    }
});