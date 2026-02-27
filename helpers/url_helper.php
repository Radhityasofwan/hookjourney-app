<?php

/**
 * Mendapatkan base URL aplikasi (ditambah path jika ada)
 */
function base_url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Mendapatkan URL untuk aset statis (css, js, img) di folder public/assets
 */
function asset($path) {
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Melakukan HTTP Redirect
 */
function redirect($path) {
    header("Location: " . base_url($path));
    exit;
}

/**
 * Mendapatkan URI segment saat ini
 * Berguna untuk mendeteksi menu sidebar yang sedang aktif
 */
function current_uri() {
    $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptName !== '/') {
        $requestUrl = str_replace($scriptName, '', $requestUrl);
    }
    return trim(explode('?', $requestUrl)[0], '/');
}