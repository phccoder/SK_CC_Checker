<?php
// Include the main configuration
include_once __DIR__ . '/config.php';

// Securely start a session if authentication is enabled
if ($forceAuth) {
    // This check prevents "session already started" warnings
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

if (php_sapi_name() !== 'cli') {

    // Force HTTPS logic
    if ($forceHttps) {
        if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirect, true, 301);
            exit();
        }
    }
    
}
?>