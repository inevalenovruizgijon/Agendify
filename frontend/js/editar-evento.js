// Referencias a los elementos de Edición
const editModal = document.getElementById('editEventModal');
const closeEditBtn = document.getElementById('closeEditModal');
const cancelEditBtn = document.getElementById('btnCancelEdit');

// Función para abrir el modal de edición
// Podrías llamar a esta función cuando el usuario haga click en un evento del calendario
function openEditModal() {
    editModal.classList.add('active');
}

// Función para cerrar
const closeEdit = () => editModal.classList.remove('active');

closeEditBtn.addEventListener('click', closeEdit);
cancelEditBtn.addEventListener('click', closeEdit);

// Cerrar si se hace click en el fondo (overlay)
editModal.addEventListener('click', (e) => {
    if (e.target === editModal) closeEdit();
});

// Evitar que el formulario recargue la página (solo para pruebas)
document.getElementById('editEventForm').addEventListener('submit', (e) => {
    e.preventDefault();
    console.log("Cambios guardados con éxito");
    closeEdit();
});

// ELIMINA ESTA LÍNEA CUANDO LO JUNTES CON EL CALENDARIO:
openEditModal();