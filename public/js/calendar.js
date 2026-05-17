document.addEventListener('DOMContentLoaded', () => {

    // Cogemos los elementos del HTML que vamos a necesitar
    const calendarGrid  = document.getElementById('calendarGrid');
    const monthNameSpan = document.getElementById('monthName');
    const prevBtn       = document.getElementById('prevMonth');
    const nextBtn       = document.getElementById('nextMonth');
    const todayBtn      = document.getElementById('todayBtn');

    // Fecha actual, se usa para saber qué mes mostrar
    let date = new Date();

    // Nombres de los meses en español para mostrarlos en la cabecera
    const MONTH_LABELS = [
        'Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
    ];

    // Colores y etiquetas para cada nivel de prioridad
    const PRIORITY = {
        alta:  { bg: '#fee2e2', color: '#b91c1c', dot: '#ef4444', label: '↑ Alta'  },
        media: { bg: '#fef3c7', color: '#92400e', dot: '#f59e0b', label: '→ Media' },
        baja:  { bg: '#dcfce7', color: '#166534', dot: '#22c55e', label: '↓ Baja'  },
    };

    // Devuelve los colores de una actividad según su prioridad
    function pri(p) { return PRIORITY[(p||'').toLowerCase()] || PRIORITY.baja; }

    // Agrupa todas las actividades por fecha (YYYY-MM-DD) en un objeto
    // para poder acceder rápido a las del día cuando pintamos el calendario
    function buildMap() {
        const map = {};
        const src = typeof ACTIVIDADES !== 'undefined' ? ACTIVIDADES : [];
        src.forEach(a => {
            const k = a.fecha.slice(0,10);
            (map[k] = map[k] || []).push(a);
        });
        return map;
    }

    /* 
       POPUP INDIVIDUAL  (clic en chip)
       Aparece al pulsar una actividad concreta
       dentro de una celda del calendario
    */
    let openPopup = null;

    // Cierra el popup si hay uno abierto
    function closePopup() { if (openPopup) { openPopup.remove(); openPopup = null; } }

    // Crea y muestra el popup con los detalles de una actividad
    function showChipPopup(act, chipEl) {
        closePopup();
        const p = pri(act.prioridad);
        const pop = document.createElement('div');
        pop.className = 'ag-popup';

        // Contenido del popup: título, prioridad, fecha y botones de acción
        pop.innerHTML = `
            <div class="ag-popup__head">
                <span>${act.titulo}</span>
                <button class="ag-popup__x">✕</button>
            </div>
            <div class="ag-popup__meta">
                <span class="ag-badge" style="background:${p.bg};color:${p.color}">
                    <span class="ag-dot" style="background:${p.dot}"></span>${p.label}
                </span>
                <span class="ag-popup__date">📅 ${act.fecha.slice(0,10)}${act.hora ? ' · '+act.hora.slice(0,5) : ''}</span>
            </div>
            <div class="ag-popup__actions">
                <button class="ag-act ag-act--ok"  data-id="${act.id}">✔ Realizada</button>
                <button class="ag-act ag-act--ed"  data-id="${act.id}">✏ Editar</button>
                <button class="ag-act ag-act--del" data-id="${act.id}">🗑 Eliminar</button>
            </div>`;

        // Lo posicionamos justo debajo del chip que se pulsó
        const r = chipEl.getBoundingClientRect();
        pop.style.cssText = `position:fixed;top:${r.bottom+6}px;left:${r.left}px;z-index:99999`;
        document.body.appendChild(pop);
        openPopup = pop;

        //Si el popup se sale de la pantalla, lo recolocamos
        const pr = pop.getBoundingClientRect();
        if (pr.right  > innerWidth  - 8) pop.style.left = (innerWidth  - pr.width  - 8)+'px';
        if (pr.bottom > innerHeight - 8) pop.style.top  = (r.top - pr.height - 6)+'px';

        //Botón cerrar
        pop.querySelector('.ag-popup__x').onclick = closePopup;

        // Botón "Realizada": llama al backend para marcarla como completada
        pop.querySelector('.ag-act--ok').onclick = () => {
            if (!confirm(`¿Marcar "${act.titulo}" como realizada?`)) return;
            fetch('../../backend/actividades/completar.php', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body: JSON.stringify({id: act.id})
            }).then(r=>r.json()).then(d=>{ if(d.ok){closePopup();renderCalendar();} else alert('Error.'); })
              .catch(()=>alert('Error de conexión.'));
        };

        // Botón "Editar": redirige a la página de actividades con el id para editar
        pop.querySelector('.ag-act--ed').onclick = () => {
            closePopup();
            location.href = `actividades.php?editar=${act.id}`;
        };

        //Botón "Eliminar": llama al backend para borrarla y recarga el calendario
        pop.querySelector('.ag-act--del').onclick = () => {
            if (!confirm(`¿Eliminar "${act.titulo}"?`)) return;
            fetch('../../backend/actividades/eliminar.php', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body: JSON.stringify({id: act.id})
            }).then(r=>r.json()).then(d=>{ if(d.ok){closePopup();renderCalendar();} else alert('Error.'); })
              .catch(()=>alert('Error de conexión.'));
        };
    }

    /* 
       MODAL DEL DÍA  (clic en celda)
       Muestra todas las actividades de un día
       cuando el usuario pulsa sobre una celda
    */
    let dayModal = null;

    // Cierra el modal del día con una pequeña animación de salida
    function closeDayModal() {
        if (dayModal) { dayModal.classList.remove('ag-day-modal--in'); setTimeout(()=>{if(dayModal){dayModal.remove();dayModal=null;}},220); }
    }

    // Crea y muestra el modal con todas las actividades del día pulsado
    function showDayModal(key, acts) {
        closeDayModal();

        // Formateamos la fecha para mostrarla en el título del modal
        const [y,m,d] = key.split('-');
        const label = `${parseInt(d)} de ${MONTH_LABELS[parseInt(m)-1]} de ${y}`;

        // Generamos una fila por cada actividad del día
        const rows = acts.map(a => {
            const p = pri(a.prioridad);
            const hora = a.hora ? a.hora.slice(0,5) : '--:--';
            return `
            <div class="ag-dm-row">
                <div class="ag-dm-bar" style="background:${p.dot}"></div>
                <div class="ag-dm-time">${hora}</div>
                <div class="ag-dm-body">
                    <span class="ag-dm-title">${a.titulo}</span>
                    <span class="ag-badge" style="background:${p.bg};color:${p.color}">
                        <span class="ag-dot" style="background:${p.dot}"></span>${p.label}
                    </span>
                </div>
                <div class="ag-dm-btns">
                    <button class="ag-dm-btn ag-dm-btn--ed"  data-id="${a.id}" title="Editar">✏</button>
                    <button class="ag-dm-btn ag-dm-btn--del" data-id="${a.id}" data-titulo="${a.titulo}" title="Eliminar">🗑</button>
                </div>
            </div>`;
        }).join('');

        //Creamos el modal con la cabecera, lista de actividades y enlace a actividades
        const ov = document.createElement('div');
        ov.className = 'ag-day-modal';
        ov.innerHTML = `
            <div class="ag-day-modal__card">
                <div class="ag-day-modal__head">
                    <div>
                        <small>Actividades del día</small>
                        <strong>${label}</strong>
                    </div>
                    <button class="ag-day-modal__x">✕</button>
                </div>
                <p class="ag-day-modal__count">${acts.length} actividad${acts.length!==1?'es':''}</p>
                <div class="ag-day-modal__list">${rows}</div>
                <div class="ag-day-modal__foot">
                    <a href="actividades.php">Ver todas las actividades →</a>
                </div>
            </div>`;

        document.body.appendChild(ov);
        dayModal = ov;

        //Pequeña animación de entrada
        requestAnimationFrame(() => ov.classList.add('ag-day-modal--in'));

        // Cerrar al pulsar la X o fuera del modal
        ov.querySelector('.ag-day-modal__x').onclick = closeDayModal;
        ov.onclick = e => { if(e.target===ov) closeDayModal(); };

        // Botón editar de cada fila: redirige a actividades con el id
        ov.querySelectorAll('.ag-dm-btn--ed').forEach(btn => {
            btn.onclick = e => { e.stopPropagation(); closeDayModal(); location.href=`actividades.php?editar=${btn.dataset.id}`; };
        });

        //Botón eliminar de cada fila: llama al backend y recarga
        ov.querySelectorAll('.ag-dm-btn--del').forEach(btn => {
            btn.onclick = e => {
                e.stopPropagation();
                if (!confirm(`¿Eliminar "${btn.dataset.titulo}"?`)) return;
                fetch('../../backend/actividades/eliminar.php', {
                    method:'POST', headers:{'Content-Type':'application/json'},
                    body: JSON.stringify({id: btn.dataset.id})
                }).then(r=>r.json()).then(d=>{ if(d.ok){closeDayModal();renderCalendar();} else alert('Error.'); })
                  .catch(()=>alert('Error de conexión.'));
            };
        });
    }

    //Si el usuario pulsa fuera de un popup abierto, lo cerramos
    document.addEventListener('click', e => {
        if (openPopup && !openPopup.contains(e.target)) closePopup();
    });

    /*
       Dibuja el calendario completo del mes actual
     */
    function renderCalendar() {
        calendarGrid.innerHTML = '';
        const year  = date.getFullYear();
        const month = date.getMonth();
        const today = new Date();

        //Actualizamos el título con el mes y año actual
        monthNameSpan.innerHTML = `${MONTH_LABELS[month]} <span>${year}</span>`;

        //Pintamos la fila de nombres de días (Lun, Mar, ...)
        ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'].forEach(d => {
            const el = document.createElement('div');
            el.className = 'day-name';
            el.textContent = d;
            calendarGrid.appendChild(el);
        });

        //Calculamos en qué día de la semana empieza el mes (0=Lun, 6=Dom)
        let firstDay = new Date(year, month, 1).getDay() - 1;
        if (firstDay === -1) firstDay = 6;
        const lastDay     = new Date(year, month+1, 0).getDate();
        const prevLastDay = new Date(year, month,   0).getDate();

        // Mapa de actividades agrupadas por fecha
        const actMap = buildMap();

        //Rellenamos las celdas vacías del inicio (días del mes anterior)
        for (let x = firstDay; x > 0; x--) {
            const el = document.createElement('div');
            el.className = 'day empty';
            el.innerHTML = `<span>${prevLastDay - x + 1}</span>`;
            calendarGrid.appendChild(el);
        }

        //Pintamos cada día del mes actual
        for (let i = 1; i <= lastDay; i++) {
            const mm  = String(month+1).padStart(2,'0');
            const dd  = String(i).padStart(2,'0');
            const key = `${year}-${mm}-${dd}`;

            //Actividades que hay en este día (vacío si no hay ninguna)
            const acts = actMap[key] || [];

            const isToday = i===today.getDate() && month===today.getMonth() && year===today.getFullYear();

            const cell = document.createElement('div');
            cell.className = 'day' + (isToday ? ' current-day-highlight' : '') + (acts.length ? ' has-events' : '');

            //Número del día
            const numEl = document.createElement('span');
            numEl.className = 'ag-day-num';
            numEl.textContent = i;
            cell.appendChild(numEl);

            //Si hay actividades, pintamos los chips (máximo 3, el resto muestra "+X más")
            if (acts.length > 0) {
                const wrap = document.createElement('div');
                wrap.className = 'ag-chips';

                const MAX = 3;
                acts.slice(0, MAX).forEach(a => {
                    const p    = pri(a.prioridad);
                    const hora = a.hora ? a.hora.slice(0,5) : '';
                    const chip = document.createElement('div');
                    chip.className = 'ag-chip';
                    chip.style.cssText = `--cb:${p.bg};--ct:${p.color};--cd:${p.dot}`;
                    chip.innerHTML = `<span class="ag-chip__dot"></span>${hora?`<span class="ag-chip__time">${hora}</span>`:''}<span class="ag-chip__name">${a.titulo}</span>`;
                    chip.title = `${a.titulo}${hora?' · '+hora:''}`;

                    //Al pulsar un chip se abre el popup de esa actividad
                    chip.onclick = e => { e.stopPropagation(); showChipPopup(a, chip); };
                    wrap.appendChild(chip);
                });

                //Si hay más de 3 actividades, mostramos cuántas quedan
                if (acts.length > MAX) {
                    const more = document.createElement('div');
                    more.className = 'ag-chip-more';
                    more.textContent = `+${acts.length - MAX} más`;
                    wrap.appendChild(more);
                }

                cell.appendChild(wrap);
            }

            //Al pulsar la celda se abre el modal con todas las actividades del día
            cell.onclick = () => { if (acts.length > 0) showDayModal(key, acts); };

            calendarGrid.appendChild(cell);
        }
    }

    //Botones de navegación entre meses
    prevBtn.addEventListener('click', () => { date.setMonth(date.getMonth()-1); renderCalendar(); });
    nextBtn.addEventListener('click', () => { date.setMonth(date.getMonth()+1); renderCalendar(); });
    if (todayBtn) todayBtn.addEventListener('click', () => { date = new Date(); renderCalendar(); });

    //Modal de nuevo evento: abrir y cerrar
    const eventModal     = document.getElementById('eventModal');
    const btnOpenEvent   = document.querySelector('.btn-add-event');
    const btnCloseEvent  = document.getElementById('closeEventModal');
    const btnCancelEvent = document.getElementById('btnCancelEvent');
    btnOpenEvent .addEventListener('click', () => eventModal.classList.add('active'));
    btnCloseEvent .addEventListener('click', () => eventModal.classList.remove('active'));
    btnCancelEvent.addEventListener('click', () => eventModal.classList.remove('active'));

    //Cerrar modal si se pulsa fuera de él
    window.addEventListener('click', e => { if(e.target===eventModal) eventModal.classList.remove('active'); });

    //Pintamos el calendario al cargar la página
    renderCalendar();
});