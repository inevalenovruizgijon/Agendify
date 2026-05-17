<?php
session_start(); // Inicia la sesión para acceder a los datos del usuario

// Si el usuario no ha iniciado sesión, lo redirige al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../frontend/html/index.php");
    exit();
}

require_once '../../backend/config/conexion.php'; // Carga la conexión a la base de datos

$usuario_id = $_SESSION['usuario_id']; // Guarda el ID del usuario en sesión

$mensaje_exito = '';
$mensaje_error = '';

// Procesa el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    // EDITAR perfil del usuario
    if ($_POST['accion'] === 'editar') {
        // Limpia y escapa los campos de texto para evitar inyección SQL
        $nombre = trim(mysqli_real_escape_string($conexion, $_POST['nombre']));
        $email  = trim(mysqli_real_escape_string($conexion, $_POST['email']));
        $bio    = trim(mysqli_real_escape_string($conexion, $_POST['bio']));

        // Comprueba que el email no esté siendo usado por otro usuario
        $check = mysqli_query($conexion, "SELECT id FROM usuarios WHERE email = '$email' AND id != $usuario_id");
        if (mysqli_num_rows($check) > 0) {
            $mensaje_error = 'Ese email ya está en uso por otra cuenta.';
        } else {

            $sql_foto_perfil = ""; // Fragmento SQL para actualizar la foto, vacío si no se sube ninguna

            // Procesa la foto de perfil solo si se ha subido un archivo sin errores
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $file_tmp  = $_FILES['foto_perfil']['tmp_name'];
                $file_name = $_FILES['foto_perfil']['name'];
                $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // Obtiene la extensión en minúsculas

                $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_ext, $extensiones_permitidas)) {
                    // Ruta absoluta a la carpeta donde se guardan las imágenes de perfil
                    $carpeta_assets = dirname(__DIR__, 1) . "/assets/img/";

                    // Crea la carpeta si no existe
                    if (!file_exists($carpeta_assets)) {
                        mkdir($carpeta_assets, 0777, true);
                    }

                    // Nombre único para el archivo: user_{id}_{timestamp}.{ext}
                    $nuevo_nombre_archivo = "user_" . $usuario_id . "_" . time() . "." . $file_ext;
                    $directorio_destino   = $carpeta_assets . $nuevo_nombre_archivo;

                    // Mueve el archivo temporal al destino final
                    if (move_uploaded_file($file_tmp, $directorio_destino)) {
                        $sql_foto_perfil = ", foto_perfil = '$nuevo_nombre_archivo'"; // Añade la foto al UPDATE
                    } else {
                        $mensaje_error = 'Hubo un error al guardar la imagen en el servidor.';
                    }
                } else {
                    $mensaje_error = 'Formato de imagen no válido. Usa JPG, PNG, GIF o WEBP.';
                }
            }

            // Solo actualiza si no hubo errores con la imagen
            if (empty($mensaje_error)) {
                $query_update = "UPDATE usuarios SET nombre = '$nombre', email = '$email' $sql_foto_perfil WHERE id = $usuario_id";

                if (mysqli_query($conexion, $query_update)) {
                    $_SESSION['usuario_nombre'] = $nombre; // Actualiza el nombre en sesión
                    $mensaje_exito = 'Perfil actualizado correctamente.';

                    // Si se subió foto nueva, actualiza también la sesión
                    if (!empty($sql_foto_perfil)) {
                        $_SESSION['usuario_foto'] = $nuevo_nombre_archivo;
                    }
                } else {
                    $mensaje_error = 'Error al actualizar los datos en la base de datos.';
                }
            }
        }
    }

    // GUARDAR preferencia de avisos por email
    if ($_POST['accion'] === 'avisos') {
        $minutos_raw = (int) $_POST['aviso_minutos'];

        // Si eligió "personalizado" (value=0), usa el campo de texto adicional
        if ($minutos_raw === 0 && isset($_POST['aviso_personalizado'])) {
            $minutos_raw = (int) $_POST['aviso_personalizado'];
        }

        // Limita el valor entre 5 minutos y 7 días (10080 minutos)
        $minutos = max(5, min($minutos_raw, 10080));
        mysqli_query($conexion, "UPDATE usuarios SET aviso_minutos = $minutos WHERE id = $usuario_id");
        $mensaje_exito = 'Preferencia de avisos guardada.';
    }

    // ELIMINAR cuenta del usuario
    if ($_POST['accion'] === 'eliminar') {
        mysqli_query($conexion, "DELETE FROM usuarios WHERE id = $usuario_id");
        session_destroy(); // Destruye la sesión tras eliminar la cuenta
        header("Location: ../../public/views/index.php?eliminado=1");
        exit();
    }
}

// Obtiene los datos del usuario desde la base de datos
$res     = mysqli_query($conexion, "SELECT nombre, email, foto_perfil, fecha_registro, aviso_minutos FROM usuarios WHERE id = $usuario_id");
$usuario = mysqli_fetch_assoc($res);

// Cuenta el total de actividades del usuario
$total_res = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM actividades WHERE usuario_id = $usuario_id");
$total     = mysqli_fetch_assoc($total_res)['total'];

// Cuenta las actividades del mes actual
$mes_res  = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM actividades WHERE usuario_id = $usuario_id AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
$este_mes = mysqli_fetch_assoc($mes_res)['total'];

// Cuenta las actividades de hoy
$hoy_res = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM actividades WHERE usuario_id = $usuario_id AND fecha = CURDATE()");
$hoy     = mysqli_fetch_assoc($hoy_res)['total'];

// Genera las iniciales del nombre para el avatar (ej: "Juan Pérez" → "JP")
$palabras  = explode(' ', trim($usuario['nombre']));
$iniciales = strtoupper(substr($palabras[0], 0, 1) . (isset($palabras[1]) ? substr($palabras[1], 0, 1) : ''));

// Extrae el año de registro para mostrarlo en el perfil
$anio_registro = date('Y', strtotime($usuario['fecha_registro']));

// Opciones predefinidas de aviso (en minutos)
$aviso_actual = (int) ($usuario['aviso_minutos'] ?? 60);
$opciones_aviso = [15, 30, 60, 120, 1440, 2880];
// Determina si el valor guardado es personalizado (no está en las opciones predefinidas)
$es_personalizado = !in_array($aviso_actual, $opciones_aviso);

mysqli_close($conexion); // Cierra la conexión a la base de datos
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
    <link rel="icon" type="image/png" href="../assets/img/logoFavicon.png">
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
                <a href="actividades.php" class="nav-item">
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

        <main class="main-content">
            <header class="content-header">
                <h1>Mi perfil</h1>
                <a href="../../backend/auth/logout.php" class="mobile-logout-btn">
                    <i class="ri-logout-box-line"></i>
                </a>
            </header>

            <?php /* Muestra el mensaje de éxito si la operación se completó correctamente */ ?>
            <?php if ($mensaje_exito): ?>
                <div class="alert alert-success"><i class="ri-checkbox-circle-line"></i> <?= htmlspecialchars($mensaje_exito) ?></div>
            <?php endif; ?>
            <?php /* Muestra el mensaje de error si algo falló */ ?>
            <?php if ($mensaje_error): ?>
                <div class="alert alert-error"><i class="ri-error-warning-line"></i> <?= htmlspecialchars($mensaje_error) ?></div>
            <?php endif; ?>

            <section class="profile-card">

                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php /* Si tiene foto personalizada la muestra; si no, muestra las iniciales */ ?>
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

                <!-- Vista de solo lectura del perfil -->
                <div class="profile-body" id="viewMode">
                    <div class="info-group">
                        <label>Biografía</label>
                        <div class="info-box">
                            Usuario de Agendify desde <?= $anio_registro ?>
                        </div>
                    </div>

                    <!-- Estadísticas de actividades del usuario -->
                    <div class="stats-container">
                        <label>Estadísticas</label>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <h3><?= $total ?></h3>        <!-- Total histórico -->
                                <p>Eventos totales</p>
                            </div>
                            <div class="stat-card">
                                <h3><?= $este_mes ?></h3>     <!-- Solo el mes actual -->
                                <p>Este mes</p>
                            </div>
                            <div class="stat-card">
                                <h3><?= $hoy ?></h3>          <!-- Solo hoy -->
                                <p>Hoy</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de preferencia de recordatorios por email -->
                    <div class="avisos-section">
                        <label>Recordatorios por email</label>
                        <form method="POST" action="profile.php" class="avisos-form">
                            <input type="hidden" name="accion" value="avisos">
                            <div class="avisos-opciones">

                                <?php /* Cada opción marca 'activa' si coincide con el valor guardado en BD */ ?>
                                <label class="aviso-opcion <?= (!$es_personalizado && $aviso_actual === 15)   ? 'activa' : '' ?>">
                                    <input type="radio" name="aviso_minutos" value="15"
                                        <?= (!$es_personalizado && $aviso_actual === 15) ? 'checked' : '' ?>
                                        onchange="togglePersonalizado(this.value)">
                                    <span><i class="ri-time-line"></i> 15 min antes</span>
                                </label>
                                <label class="aviso-opcion <?= (!$es_personalizado && $aviso_actual === 30)   ? 'activa' : '' ?>">
                                    <input type="radio" name="aviso_minutos" value="30"
                                        <?= (!$es_personalizado && $aviso_actual === 30) ? 'checked' : '' ?>
                                        onchange="togglePersonalizado(this.value)">
                                    <span><i class="ri-time-line"></i> 30 min antes</span>
                                </label>
                                <label class="aviso-opcion <?= (!$es_personalizado && $aviso_actual === 60)   ? 'activa' : '' ?>">
                                    <input type="radio" name="aviso_minutos" value="60"
                                        <?= (!$es_personalizado && $aviso_actual === 60) ? 'checked' : '' ?>
                                        onchange="togglePersonalizado(this.value)">
                                    <span><i class="ri-time-line"></i> 1 hora antes</span>
                                </label>
                                <label class="aviso-opcion <?= (!$es_personalizado && $aviso_actual === 120)  ? 'activa' : '' ?>">
                                    <input type="radio" name="aviso_minutos" value="120"
                                        <?= (!$es_personalizado && $aviso_actual === 120) ? 'checked' : '' ?>
                                        onchange="togglePersonalizado(this.value)">
                                    <span><i class="ri-time-line"></i> 2 horas antes</span>
                                </label>
                                <label class="aviso-opcion <?= (!$es_personalizado && $aviso_actual === 1440) ? 'activa' : '' ?>">
                                    <input type="radio" name="aviso_minutos" value="1440"
                                        <?= (!$es_personalizado && $aviso_actual === 1440) ? 'checked' : '' ?>
                                        onchange="togglePersonalizado(this.value)">
                                    <span><i class="ri-time-line"></i> 24 horas antes</span>
                                </label>
                                <label class="aviso-opcion <?= (!$es_personalizado && $aviso_actual === 2880) ? 'activa' : '' ?>">
                                    <input type="radio" name="aviso_minutos" value="2880"
                                        <?= (!$es_personalizado && $aviso_actual === 2880) ? 'checked' : '' ?>
                                        onchange="togglePersonalizado(this.value)">
                                    <span><i class="ri-time-line"></i> 2 días antes</span>
                                </label>
                                <?php /* Opción "Personalizado": value=0 activa el campo de texto adicional via JS */ ?>
                                <label class="aviso-opcion <?= $es_personalizado ? 'activa' : '' ?>">
                                    <input type="radio" name="aviso_minutos" value="0"
                                        <?= $es_personalizado ? 'checked' : '' ?>
                                        onchange="togglePersonalizado(this.value)">
                                    <span><i class="ri-edit-line"></i> Personalizado</span>
                                </label>
                            </div>

                            <?php /* Campo de minutos personalizados: visible solo si el valor guardado no es predefinido */ ?>
                            <div class="aviso-personalizado" id="campoPersonalizado" style="display:<?= $es_personalizado ? 'flex' : 'none' ?>">
                                <input type="number" name="aviso_personalizado" id="aviso_personalizado"
                                    min="5" max="10080"
                                    placeholder="Minutos antes del evento"
                                    value="<?= $es_personalizado ? $aviso_actual : '' ?>">
                                <span class="aviso-hint">Entre 5 minutos y 7 días</span>
                            </div>

                            <button type="submit" class="btn-avisos">Guardar preferencia</button>
                        </form>

                        <?php /* Muestra en texto legible cuánto tiempo antes recibirá el aviso */ ?>
                        <?php if ($aviso_actual > 0): ?>
                        <p class="aviso-resumen">
                            Recibirás avisos
                            <strong>
                                <?php
                                // Convierte minutos a días, horas o minutos según la magnitud
                                if ($aviso_actual >= 1440) echo ($aviso_actual / 1440) . ' día(s)';
                                elseif ($aviso_actual >= 60) echo ($aviso_actual / 60) . ' hora(s)';
                                else echo $aviso_actual . ' minutos';
                                ?>
                            </strong>
                            antes de cada evento.
                        </p>
                        <?php endif; ?>
                    </div>

                    <div class="profile-actions">
                        <!-- Llama a abrirEdicion() para ocultar viewMode y mostrar editMode -->
                        <button class="btn-edit" onclick="abrirEdicion()">Editar perfil</button>
                        <!-- Llama a abrirModal() para mostrar el modal de confirmación de borrado -->
                        <button class="btn-delete" onclick="abrirModal()">Eliminar cuenta</button>
                    </div>
                </div>

                <!-- Formulario de edición del perfil (oculto por defecto, JS lo muestra) -->
                <div class="profile-body" id="editMode" style="display:none;">
                    <!-- enctype necesario para poder subir archivos (foto de perfil) -->
                    <form method="POST" action="profile.php" enctype="multipart/form-data">
                        <input type="hidden" name="accion" value="editar">

                        <div class="form-group">
                            <label for="foto_perfil">Foto de perfil</label>
                            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label for="nombre">Nombre completo</label>
                            <!-- Precarga el valor actual del usuario -->
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
                            <!-- Cancela la edición volviendo a viewMode sin recargar la página -->
                            <button type="button" class="btn-delete" onclick="cerrarEdicion()">Cancelar</button>
                        </div>
                    </form>
                </div>

            </section>
        </main>
    </div>

    <!-- Modal de confirmación para eliminar la cuenta -->
    <div class="modal-overlay" id="modalEliminar">
        <div class="modal-box">
            <h3><i class="ri-error-warning-line"></i> ¿Eliminar cuenta?</h3>
            <p>Esta acción es <strong>irreversible</strong>. Se eliminarán todos tus datos y eventos de Agendify.</p>
            <!-- El campo oculto indica al POST que ejecute la lógica de eliminar -->
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
        // Muestra el formulario de edición y oculta la vista de solo lectura
        function abrirEdicion() {
            document.getElementById('viewMode').style.display = 'none';
            document.getElementById('editMode').style.display = 'block';
        }

        // Vuelve a la vista de solo lectura y oculta el formulario de edición
        function cerrarEdicion() {
            document.getElementById('editMode').style.display = 'none';
            document.getElementById('viewMode').style.display = 'block';
        }

        // Muestra el modal de confirmación de borrado de cuenta
        function abrirModal() {
            document.getElementById('modalEliminar').style.display = 'flex';
        }

        // Oculta el modal de confirmación de borrado
        function cerrarModal() {
            document.getElementById('modalEliminar').style.display = 'none';
        }

        // Cierra el modal al hacer clic fuera del contenido (sobre el overlay)
        document.getElementById('modalEliminar').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });

        // Muestra u oculta el campo de minutos personalizados según la opción seleccionada
        function togglePersonalizado(valor) {
            var campo = document.getElementById('campoPersonalizado');
            var input = document.getElementById('aviso_personalizado');

            // Quita la clase 'activa' de todas las opciones
            document.querySelectorAll('.aviso-opcion').forEach(function(el) {
                el.classList.remove('activa');
            });

            // Marca como activa la opción seleccionada
            var radio = document.querySelector('.aviso-opcion input[value="' + valor + '"]');
            if (radio) {
                radio.closest('.aviso-opcion').classList.add('activa');
            }

            // Si eligió "personalizado" (value=0), muestra el campo y lo hace obligatorio
            if (valor === '0') {
                campo.style.display = 'flex';
                input.required = true;
                input.focus();
            } else {
                // Para cualquier otra opción, oculta el campo y lo hace opcional
                campo.style.display = 'none';
                input.required = false;
            }
        }
    </script>

</body>
</html>