<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades | Agendify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/calendar.css"> 
    <link rel="stylesheet" href="../css/actividades.css">
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
            <a href="calendar.php" class="nav-item">
                <i class="ri-calendar-line"></i> <span>Calendario</span>
            </a>
            <a href="actividades.php" class="nav-item active">
                <i class="ri-list-check"></i> <span>Actividades</span>
            </a>
            <a href="#" class="nav-item">
                <i class="ri-user-line"></i> <span>Perfil</span>
            </a>
            <div class="nav-divider"></div>
            <a href="index.php" class="nav-item logout">
                <i class="ri-logout-box-line"></i> <span>Cerrar sesión</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <p>© 2026 Agendify Team</p>
        </div>
    </aside>

    <main class="calendar-main">
        <header class="calendar-header">
            <div class="header-info">
                <h1>Listado de <span>Actividades</span></h1>
            </div>
            <button class="btn-add-event" id="openModalBtn">
                <i class="ri-add-line"></i>
                <span>Nuevo evento</span>
            </button>
        </header>

        <section class="activities-layout">
            
            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Pendientes</h2>
                    <span class="count-badge" id="count-pending">0</span>
                </div>
                <div class="column-content container-white" id="list-pending">
                    <div class="empty-state">
                        <i class="ri-inbox-line"></i>
                        <p>No hay tareas pendientes</p>
                    </div>
                </div>
            </div>

            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Próximas</h2>
                    <span class="count-badge" id="count-upcoming">0</span>
                </div>
                <div class="column-content container-white" id="list-upcoming">
                    <div class="empty-state">
                        <i class="ri-calendar-event-line"></i>
                        <p>Sin eventos próximos</p>
                    </div>
                </div>
            </div>

            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Realizadas</h2>
                    <span class="count-badge" id="count-done">0</span>
                </div>
                <div class="column-content container-white" id="list-done">
                    <div class="empty-state">
                        <i class="ri-checkbox-circle-line"></i>
                        <p>Aún no has completado nada</p>
                    </div>
                </div>
            </div>
        </section>

        
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
                    <textarea placeholder="Detalles del evento..." rows="3"></textarea>
                </div>
                <div class="event-modal-actions">
                    <button type="submit" class="btn-create">Crear Evento</button>
                    <button type="button" class="btn-cancel" id="btnCancelEvent">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/nuevo-evento.js"></script>
</body>
</html>