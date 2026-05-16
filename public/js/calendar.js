document.addEventListener('DOMContentLoaded', () => {

const calendarGrid  = document.getElementById('calendarGrid');
const monthNameSpan = document.getElementById('monthName');
const prevBtn       = document.getElementById('prevMonth');
const nextBtn       = document.getElementById('nextMonth');
const todayBtn      = document.getElementById('todayBtn');

let date = new Date();

const MONTH_LABELS = [
    'Enero','Febrero','Marzo','Abril','Mayo','Junio',
    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
];

const PRIORITY_COLOR = {
    alta:  '#ef4444',
    media: '#f59e0b',
    baja:  '#22c55e',
};

function buildActivityMap() {
    const map = {};
    const source = (typeof ACTIVIDADES !== 'undefined') ? ACTIVIDADES : [];
    source.forEach(act => {
        const key = act.fecha.slice(0, 10);
        if (!map[key]) map[key] = [];
        map[key].push(act);
    });
    return map;
}

//  Popup de actividad 
let activePopup = null;

function closePopup() {
    if (activePopup) { activePopup.remove(); activePopup = null; }
}

function showActivityPopup(act, chipEl) {
    closePopup();

    const PRIORITY_LABEL = { alta: 'Alta prioridad', media: 'Media prioridad', baja: 'Baja prioridad' };
    const PRIORITY_CLASS  = { alta: 'pop-badge--alta', media: 'pop-badge--media', baja: 'pop-badge--baja' };

    const popup = document.createElement('div');
    popup.classList.add('activity-popup');
    popup.innerHTML = `
        <div class="pop-header">
            <span class="pop-title">${act.titulo}</span>
            <button class="pop-close" title="Cerrar">×</button>
        </div>
        <div class="pop-meta">
            <span class="pop-badge ${PRIORITY_CLASS[act.prioridad] || ''}">${PRIORITY_LABEL[act.prioridad] || act.prioridad}</span>
            <span class="pop-date">📅 ${act.fecha ? act.fecha.slice(0, 10) : ''} ${act.hora ? '· ' + act.hora.slice(0, 5) : ''}</span>
        </div>
        <div class="pop-actions">
            <button class="pop-btn pop-btn--done" data-id="${act.id}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                Realizada
            </button>
            <button class="pop-btn pop-btn--edit" data-id="${act.id}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </button>
            <button class="pop-btn pop-btn--delete" data-id="${act.id}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                Eliminar
            </button>
        </div>
    `;

    // Posicionar relativo al chip
    const rect = chipEl.getBoundingClientRect();
    popup.style.position = 'fixed';
    popup.style.top  = (rect.bottom + 6) + 'px';
    popup.style.left = rect.left + 'px';
    document.body.appendChild(popup);
    activePopup = popup;

    // Ajustar si se sale por la derecha
    const pRect = popup.getBoundingClientRect();
    if (pRect.right > window.innerWidth - 10) {
        popup.style.left = (window.innerWidth - pRect.width - 10) + 'px';
    }
    // Ajustar si se sale por abajo
    if (pRect.bottom > window.innerHeight - 10) {
        popup.style.top = (rect.top - pRect.height - 6) + 'px';
    }

    popup.querySelector('.pop-close').addEventListener('click', closePopup);

    popup.querySelector('.pop-btn--done').addEventListener('click', () => {
        if (!confirm(`¿Marcar "${act.titulo}" como realizada?`)) return;
        fetch('../../backend/actividades/completar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: act.id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) { closePopup(); renderCalendar(); }
            else alert('Error al marcar como realizada.');
        })
        .catch(() => alert('Error de conexión.'));
    });

    popup.querySelector('.pop-btn--edit').addEventListener('click', () => {
        closePopup();
        window.location.href = `actividades.php?editar=${act.id}`;
    });

    popup.querySelector('.pop-btn--delete').addEventListener('click', () => {
        if (!confirm(`¿Eliminar "${act.titulo}"? Esta acción no se puede deshacer.`)) return;
        fetch('../../backend/actividades/eliminar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: act.id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) { closePopup(); renderCalendar(); }
            else alert('Error al eliminar la actividad.');
        })
        .catch(() => alert('Error de conexión.'));
    });
}

// Cerrar popup al hacer clic fuera
document.addEventListener('click', e => {
    if (activePopup && !activePopup.contains(e.target)) closePopup();
});

// Renderizado del calendario
function renderCalendar() {
    calendarGrid.innerHTML = '';

    const year  = date.getFullYear();
    const month = date.getMonth();
    const today = new Date();

    monthNameSpan.innerHTML = `${MONTH_LABELS[month]} <span>${year}</span>`;

    ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'].forEach(d => {
        const el = document.createElement('div');
        el.classList.add('day-name');
        el.innerText = d;
        calendarGrid.appendChild(el);
    });

    let firstDayIndex = new Date(year, month, 1).getDay() - 1;
    if (firstDayIndex === -1) firstDayIndex = 6;

    const lastDay     = new Date(year, month + 1, 0).getDate();
    const prevLastDay = new Date(year, month, 0).getDate();
    const actMap      = buildActivityMap();

    for (let x = firstDayIndex; x > 0; x--) {
        const el = document.createElement('div');
        el.classList.add('day', 'empty');
        el.innerText = prevLastDay - x + 1;
        calendarGrid.appendChild(el);
    }

    for (let i = 1; i <= lastDay; i++) {
        const el = document.createElement('div');
        el.classList.add('day');

        const isToday = (
            i === today.getDate() &&
            month === today.getMonth() &&
            year  === today.getFullYear()
        );
        if (isToday) el.classList.add('current-day-highlight');

        const mm   = String(month + 1).padStart(2, '0');
        const dd   = String(i).padStart(2, '0');
        const key  = `${year}-${mm}-${dd}`;
        const acts = actMap[key] || [];

        const numSpan = document.createElement('span');
        numSpan.textContent = i;
        el.appendChild(numSpan);

        if (acts.length > 0) {
            const dotsWrap = document.createElement('div');
            dotsWrap.classList.add('day-events');

            acts.slice(0, 2).forEach(act => {
                const chip = document.createElement('span');
                chip.classList.add('event-chip');
                chip.style.setProperty('--chip-color', PRIORITY_COLOR[act.prioridad] || '#94a3b8');
                chip.textContent = act.titulo;
                chip.title = act.titulo;

                // ── Abrir popup al hacer clic en el chip ──
                chip.addEventListener('click', e => {
                    e.stopPropagation();
                    showActivityPopup(act, chip);
                });

                dotsWrap.appendChild(chip);
            });

            if (acts.length > 2) {
                const more = document.createElement('span');
                more.classList.add('event-more');
                more.textContent = `+${acts.length - 2} más`;
                dotsWrap.appendChild(more);
            }

            el.appendChild(dotsWrap);
            el.classList.add('has-events');
        }

        calendarGrid.appendChild(el);
    }
}

//  Controles de navegación 
prevBtn.addEventListener('click', () => {
    date.setMonth(date.getMonth() - 1);
    renderCalendar();
});

nextBtn.addEventListener('click', () => {
    date.setMonth(date.getMonth() + 1);
    renderCalendar();
});

if (todayBtn) {
    todayBtn.addEventListener('click', () => {
        date = new Date();
        renderCalendar();
    });
}

//Modal de nuevo evento
const eventModal     = document.getElementById('eventModal');
const btnOpenEvent   = document.querySelector('.btn-add-event');
const btnCloseEvent  = document.getElementById('closeEventModal');
const btnCancelEvent = document.getElementById('btnCancelEvent');

btnOpenEvent.addEventListener('click', () => eventModal.classList.add('active'));

[btnCloseEvent, btnCancelEvent].forEach(btn => {
    btn.addEventListener('click', () => eventModal.classList.remove('active'));
});

window.addEventListener('click', e => {
    if (e.target === eventModal) eventModal.classList.remove('active');
});

renderCalendar();

}); 