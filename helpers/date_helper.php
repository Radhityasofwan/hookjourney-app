<?php

function format_date($dateStr) {
    if (empty($dateStr)) return '-';
    return date('d M Y', strtotime($dateStr));
}

function format_datetime($dateStr) {
    if (empty($dateStr)) return '-';
    return date('d M Y H:i', strtotime($dateStr));
}