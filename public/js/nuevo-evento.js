document.addEventListener('DOMContentLoaded', () => {

    //  Modal de crear nuevo evento 
    const modal     = document.getElementById('eventModal');
    const openBtn   = document.querySelector('.btn-add-event');
    const closeBtn  = document.getElementById('closeEventModal');
    const cancelBtn = document.getElementById('btnCancelEvent');

    // Cierra el modal, resetea el formulario y vuelve a marcar "Media" por defecto
    const closeModal = () => {
        modal.classList.remove('active');
        document.getElementById('newEventForm').reset();
        document.querySelectorAll('#eventModal .prio-opt').forEach(l => l.classList.remove('selected'));
        const mediaDefault = document.querySelector('#eventModal .prio-media');
        if (mediaDefault) mediaDefault.classList.add('selected');
    };

    // Abrir y cerrar el modal con los botones correspondientes
    if (openBtn)    openBtn.addEventListener('click', () => modal.classList.add('active'));
    if (closeBtn)   closeBtn.addEventListener('click', closeModal);
    if (cancelBtn)  cancelBtn.addEventListener('click', closeModal);

    // Cerrar si el usuario pulsa fuera del modal
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    // Modal de editar evento 
    const editModal     = document.getElementById('editEventModal');
    const closeEditBtn  = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('btnCancelEdit');

    // Cierra el modal de edición
    const closeEdit = () => editModal.classList.remove('active');

    // Cerrar con la X o con el botón Cancelar
    if (closeEditBtn)   closeEditBtn.addEventListener('click', closeEdit);
    if (cancelEditBtn)  cancelEditBtn.addEventListener('click', closeEdit);

    // Cerrar si el usuario pulsa fuera del modal
    editModal.addEventListener('click', (e) => { if (e.target === editModal) closeEdit(); });

    // Selector de prioridad (Baja / Media / Alta) 
    // Recorre todos los selectores de prioridad de la página (crear y editar)
    document.querySelectorAll('.prioridad-selector').forEach(selector => {
        const opts = selector.querySelectorAll('.prio-opt');
        opts.forEach(opt => {
            const radio = opt.querySelector('input[type=radio]');

            // Marca visualmente la opción que ya viene seleccionada por defecto
            if (radio.checked) opt.classList.add('selected');

            // Al pulsar una opción, quita la selección de las demás y marca la elegida
            opt.addEventListener('click', () => {
                opts.forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
            });
        });
    });
});

//  Abrir modal de edición con los datos de una actividad 
// Se llama desde cada tarjeta de actividad pasando sus datos como parámetros
function abrirEdicion(id, titulo, descripcion, fecha, hora, prioridad) {

    // Rellenamos los campos del formulario con los datos de la actividad
    document.getElementById('edit_id').value          = id;
    document.getElementById('edit_titulo').value      = titulo;
    document.getElementById('edit_descripcion').value = descripcion;
    document.getElementById('edit_fecha').value       = fecha;
    document.getElementById('edit_hora').value        = hora;

    // Marcamos el radio y el estilo visual de la prioridad correcta
    const radios = document.querySelectorAll('#editEventModal input[name="prioridad"]');
    const opts   = document.querySelectorAll('#editEventModal .prio-opt');
    opts.forEach(o => o.classList.remove('selected'));
    radios.forEach(r => {
        r.checked = (r.value === prioridad);
        if (r.checked) r.closest('.prio-opt').classList.add('selected');
    });

    // Abrimos el modal
    document.getElementById('editEventModal').classList.add('active');
}

// Marcar una actividad como realizada 
// Llama al backend y recarga la página si todo va bien
function marcarRealizada(id) {
    if (!confirm('¿Marcar esta actividad como realizada?')) return;
    fetch('../../backend/actividades/completar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) window.location.reload();
        else alert('Error al actualizar.');
    })
    .catch(() => alert('Error de conexión.'));
}

//  Menú lateral móvil
document.addEventListener("DOMContentLoaded", function() {
    const openMenuBtn  = document.getElementById("openMenuBtn");
    const closeMenuBtn = document.getElementById("closeMenuBtn");
    const sidebarMenu  = document.getElementById("sidebarMenu");

    // Abre el menú lateral al pulsar el botón de hamburguesa
    if (openMenuBtn && sidebarMenu) {
        openMenuBtn.addEventListener("click", () => {
            sidebarMenu.classList.add("active");
        });
    }

    // Cierra el menú lateral al pulsar la X
    if (closeMenuBtn && sidebarMenu) {
        closeMenuBtn.addEventListener("click", () => {
            sidebarMenu.classList.remove("active");
        });
    }

    // Cierra el menú si el usuario pulsa fuera de él
    document.addEventListener("click", (event) => {
        if (sidebarMenu && sidebarMenu.classList.contains("active")) {
            if (!sidebarMenu.contains(event.target) && !openMenuBtn.contains(event.target)) {
                sidebarMenu.classList.remove("active");
            }
        }
    });
});