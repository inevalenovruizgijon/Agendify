<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendify | Tu tiempo, bajo control</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" type="image/png" href="../assets/img/logoFavicon.png">

</head>
<body>

    <!-- Barra de navegación principal -->
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo con icono y nombre de la marca -->
            <a href="#" class="logo">
                <div class="logo-icon"><i class="ri-calendar-check-fill"></i></div>
                <div class="logo-text">
                    <span class="brand-name">Agendify</span>
                    <span class="brand-sub">Agencia Digital Moderna</span>
                </div>
            </a>
            <!-- Botones de acceso: abren el modal de login/registro via script.js -->
            <div class="nav-auth">
                <a href="#" class="link-login" id="navLogin">Login</a>
                <a href="#" class="btn-nav" id="navRegister">Empezar Gratis</a>
            </div>
        </div>
    </nav>

    <!-- Sección principal (hero) con slogan, imágenes y CTA -->
    <main class="hero-section">

        <!-- Bloque de texto con el slogan principal y el secundario -->
        <div class="slogan-container">
            <h1 class="main-slogan">
                Ordena tu <span class="text-focus">mundo</span>,<br>
                <span class="text-accent">acelera tu meta.</span>
            </h1>
            
            <div class="sub-slogan-wrapper">
                <h2 class="sub-slogan">Domina tu tiempo con Agendify</h2>
                <div class="floating-tag">Y así conquistarás tu vida</div>
            </div>
        </div>

        <!-- Grid de imágenes decorativas de fondo -->
        <div class="image-grid">
            <div class="img-card card-1">
                <img src="https://images.pexels.com/photos/5124870/pexels-photo-5124870.jpeg" alt="Work">
            </div>
            <div class="img-card card-2">
                <img src="https://images.pexels.com/photos/4050216/pexels-photo-4050216.jpeg" alt="Meeting">
            </div>
            <div class="img-card card-3">
                <img src="https://images.pexels.com/photos/11333728/pexels-photo-11333728.jpeg" alt="Planning">
            </div>
        </div>

        <!-- Botón de llamada a la acción principal -->
        <div class="cta-container">
            <button class="btn-primary-pro">
                Explorar Agendify <i class="ri-arrow-right-up-line"></i>
            </button>
        </div>
    </main>

    <!-- Pie de página con logo, copyright y redes sociales -->
    <footer class="footer-pro">
        <div class="footer-content">
            <div class="footer-left">
                <div class="f-logo"><i class="ri-calendar-check-fill"></i> Agendify</div>
                <p>© 2026 Crafted for productivity.</p>
            </div>
            <div class="footer-right">
                <!-- Enlaces a redes sociales -->
                <div class="social-pills">
                    <a href="#"><i class="ri-instagram-fill"></i></a>
                    <a href="#"><i class="ri-twitter-x-fill"></i></a>
                    <a href="#"><i class="ri-linkedin-box-fill"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal compartido para Login y Registro.
         script.js alterna entre ambos modos cambiando el action del form,
         los textos y ocultando/mostrando el campo de nombre -->
    <div class="modal-overlay" id="authModal">
        <div class="modal-card">
            <!-- Botón para cerrar el modal -->
            <button class="close-modal" id="closeAuth"><i class="ri-close-line"></i></button>
            
            <div class="modal-body">
                <!-- Cabecera del modal: título y subtítulo cambian según el modo (login/registro) -->
                <div class="modal-header-pro">
                    <i class="ri-calendar-check-fill"></i>
                    <h2 id="modalTitle">Bienvenido de <span>nuevo</span></h2>
                    <p id="modalSubtitle">Ingresa tus datos para continuar</p>
                </div>

                <!-- Alerta de error/éxito: oculta por defecto, script.js la muestra si hay mensajes -->
                <div id="authAlert" class="alert-container d-none">
                    <i class="ri-error-warning-line"></i>
                    <span id="alertText"></span>
                </div>

                <!-- Formulario de autenticación.
                     En modo login apunta a login.php; script.js cambia el action a register.php en modo registro -->
                <form class="modal-form" id="authForm" method="POST" action="../../backend/auth/login.php">
                    
                    <!-- Campo de nombre: solo visible en modo registro (script.js quita la clase d-none) -->
                    <div class="input-field d-none" id="groupName">
                        <label>Nombre Completo</label>
                        <div class="input-inner">
                            <i class="ri-user-3-line"></i>
                            <input type="text" name="nombre" placeholder="Tu nombre">
                        </div>
                    </div>

                    <!-- Campo de email: visible siempre en ambos modos -->
                    <div class="input-field">
                        <label>Email</label>
                        <div class="input-inner">
                            <i class="ri-mail-line"></i>
                            <input type="email" name="email" placeholder="tu@ejemplo.com" required>
                        </div>
                    </div>

                    <!-- Campo de contraseña: visible siempre en ambos modos -->
                    <div class="input-field">
                        <label>Contraseña</label>
                        <div class="input-inner">
                            <i class="ri-lock-2-line"></i>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- Botón de envío: su texto cambia entre "Entrar" y "Registrarse" según el modo -->
                    <button type="submit" class="btn-submit-modal" id="btnSubmit">Entrar a Agendify</button>
                </form>
                
                <!-- Enlace para alternar entre login y registro -->
                <div class="modal-footer-text">
                    <span id="footerQuestion">¿No tienes cuenta?</span> 
                    <a href="#" id="toggleAuth">Regístrate</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestiona la apertura/cierre del modal y el cambio entre modo login y registro -->
    <script src="../js/script.js"></script>
    
</body>
</html>