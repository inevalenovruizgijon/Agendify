<?php
include_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $password = $_POST['password'];

    $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

    $nombre_foto = "default-profile.png"; // Valor inicial

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $ruta_destino = "../../uploads/";
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_foto = time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
        
        move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino . $nombre_foto);
    }

    $sql = "INSERT INTO usuarios (nombre, email, password, foto_perfil) 
            VALUES ('$nombre', '$email', '$password_encriptada', '$nombre_foto')";

    if (mysqli_query($conexion, $sql)) {
        header("Location: ../../frontend/html/index.php?registro=ok");
    } else {
        header("Location: ../../frontend/html/index.php?error=db");
    }
}
?>