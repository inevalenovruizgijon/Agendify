<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/editar-evento.css">
    <link rel="icon" type="image/png" href="../assets/img/logoFavicon.png">

</head>

<body>

    <!-- Modal de edición de evento -->
    <div class="modal-overlay" id="editEventModal">
        <div class="modal-card">

            <!-- Cabecera del modal con título y botón de cierre -->
            <header class="modal-header edit-mode">
                <h2>Editar evento</h2>
                <!-- El JS usa este ID para cerrar el modal -->
                <button class="close-modal-btn" id="closeEditModal">
                    <i class="ri-close-line"></i>
                </button>
            </header>

            <!-- Formulario de edición. El JS en editar-evento.js gestiona el envío -->
            <form class="event-form" id="editEventForm">

                <div class="input-group">
                    <label>Título *</label>
                    <!-- Valor de ejemplo; en producción el JS rellena este campo con los datos reales del evento -->
                    <input type="text" value="Reunión de equipo" required>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label>Fecha *</label>
                        <input type="date" value="2025-12-20" required>
                    </div>
                    <div class="input-group">
                        <label>Hora *</label>
                        <input type="time" value="10:00" required>
                    </div>
                </div>

                <!-- Fecha límite independiente de la fecha de inicio -->
                <div class="input-group">
                    <label>Fecha límite *</label>
                    <input type="date" value="2025-12-20" required>
                </div>

                <div class="input-group">
                    <label>Descripción</label>
                    <textarea rows="3">Demo producto nuevo</textarea>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn-save">Guardar Cambios</button>
                    <!-- type="button" evita que el botón Cancelar envíe el formulario accidentalmente -->
                    <button type="button" class="btn-cancel" id="btnCancelEdit">Cancelar</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Gestiona la apertura/cierre del modal y el envío del formulario de edición -->
    <script src="../js/editar-evento.js"></script>

</body>

</html>