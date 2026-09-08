<?php
//Script Author: phccoder https://t.me/PHCC0D3r

/*===[PHP Functions]==========================================*/
// Append a timestamped line to logs/checker.log (created automatically).
function phc_log($source, $msg) {
    $dir = __DIR__ . '/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    $line = '[' . date('Y-m-d H:i:s') . '] [' . $source . '] ' . $msg . PHP_EOL;
    @file_put_contents($dir . '/checker.log', $line, FILE_APPEND);
}

// Mask a Stripe Secret Key so we never write the full key to disk.
function phc_mask_sk($sk) {
    $sk = (string)$sk;
    $len = strlen($sk);
    if ($len <= 8) {
        return str_repeat('*', $len);
    }
    return substr($sk, 0, 6) . str_repeat('*', $len - 10) . substr($sk, -4);
}

// Shorten a raw response body for logging purposes.
function phc_shorten($body, $max = 400) {
    $body = (string)$body;
    if (strlen($body) <= $max) {
        return $body;
    }
    return substr($body, 0, $max) . '...[truncated]';
}
?>