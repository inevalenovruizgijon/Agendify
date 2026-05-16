<?php
// include_once '../config/conexion.php';
require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $password = $_POST['password'];
    $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

    //VERIFICAR SI EL EMAIL YA EXISTE
    $checkEmail = "SELECT email FROM usuarios WHERE email = '$email'";
    $resCheck = mysqli_query($conexion, $checkEmail);

    if (mysqli_num_rows($resCheck) > 0) {
        header("Location: ../../frontend/html/index.php?error=exists");
        exit();
    }

    //PROCESAR REGISTRO
    $nombre_foto = "default-profile.png"; 
    $sql = "INSERT INTO usuarios (nombre, email, password, foto_perfil) 
            VALUES ('$nombre', '$email', '$password_encriptada', '$nombre_foto')";

    if (mysqli_query($conexion, $sql)) {
        header("Location: ../../frontend/html/index.php?registro=ok");
    } else {
        header("Location: ../../frontend/html/index.php?error=db");
    }
    exit();
}
?>