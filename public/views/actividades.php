<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../../backend/config/conexion.php';

$usuario_id = $_SESSION['usuario_id'];

function diasRestantes($fecha) {
    $hoy   = new DateTime(date('Y-m-d'));
    $event = new DateTime($fecha);
    return (int) $hoy->diff($event)->format('%r%a');
}

function estadoPorFecha($fecha) {
    $dias = diasRestantes($fecha);
    if ($dias < 0)   return 'realizada';
    if ($dias <= 7)  return 'pendiente';   
    return 'proxima';                      
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    if ($_POST['accion'] === 'crear') {
        $titulo      = trim(mysqli_real_escape_string($conexion, $_POST['titulo']));
        $descripcion = trim(mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? ''));
        $fecha       = mysqli_real_escape_string($conexion, $_POST['fecha']);
        $hora        = mysqli_real_escape_string($conexion, $_POST['hora']);
        $prioridad   = mysqli_real_escape_string($conexion, $_POST['prioridad'] ?? 'media');
        $estado      = estadoPorFecha($_POST['fecha']);

        mysqli_query($conexion,
            "INSERT INTO actividades (usuario_id, titulo, descripcion, fecha, hora, estado, prioridad)
             VALUES ($usuario_id, '$titulo', '$descripcion', '$fecha', '$hora', '$estado', '$prioridad')"
        );
    }

    if ($_POST['accion'] === 'editar') {
        $id          = (int) $_POST['actividad_id'];
        $titulo      = trim(mysqli_real_escape_string($conexion, $_POST['titulo']));
        $descripcion = trim(mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? ''));
        $fecha       = mysqli_real_escape_string($conexion, $_POST['fecha']);
        $hora        = mysqli_real_escape_string($conexion, $_POST['hora']);
        $prioridad   = mysqli_real_escape_string($conexion, $_POST['prioridad'] ?? 'media');
        $estado      = estadoPorFecha($_POST['fecha']);

        mysqli_query($conexion,
            "UPDATE actividades SET titulo='$titulo', descripcion='$descripcion',
             fecha='$fecha', hora='$hora', prioridad='$prioridad', estado='$estado'
             WHERE id=$id AND usuario_id=$usuario_id"
        );
    }

    if ($_POST['accion'] === 'eliminar') {
        $id = (int) $_POST['actividad_id'];
        mysqli_query($conexion, "DELETE FROM actividades WHERE id=$id AND usuario_id=$usuario_id");
    }

    header("Location: actividades.php");
    exit();
}

function getActividades($conexion, $usuario_id, $estado) {
    $estado = mysqli_real_escape_string($conexion, $estado);
    $res = mysqli_query($conexion,
        "SELECT * FROM actividades
         WHERE usuario_id = $usuario_id AND estado = '$estado'
         ORDER BY fecha ASC, hora ASC"
    );
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row;
    }
    return $rows;
}

$pendientes = getActividades($conexion, $usuario_id, 'pendiente');
$proximas   = getActividades($conexion, $usuario_id, 'proxima');
$realizadas = getActividades($conexion, $usuario_id, 'realizada');

mysqli_close($conexion);

function prioridadClase($prioridad) {
    return [
        'alta'  => 'card-alta',
        'media' => 'card-media',
        'baja'  => 'card-baja',
    ][$prioridad] ?? 'card-media';
}

function prioridadIcono($prioridad) {
    return [
        'alta'  => 'ri-alarm-warning-fill',
        'media' => 'ri-flag-fill',
        'baja'  => 'ri-leaf-fill',
    ][$prioridad] ?? 'ri-flag-fill';
}

function renderTarjeta($act) {
    $titulo      = htmlspecialchars($act['titulo']);
    $descripcion = htmlspecialchars($act['descripcion'] ?? '');
    $fecha       = date('d M Y', strtotime($act['fecha']));
    $hora        = date('H:i', strtotime($act['hora']));
    $id          = (int) $act['id'];
    $estado      = $act['estado'];
    $fecha_raw   = $act['fecha'];
    $hora_raw    = substr($act['hora'], 0, 5);
    $prioridad   = $act['prioridad'];
    $cardClase   = prioridadClase($prioridad);
    $icono       = prioridadIcono($prioridad);
    $diasTexto   = '';

    if ($estado !== 'realizada') {
        $dias = diasRestantes($act['fecha']);
        if ($dias === 0)        $diasTexto = "Hoy";
        elseif ($dias === 1)    $diasTexto = "Mañana";
        elseif ($dias > 1)      $diasTexto = "En $dias días";
    } else {
        $diasTexto = "Completada";
    }

    $desc_html = $descripcion
        ? "<p class='ac-desc'>$descripcion</p>"
        : "";

    $titulo_js      = addslashes($titulo);
    $descripcion_js = addslashes($descripcion);
    $btn_realizada = '';
if ($estado !== 'realizada') {
    $btn_realizada = "
        <button class='ac-btn-done' onclick=\"marcarRealizada($id)\">
            <i class='ri-check-line'></i> Realizada
        </button>
    ";
}
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

            <!-- pendientes: quedan 0–7 días -->
            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Pendientes</h2>
                    <span class="count-badge count-pendiente"><?= count($pendientes) ?></span>
                </div>
                <div class="column-content" id="list-pending">
                    <?php if (empty($pendientes)): ?>
                        <div class="empty-state">
                            <i class="ri-inbox-line"></i>
                            <p>No hay tareas pendientes</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendientes as $act): echo renderTarjeta($act); endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- proximas : más de 7 días -->
            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Próximas</h2>
                    <span class="count-badge count-proxima"><?= count($proximas) ?></span>
                </div>
                <div class="column-content" id="list-upcoming">
                    <?php if (empty($proximas)): ?>
                        <div class="empty-state">
                            <i class="ri-calendar-event-line"></i>
                            <p>Sin eventos próximos</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($proximas as $act): echo renderTarjeta($act); endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- REALIZADAS -->
            <div class="activity-column">
                <div class="column-header">
                    <h2 class="column-title">Realizadas</h2>
                    <span class="count-badge count-realizada"><?= count($realizadas) ?></span>
                </div>
                <div class="column-content" id="list-done">
                    <?php if (empty($realizadas)): ?>
                        <div class="empty-state">
                            <i class="ri-checkbox-circle-line"></i>
                            <p>Aún no has completado nada</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($realizadas as $act): echo renderTarjeta($act); endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </section>
    </main>

    <!--Nuevo evento-->
    <div class="modal-overlay" id="eventModal">
        <div class="modal-card event-card">
            <div class="event-modal-header">
                <h2><i class="ri-add-circle-line"></i> Nuevo evento</h2>
                <button class="close-modal" id="closeEventModal"><i class="ri-close-circle-line"></i></button>
            </div>
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

    <!-- Editar evento-->
    <div class="modal-overlay" id="editEventModal">
        <div class="modal-card event-card">
            <div class="event-modal-header">
                <h2><i class="ri-edit-line"></i> Editar evento</h2>
                <button class="close-modal" id="closeEditModal"><i class="ri-close-circle-line"></i></button>
            </div>
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