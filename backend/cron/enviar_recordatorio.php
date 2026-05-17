<?php
// 1. Autoload de Composer (PHPMailer + dotenv)
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. Cargar el .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// 3. Definir constantes desde el .env
define('CRON_SECRET',    $_ENV['CRON_SECRET']);
define('GMAIL_USER',     $_ENV['GMAIL_USER']);
define('GMAIL_PASSWORD', $_ENV['GMAIL_PASSWORD']);

// 4. Comprobar la clave secreta
// $cabecera_recibida = $_SERVER['HTTP_X_CRON_SECRET'] ?? '';
// $param_recibido    = $_GET['secret'] ?? '';

// if ($cabecera_recibida !== CRON_SECRET && $param_recibido !== CRON_SECRET) {
//     http_response_code(403);
//     exit('Acceso denegado');
// }

// 5. Conexión a la BD
require_once __DIR__ . '/../config/conexion.php';

// ── Buscar actividades que necesitan recordatorio ─────────────────────────────
$query = "
    SELECT
        a.id            AS actividad_id,
        a.titulo,
        a.descripcion,
        CONCAT(a.fecha, ' ', a.hora) AS fecha,
        u.nombre        AS usuario_nombre,
        u.email         AS usuario_email,
        u.aviso_minutos
    FROM actividades a
    JOIN usuarios u ON u.id = a.usuario_id
    WHERE
        a.recordatorio_enviado = 0
        AND CONCAT(a.fecha, ' ', a.hora) > NOW()
        AND CONCAT(a.fecha, ' ', a.hora) <= DATE_ADD(NOW(), INTERVAL u.aviso_minutos MINUTE)
";

$resultado = mysqli_query($conexion, $query);

if (!$resultado) {
    error_log('Agendify cron - error query: ' . mysqli_error($conexion));
    exit('Error en la query');
}

$enviados = 0;
$errores  = 0;

while ($row = mysqli_fetch_assoc($resultado)) {

    $enviado = enviarRecordatorio(
        $row['usuario_email'],
        $row['usuario_nombre'],
        $row['titulo'],
        $row['descripcion'],
        $row['fecha'],
        $row['aviso_minutos']
    );

    if ($enviado) {
        $id = (int) $row['actividad_id'];
        mysqli_query($conexion, "UPDATE actividades SET recordatorio_enviado = 1 WHERE id = $id");
        $enviados++;
    } else {
        $errores++;
    }
}

mysqli_close($conexion);

echo json_encode([
    'ok'        => true,
    'enviados'  => $enviados,
    'errores'   => $errores,
    'timestamp' => date('Y-m-d H:i:s'),
]);


//Función de envío
function enviarRecordatorio(string $email, string $nombre, string $titulo, string $descripcion, string $fecha, int $minutos): bool
{
    if ($minutos >= 1440) {
        $cuando = ($minutos / 1440) . ' día(s)';
    } elseif ($minutos >= 60) {
        $cuando = ($minutos / 60) . ' hora(s)';
    } else {
        $cuando = $minutos . ' minutos';
    }

    $fecha_formateada = date('d/m/Y \a \l\a\s H:i', strtotime($fecha));

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = GMAIL_USER;
        $mail->Password   = GMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(GMAIL_USER, 'Agendify');
        $mail->addAddress($email, $nombre);

        $mail->Subject = "Recordatorio: $titulo – en $cuando";
        $mail->isHTML(true);
        $mail->Body    = plantillaEmail($nombre, $titulo, $descripcion, $fecha_formateada, $cuando);
        $mail->AltBody = "Hola $nombre,\n\nTienes un evento próximo: $titulo\nFecha: $fecha_formateada\n\nAgendify";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Agendify cron - error enviando a $email: " . $mail->ErrorInfo);
        return false;
    }
}


//Plantilla del email
function plantillaEmail(string $nombre, string $titulo, string $descripcion, string $fecha, string $cuando): string
{
    $desc = $descripcion ? "<p style='color:#555;margin:0 0 16px'>$descripcion</p>" : '';

    return "
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='UTF-8'></head>
<body style='margin:0;padding:0;background:#f5f5f5;font-family:Inter,Arial,sans-serif'>
  <table width='100%' cellpadding='0' cellspacing='0'>
    <tr>
      <td align='center' style='padding:40px 16px'>
        <table width='560' cellpadding='0' cellspacing='0'
               style='background:#fff;border-radius:8px;border:1px solid #e0e0e0;overflow:hidden'>

          <tr>
            <td style='background:#1a1a2e;padding:24px 32px'>
              <p style='margin:0;color:#fff;font-size:18px;font-weight:600'>Agendify</p>
            </td>
          </tr>

          <tr>
            <td style='padding:32px'>
              <p style='margin:0 0 16px;color:#1a1a1a;font-size:15px'>Hola, <strong>$nombre</strong></p>
              <p style='margin:0 0 24px;color:#555;font-size:14px'>
                Tienes un evento programado en <strong>$cuando</strong>:
              </p>

              <table width='100%' cellpadding='0' cellspacing='0'
                     style='background:#f9f9f9;border:1px solid #e0e0e0;border-radius:6px;margin-bottom:24px'>
                <tr>
                  <td style='padding:20px 24px'>
                    <p style='margin:0 0 4px;font-size:16px;font-weight:600;color:#1a1a1a'>$titulo</p>
                    <p style='margin:0 0 12px;font-size:13px;color:#888'>📅 $fecha</p>
                    $desc
                  </td>
                </tr>
              </table>

              <p style='margin:0;font-size:13px;color:#999'>
                Este recordatorio se envió porque tienes configurado el aviso $cuando antes en tu perfil de Agendify.
              </p>
            </td>
          </tr>

          <tr>
            <td style='border-top:1px solid #e0e0e0;padding:16px 32px'>
              <p style='margin:0;font-size:12px;color:#bbb;text-align:center'>© 2026 Agendify Team</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
";
}