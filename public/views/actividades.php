<?php
session_start(); // Inicia la sesión para acceder a los datos del usuario

// Si el usuario no ha iniciado sesión, lo redirige al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../../backend/config/conexion.php'; // Carga la conexión a la base de datos

$usuario_id = $_SESSION['usuario_id']; // Guarda el ID del usuario en sesión

// Calcula los días que faltan (o han pasado) desde hoy hasta una fecha dada
// Devuelve negativo si la fecha ya pasó
function diasRestantes($fecha) {
    $hoy   = new DateTime(date('Y-m-d'));
    $event = new DateTime($fecha);
    return (int) $hoy->diff($event)->format('%r%a');
}

// Determina el estado de una actividad según su fecha:
// - 'realizada'  → fecha ya pasó
// - 'pendiente'  → quedan 7 días o menos
// - 'proxima'    → quedan más de 7 días
function estadoPorFecha($fecha) {
    $dias = diasRestantes($fecha);
    if ($dias < 0)   return 'realizada';
    if ($dias <= 7)  return 'pendiente';
    return 'proxima';
}

// Procesa el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    // CREAR nueva actividad
    if ($_POST['accion'] === 'crear') {
        // Limpia y escapa los datos del formulario para evitar inyección SQL
        $titulo      = trim(mysqli_real_escape_string($conexion, $_POST['titulo']));
        $descripcion = trim(mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? ''));
        $fecha       = mysqli_real_escape_string($conexion, $_POST['fecha']);
        $hora        = mysqli_real_escape_string($conexion, $_POST['hora']);
        $prioridad   = mysqli_real_escape_string($conexion, $_POST['prioridad'] ?? 'media');
        $estado      = estadoPorFecha($_POST['fecha']); // Calcula el estado según la fecha

        mysqli_query($conexion,
            "INSERT INTO actividades (usuario_id, titulo, descripcion, fecha, hora, estado, prioridad)
             VALUES ($usuario_id, '$titulo', '$descripcion', '$fecha', '$hora', '$estado', '$prioridad')"
        );
    }

    // EDITAR actividad existente
    if ($_POST['accion'] === 'editar') {
        $id          = (int) $_POST['actividad_id']; // Casteo a entero para evitar inyección
        $titulo      = trim(mysqli_real_escape_string($conexion, $_POST['titulo']));
        $descripcion = trim(mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? ''));
        $fecha       = mysqli_real_escape_string($conexion, $_POST['fecha']);
        $hora        = mysqli_real_escape_string($conexion, $_POST['hora']);
        $prioridad   = mysqli_real_escape_string($conexion, $_POST['prioridad'] ?? 'media');
        $estado      = estadoPorFecha($_POST['fecha']); // Recalcula el estado con la nueva fecha

        // Solo actualiza si la actividad pertenece al usuario en sesión
        mysqli_query($conexion,
            "UPDATE actividades SET titulo='$titulo', descripcion='$descripcion',
             fecha='$fecha', hora='$hora', prioridad='$prioridad', estado='$estado'
             WHERE id=$id AND usuario_id=$usuario_id"
        );
    }

    // ELIMINAR actividad
    if ($_POST['accion'] === 'eliminar') {
        $id = (int) $_POST['actividad_id'];
        // Solo elimina si la actividad pertenece al usuario en sesión
        mysqli_query($conexion, "DELETE FROM actividades WHERE id=$id AND usuario_id=$usuario_id");
    }

    // Recarga la página para reflejar los cambios (patrón PRG: Post/Redirect/Get)
    header("Location: actividades.php");
    exit();
}

// Obtiene las actividades de un usuario filtradas por estado
function getActividades($conexion, $usuario_id, $estado) {
    $estado = mysqli_real_escape_string($conexion, $estado);
    $res = mysqli_query($conexion,
        "SELECT * FROM actividades
         WHERE usuario_id = $usuario_id AND estado = '$estado'
         ORDER BY fecha ASC, hora ASC" // Ordena por fecha y hora más próximas primero
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row; // Acumula cada fila en un array
    }
    return $rows;
}

// Carga las tres listas de actividades según su estado
$pendientes = getActividades($conexion, $usuario_id, 'pendiente');
$proximas   = getActividades($conexion, $usuario_id, 'proxima');
$realizadas = getActividades($conexion, $usuario_id, 'realizada');

mysqli_close($conexion); // Cierra la conexión a la base de datos

// Devuelve la clase CSS correspondiente a la prioridad de la tarjeta
function prioridadClase($prioridad) {
    return [
        'alta'  => 'card-alta',
        'media' => 'card-media',
        'baja'  => 'card-baja',
    ][$prioridad] ?? 'card-media'; // 'media' como valor por defecto
}

// Devuelve el icono Remix Icon correspondiente a la prioridad
function prioridadIcono($prioridad) {
    return [
        'alta'  => 'ri-alarm-warning-fill',
        'media' => 'ri-flag-fill',
        'baja'  => 'ri-leaf-fill',
    ][$prioridad] ?? 'ri-flag-fill';
}

// Genera el HTML completo de una tarjeta de actividad
function renderTarjeta($act) {
    // Escapa los textos para mostrarlos seguros en HTML
    $titulo      = htmlspecialchars($act['titulo']);
    $descripcion = htmlspecialchars($act['descripcion'] ?? '');

    // Formatea fecha y hora para mostrarlas de forma legible
    $fecha     = date('d M Y', strtotime($act['fecha']));
    $hora      = date('H:i', strtotime($act['hora']));

    $id        = (int) $act['id'];
    $estado    = $act['estado'];
    $fecha_raw = $act['fecha'];              // Fecha en formato YYYY-MM-DD para el input date
    $hora_raw  = substr($act['hora'], 0, 5); // Solo HH:MM para el input time
    $prioridad = $act['prioridad'];
    $cardClase = prioridadClase($prioridad);
    $icono     = prioridadIcono($prioridad);
    $diasTexto = '';

    // Texto descriptivo de tiempo restante (solo para actividades no realizadas)
    if ($estado !== 'realizada') {
        $dias = diasRestantes($act['fecha']);
        if ($dias === 0)     $diasTexto = "Hoy";
        elseif ($dias === 1) $diasTexto = "Mañana";
        elseif ($dias > 1)   $diasTexto = "En $dias días";
    } else {
        $diasTexto = "Completada";
    }

    // Bloque de descripción: solo se renderiza si existe contenido
    $desc_html = $descripcion
        ? "<p class='ac-desc'>$descripcion</p>"
        : "";

    // Escapa los valores para usarlos de forma segura dentro de atributos onclick en JS
    $titulo_js      = addslashes($titulo);
    $descripcion_js = addslashes($descripcion);

    // Botón "Realizada" solo visible en actividades que aún no están completadas
    $btn_realizada = '';
    if ($estado !== 'realizada') {
        $btn_realizada = "
            <button class='ac-btn-done' onclick=\"marcarRealizada($id)\">
                <i class='ri-check-line'></i> Realizada
            </button>
        ";
    }

    // Retorna el HTML completo de la tarjeta con todos los datos interpolados
    return "
    <div class='activity-card $cardClase' data-id='$id'>
        <div class='ac-priority-bar'></div>
        <div class='ac-body'>
            <div class='ac-top'>
                <span class='ac-title'>$titulo</span>
                <span class='ac-pill $cardClase'>
                    <i class='$icono'></i> " . ucfirst($prioridad) . "
                </span>
            </div>
            $desc_html
            <div class='ac-meta'>
                <span class='ac-meta-item'><i class='ri-calendar-line'></i> $fecha</span>
                <span class='ac-meta-item'><i class='ri-time-line'></i> $hora</span>
                <span class='ac-dias'>$diasTexto</span>
            </div>
            <div class='ac-actions'>
            $btn_realizada
                <button class='ac-btn-edit'
                    onclick=\"abrirEdicion($id, '$titulo_js', '$descripcion_js', '$fecha_raw', '$hora_raw', '$prioridad')\">
                    <i class='ri-edit-line'></i> Editar
                </button>
                <form method='POST' style='display:inline;'
                      onsubmit=\"return confirm('¿Eliminar esta actividad?')\">
                    <input type='hidden' name='accion' value='eliminar'>
                    <input type='hidden' name='actividad_id' value='$id'>
                    <button type='submit' class='ac-btn-delete'>
                        <i class='ri-delete-bin-line'></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades | Agendify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/actividades.css">
    <link rel="stylesheet" href="../css/calendar.css">
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
            <a href="calendar.php" class="nav-item">
                <i class="ri-calendar-line"></i> <span>Calendario</span>
            </a>
            <a href="actividades.php" class="nav-item active">
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
                <h1>Listado de <span>Actividades</span></h1>
            </div>
            <button class="btn-add-event" id="openModalBtn">
                <i class="ri-add-line"></i>
                <span>Nuevo evento</span>
            </button>
        </header>

        <section class="activities-layout">

            <!-- Columna: actividades con 0-7 días restantes -->
            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Pendientes</h2>
                    <?php /* Muestra el total de actividades pendientes */ ?>
                    <span class="count-badge count-pendiente"><?= count($pendientes) ?></span>
                </div>
                <div class="column-content" id="list-pending">
                    <?php if (empty($pendientes)): ?>
                        <div class="empty-state">
                            <i class="ri-inbox-line"></i>
                            <p>No hay tareas pendientes</p>
                        </div>
                    <?php else: ?>
                        <?php /* Renderiza una tarjeta por cada actividad pendiente */ ?>
                        <?php foreach ($pendientes as $act): echo renderTarjeta($act); endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Columna: actividades con más de 7 días restantes -->
            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Próximas</h2>
                    <?php /* Muestra el total de actividades próximas */ ?>
                    <span class="count-badge count-proxima"><?= count($proximas) ?></span>
                </div>
                <div class="column-content" id="list-upcoming">
                    <?php if (empty($proximas)): ?>
                        <div class="empty-state">
                            <i class="ri-calendar-event-line"></i>
                            <p>Sin eventos próximos</p>
                        </div>
                    <?php else: ?>
                        <?php /* Renderiza una tarjeta por cada actividad próxima */ ?>
                        <?php foreach ($proximas as $act): echo renderTarjeta($act); endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Columna: actividades cuya fecha ya pasó -->
            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Realizadas</h2>
                    <?php /* Muestra el total de actividades realizadas */ ?>
                    <span class="count-badge count-realizada"><?= count($realizadas) ?></span>
                </div>
                <div class="column-content" id="list-done">
                    <?php if (empty($realizadas)): ?>
                        <div class="empty-state">
                            <i class="ri-checkbox-circle-line"></i>
                            <p>Aún no has completado nada</p>
                        </div>
                    <?php else: ?>
                        <?php /* Renderiza una tarjeta por cada actividad realizada */ ?>
                        <?php foreach ($realizadas as $act): echo renderTarjeta($act); endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </section>
    </main>

    <!-- Modal para crear un nuevo evento -->
    <div class="modal-overlay" id="eventModal">
        <div class="modal-card event-card">
            <div class="event-modal-header">
                <h2><i class="ri-add-circle-line"></i> Nuevo evento</h2>
                <button class="close-modal" id="closeEventModal"><i class="ri-close-circle-line"></i></button>
            </div>
            <?php /* El campo oculto 'accion' indica al POST que debe ejecutar la lógica de crear */ ?>
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

    <!-- Modal para editar un evento existente -->
    <div class="modal-overlay" id="editEventModal">
        <div class="modal-card event-card">
            <div class="event-modal-header">
                <h2><i class="ri-edit-line"></i> Editar evento</h2>
                <button class="close-modal" id="closeEditModal"><i class="ri-close-circle-line"></i></button>
            </div>
            <?php /* Los campos ocultos envían la acción 'editar' y el ID de la actividad a modificar.
                     El JS rellena estos campos al abrir el modal con abrirEdicion() */ ?>
            <form class="event-form" method="POST" action="actividades.php" id="editEventForm">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="actividad_id" id="edit_id">
                <div class="input-group">
                    <label><i class="ri-text"></i> Título *</label>
                    <input type="text" name="titulo" id="edit_titulo" placeholder="Nombre del evento" required>
                </div>
                <div class="input-row">
                    <div class="input-group">
                        <label><i class="ri-calendar-line"></i> Fecha *</label>
                        <input type="date" name="fecha" id="edit_fecha" required>
                    </div>
                    <div class="input-group">
                        <label><i class="ri-time-line"></i> Hora *</label>
                        <input type="time" name="hora" id="edit_hora" required>
                    </div>
                </div>
                <div class="input-group">
                    <label><i class="ri-flag-line"></i> Prioridad</label>
                    <div class="prioridad-selector">
                        <label class="prio-opt prio-baja">
                            <input type="radio" name="prioridad" value="baja" id="edit_prio_baja">
                            <span><i class="ri-leaf-fill"></i> Baja</span>
                        </label>
                        <label class="prio-opt prio-media">
                            <input type="radio" name="prioridad" value="media" id="edit_prio_media">
                            <span><i class="ri-flag-fill"></i> Media</span>
                        </label>
                        <label class="prio-opt prio-alta">
                            <input type="radio" name="prioridad" value="alta" id="edit_prio_alta">
                            <span><i class="ri-alarm-warning-fill"></i> Alta</span>
                        </label>
                    </div>
                </div>
                <div class="input-group">
                    <label><i class="ri-file-text-line"></i> Descripción</label>
                    <textarea name="descripcion" id="edit_descripcion" rows="3"></textarea>
                </div>
                <div class="event-modal-actions">
                    <button type="submit" class="btn-create"><i class="ri-save-line"></i> Guardar cambios</button>
                    <button type="button" class="btn-cancel" id="btnCancelEdit">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/nuevo-evento.js"></script>
</body>
</html>