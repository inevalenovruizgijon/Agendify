<?php
session_start(); // Inicia la sesión para acceder a los datos del usuario

// Si el usuario no ha iniciado sesión, lo redirige al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../../backend/config/conexion.php'; // Carga la conexión a la base de datos

$usuario_id = $_SESSION['usuario_id']; // Guarda el ID del usuario en sesión

// Obtiene todas las actividades del usuario ordenadas por fecha y hora
$res = mysqli_query($conexion,
     "SELECT id, fecha, hora, titulo, prioridad FROM actividades
     WHERE usuario_id = $usuario_id
     ORDER BY fecha ASC, hora ASC"
);

// Construye un array con los datos de cada actividad para pasarlos al JS
$actividades_json = [];
while ($row = mysqli_fetch_assoc($res)) {
    $actividades_json[] = [
        'id'        => (int)$row['id'],               // Casteo a entero por seguridad
        'fecha'     => $row['fecha'],
        'hora'      => $row['hora'],
        'titulo'    => htmlspecialchars($row['titulo']), // Escapa el título para evitar XSS
        'prioridad' => $row['prioridad'],
    ];
}

mysqli_close($conexion); // Cierra la conexión a la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario | Agendify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/calendar.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="icon" type="image/png" href="../assets/img/logoFavicon.png">
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
                <i class="ri-calendar-line"></i> <span>Calendario</span>
            </a>
            <a href="actividades.php" class="nav-item ">
                <i class="ri-list-check"></i> <span>Actividades</span>
            </a>
            <a href="profile.php" class="nav-item">
                <i class="ri-user-line"></i> <span>Perfil</span>
            </a>
            <div class="nav-divider"></div>
            <a href="../../backend/auth/logout.php" class="nav-item logout">
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
                <!-- El calendario se genera dinámicamente desde calendar.js -->
            </div>
        </section>

        <footer class="calendar-footer">
            <a href="actividades.php" class="view-list">
                <i class="ri-list-check"></i> Ver lista completa de actividades
            </a>
        </footer>
    </main>

    <!-- Modal para crear un nuevo evento -->
    <div class="modal-overlay" id="eventModal">
    <div class="modal-card event-card">
        <div class="event-modal-header">
            <h2><i class="ri-add-circle-line"></i> Nuevo evento</h2>
            <button class="close-modal" id="closeEventModal"><i class="ri-close-circle-line"></i></button>
        </div>

        <!-- method, action y name añadidos para que el POST funcione -->
        <form class="event-form" method="POST" action="actividades.php" id="newEventForm">
            <input type="hidden" name="accion" value="crear">

            <div class="input-group">
                <label><i class="ri-text"></i> Título *</label>
                <input type="text" name="titulo" placeholder="Nombre del evento" required>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label><i class="ri-calendar-line"></i> Fecha *</label>
                    <input type="date" name="fecha" required>
                </div>
                <div class="input-group">
                    <label><i class="ri-time-line"></i> Hora *</label>
                    <input type="time" name="hora" required>
                </div>
            </div>

            <div class="input-group">
                <label><i class="ri-flag-line"></i> Prioridad</label>
                <div class="prioridad-selector">
                    <label class="prio-opt prio-baja">
                        <input type="radio" name="prioridad" value="baja">
                        <span><i class="ri-leaf-fill"></i> Baja</span>
                    </label>
                    <label class="prio-opt prio-media">
                        <input type="radio" name="prioridad" value="media" checked>
                        <span><i class="ri-flag-fill"></i> Media</span>
                    </label>
                    <label class="prio-opt prio-alta">
                        <input type="radio" name="prioridad" value="alta">
                        <span><i class="ri-alarm-warning-fill"></i> Alta</span>
                    </label>
                </div>
            </div>

            <div class="input-group">
                <label><i class="ri-file-text-line"></i> Descripción</label>
                <textarea name="descripcion" placeholder="Detalles del evento..." rows="3"></textarea>
            </div>

            <div class="event-modal-actions">
                <button type="submit" class="btn-create"><i class="ri-check-line"></i> Crear Evento</button>
                <button type="button" class="btn-cancel" id="btnCancelEvent">Cancelar</button>
            </div>
        </form>
    </div>
</div>
    <?php
    // Convierte el array PHP a JSON y lo inyecta como variable global de JavaScript.
    // Así calendar.js puede leer las actividades sin hacer peticiones adicionales al servidor.
    ?>
    <script>const ACTIVIDADES = <?= json_encode($actividades_json) ?>;</script>
    <script src="../js/calendar.js"></script>
    <script src="../js/nuevo-evento.js"></script>
</body>
</html>