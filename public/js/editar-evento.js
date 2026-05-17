// Cogemos el modal de edición y sus botones de cerrar del HTML
const editModal = document.getElementById('editEventModal');
const closeEditBtn = document.getElementById('closeEditModal');
const cancelEditBtn = document.getElementById('btnCancelEdit');

// Abre el modal añadiendo la clase 'active' que lo hace visible
function openEditModal() {
    editModal.classList.add('active');
}

// Cierra el modal quitando la clase 'active'
const closeEdit = () => editModal.classList.remove('active');

// Cerramos el modal tanto con la X como con el botón Cancelar
closeEditBtn.addEventListener('click', closeEdit);
cancelEditBtn.addEventListener('click', closeEdit);

// Si el usuario pulsa fuera del modal (en el fondo oscuro), también se cierra
editModal.addEventListener('click', (e) => {
    if (e.target === editModal) closeEdit();
});

// Al enviar el formulario de edición, evitamos que recargue la página
// y cerramos el modal (aquí iría la lógica real de guardado)
document.getElementById('editEventForm').addEventListener('submit', (e) => {
    e.preventDefault();
    console.log("Cambios guardados con éxito");
    closeEdit();
});

// Abrimos el modal automáticamente al cargar la página
openEditModal();