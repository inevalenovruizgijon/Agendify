<?php
$host = "localhost";
$user = "root";
$pass = "toor";
$db   = "agendify_db";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");