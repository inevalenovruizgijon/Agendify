document.addEventListener('DOMContentLoaded', () => {

    // Crear nuevo evento
    const modal     = document.getElementById('eventModal');
    const openBtn   = document.querySelector('.btn-add-event');
    const closeBtn  = document.getElementById('closeEventModal');
    const cancelBtn = document.getElementById('btnCancelEvent');

    const closeModal = () => {
        modal.classList.remove('active');
        document.getElementById('newEventForm').reset();
        document.querySelectorAll('#eventModal .prio-opt').forEach(l => l.classList.remove('selected'));
        const mediaDefault = document.querySelector('#eventModal .prio-media');
        if (mediaDefault) mediaDefault.classList.add('selected');
    };

    if (openBtn)    openBtn.addEventListener('click', () => modal.classList.add('active'));
    if (closeBtn)   closeBtn.addEventListener('click', closeModal);
    if (cancelBtn)  cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    // Editar evento
    const editModal     = document.getElementById('editEventModal');
    const closeEditBtn  = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('btnCancelEdit');

    const closeEdit = () => editModal.classList.remove('active');

    if (closeEditBtn)   closeEditBtn.addEventListener('click', closeEdit);
    if (cancelEditBtn)  cancelEditBtn.addEventListener('click', closeEdit);
    editModal.addEventListener('click', (e) => { if (e.target === editModal) closeEdit(); });


    document.querySelectorAll('.prioridad-selector').forEach(selector => {
        const opts = selector.querySelectorAll('.prio-opt');
        opts.forEach(opt => {
            const radio = opt.querySelector('input[type=radio]');
            if (radio.checked) opt.classList.add('selected');
            opt.addEventListener('click', () => {
                opts.forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
            });
        });
    });
});

function abrirEdicion(id, titulo, descripcion, fecha, hora, prioridad) {
    document.getElementById('edit_id').value          = id;
    document.getElementById('edit_titulo').value      = titulo;
    document.getElementById('edit_descripcion').value = descripcion;
    document.getElementById('edit_fecha').value       = fecha;
    document.getElementById('edit_hora').value        = hora;

    const radios = document.querySelectorAll('#editEventModal input[name="prioridad"]');
    const opts   = document.querySelectorAll('#editEventModal .prio-opt');
    opts.forEach(o => o.classList.remove('selected'));
    radios.forEach(r => {
        r.checked = (r.value === prioridad);
        if (r.checked) r.closest('.prio-opt').classList.add('selected');
    });

    document.getElementById('editEventModal').classList.add('active');
}
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


document.addEventListener("DOMContentLoaded", function() {
    const openMenuBtn = document.getElementById("openMenuBtn");
    const closeMenuBtn = document.getElementById("closeMenuBtn");
    const sidebarMenu = document.getElementById("sidebarMenu");

    // Abrir menú
    if (openMenuBtn && sidebarMenu) {
        openMenuBtn.addEventListener("click", () => {
            sidebarMenu.classList.add("active");
        });
    }

    // Cerrar menú
    if (closeMenuBtn && sidebarMenu) {
        closeMenuBtn.addEventListener("click", () => {
            sidebarMenu.classList.remove("active");
        });
    }

    // Opcional: Cerrar el menú si se hace clic fuera de él
    document.addEventListener("click", (event) => {
        if (sidebarMenu && sidebarMenu.classList.contains("active")) {
            if (!sidebarMenu.contains(event.target) && !openMenuBtn.contains(event.target)) {
                sidebarMenu.classList.remove("active");
            }
        }
    });
});