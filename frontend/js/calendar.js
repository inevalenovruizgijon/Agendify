const calendarGrid = document.getElementById('calendarGrid');
const monthNameSpan = document.getElementById('monthName');
const prevBtn = document.getElementById('prevMonth');
const nextBtn = document.getElementById('nextMonth');

let date = new Date(); // Fecha actual

function renderCalendar() {
    calendarGrid.innerHTML = ''; 
    
    const daysOfWeek = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    daysOfWeek.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.classList.add('day-name');
        dayHeader.innerText = day;
        calendarGrid.appendChild(dayHeader);
    });

    const year = date.getFullYear();
    const month = date.getMonth();

    const monthLabels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    monthNameSpan.innerHTML = `${monthLabels[month]} <span>${year}</span>`;

    let firstDayIndex = new Date(year, month, 1).getDay() - 1;
    if (firstDayIndex === -1) firstDayIndex = 6; 

    const lastDay = new Date(year, month + 1, 0).getDate();
    const prevLastDay = new Date(year, month, 0).getDate();

    for (let x = firstDayIndex; x > 0; x--) {
        const emptyDay = document.createElement('div');
        emptyDay.classList.add('day', 'empty');
        emptyDay.innerText = prevLastDay - x + 1;
        calendarGrid.appendChild(emptyDay);
    }

    for (let i = 1; i <= lastDay; i++) {
        const daySquare = document.createElement('div');
        daySquare.classList.add('day');
        
        // Marcar el día de hoy
        const today = new Date();
        if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
            daySquare.classList.add('current-day-highlight');
        }

        daySquare.innerHTML = `<span>${i}</span>`;
        calendarGrid.appendChild(daySquare);
    }
}

prevBtn.addEventListener('click', () => {
    date.setMonth(date.getMonth() - 1);
    renderCalendar();
});

nextBtn.addEventListener('click', () => {
    date.setMonth(date.getMonth() + 1);
    renderCalendar();
});
const eventModal = document.getElementById('eventModal');
const btnOpenEvent = document.querySelector('.btn-add-event'); 
const btnCloseEvent = document.getElementById('closeEventModal');
const btnCancelEvent = document.getElementById('btnCancelEvent');

btnOpenEvent.addEventListener('click', () => {
    eventModal.classList.add('active');
});

[btnCloseEvent, btnCancelEvent].forEach(btn => {
    btn.addEventListener('click', () => {
        eventModal.classList.remove('active');
    });
});

window.addEventListener('click', (e) => {
    if (e.target === eventModal) {
        eventModal.classList.remove('active');
    }
});

renderCalendar();

