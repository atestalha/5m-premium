<?php
require __DIR__ . '/lib.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo '{"ok":false}'; exit; }
if (!empty($_POST['web'])) { echo '{"ok":true}'; exit; } // bot tuzağı
$f = fn($k, $n) => mb_substr(trim((string)($_POST[$k] ?? '')), 0, $n);
$m = ['id' => new_id('m'), 'tarih' => date('c'), 'okundu' => false,
      'ad' => $f('ad', 80), 'telefon' => $f('telefon', 30), 'hizmet' => $f('hizmet', 80),
      'arac' => $f('arac', 80), 'mesaj' => $f('mesaj', 1500)];
if ($m['ad'] === '' || $m['telefon'] === '') { http_response_code(422); echo '{"ok":false,"hata":"Ad ve telefon gerekli"}'; exit; }
// basit hız sınırı: aynı IP'den 1 dakikada en fazla 3 mesaj
$ipf = DATA_DIR . '/.ip-' . substr(sha1($_SERVER['REMOTE_ADDR'] ?? ''), 0, 16);
$times = array_filter(read_json($ipf, []), fn($t) => $t > time() - 60);
if (count($times) >= 3) { http_response_code(429); echo '{"ok":false}'; exit; }
$times[] = time(); write_json($ipf, array_values($times));
$fp = fopen(MSG_FILE . '.lock', 'c'); flock($fp, LOCK_EX);
$list = read_json(MSG_FILE, []);
array_unshift($list, $m);
$list = array_slice($list, 0, 1000);
$ok = write_json(MSG_FILE, $list);
flock($fp, LOCK_UN); fclose($fp);
echo json_encode(['ok' => $ok]);
