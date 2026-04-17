<?php
require_once __DIR__ . '/../functions.php';
if (!empty($_SESSION['admin_id'])) {
    denetim_kaydet('cikis_yapildi');
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
header('Location: index.php');
exit;
