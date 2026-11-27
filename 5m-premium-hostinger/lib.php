<?php
// 5M Premium — ortak yardımcılar
declare(strict_types=1);
date_default_timezone_set('Europe/Istanbul');

const DATA_DIR = __DIR__ . '/data';
const SITE_FILE = DATA_DIR . '/site.json';
const MSG_FILE = DATA_DIR . '/mesajlar.json';
const AUTH_FILE = DATA_DIR . '/yonetici.json';
const UPLOAD_DIR = __DIR__ . '/uploads';
const DEFAULT_PASSWORD = '5mpremium2026';

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function read_json(string $file, $default) {
    if (!is_file($file)) return $default;
    $d = json_decode((string)file_get_contents($file), true);
    return is_array($d) ? $d : $default;
}

function write_json(string $file, $data): bool {
    $tmp = $file . '.tmp' . bin2hex(random_bytes(4));
    $ok = file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    if ($ok === false) return false;
    return rename($tmp, $file);
}

function site(): array {
    static $s = null;
    if ($s === null) $s = read_json(SITE_FILE, []);
    return $s;
}

function tel_link(string $t): string {
    $d = preg_replace('/\D+/', '', $t);
    if (str_starts_with($d, '0')) $d = '90' . substr($d, 1);
    return '+' . $d;
}

function wa_link(string $num, string $text = ''): string {
    $d = preg_replace('/\D+/', '', $num);
    return 'https://wa.me/' . $d . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

function paragraphs(string $t): string {
    $out = '';
    foreach (preg_split("/\n\s*\n/", trim($t)) as $p) {
        if (trim($p) !== '') $out .= '<p>' . nl2br(e(trim($p))) . '</p>';
    }
    return $out;
}

function new_id(string $p = 'r'): string { return $p . bin2hex(random_bytes(4)); }
