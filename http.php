<?php
//Script Author: phccoder https://t.me/PHCC0D3r

/*===[Include logger]=========================================*/
include_once __DIR__ . '/logger.php';

/*===[PHP Functions]==========================================*/
// Perform one HTTP request. Uses cURL when available, otherwise falls
// back to file_get_contents() with a stream context (works without curl,
// as long as allow_url_fopen is enabled).
// Returns: array('body' => string|false, 'code' => int, 'errno' => int, 'error' => string)
function http_request($url, $method = 'GET', $postfields = null, $userpwd = null) {
    $cacert = __DIR__ . '/cacert.pem';
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        if (is_file($cacert)) {
            curl_setopt($ch, CURLOPT_CAINFO, $cacert);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($postfields !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            }
        }
        if ($userpwd !== null) {
            curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
        }
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($errno) {
            phc_log('http', 'curl error errno=' . $errno . ' error=' . $error . ' url=' . $url);
        }
        return array('body' => $body, 'code' => $code, 'errno' => $errno, 'error' => $error);
    }

    $header = array('Content-Type: application/x-www-form-urlencoded');
    if ($userpwd !== null) {
        $header[] = 'Authorization: Basic ' . base64_encode($userpwd);
    }
    $ctxopts = array(
        'http' => array(
            'method' => $method,
            'header' => implode("\r\n", $header),
            'timeout' => 30,
            'ignore_errors' => true,
        ),
    );
    if (is_file($cacert)) {
        $ctxopts['ssl'] = array('cafile' => $cacert, 'verify_peer' => true, 'verify_peer_name' => true);
    } else {
        $ctxopts['ssl'] = array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true);
    }
    if ($method === 'POST' && $postfields !== null) {
        $ctxopts['http']['content'] = $postfields;
    }
    $ctx = stream_context_create($ctxopts);
    $body = @file_get_contents($url, false, $ctx);
    $code = 0;
    if (isset($http_response_header) && is_array($http_response_header)) {
        if (preg_match('#HTTP/\S+\s+(\d+)#', $http_response_header[0], $m)) {
            $code = (int)$m[1];
        }
    }
    if ($body === false) {
        phc_log('http', 'file_get_contents failed url=' . $url);
    }
    return array('body' => $body, 'code' => $code, 'errno' => 0, 'error' => '');
}
?>