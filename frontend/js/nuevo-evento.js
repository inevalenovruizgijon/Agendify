// Esperar a que el DOM esté cargado para evitar errores
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('eventModal');
    const openBtn = document.querySelector('.btn-add-event'); 
    const closeBtn = document.getElementById('closeEventModal');
    const cancelBtn = document.getElementById('btnCancelEvent');

    if (openBtn) {
        openBtn.addEventListener('click', () => {
            modal.classList.add('active');
        });
    }

    const closeModal = () => {
        modal.classList.remove('active');
        // Opcional: Limpiar el formulario al cerrar
        document.getElementById('newEventForm').reset();
    };

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Cerrar al hacer clic fuera de la tarjeta blanca
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
});