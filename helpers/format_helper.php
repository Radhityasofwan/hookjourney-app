<?php

function format_rupiah($angka) {
    // Tangani nilai null menjadi 0 (Fix untuk error Deprecated di PHP 8.1+)
    $angka = is_numeric($angka) ? (float) $angka : 0;
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function format_number($angka) {
    // Tangani nilai null menjadi 0
    $angka = is_numeric($angka) ? (float) $angka : 0;
    return number_format($angka, 0, ',', '.');
}

function format_percentage($angka) {
    // Tangani nilai null menjadi 0
    $angka = is_numeric($angka) ? (float) $angka : 0;
    return number_format($angka, 2, ',', '.') . '%';
}