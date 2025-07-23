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

// Theme Logic (no changes needed)
$theme_cookie = $_COOKIE['checker_theme'] ?? 'dark';
if ($theme_cookie === 'dark') {
    $theme_background = '#212121';
    $theme_text = '#FFFFFF';
    $theme_background_opp = '#FFFFFF';
    $theme_text_opp = '#000000';
} else {
    $theme_background = '#FFFFFF';
    $theme_text = '#000000';
    $theme_background_opp = '#212121';
    $theme_text_opp = '#FFFFFF';
}
?>