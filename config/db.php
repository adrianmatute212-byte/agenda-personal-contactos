<?php
/**
 * Configuración de conexión a la base de datos (PDO - MySQL / MariaDB).
 *
 * INSTRUCCIONES:
 * - Para probar en tu computadora con XAMPP, deja los valores por defecto.
 * - Cuando subas el proyecto a InfinityFree, reemplaza estos 4 valores con los
 *   datos exactos de tu cuenta (Panel de InfinityFree -> MySQL Details).
 */

$db_host = 'localhost';        // En InfinityFree ej.: sql304.infinityfree.com
$db_name = 'agenda_contactos'; // En InfinityFree ej.: if0_12345678_agenda
$db_user = 'root';             // En InfinityFree ej.: if0_12345678
$db_pass = '';                 // En InfinityFree: contraseña de tu cuenta de hosting

$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error de conexión</title>';
    echo '<style>body{font-family:system-ui,sans-serif;background:#f4f6f8;color:#1f2937;padding:2rem;}';
    echo '.box{max-width:600px;margin:3rem auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:1.5rem;}';
    echo 'h1{margin-top:0;color:#b91c1c;font-size:1.25rem;}code{background:#f3f4f6;padding:2px 6px;border-radius:4px;}</style></head><body>';
    echo '<div class="box"><h1>No se pudo conectar a la base de datos</h1>';
    echo '<p>Verifica que los datos de conexión en <code>config/db.php</code> coincidan con tu servidor MySQL (XAMPP o InfinityFree) y que hayas importado <code>database.sql</code>.</p>';
    echo '</div></body></html>';
    exit;
}

/**
 * Escapa texto para evitar vulnerabilidades XSS al imprimir en HTML.
 */
function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
