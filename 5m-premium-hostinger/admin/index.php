<?php
// 5M Premium — Yönetim paneli
require dirname(__DIR__) . '/lib.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']), 'path' => '/']);
session_name('5m_yonetim');
session_start();

const ROOT = __DIR__ . '/..';
$ETIKETLER = ['PPF Kaplama', 'Cam Filmi', 'Renk Değişimi', 'Seramik Kaplama', 'Pasta – Cila', 'Buharlı Yıkama', 'Araç Yıkama', 'Mobilya Kaplama'];
$IKONLAR = ['kalkan' => 'Kalkan', 'gunes' => 'Güneş', 'renk' => 'Renk paleti', 'mobilya' => 'Mobilya', 'elmas' => 'Elmas', 'parlak' => 'Parıltı', 'buhar' => 'Buhar', 'damla' => 'Damla'];

/* ---------- yardımcılar ---------- */
function auth_data(): array {
    $a = read_json(AUTH_FILE, []);
    if (empty($a['hash'])) { $a = ['hash' => password_hash(DEFAULT_PASSWORD, PASSWORD_DEFAULT), 'varsayilan' => true]; write_json(AUTH_FILE, $a); }
    return $a;
}
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function flash(string $m, string $t = 'ok'): void { $_SESSION['flash'] = [$m, $t]; }
function go(string $s, string $extra = ''): never { header('Location: ?s=' . $s . $extra); exit; }
function load(): array { return read_json(SITE_FILE, []); }
function save(array $d): bool { return write_json(SITE_FILE, $d); }
function post(string $k, int $max = 5000): string { return mb_substr(trim((string)($_POST[$k] ?? '')), 0, $max); }

/** Yüklenen görseli küçültüp uploads/ içine JPG olarak kaydeder, site köküne göre yolu döner. */
function save_image(string $tmp, int $max = 1800): ?string {
    $info = @getimagesize($tmp);
    if (!$info) return null;
    switch ($info[2]) {
        case IMAGETYPE_JPEG: $src = @imagecreatefromjpeg($tmp); break;
        case IMAGETYPE_PNG: $src = @imagecreatefrompng($tmp); break;
        case IMAGETYPE_WEBP: $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : null; break;
        default: return null;
    }
    if (!$src) return null;
    if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $o = @exif_read_data($tmp)['Orientation'] ?? 1;
        if ($o == 3) $src = imagerotate($src, 180, 0); elseif ($o == 6) $src = imagerotate($src, -90, 0); elseif ($o == 8) $src = imagerotate($src, 90, 0);
    }
    $w = imagesx($src); $h = imagesy($src); $k = min(1, $max / max($w, $h));
    $nw = (int)round($w * $k); $nh = (int)round($h * $k);
    $dst = imagecreatetruecolor($nw, $nh);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 11, 11, 12));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $name = date('Ymd') . '-' . bin2hex(random_bytes(5)) . '.jpg';
    imagejpeg($dst, UPLOAD_DIR . '/' . $name, 82);
    imagedestroy($src); imagedestroy($dst);
    return 'uploads/' . $name;
}
function uploaded(string $field): array {
    $out = [];
    if (empty($_FILES[$field])) return $out;
    $f = $_FILES[$field];
    $names = is_array($f['name']) ? $f['tmp_name'] : [$f['tmp_name']];
    $errs = is_array($f['error']) ? $f['error'] : [$f['error']];
    foreach ($names as $i => $tmp) {
        if (($errs[$i] ?? 1) !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) continue;
        if ($p = save_image($tmp)) $out[] = $p;
    }
    return $out;
}
function drop_file(string $path): void {
    if (str_starts_with($path, 'uploads/') && !str_contains($path, '..')) @unlink(ROOT . '/' . $path);
}
function move_item(array &$arr, int $i, int $dir): void {
    $j = $i + $dir;
    if ($i < 0 || $j < 0 || $i >= count($arr) || $j >= count($arr)) return;
    [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
}
function find_idx(array $arr, string $id): int { foreach ($arr as $i => $x) if (($x['id'] ?? '') === $id) return $i; return -1; }
function img_url(string $p): string { return '../' . $p; }

/* ---------- giriş / çıkış ---------- */
$s = $_GET['s'] ?? 'referanslar';
$authed = !empty($_SESSION['ok']);

if ($s === 'cikis') { $_SESSION = []; session_destroy(); header('Location: ./'); exit; }

if (!$authed && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sifre'])) {
    $lf = DATA_DIR . '/.giris-' . substr(sha1($_SERVER['REMOTE_ADDR'] ?? ''), 0, 16);
    $tries = array_filter(read_json($lf, []), fn($t) => $t > time() - 900);
    if (count($tries) >= 8) { $hata = 'Çok fazla hatalı deneme. 15 dakika sonra tekrar deneyin.'; }
    elseif (password_verify((string)$_POST['sifre'], auth_data()['hash'])) {
        session_regenerate_id(true); $_SESSION['ok'] = 1; @unlink($lf); header('Location: ./'); exit;
    } else { $tries[] = time(); write_json($lf, array_values($tries)); $hata = 'Şifre hatalı.'; }
}

if (!$authed) { ?>
<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<title>Yönetim girişi · 5M Premium</title><link rel="icon" href="../assets/img/site/favicon.png"><link rel="stylesheet" href="panel.css"></head>
<body class="login"><form method="post" class="login-box">
<img src="../assets/img/site/logo-5m.png" alt="5M Premium">
<h1>Yönetim paneli</h1>
<label>Şifre<input type="password" name="sifre" autocomplete="current-password" required autofocus></label>
<?php if (!empty($hata)): ?><p class="err"><?= e($hata) ?></p><?php endif; ?>
<button class="b red">Giriş yap</button>
<a href="../" class="back">← Siteye dön</a>
</form></body></html>
<?php exit; }

/* ---------- işlemler (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf(), (string)($_POST['csrf'] ?? ''))) { flash('Oturum süresi doldu, tekrar deneyin.', 'err'); go($s); }
    $d = load();
    $a = $_POST['a'] ?? '';
    $id = (string)($_POST['id'] ?? '');

    switch ($a) {
        /* referanslar */
        case 'ref_kaydet':
            $etik = array_values(array_unique(array_filter(array_map('trim', array_merge((array)($_POST['etiket'] ?? []), explode(',', post('etiket_diger', 300)))))));
            $arac = post('arac', 120);
            if ($arac === '') { flash('Araç adı boş olamaz.', 'err'); go($s, $id ? '&duzenle=' . urlencode($id) : '&yeni=1'); }
            $yeni = uploaded('foto');
            if ($id === '') {
                if (!$yeni) { flash('En az bir fotoğraf ekleyin (JPG, PNG veya WEBP).', 'err'); go($s, '&yeni=1'); }
                array_unshift($d['referanslar'], ['id' => new_id('r'), 'arac' => $arac, 'islemler' => $etik, 'gorseller' => $yeni]);
                flash('Referans eklendi.');
            } else {
                $i = find_idx($d['referanslar'], $id);
                if ($i < 0) go($s);
                $r = &$d['referanslar'][$i];
                $r['arac'] = $arac; $r['islemler'] = $etik;
                foreach ((array)($_POST['sil_foto'] ?? []) as $p) { $r['gorseller'] = array_values(array_diff($r['gorseller'], [$p])); drop_file($p); }
                $r['gorseller'] = array_merge($r['gorseller'], $yeni);
                $kapak = (string)($_POST['kapak'] ?? '');
                if ($kapak !== '' && in_array($kapak, $r['gorseller'], true)) $r['gorseller'] = array_values(array_merge([$kapak], array_diff($r['gorseller'], [$kapak])));
                unset($r);
                flash('Referans kaydedildi.');
            }
            save($d); go($s);
        case 'ref_sil':
            $i = find_idx($d['referanslar'], $id);
            if ($i >= 0) { foreach ($d['referanslar'][$i]['gorseller'] as $p) drop_file($p); array_splice($d['referanslar'], $i, 1); save($d); flash('Referans silindi.'); }
            go($s);
        case 'ref_tasi':
            move_item($d['referanslar'], find_idx($d['referanslar'], $id), (int)$_POST['yon']); save($d); go($s);

        /* hizmetler */
        case 'hiz_kaydet':
            $h = ['baslik' => post('baslik', 120), 'grup' => post('grup', 40) ?: 'Kaplama', 'ikon' => array_key_exists($_POST['ikon'] ?? '', $IKONLAR) ? $_POST['ikon'] : 'kalkan',
                  'kisa' => post('kisa', 200), 'detay' => post('detay', 2000),
                  'maddeler' => array_values(array_filter(array_map('trim', explode("\n", post('maddeler', 2000)))))];
            if ($h['baslik'] === '') { flash('Hizmet adı boş olamaz.', 'err'); go($s); }
            $foto = uploaded('foto');
            if ($id === '') {
                $h['id'] = new_id('h'); $h['gorsel'] = $foto[0] ?? '';
                $d['hizmetler'][] = $h; flash('Hizmet eklendi.');
            } else {
                $i = find_idx($d['hizmetler'], $id); if ($i < 0) go($s);
                $old = $d['hizmetler'][$i];
                $h['id'] = $id; $h['gorsel'] = $old['gorsel'];
                if (!empty($_POST['gorsel_kaldir'])) { drop_file($old['gorsel']); $h['gorsel'] = ''; }
                if ($foto) { drop_file($old['gorsel']); $h['gorsel'] = $foto[0]; }
                $d['hizmetler'][$i] = $h; flash('Hizmet kaydedildi.');
            }
            save($d); go($s);
        case 'hiz_sil':
            $i = find_idx($d['hizmetler'], $id);
            if ($i >= 0) { drop_file($d['hizmetler'][$i]['gorsel']); array_splice($d['hizmetler'], $i, 1); save($d); flash('Hizmet silindi.'); }
            go($s);
        case 'hiz_tasi':
            move_item($d['hizmetler'], find_idx($d['hizmetler'], $id), (int)$_POST['yon']); save($d); go($s);

        /* sık sorulanlar */
        case 'sss_kaydet':
            $q = ['soru' => post('soru', 200), 'cevap' => post('cevap', 3000)];
            if ($q['soru'] === '' || $q['cevap'] === '') { flash('Soru ve cevap boş olamaz.', 'err'); go($s, $id ? '&duzenle=' . urlencode($id) : '&yeni=1'); }
            $d['sss'] = $d['sss'] ?? [];
            if ($id === '') { $q['id'] = new_id('s'); $d['sss'][] = $q; flash('Soru eklendi.'); }
            else { $i = find_idx($d['sss'], $id); if ($i < 0) go($s); $q['id'] = $id; $d['sss'][$i] = $q; flash('Soru kaydedildi.'); }
            save($d); go($s);
        case 'sss_sil':
            $i = find_idx($d['sss'] ?? [], $id);
            if ($i >= 0) { array_splice($d['sss'], $i, 1); save($d); flash('Soru silindi.'); }
            go($s);
        case 'sss_tasi':
            move_item($d['sss'], find_idx($d['sss'], $id), (int)$_POST['yon']); save($d); go($s);

        /* metinler + ana sayfa görselleri */
        case 'metin_kaydet':
            foreach (['hero_ust' => 120, 'hero_baslik' => 120, 'hero_alt' => 400, 'hakkimizda_baslik' => 400, 'hakkimizda' => 4000, 'referans_alt' => 300, 'iletisim_alt' => 300] as $k => $n) $d['metinler'][$k] = post($k, $n);
            save($d); flash('Metinler kaydedildi.'); go($s);
        case 'hero_ekle':
            $d['hero'] = array_merge($d['hero'], uploaded('foto')); save($d); flash('Görsel eklendi.'); go($s);
        case 'hero_sil':
            $p = (string)($_POST['yol'] ?? '');
            if (count($d['hero']) <= 1) { flash('En az bir ana sayfa görseli kalmalı.', 'err'); go($s); }
            $d['hero'] = array_values(array_diff($d['hero'], [$p])); drop_file($p); save($d); flash('Görsel kaldırıldı.'); go($s);
        case 'hero_tasi':
            $i = array_search((string)($_POST['yol'] ?? ''), $d['hero'], true);
            if ($i !== false) { move_item($d['hero'], $i, (int)$_POST['yon']); save($d); }
            go($s);

        /* iletişim */
        case 'iletisim_kaydet':
            foreach (['firma' => 120, 'telefon1' => 30, 'telefon2' => 30, 'whatsapp' => 20, 'adres' => 200, 'harita' => 600, 'harita_embed' => 1200, 'instagram' => 200, 'calisma' => 120] as $k => $n) $d['ayarlar'][$k] = post($k, $n);
            $d['ayarlar']['whatsapp'] = preg_replace('/\D+/', '', $d['ayarlar']['whatsapp']);
            if (str_starts_with($d['ayarlar']['whatsapp'], '0')) $d['ayarlar']['whatsapp'] = '90' . substr($d['ayarlar']['whatsapp'], 1);
            save($d); flash('İletişim bilgileri kaydedildi.'); go($s);

        /* mesajlar */
        case 'msg_okundu': case 'msg_sil': case 'msg_hepsi_okundu':
            $fp = fopen(MSG_FILE . '.lock', 'c'); flock($fp, LOCK_EX);
            $list = read_json(MSG_FILE, []);
            foreach ($list as $k => &$m) {
                if ($a === 'msg_hepsi_okundu' || $m['id'] === $id) { if ($a === 'msg_sil') unset($list[$k]); else $m['okundu'] = true; }
            }
            unset($m);
            write_json(MSG_FILE, array_values($list)); flock($fp, LOCK_UN); fclose($fp);
            go($s);

        /* ayarlar */
        case 'sifre':
            $au = auth_data();
            if (!password_verify((string)($_POST['eski'] ?? ''), $au['hash'])) { flash('Mevcut şifre hatalı.', 'err'); go($s); }
            $y = (string)($_POST['yeni'] ?? '');
            if (mb_strlen($y) < 8) { flash('Yeni şifre en az 8 karakter olmalı.', 'err'); go($s); }
            if ($y !== (string)($_POST['yeni2'] ?? '')) { flash('Yeni şifreler aynı değil.', 'err'); go($s); }
            write_json(AUTH_FILE, ['hash' => password_hash($y, PASSWORD_DEFAULT), 'varsayilan' => false]);
            session_regenerate_id(true); flash('Şifre değiştirildi.'); go($s);
        case 'geri_yukle':
            $j = json_decode((string)@file_get_contents($_FILES['yedek']['tmp_name'] ?? ''), true);
            if (!is_array($j) || !isset($j['ayarlar'], $j['metinler'], $j['hizmetler'], $j['referanslar'], $j['hero'])) { flash('Geçersiz yedek dosyası.', 'err'); go($s); }
            save($j); flash('Yedek geri yüklendi.'); go($s);
    }
    go($s);
}

if ($s === 'yedek') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="5m-premium-yedek-' . date('Y-m-d') . '.json"');
    readfile(SITE_FILE); exit;
}

/* ---------- görünüm ---------- */
$d = load();
$msgs = read_json(MSG_FILE, []);
$okunmamis = count(array_filter($msgs, fn($m) => empty($m['okundu'])));
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$varsayilan = !empty(auth_data()['varsayilan']);
$menu = ['referanslar' => 'Referanslar', 'hizmetler' => 'Hizmetler', 'sss' => 'Sık sorulanlar', 'metinler' => 'Ana sayfa & metinler', 'iletisim' => 'İletişim bilgileri', 'mesajlar' => 'Mesajlar', 'ayarlar' => 'Ayarlar'];
if (!isset($menu[$s])) $s = 'referanslar';
$tumEtiketler = array_values(array_unique(array_merge($ETIKETLER, ...array_map(fn($r) => $r['islemler'], $d['referanslar'] ?: [['islemler' => []]]))));
?><!DOCTYPE html>
<html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<title><?= e($menu[$s]) ?> · 5M Premium yönetim</title><link rel="icon" href="../assets/img/site/favicon.png"><link rel="stylesheet" href="panel.css"></head>
<body>
<header class="bar">
  <a href="./" class="logo"><img src="../assets/img/site/logo-5m.png" alt="5M Premium"><span>Yönetim</span></a>
  <nav class="tabs">
    <?php foreach ($menu as $k => $t): ?><a href="?s=<?= $k ?>" class="<?= $k === $s ? 'on' : '' ?>"><?= e($t) ?><?php if ($k === 'mesajlar' && $okunmamis): ?> <em><?= $okunmamis ?></em><?php endif; ?></a><?php endforeach; ?>
  </nav>
  <div class="bar-r"><a href="../" target="_blank" class="b ghost">Siteyi aç</a><a href="?s=cikis" class="b ghost">Çıkış</a></div>
</header>
<main class="main">
<?php if ($varsayilan): ?><div class="note err">Varsayılan şifreyi kullanıyorsunuz. <a href="?s=ayarlar">Ayarlar</a> bölümünden hemen değiştirin.</div><?php endif; ?>
<?php if ($flash): ?><div class="note <?= e($flash[1]) ?>"><?= e($flash[0]) ?></div><?php endif; ?>

<?php if ($s === 'referanslar'):
  $duz = isset($_GET['duzenle']) ? find_idx($d['referanslar'], (string)$_GET['duzenle']) : -1;
  $r = $duz >= 0 ? $d['referanslar'][$duz] : null;
  $formAcik = $r || isset($_GET['yeni']); ?>
  <div class="head"><h1>Referanslar <small><?= count($d['referanslar']) ?> araç</small></h1><?php if (!$formAcik): ?><a class="b red" href="?s=referanslar&yeni=1">+ Yeni referans</a><?php endif; ?></div>
  <?php if ($formAcik): ?>
  <form method="post" enctype="multipart/form-data" class="card form">
    <?= csrf_field() ?><input type="hidden" name="a" value="ref_kaydet"><input type="hidden" name="id" value="<?= e($r['id'] ?? '') ?>">
    <h2><?= $r ? 'Referansı düzenle' : 'Yeni referans ekle' ?></h2>
    <label>Araç marka / model<input name="arac" required value="<?= e($r['arac'] ?? '') ?>" placeholder="Örn. BMW 520i"></label>
    <fieldset><legend>Yapılan işlemler</legend><div class="checks">
      <?php foreach ($tumEtiketler as $t): ?><label class="ck"><input type="checkbox" name="etiket[]" value="<?= e($t) ?>" <?= in_array($t, $r['islemler'] ?? [], true) ? 'checked' : '' ?>><span><?= e($t) ?></span></label><?php endforeach; ?>
    </div><label class="mt">Listede olmayan işlem (virgülle ayırın)<input name="etiket_diger" placeholder="Örn. Jant boyama, Far kaplama"></label></fieldset>
    <?php if ($r): ?><fieldset><legend>Fotoğraflar — kapak fotoğrafını seçin, silmek istediklerinizi işaretleyin</legend><div class="pics">
      <?php foreach ($r['gorseller'] as $k => $p): ?><div class="pic"><img src="<?= e(img_url($p)) ?>" alt=""><label class="ck"><input type="radio" name="kapak" value="<?= e($p) ?>" <?= $k === 0 ? 'checked' : '' ?>><span>Kapak</span></label><label class="ck del"><input type="checkbox" name="sil_foto[]" value="<?= e($p) ?>"><span>Sil</span></label></div><?php endforeach; ?>
    </div></fieldset><?php endif; ?>
    <label><?= $r ? 'Fotoğraf ekle' : 'Fotoğraflar' ?> <small>(birden fazla seçebilirsiniz; ilk fotoğraf kapak olur)</small><input type="file" name="foto[]" accept="image/jpeg,image/png,image/webp" multiple <?= $r ? '' : 'required' ?>></label>
    <div class="row"><button class="b red">Kaydet</button><a class="b ghost" href="?s=referanslar">Vazgeç</a></div>
  </form>
  <?php endif; ?>
  <div class="list">
    <?php foreach ($d['referanslar'] as $i => $x): ?>
      <div class="item">
        <img src="<?= e(img_url($x['gorseller'][0] ?? 'assets/img/site/favicon.png')) ?>" alt="">
        <div class="item-t"><b><?= e($x['arac']) ?></b><span><?= e(implode(' · ', $x['islemler'])) ?> — <?= count($x['gorseller']) ?> fotoğraf</span></div>
        <div class="item-a">
          <form method="post"><?= csrf_field() ?><input type="hidden" name="a" value="ref_tasi"><input type="hidden" name="id" value="<?= e($x['id']) ?>"><button class="ib" name="yon" value="-1" <?= $i === 0 ? 'disabled' : '' ?> aria-label="Yukarı taşı">↑</button><button class="ib" name="yon" value="1" <?= $i === count($d['referanslar']) - 1 ? 'disabled' : '' ?> aria-label="Aşağı taşı">↓</button></form>
          <a class="b ghost sm" href="?s=referanslar&duzenle=<?= e(urlencode($x['id'])) ?>">Düzenle</a>
          <form method="post" onsubmit="return confirm('<?= e($x['arac']) ?> silinsin mi? Fotoğrafları da silinir.')"><?= csrf_field() ?><input type="hidden" name="a" value="ref_sil"><input type="hidden" name="id" value="<?= e($x['id']) ?>"><button class="b ghost sm danger">Sil</button></form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php elseif ($s === 'hizmetler'):
  $duz = isset($_GET['duzenle']) ? find_idx($d['hizmetler'], (string)$_GET['duzenle']) : -1;
  $h = $duz >= 0 ? $d['hizmetler'][$duz] : null;
  $formAcik = $h || isset($_GET['yeni']); ?>
  <div class="head"><h1>Hizmetler <small><?= count($d['hizmetler']) ?> hizmet</small></h1><?php if (!$formAcik): ?><a class="b red" href="?s=hizmetler&yeni=1">+ Yeni hizmet</a><?php endif; ?></div>
  <?php if ($formAcik): ?>
  <form method="post" enctype="multipart/form-data" class="card form">
    <?= csrf_field() ?><input type="hidden" name="a" value="hiz_kaydet"><input type="hidden" name="id" value="<?= e($h['id'] ?? '') ?>">
    <h2><?= $h ? 'Hizmeti düzenle' : 'Yeni hizmet ekle' ?></h2>
    <div class="g2"><label>Hizmet adı<input name="baslik" required value="<?= e($h['baslik'] ?? '') ?>"></label>
    <label>Grup<input name="grup" list="gruplar" value="<?= e($h['grup'] ?? 'Kaplama') ?>"><datalist id="gruplar"><option>Kaplama</option><option>Koruma</option><option>Temizlik</option></datalist></label></div>
    <label>İkon<select name="ikon"><?php foreach ($IKONLAR as $k => $t): ?><option value="<?= $k ?>" <?= ($h['ikon'] ?? '') === $k ? 'selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></label>
    <label>Kısa açıklama <small>(kartta görünür)</small><input name="kisa" maxlength="200" value="<?= e($h['kisa'] ?? '') ?>"></label>
    <label>Detaylı açıklama <small>(karta tıklanınca açılır)</small><textarea name="detay" rows="5"><?= e($h['detay'] ?? '') ?></textarea></label>
    <label>Öne çıkan maddeler <small>(her satıra bir madde; hizmete tıklanınca açılan pencerede listelenir)</small><textarea name="maddeler" rows="4"><?= e(implode("\n", $h['maddeler'] ?? [])) ?></textarea></label>
    <?php if (!empty($h['gorsel'])): ?><div class="pics"><div class="pic"><img src="<?= e(img_url($h['gorsel'])) ?>" alt=""><label class="ck del"><input type="checkbox" name="gorsel_kaldir" value="1"><span>Görseli kaldır</span></label></div></div><?php endif; ?>
    <label><?= !empty($h['gorsel']) ? 'Görseli değiştir' : 'Görsel' ?> <small>(isteğe bağlı; yoksa kartta ikon gösterilir)</small><input type="file" name="foto" accept="image/jpeg,image/png,image/webp"></label>
    <div class="row"><button class="b red">Kaydet</button><a class="b ghost" href="?s=hizmetler">Vazgeç</a></div>
  </form>
  <?php endif; ?>
  <div class="list">
    <?php foreach ($d['hizmetler'] as $i => $x): ?>
      <div class="item">
        <?php if ($x['gorsel']): ?><img src="<?= e(img_url($x['gorsel'])) ?>" alt=""><?php else: ?><span class="noimg">Görsel yok</span><?php endif; ?>
        <div class="item-t"><b><?= e($x['baslik']) ?></b><span><?= e($x['grup']) ?> — <?= e($x['kisa']) ?></span></div>
        <div class="item-a">
          <form method="post"><?= csrf_field() ?><input type="hidden" name="a" value="hiz_tasi"><input type="hidden" name="id" value="<?= e($x['id']) ?>"><button class="ib" name="yon" value="-1" <?= $i === 0 ? 'disabled' : '' ?> aria-label="Yukarı taşı">↑</button><button class="ib" name="yon" value="1" <?= $i === count($d['hizmetler']) - 1 ? 'disabled' : '' ?> aria-label="Aşağı taşı">↓</button></form>
          <a class="b ghost sm" href="?s=hizmetler&duzenle=<?= e(urlencode($x['id'])) ?>">Düzenle</a>
          <form method="post" onsubmit="return confirm('<?= e($x['baslik']) ?> silinsin mi?')"><?= csrf_field() ?><input type="hidden" name="a" value="hiz_sil"><input type="hidden" name="id" value="<?= e($x['id']) ?>"><button class="b ghost sm danger">Sil</button></form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php elseif ($s === 'sss'):
  $sss = $d['sss'] ?? [];
  $duz = isset($_GET['duzenle']) ? find_idx($sss, (string)$_GET['duzenle']) : -1;
  $q = $duz >= 0 ? $sss[$duz] : null;
  $formAcik = $q || isset($_GET['yeni']); ?>
  <div class="head"><h1>Sık sorulanlar <small><?= count($sss) ?> soru</small></h1><?php if (!$formAcik): ?><a class="b red" href="?s=sss&yeni=1">+ Yeni soru</a><?php endif; ?></div>
  <?php if ($formAcik): ?>
  <form method="post" class="card form">
    <?= csrf_field() ?><input type="hidden" name="a" value="sss_kaydet"><input type="hidden" name="id" value="<?= e($q['id'] ?? '') ?>">
    <h2><?= $q ? 'Soruyu düzenle' : 'Yeni soru ekle' ?></h2>
    <label>Soru<input name="soru" required value="<?= e($q['soru'] ?? '') ?>"></label>
    <label>Cevap <small>(paragrafları boş satırla ayırın)</small><textarea name="cevap" rows="5" required><?= e($q['cevap'] ?? '') ?></textarea></label>
    <div class="row"><button class="b red">Kaydet</button><a class="b ghost" href="?s=sss">Vazgeç</a></div>
  </form>
  <?php endif; ?>
  <?php if (!$sss): ?><div class="card empty">Henüz soru yok. Soru eklemezseniz bu bölüm sitede gizlenir.</div><?php endif; ?>
  <div class="list">
    <?php foreach ($sss as $i => $x): ?>
      <div class="item">
        <div class="item-t"><b><?= e($x['soru']) ?></b><span><?= e(mb_strimwidth($x['cevap'], 0, 120, '…')) ?></span></div>
        <div class="item-a">
          <form method="post"><?= csrf_field() ?><input type="hidden" name="a" value="sss_tasi"><input type="hidden" name="id" value="<?= e($x['id']) ?>"><button class="ib" name="yon" value="-1" <?= $i === 0 ? 'disabled' : '' ?> aria-label="Yukarı taşı">↑</button><button class="ib" name="yon" value="1" <?= $i === count($sss) - 1 ? 'disabled' : '' ?> aria-label="Aşağı taşı">↓</button></form>
          <a class="b ghost sm" href="?s=sss&duzenle=<?= e(urlencode($x['id'])) ?>">Düzenle</a>
          <form method="post" onsubmit="return confirm('Bu soru silinsin mi?')"><?= csrf_field() ?><input type="hidden" name="a" value="sss_sil"><input type="hidden" name="id" value="<?= e($x['id']) ?>"><button class="b ghost sm danger">Sil</button></form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php elseif ($s === 'metinler'): $m = $d['metinler']; ?>
  <div class="head"><h1>Ana sayfa & metinler</h1></div>
  <section class="card form">
    <h2>Ana sayfa görselleri <small>(sırayla geçiş yapar)</small></h2>
    <div class="pics">
      <?php foreach ($d['hero'] as $i => $p): ?><div class="pic wide"><img src="<?= e(img_url($p)) ?>" alt="">
        <form method="post" class="row"><?= csrf_field() ?><input type="hidden" name="yol" value="<?= e($p) ?>">
          <button class="ib" name="a" value="hero_tasi" onclick="this.form.yon.value=-1" <?= $i === 0 ? 'disabled' : '' ?> aria-label="Öne al">←</button>
          <button class="ib" name="a" value="hero_tasi" onclick="this.form.yon.value=1" <?= $i === count($d['hero']) - 1 ? 'disabled' : '' ?> aria-label="Sona al">→</button>
          <input type="hidden" name="yon" value="0">
          <button class="b ghost sm danger" name="a" value="hero_sil" onclick="return confirm('Bu görsel kaldırılsın mı?')">Kaldır</button></form></div><?php endforeach; ?>
    </div>
    <form method="post" enctype="multipart/form-data" class="row"><?= csrf_field() ?><input type="hidden" name="a" value="hero_ekle"><input type="file" name="foto[]" accept="image/jpeg,image/png,image/webp" multiple required><button class="b red">Görsel ekle</button></form>
    <p class="hint">Yatay (geniş) fotoğraflar en iyi sonucu verir.</p>
  </section>
  <form method="post" class="card form">
    <?= csrf_field() ?><input type="hidden" name="a" value="metin_kaydet">
    <h2>Metinler</h2>
    <label>Başlık üstü küçük yazı<input name="hero_ust" value="<?= e($m['hero_ust']) ?>"></label>
    <label>Ana başlık<input name="hero_baslik" value="<?= e($m['hero_baslik']) ?>"></label>
    <label>Başlık altı yazı<textarea name="hero_alt" rows="2"><?= e($m['hero_alt']) ?></textarea></label>
    <label>Hakkımızda başlığı<textarea name="hakkimizda_baslik" rows="2"><?= e($m['hakkimizda_baslik']) ?></textarea></label>
    <label>Hakkımızda yazısı <small>(paragrafları boş satırla ayırın)</small><textarea name="hakkimizda" rows="8"><?= e($m['hakkimizda']) ?></textarea></label>
    <label>Referanslar açıklaması<input name="referans_alt" value="<?= e($m['referans_alt']) ?>"></label>
    <label>İletişim açıklaması<input name="iletisim_alt" value="<?= e($m['iletisim_alt']) ?>"></label>
    <div class="row"><button class="b red">Kaydet</button></div>
  </form>

<?php elseif ($s === 'iletisim'): $A = $d['ayarlar']; ?>
  <div class="head"><h1>İletişim bilgileri</h1></div>
  <form method="post" class="card form">
    <?= csrf_field() ?><input type="hidden" name="a" value="iletisim_kaydet">
    <label>Firma adı<input name="firma" value="<?= e($A['firma']) ?>"></label>
    <div class="g2"><label>Telefon 1<input name="telefon1" value="<?= e($A['telefon1']) ?>"></label><label>Telefon 2 <small>(boş bırakılabilir)</small><input name="telefon2" value="<?= e($A['telefon2']) ?>"></label></div>
    <label>WhatsApp numarası <small>(örn. 0534 833 09 23)</small><input name="whatsapp" value="<?= e($A['whatsapp']) ?>"></label>
    <label>Adres<input name="adres" value="<?= e($A['adres']) ?>"></label>
    <label>Çalışma saatleri <small>(boş bırakılırsa sitede görünmez)</small><input name="calisma" value="<?= e($A['calisma']) ?>" placeholder="Örn. Pazartesi – Cumartesi, 09:00 – 19:00"></label>
    <label>Google Haritalar bağlantısı <small>(yol tarifi butonu)</small><input name="harita" value="<?= e($A['harita']) ?>"></label>
    <label>Harita gömme adresi <small>(Google Haritalar → Paylaş → Harita yerleştir → src="…" içindeki adres)</small><input name="harita_embed" value="<?= e($A['harita_embed']) ?>"></label>
    <label>Instagram adresi<input name="instagram" value="<?= e($A['instagram']) ?>"></label>
    <div class="row"><button class="b red">Kaydet</button></div>
  </form>

<?php elseif ($s === 'mesajlar'): ?>
  <div class="head"><h1>Mesajlar <small><?= count($msgs) ?> mesaj, <?= $okunmamis ?> okunmamış</small></h1>
    <?php if ($okunmamis): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="a" value="msg_hepsi_okundu"><button class="b ghost">Tümünü okundu yap</button></form><?php endif; ?></div>
  <?php if (!$msgs): ?><div class="card empty">Henüz mesaj yok. Sitedeki teklif formundan gelen mesajlar burada görünecek.</div><?php endif; ?>
  <div class="msgs">
    <?php foreach ($msgs as $m): $wl = wa_link(tel_link($m['telefon']), 'Merhaba ' . $m['ad'] . ', 5M Premium\'dan yazıyorum. Teklif talebiniz için teşekkürler.'); ?>
      <article class="card msg <?= empty($m['okundu']) ? 'new' : '' ?>">
        <div class="msg-h"><b><?= e($m['ad']) ?></b><time><?= e(date('d.m.Y H:i', strtotime($m['tarih']))) ?></time></div>
        <div class="msg-m"><span><?= e($m['telefon']) ?></span><span><?= e($m['hizmet']) ?></span><?php if ($m['arac']): ?><span><?= e($m['arac']) ?></span><?php endif; ?></div>
        <?php if ($m['mesaj']): ?><p><?= nl2br(e($m['mesaj'])) ?></p><?php endif; ?>
        <div class="row">
          <a class="b red sm" href="tel:<?= e(tel_link($m['telefon'])) ?>">Ara</a>
          <a class="b wa sm" href="<?= e($wl) ?>" target="_blank" rel="noopener">WhatsApp</a>
          <?php if (empty($m['okundu'])): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="a" value="msg_okundu"><input type="hidden" name="id" value="<?= e($m['id']) ?>"><button class="b ghost sm">Okundu</button></form><?php endif; ?>
          <form method="post" onsubmit="return confirm('Mesaj silinsin mi?')"><?= csrf_field() ?><input type="hidden" name="a" value="msg_sil"><input type="hidden" name="id" value="<?= e($m['id']) ?>"><button class="b ghost sm danger">Sil</button></form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

<?php elseif ($s === 'ayarlar'): ?>
  <div class="head"><h1>Ayarlar</h1></div>
  <form method="post" class="card form">
    <?= csrf_field() ?><input type="hidden" name="a" value="sifre">
    <h2>Şifre değiştir</h2>
    <label>Mevcut şifre<input type="password" name="eski" required autocomplete="current-password"></label>
    <div class="g2"><label>Yeni şifre <small>(en az 8 karakter)</small><input type="password" name="yeni" required minlength="8" autocomplete="new-password"></label>
    <label>Yeni şifre (tekrar)<input type="password" name="yeni2" required minlength="8" autocomplete="new-password"></label></div>
    <div class="row"><button class="b red">Şifreyi değiştir</button></div>
  </form>
  <section class="card form">
    <h2>Yedek</h2>
    <p class="hint">Sitedeki tüm metin, hizmet ve referans bilgilerini tek dosya olarak indirin. Fotoğraflar <code>uploads</code> klasöründe durur.</p>
    <div class="row"><a class="b ghost" href="?s=yedek">Yedeği indir</a></div>
    <form method="post" enctype="multipart/form-data" class="row" onsubmit="return confirm('Mevcut içerik yedekteki içerikle değiştirilecek. Devam edilsin mi?')"><?= csrf_field() ?><input type="hidden" name="a" value="geri_yukle"><input type="file" name="yedek" accept="application/json,.json" required><button class="b ghost">Yedekten geri yükle</button></form>
  </section>
<?php endif; ?>
</main>
</body></html>
