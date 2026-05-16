<?php
session_start();

// Redirigir si no hay sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../frontend/html/index.php");
    exit();
}

// Conexión a la base de datos
require_once '../../backend/config/conexion.php';

$usuario_id = $_SESSION['usuario_id'];

$mensaje_exito = '';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    if ($_POST['accion'] === 'editar') {
        $nombre = trim(mysqli_real_escape_string($conexion, $_POST['nombre']));
        $email  = trim(mysqli_real_escape_string($conexion, $_POST['email']));
        $bio    = trim(mysqli_real_escape_string($conexion, $_POST['bio']));

        // Validar que el email no esté en uso por otro usuario
        $check = mysqli_query($conexion, "SELECT id FROM usuarios WHERE email = '$email' AND id != $usuario_id");
        if (mysqli_num_rows($check) > 0) {
            $mensaje_error = 'Ese email ya está en uso por otra cuenta.';
        } else {
            mysqli_query($conexion, "UPDATE usuarios SET nombre = '$nombre', email = '$email' WHERE id = $usuario_id");
            $_SESSION['usuario_nombre'] = $nombre;
            $mensaje_exito = 'Perfil actualizado correctamente.';
        }
    }

    if ($_POST['accion'] === 'eliminar') {
        mysqli_query($conexion, "DELETE FROM usuarios WHERE id = $usuario_id");
        session_destroy();
        header("Location: ../../frontend/html/index.php?eliminado=1");
        exit();
    }
}

// ── Obtener datos del usuario desde la BD ───────────────────────────────────
$res     = mysqli_query($conexion, "SELECT nombre, email, foto_perfil, fecha_registro FROM usuarios WHERE id = $usuario_id");
$usuario = mysqli_fetch_assoc($res);

// ── Estadísticas de actividades ─────────────────────────────────────────────
$total_res = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM actividades WHERE usuario_id = $usuario_id");
$total     = mysqli_fetch_assoc($total_res)['total'];

$mes_res  = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM actividades WHERE usuario_id = $usuario_id AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
$este_mes = mysqli_fetch_assoc($mes_res)['total'];

$hoy_res = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM actividades WHERE usuario_id = $usuario_id AND fecha = CURDATE()");
$hoy     = mysqli_fetch_assoc($hoy_res)['total'];

// ── Iniciales para el avatar ─────────────────────────────────────────────────
$palabras  = explode(' ', trim($usuario['nombre']));
$iniciales = strtoupper(substr($palabras[0], 0, 1) . (isset($palabras[1]) ? substr($palabras[1], 0, 1) : ''));

// ── Año de registro ──────────────────────────────────────────────────────────
$anio_registro = date('Y', strtotime($usuario['fecha_registro']));

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil – Agendify</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>
<div class="dashboard-container">

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
            <a href="actividades.php" class="nav-item ">
                <i class="ri-list-check"></i> <span>Actividades</span>
            </a>
            <a href="profile.php" class="nav-item active">
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

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <header class="content-header">
            <h1>Mi perfil</h1>
        </header>

        <?php if ($mensaje_exito): ?>
            <div class="alert alert-success"><i class="ri-checkbox-circle-line"></i> <?= htmlspecialchars($mensaje_exito) ?></div>
        <?php endif; ?>
        <?php if ($mensaje_error): ?>
            <div class="alert alert-error"><i class="ri-error-warning-line"></i> <?= htmlspecialchars($mensaje_error) ?></div>
        <?php endif; ?>

        <section class="profile-card">

            <!-- Cabecera -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if ($usuario['foto_perfil'] && $usuario['foto_perfil'] !== 'default-profile.png'): ?>
                        <img src="../assets/img/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Avatar">
                    <?php else: ?>
                        <div class="avatar-initials"><?= $iniciales ?></div>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
                    <p class="profile-email"><i class="ri-mail-line"></i> <?= htmlspecialchars($usuario['email']) ?></p>
                    <p class="profile-since"><i class="ri-calendar-line"></i> Usuario desde <?= $anio_registro ?></p>
                </div>
            </div>

            <div class="profile-body" id="viewMode">
                <div class="info-group">
                    <label>Biografía</label>
                    <div class="info-box">
                        Usuario de Agendify desde <?= $anio_registro ?>
                    </div>
                </div>

                <div class="stats-container">
                    <label>Estadísticas</label>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3><?= $total ?></h3>
                            <p>Eventos totales</p>
                        </div>
                        <div class="stat-card">
                            <h3><?= $este_mes ?></h3>
                            <p>Este mes</p>
                        </div>
                        <div class="stat-card">
                            <h3><?= $hoy ?></h3>
                            <p>Hoy</p>
                        </div>
                    </div>
                </div>

                <div class="profile-actions">
                    <button class="btn-edit" onclick="abrirEdicion()">Editar perfil</button>
                    <button class="btn-delete" onclick="abrirModal()">Eliminar cuenta</button>
                </div>
            </div>

            <!-- Cuerpo: Formulario de edición -->
            <div class="profile-body" id="editMode" style="display:none;">
                <form method="POST" action="profile.php">
                    <input type="hidden" name="accion" value="editar">

                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre"
                               value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email"
                               value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="bio">Biografía</label>
                        <textarea id="bio" name="bio" rows="3">Usuario de Agendify desde <?= $anio_registro ?></textarea>
                    </div>

                    <div class="profile-actions">
                        <button type="submit" class="btn-edit">Guardar cambios</button>
                        <button type="button" class="btn-delete" onclick="cerrarEdicion()">Cancelar</button>
                    </div>
                </form>
            </div>

        </section>
    </main>
</div>

<!--eliminar cuenta -->
<div class="modal-overlay" id="modalEliminar">
    <div class="modal-box">
        <h3><i class="ri-error-warning-line"></i> ¿Eliminar cuenta?</h3>
        <p>Esta acción es <strong>irreversible</strong>. Se eliminarán todos tus datos y eventos de Agendify.</p>
        <form method="POST" action="profile.php">
            <input type="hidden" name="accion" value="eliminar">
            <div class="modal-actions">
                <button type="button" class="btn-edit" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-confirm-delete">Sí, eliminar mi cuenta</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirEdicion() {
        document.getElementById('viewMode').style.display = 'none';
        document.getElementById('editMode').style.display = 'block';
    }
    function cerrarEdicion() {
        document.getElementById('editMode').style.display = 'none';
        document.getElementById('viewMode').style.display = 'block';
    }
    function abrirModal() {
        document.getElementById('modalEliminar').style.display = 'flex';
    }
    function cerrarModal() {
        document.getElementById('modalEliminar').style.display = 'none';
    }
    document.getElementById('modalEliminar').addEventListener('click', function(e) {
        if (e.target === this) cerrarModal();
    });
</script>

</body>
</html>