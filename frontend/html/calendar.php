<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario | Agendify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/calendar.css">
</head>
<body class="calendar-page">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="ri-calendar-check-fill"></i>
            <div class="logo-text">
                <span class="brand-name">Agendify</span>
                <span class="brand-sub">Agencia Digital</span>
            </div>
        </div>

        <nav class="sidebar-nav">
    <a href="calendar.php" class="nav-item active">
        <i class="ri-calendar-line"></i>
        <span>Calendario</span>
    </a>
    <!-- ENLACE A ACTIVIDADES -->
    <a href="actividades.php" class="nav-item">
        <i class="ri-list-check"></i>
        <span>Actividades</span>
    </a>
    <a href="#" class="nav-item">
        <i class="ri-user-line"></i>
        <span>Perfil</span>
    </a>
    <div class="nav-divider"></div>
    <a href="index.php" class="nav-item logout">
        <i class="ri-logout-box-line"></i>
        <span>Cerrar sesión</span>
    </a>
</nav>

        <div class="sidebar-footer">
            <p>© 2026 Agendify Team</p>
        </div>
    </aside>

    <main class="calendar-main">
        
        <header class="calendar-header">
            <div class="header-info">
                <h1 id="monthName">Marzo <span>2025</span></h1>
                <div class="calendar-controls">
                    <button class="btn-icon" id="prevMonth" title="Mes anterior">
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                    <button class="btn-icon" id="nextMonth" title="Mes siguiente">
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                    <button class="btn-today" id="todayBtn">Hoy</button>
                </div>
            </div>
            
            <button class="btn-add-event">
                <i class="ri-add-line"></i> 
                <span>Nuevo evento</span>
            </button>
        </header>

        <section class="calendar-container">
            <div class="calendar-grid" id="calendarGrid">
                </div>
        </section>

        <footer class="calendar-footer">
            <a href="actividades.php" class="view-list">
                <i class="ri-list-check"></i> Ver lista completa de actividades
            </a>
        </footer>
    </main>
    <div class="modal-overlay" id="eventModal">
    <div class="modal-card event-card">
        <div class="event-modal-header">
            <h2>Nuevo evento</h2>
            <button class="close-modal" id="closeEventModal"><i class="ri-close-circle-line"></i></button>
        </div>
        
        <form class="event-form" id="newEventForm">
            <div class="input-group">
                <label>Título *</label>
                <input type="text" placeholder="Nombre del evento" required>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Fecha *</label>
                    <input type="date" required>
                </div>
                <div class="input-group">
                    <label>Hora *</label>
                    <input type="time" required>
                </div>
            </div>

            <div class="input-group">
                <label>Descripción</label>
                <textarea placeholder="Detalles del evento..." rows="4"></textarea>
            </div>

            <div class="event-modal-actions">
                <button type="submit" class="btn-create">Crear Evento</button>
                <button type="button" class="btn-cancel" id="btnCancelEvent">Cancelar</button>
            </div>
        </form>
    </div>
</div>
    <script src="../js/calendar.js"></script>
</body>
</html>