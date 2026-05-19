<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda · Agendify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/ayuda.css">       
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <div class="logo-icon"><i class="fa-regular fa-calendar-check"></i></div>
            <div>
                <span class="brand-name">Agendify</span>
                <span class="brand-sub">Tu tiempo, bajo control</span>
            </div>
        </a>
        <a href="calendar.php" class="btn-nav"><i class="fa-solid fa-arrow-left" style="margin-right:8px;"></i>Volver a la app</a>
    </div>
</nav>

<!-- HERO -->
<section class="help-hero">
    <div class="help-hero-badge"><i class="fa-solid fa-circle-question"></i> Centro de Ayuda</div>
    <h1>¿En qué podemos <span>ayudarte?</span></h1>
    <p>Encuentra respuestas rápidas o sigue nuestras guías paso a paso.</p>
</section>

<!-- TABS -->
<div class="tabs-bar">
    <div class="tabs-inner">
        <button class="tab-btn active" data-tab="guia">
            <i class="fa-solid fa-book-open"></i> Guía de uso
        </button>
        <button class="tab-btn" data-tab="faq">
            <i class="fa-solid fa-circle-question"></i> Preguntas frecuentes
        </button>
    </div>
</div>

<!-- MAIN -->
<main class="help-main">

    <!-- ══ GUÍA ══ -->
    <div class="tab-panel active" id="tab-guia">
        <div class="section-heading">
            <h2>Guía de uso</h2>
            <p>Haz clic en cualquier sección para ver los pasos detallados.</p>
        </div>

        <div class="guide-grid">

            <!-- Registro -->
            <div class="guide-card" data-card>
                <div class="guide-card-header">
                    <div class="guide-icon"><i class="fa-solid fa-user-plus"></i></div>
                    <div>
                        <h3>Crear una cuenta</h3>
                        <p>Regístrate en Agendify gratis</p>
                    </div>
                    <i class="fa-solid fa-chevron-right guide-arrow"></i>
                </div>
                <div class="guide-steps">
                    <ul class="step-list">
                        <li class="step-item">
                            <div class="step-num">1</div>
                            <div class="step-text">
                                <strong>Haz clic en «Empezar Gratis»</strong>
                                <span>Botón en la esquina superior derecha de la página de inicio.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">2</div>
                            <div class="step-text">
                                <strong>Rellena el formulario</strong>
                                <span>Introduce tu nombre completo, email y una contraseña segura.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">3</div>
                            <div class="step-text">
                                <strong>Haz clic en «Registrarse»</strong>
                                <span>Tu cuenta se crea al instante y accedes al calendario.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="step-tip"><i class="fa-solid fa-lightbulb"></i> La contraseña debe tener al menos 8 caracteres con letras y números.</div>
                </div>
            </div>

            <!-- Login -->
            <div class="guide-card" data-card>
                <div class="guide-card-header">
                    <div class="guide-icon"><i class="fa-solid fa-right-to-bracket"></i></div>
                    <div>
                        <h3>Iniciar sesión</h3>
                        <p>Accede a tu cuenta de Agendify</p>
                    </div>
                    <i class="fa-solid fa-chevron-right guide-arrow"></i>
                </div>
                <div class="guide-steps">
                    <ul class="step-list">
                        <li class="step-item">
                            <div class="step-num">1</div>
                            <div class="step-text">
                                <strong>Haz clic en «Login»</strong>
                                <span>Botón en la esquina superior derecha de la página de inicio.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">2</div>
                            <div class="step-text">
                                <strong>Introduce tu email y contraseña</strong>
                                <span>Los mismos datos con los que te registraste.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">3</div>
                            <div class="step-text">
                                <strong>Haz clic en «Entrar a Agendify»</strong>
                                <span>Accederás directamente al calendario principal.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="step-tip"><i class="fa-solid fa-triangle-exclamation"></i> Si el email o la contraseña son incorrectos, aparecerá un aviso en rojo.</div>
                </div>
            </div>

            <!-- Crear evento -->
            <div class="guide-card" data-card>
                <div class="guide-card-header">
                    <div class="guide-icon"><i class="fa-solid fa-calendar-plus"></i></div>
                    <div>
                        <h3>Crear un evento</h3>
                        <p>Añade nuevas actividades a tu agenda</p>
                    </div>
                    <i class="fa-solid fa-chevron-right guide-arrow"></i>
                </div>
                <div class="guide-steps">
                    <ul class="step-list">
                        <li class="step-item">
                            <div class="step-num">1</div>
                            <div class="step-text">
                                <strong>Haz clic en «+ Nuevo evento»</strong>
                                <span>Disponible tanto en el Calendario como en Actividades.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">2</div>
                            <div class="step-text">
                                <strong>Introduce el título y la fecha</strong>
                                <span>El título es obligatorio. La fecha determina el estado del evento.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">3</div>
                            <div class="step-text">
                                <strong>Elige la prioridad</strong>
                                <span>Baja, Media o Alta. Por defecto se selecciona Media.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">4</div>
                            <div class="step-text">
                                <strong>Haz clic en «Crear Evento»</strong>
                                <span>El evento aparece en el calendario y en la columna correspondiente.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="step-tip"><i class="fa-solid fa-lightbulb"></i> Puedes añadir una descripción opcional con detalles adicionales.</div>
                </div>
            </div>

            <!-- Editar evento -->
            <div class="guide-card" data-card>
                <div class="guide-card-header">
                    <div class="guide-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                    <div>
                        <h3>Editar un evento</h3>
                        <p>Modifica los datos de una actividad</p>
                    </div>
                    <i class="fa-solid fa-chevron-right guide-arrow"></i>
                </div>
                <div class="guide-steps">
                    <ul class="step-list">
                        <li class="step-item">
                            <div class="step-num">1</div>
                            <div class="step-text">
                                <strong>Ve a la página de Actividades</strong>
                                <span>Localiza el evento que quieres modificar en su columna.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">2</div>
                            <div class="step-text">
                                <strong>Haz clic en «Editar»</strong>
                                <span>El botón con icono de lápiz está en la parte inferior de la tarjeta.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">3</div>
                            <div class="step-text">
                                <strong>Modifica los campos y guarda</strong>
                                <span>Haz clic en «Guardar cambios» para aplicar las modificaciones.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Marcar realizada -->
            <div class="guide-card" data-card>
                <div class="guide-card-header">
                    <div class="guide-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <h3>Marcar como realizada</h3>
                        <p>Completa una tarea antes de su fecha</p>
                    </div>
                    <i class="fa-solid fa-chevron-right guide-arrow"></i>
                </div>
                <div class="guide-steps">
                    <ul class="step-list">
                        <li class="step-item">
                            <div class="step-num">1</div>
                            <div class="step-text">
                                <strong>Localiza la actividad en Pendientes o Próximas</strong>
                                <span>El botón «Realizada» solo aparece si el evento aún no está completado.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">2</div>
                            <div class="step-text">
                                <strong>Haz clic en «✓ Realizada»</strong>
                                <span>Se pedirá confirmación antes de cambiar el estado.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">3</div>
                            <div class="step-text">
                                <strong>El evento se mueve a la columna Realizadas</strong>
                                <span>La página se recarga automáticamente.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Perfil -->
            <div class="guide-card" data-card>
                <div class="guide-card-header">
                    <div class="guide-icon"><i class="fa-solid fa-user-gear"></i></div>
                    <div>
                        <h3>Editar tu perfil</h3>
                        <p>Actualiza tus datos personales</p>
                    </div>
                    <i class="fa-solid fa-chevron-right guide-arrow"></i>
                </div>
                <div class="guide-steps">
                    <ul class="step-list">
                        <li class="step-item">
                            <div class="step-num">1</div>
                            <div class="step-text">
                                <strong>Ve a tu Perfil</strong>
                                <span>Accede desde el menú lateral de la aplicación.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">2</div>
                            <div class="step-text">
                                <strong>Haz clic en «Editar perfil»</strong>
                                <span>Se abre el formulario con tus datos actuales.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">3</div>
                            <div class="step-text">
                                <strong>Cambia lo que necesites y guarda</strong>
                                <span>Puedes cambiar nombre, email, biografía y foto de perfil.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="step-tip"><i class="fa-solid fa-lightbulb"></i> La foto acepta JPG, PNG, GIF y WEBP. Tamaño recomendado: 200×200 px.</div>
                </div>
            </div>

            <!-- Recordatorios -->
            <div class="guide-card" data-card>
                <div class="guide-card-header">
                    <div class="guide-icon"><i class="fa-solid fa-bell"></i></div>
                    <div>
                        <h3>Configurar recordatorios</h3>
                        <p>Recibe avisos por email antes de tus eventos</p>
                    </div>
                    <i class="fa-solid fa-chevron-right guide-arrow"></i>
                </div>
                <div class="guide-steps">
                    <ul class="step-list">
                        <li class="step-item">
                            <div class="step-num">1</div>
                            <div class="step-text">
                                <strong>Ve a tu Perfil</strong>
                                <span>Localiza la sección «Recordatorios por email».</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">2</div>
                            <div class="step-text">
                                <strong>Selecciona la antelación</strong>
                                <span>Elige entre 15 min, 30 min, 1 h, 2 h, 24 h o 2 días.</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">3</div>
                            <div class="step-text">
                                <strong>O usa «Personalizado»</strong>
                                <span>Introduce los minutos exactos (entre 5 y 10.080).</span>
                            </div>
                        </li>
                        <li class="step-item">
                            <div class="step-num">4</div>
                            <div class="step-text">
                                <strong>Haz clic en «Guardar preferencia»</strong>
                                <span>Se aplicará a todos tus eventos futuros.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="step-tip"><i class="fa-solid fa-lightbulb"></i> 10.080 minutos = 7 días. Es el máximo de antelación configurado.</div>
                </div>
            </div>

        </div>

        <div class="contact-banner">
            <div>
                <h3>¿No encuentras lo que buscas?</h3>
                <p>Nuestro equipo de soporte te responde por email.</p>
            </div>
            <a href="mailto:soporte@agendify.com"><i class="fa-solid fa-envelope" style="margin-right:8px;"></i>Contactar soporte</a>
        </div>
    </div>

    <div class="tab-panel" id="tab-faq">
        <div class="section-heading">
            <h2>Preguntas frecuentes</h2>
            <p>Respuestas rápidas a las dudas más comunes.</p>
        </div>

        <div class="faq-filters">
            <button class="faq-filter active" data-filter="all">Todas</button>
            <button class="faq-filter" data-filter="cuenta">Cuenta</button>
            <button class="faq-filter" data-filter="eventos">Eventos</button>
            <button class="faq-filter" data-filter="perfil">Perfil</button>
            <button class="faq-filter" data-filter="recordatorios">Recordatorios</button>
        </div>

        <div class="faq-list" id="faqList">

            <div class="faq-item" data-category="cuenta" data-faq>
                <div class="faq-question">
                    <div class="faq-q-icon">?</div>
                    <h4>¿Puedo usar Agendify desde el móvil?</h4>
                    <i class="fa-solid fa-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">Sí. Agendify está diseñado con un diseño responsive que se adapta a pantallas de cualquier tamaño, incluyendo smartphones y tablets. Solo necesitas un navegador actualizado.</div>
            </div>

            <div class="faq-item" data-category="cuenta" data-faq>
                <div class="faq-question">
                    <div class="faq-q-icon">?</div>
                    <h4>¿Puedo tener más de una cuenta con el mismo email?</h4>
                    <i class="fa-solid fa-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">No. Cada dirección de email solo puede estar asociada a una cuenta. Si intentas registrarte con un email ya existente, el sistema mostrará un mensaje de error.</div>
            </div>

            <div class="faq-item" data-category="cuenta" data-faq>
                <div class="faq-question">
                    <div class="faq-q-icon">?</div>
                    <h4>He olvidado mi contraseña, ¿qué hago?</h4>
                    <i class="fa-solid fa-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">Actualmente Agendify no dispone de un sistema de recuperación de contraseña automático. Contacta con el equipo de soporte en soporte@agendify.com para que te ayuden a restablecer el acceso.</div>
            </div>

            <div class="faq-item" data-category="eventos" data-faq>
                <div class="faq-question">
                    <div class="faq-q-icon">?</div>
                    <h4>¿Qué pasa si la fecha de un evento pasa sin marcarlo como realizado?</h4>
                    <i class="fa-solid fa-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">El sistema cambia automáticamente el estado del evento a «Realizada» cuando su fecha ya ha transcurrido. Aparecerá en la columna Realizadas sin que tengas que hacer nada.</div>
            </div>

            <div class="faq-item" data-category="eventos" data-faq>
                <div class="faq-question">
                    <div class="faq-q-icon">?</div>
                    <h4>¿Puedo eliminar solo algunas actividades?</h4>
                    <i class="fa-solid fa-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">Sí. Cada tarjeta de actividad tiene su propio botón «Eliminar». Solo se borrará esa actividad concreta; el resto permanecerán intactas. Ten en cuenta que esta acción es irreversible.</div>
            </div>

            <div class="faq-item" data-category="perfil" data-faq>
                <div class="faq-question">
                    <div class="faq-q-icon">?</div>
                    <h4>¿Puedo cambiar el email de mi cuenta?</h4>
                    <i class="fa-solid fa-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">Sí. Desde la página de Perfil, en el formulario de edición, puedes cambiar tu dirección de email. El sistema comprobará que el nuevo email no esté en uso por otra cuenta.</div>
            </div>

            <div class="faq-item" data-category="perfil" data-faq>
                <div class="faq-question">
                    <div class="faq-q-icon">?</div>
                    <h4>¿En qué formatos puedo subir mi foto de perfil?</h4>
                    <i class="fa-solid fa-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">Se aceptan los formatos JPG, JPEG, PNG, GIF y WEBP. El tamaño recomendado es 200×200 píxeles o similar con proporción cuadrada.</div>
            </div>

            <div class="faq-item" data-category="recordatorios" data-faq>
                <div class="faq-question">
                    <div class="faq-q-icon">?</div>
                    <h4>¿Cómo funcionan los recordatorios por email?</h4>
                    <i class="fa-solid fa-chevron-down faq-chevron"></i>
                </div>
                <div class="faq-answer">Una vez configurada tu preferencia en el Perfil, el sistema enviará automáticamente un email a tu dirección registrada con la antelación indicada antes de cada evento. Puedes elegir entre opciones predefinidas o un tiempo personalizado.</div>
            </div>

        </div>

        <div class="no-results" id="noResults">
            <i class="fa-solid fa-magnifying-glass"></i>
            <p>No se encontraron preguntas con ese filtro o búsqueda.</p>
        </div>

        <div class="contact-banner">
            <div>
                <h3>¿Tienes otra pregunta?</h3>
                <p>Escríbenos y te respondemos lo antes posible.</p>
            </div>
            <a href="mailto:agendify.es@gmail.com"><i class="fa-solid fa-envelope" style="margin-right:8px;"></i>agendify.es@gmail.com</a>
        </div>
    </div>

</main>

<footer class="footer-pro">
    <div class="footer-content">
        <a href="index.php" class="f-logo">
            <i class="fa-regular fa-calendar-check"></i>
            Agendify
        </a>
        <div class="footer-links">
            <a href="ayuda.php" class="active-link">Ayuda</a>
        </div>
        <div class="social-pills">
            <a href="#" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" title="Twitter/X"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" title="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
        </div>
    </div>
</footer>

<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
        });
    });

    document.querySelectorAll('[data-card]').forEach(card => {
        card.querySelector('.guide-card-header').addEventListener('click', () => {
            card.classList.toggle('open');
        });
    });

    document.querySelectorAll('[data-faq]').forEach(item => {
        item.querySelector('.faq-question').addEventListener('click', () => {
            item.classList.toggle('open');
        });
    });

    document.querySelectorAll('.faq-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.faq-filter').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterFaq();
        });
    });

    function filterFaq() {
        const category = document.querySelector('.faq-filter.active').dataset.filter;
        let visible = 0;

        document.querySelectorAll('[data-faq]').forEach(item => {
            const show = category === 'all' || item.dataset.category === category;
            item.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('noResults').style.display = visible === 0 ? 'block' : 'none';
    }
</script>

</body>
</html>