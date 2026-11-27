<?php
require __DIR__ . '/lib.php';
$S = site();
$A = $S['ayarlar'];
$M = $S['metinler'];
$hizmetler = $S['hizmetler'];
$refs = $S['referanslar'];
$sss = $S['sss'] ?? [];
$wa = wa_link($A['whatsapp'], 'Merhaba, web sitenizden yazıyorum. Aracım için bilgi almak istiyorum.');
$ikon = fn($n) => '<svg class="ic" aria-hidden="true"><use href="#i-' . e($n) . '"/></svg>';
$v = '?v=' . (string)@filemtime(__DIR__ . '/assets/style.css');
?><!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($A['firma']) ?> | PPF, Cam Filmi, Seramik Kaplama · Konya</title>
<meta name="description" content="<?= e($A['firma']) ?> — Konya Karatay'da PPF kaplama, cam filmi, renk değişimi, seramik kaplama, pasta cila ve profesyonel araç yıkama. Auto İsmail'in 10 yıllık tecrübesiyle.">
<meta property="og:title" content="<?= e($A['firma']) ?>">
<meta property="og:description" content="Konya Karatay'da PPF kaplama, cam filmi, seramik kaplama ve renk değişimi.">
<meta property="og:image" content="<?= e($S['hero'][0] ?? '') ?>">
<meta name="theme-color" content="#0b0b0c">
<link rel="icon" type="image/png" href="assets/img/site/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Saira:ital,wght@0,500;0,700;0,800;1,700;1,800;1,900&family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="assets/style.css<?= $v ?>">
<script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@type' => 'AutoBodyShop', 'name' => $A['firma'], 'telephone' => tel_link($A['telefon1']), 'address' => ['@type' => 'PostalAddress', 'streetAddress' => $A['adres'], 'addressLocality' => 'Konya', 'addressCountry' => 'TR'], 'sameAs' => [$A['instagram']]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
</head>
<body>
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="i-kalkan" viewBox="0 0 24 24"><path d="M12 2.5 4.5 5.4v6.1c0 4.6 3.1 8.6 7.5 10 4.4-1.4 7.5-5.4 7.5-10V5.4L12 2.5Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m8.6 12 2.4 2.4 4.6-4.7" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></symbol>
  <symbol id="i-gunes" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M12 2.5v2.2M12 19.3v2.2M2.5 12h2.2M19.3 12h2.2M5.3 5.3l1.6 1.6M17.1 17.1l1.6 1.6M5.3 18.7l1.6-1.6M17.1 6.9l1.6-1.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>
  <symbol id="i-renk" viewBox="0 0 24 24"><path d="M12 3.2c-4.9 0-8.8 3.7-8.8 8.3 0 4.4 3.6 8.3 8 8.3 1.3 0 1.9-.7 1.9-1.6 0-1.2-1-1.4-1-2.5 0-1 .8-1.6 1.8-1.6h2.1c2.7 0 4.8-1.9 4.8-4.6 0-3.6-3.9-6.3-8.8-6.3Z" fill="none" stroke="currentColor" stroke-width="1.7"/><circle cx="7.6" cy="11.2" r="1.3" fill="currentColor"/><circle cx="10.6" cy="7.4" r="1.3" fill="currentColor"/><circle cx="15.2" cy="7.6" r="1.3" fill="currentColor"/></symbol>
  <symbol id="i-mobilya" viewBox="0 0 24 24"><rect x="3.5" y="4" width="17" height="13" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M12 4v13M3.5 10.5h17M9.5 7.3v.1M14.5 7.3v.1M9.5 13.6v.1M14.5 13.6v.1M6 17v3M18 17v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>
  <symbol id="i-elmas" viewBox="0 0 24 24"><path d="M6.5 4h11l3.5 5-9 11-9-11 3.5-5Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M3 9h18M9.5 4 8 9l4 11 4-11-1.5-5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></symbol>
  <symbol id="i-parlak" viewBox="0 0 24 24"><path d="M12 3.5 13.7 9l5.8 1.5-5.8 1.6L12 17.5l-1.7-5.4-5.8-1.6L10.3 9 12 3.5Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M18.5 15.5l.7 2 2 .7-2 .7-.7 2-.7-2-2-.7 2-.7.7-2ZM5 16l.5 1.4 1.4.5-1.4.5L5 19.8l-.5-1.4-1.4-.5 1.4-.5L5 16Z" fill="currentColor"/></symbol>
  <symbol id="i-buhar" viewBox="0 0 24 24"><path d="M8 3c-1.5 1.5 1.5 3 0 4.5M12 3c-1.5 1.5 1.5 3 0 4.5M16 3c-1.5 1.5 1.5 3 0 4.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 11h16l-1.4 8.1a2 2 0 0 1-2 1.7H7.4a2 2 0 0 1-2-1.7L4 11Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></symbol>
  <symbol id="i-damla" viewBox="0 0 24 24"><path d="M12 3s6.5 7 6.5 11.3A6.5 6.5 0 0 1 5.5 14.3C5.5 10 12 3 12 3Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 14.5a3 3 0 0 0 3 3" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>
  <symbol id="i-tel" viewBox="0 0 24 24"><path d="M6.6 3.5h2.6l1.4 4.2-2 1.4a12 12 0 0 0 6.3 6.3l1.4-2 4.2 1.4v2.6a2 2 0 0 1-2.2 2A16.6 16.6 0 0 1 4.6 5.7a2 2 0 0 1 2-2.2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></symbol>
  <symbol id="i-wa" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.3c0-.1-.2-.2-.4-.3z"/></symbol>
  <symbol id="i-konum" viewBox="0 0 24 24"><path d="M12 21.5s7-6.3 7-12a7 7 0 0 0-14 0c0 5.7 7 12 7 12Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="9.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/></symbol>
  <symbol id="i-insta" viewBox="0 0 24 24"><rect x="3.5" y="3.5" width="17" height="17" rx="5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/></symbol>
  <symbol id="i-ok" viewBox="0 0 24 24"><path d="M5 12h13M13 6.5 18.5 12 13 17.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></symbol>
  <symbol id="i-takvim" viewBox="0 0 24 24"><rect x="3.5" y="5" width="17" height="15.5" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 10h17M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>
  <symbol id="i-buyutec" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="m15.5 15.5 5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>
  <symbol id="i-anahtar" viewBox="0 0 24 24"><circle cx="8" cy="15" r="4.5" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="m11.2 11.8 8.3-8.3M16.5 6.5l2.5 2.5M14 9l2 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>
  <symbol id="i-foto" viewBox="0 0 24 24"><rect x="3" y="5.5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12.5" r="3.5" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M8.5 5.5 10 3.5h4l1.5 2" fill="none" stroke="currentColor" stroke-width="1.7"/></symbol>
  <symbol id="i-arti" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></symbol>
  <symbol id="hex" viewBox="0 0 400 346"><g fill="none" stroke="currentColor"><path d="M100 3h200l97 170-97 170H100L3 173z" stroke-width="5"/><path d="M128 52h144l72 121-72 121H128L56 173z" stroke-width="4.5"/><path d="M155 98h90l45 75-45 75h-90l-45-75z" stroke-width="4"/><path d="M177 135h46l22 38-22 38h-46l-22-38z" stroke-width="3.5"/></g></symbol>
</svg>

<header class="top" id="top">
  <div class="top-in">
    <a class="brand" href="#" aria-label="5M Premium ana sayfa"><img src="assets/img/site/logo-5m.png" alt="5M Premium" width="900" height="148"></a>
    <nav class="nav" id="nav" aria-label="Ana menü">
      <a href="#hizmetler">Hizmetler</a>
      <a href="#referanslar">Referanslar</a>
      <a href="#hakkimizda">Hakkımızda</a>
      <?php if ($sss): ?><a href="#sss">SSS</a><?php endif; ?>
      <a href="#iletisim">İletişim</a>
    </nav>
    <div class="top-cta">
      <a class="btn ghost sm hide-s" href="tel:<?= e(tel_link($A['telefon1'])) ?>"><?= $ikon('tel') ?><span><?= e($A['telefon1']) ?></span></a>
      <a class="btn red sm" href="<?= e($wa) ?>" target="_blank" rel="noopener"><?= $ikon('wa') ?><span>WhatsApp</span></a>
      <button class="burger" id="burger" aria-label="Menüyü aç" aria-expanded="false" aria-controls="nav"><span></span><span></span></button>
    </div>
  </div>
</header>

<section class="hero" aria-label="Giriş">
  <div class="hero-bg" id="heroBg">
    <?php foreach ($S['hero'] as $i => $img): ?>
      <img src="<?= e($img) ?>" alt="" class="<?= $i === 0 ? 'on' : '' ?>" <?= $i ? 'loading="lazy"' : 'fetchpriority="high"' ?>>
    <?php endforeach; ?>
  </div>
  <svg class="hero-hex" aria-hidden="true"><use href="#hex"/></svg>
  <div class="hero-in">
    <p class="kicker"><span class="dot"></span><?= e($M['hero_ust']) ?></p>
    <h1><?= e($M['hero_baslik']) ?></h1>
    <p class="lead"><?= e($M['hero_alt']) ?></p>
    <div class="hero-btns">
      <a class="btn red" href="<?= e($wa) ?>" target="_blank" rel="noopener"><?= $ikon('wa') ?>WhatsApp'tan teklif alın</a>
      <a class="btn light" href="#referanslar">Yaptığımız işler <?= $ikon('ok') ?></a>
    </div>
  </div>
  <div class="hero-strip">
    <div class="strip-in">
      <?php foreach ($hizmetler as $h): ?><a href="#hizmet-<?= e($h['id']) ?>" data-hizmet="<?= e($h['id']) ?>"><?= $ikon($h['ikon']) ?><?= e($h['baslik']) ?></a><?php endforeach; ?>
    </div>
  </div>
</section>

<section class="band light" id="hizmetler">
  <div class="wrap">
    <div class="sec-head">
      <h2>Hizmetlerimiz</h2>
      <p>Kaplamadan temizliğe, aracınızın görünüşü ve değeri için ihtiyaç duyduğu her şey tek adreste. Detayını görmek istediğiniz hizmete tıklayın.</p>
    </div>
    <div class="svc-grid">
      <?php foreach ($hizmetler as $h): ?>
        <button class="svc" id="hizmet-<?= e($h['id']) ?>" data-hizmet="<?= e($h['id']) ?>">
          <span class="svc-img<?= $h['gorsel'] ? '' : ' empty' ?>">
            <?php if ($h['gorsel']): ?><img src="<?= e($h['gorsel']) ?>" alt="<?= e($h['baslik']) ?>" loading="lazy">
            <?php else: ?><svg class="svc-hex" aria-hidden="true"><use href="#hex"/></svg><?php endif; ?>
            <span class="svc-ic"><?= $ikon($h['ikon']) ?></span>
          </span>
          <span class="svc-body">
            <span class="svc-g"><?= e($h['grup']) ?></span>
            <span class="svc-t"><?= e($h['baslik']) ?></span>
            <span class="svc-d"><?= e($h['kisa']) ?></span>
            <span class="svc-more">Detaylı bilgi <?= $ikon('ok') ?></span>
          </span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="band dark" id="referanslar">
  <div class="wrap">
    <div class="sec-head">
      <h2>Referanslarımız</h2>
      <p><?= e($M['referans_alt']) ?></p>
    </div>
    <div class="chips" id="chips" role="toolbar" aria-label="Hizmete göre filtrele"></div>
    <div class="ref-grid" id="refGrid">
      <?php foreach ($refs as $r): if (empty($r['gorseller'])) continue; ?>
        <button class="ref" data-ref="<?= e($r['id']) ?>" data-tags="<?= e(implode('|', $r['islemler'])) ?>">
          <img src="<?= e($r['gorseller'][0]) ?>" alt="<?= e($r['arac']) ?>" loading="lazy">
          <?php if (count($r['gorseller']) > 1): ?><span class="ref-n"><?= $ikon('foto') ?><?= count($r['gorseller']) ?></span><?php endif; ?>
          <span class="ref-cap"><span class="ref-t"><?= e($r['arac']) ?></span><span class="ref-tags"><?= e(implode(' · ', $r['islemler'])) ?></span></span>
        </button>
      <?php endforeach; ?>
    </div>
    <p class="ref-insta"><a href="<?= e($A['instagram']) ?>" target="_blank" rel="noopener"><?= $ikon('insta') ?>Daha fazlası için Instagram'da <b>@5m_premium</b></a></p>
  </div>
</section>

<section class="band light" id="surec">
  <div class="wrap">
    <div class="sec-head">
      <h2>Aracınız bizde nasıl ilerliyor?</h2>
      <p>Her uygulamada aynı düzenle çalışıyoruz; ne yapılacağını, ne kadar süreceğini baştan bilirsiniz.</p>
    </div>
    <ol class="steps">
      <li><span class="st-ic"><?= $ikon('takvim') ?></span><b>Randevu</b><span>Telefon ya da WhatsApp'tan aracınızı ve istediğiniz hizmeti yazın, size uygun günü belirleyelim.</span></li>
      <li><span class="st-ic"><?= $ikon('buyutec') ?></span><b>Ön kontrol</b><span>Aracınızı ışıklı alanda inceliyor, boyadaki çizik ve hasarları sizinle birlikte not alıyoruz.</span></li>
      <li><span class="st-ic"><?= $ikon('kalkan') ?></span><b>Uygulama</b><span>Yüzey hazırlığı ve temizliğin ardından seçilen kaplama ya da işlem özenle uygulanıyor.</span></li>
      <li><span class="st-ic"><?= $ikon('anahtar') ?></span><b>Kontrol ve teslim</b><span>Teslimden önce her paneli birlikte kontrol ediyor, bakım önerilerimizi anlatıyoruz.</span></li>
    </ol>
  </div>
</section>

<section class="band dark about" id="hakkimizda">
  <div class="wrap about-in">
    <figure class="about-pic">
      <img src="assets/img/site/dukkan.jpg" alt="5M Premium ve Auto İsmail dükkânı, Karatay / Konya" loading="lazy">
    </figure>
    <div class="about-txt">
      <h2><?= e($M['hakkimizda_baslik']) ?></h2>
      <div class="lineage" aria-label="Auto İsmail Galeri'den 5M Premium'a">
        <img src="assets/img/site/logo-auto-ismail.png" alt="Auto İsmail" loading="lazy">
        <span class="lin-ar"><?= $ikon('ok') ?></span>
        <img src="assets/img/site/logo-5m.png" alt="5M Premium" loading="lazy">
      </div>
      <?= paragraphs($M['hakkimizda']) ?>
      <ul class="facts">
        <li><b>10 yıl</b><span>sektör tecrübesi</span></li>
        <li><b><?= count($hizmetler) ?></b><span>farklı hizmet</span></li>
        <li><b>Karatay</b><span>Konya'daki uygulama stüdyomuz</span></li>
      </ul>
    </div>
  </div>
</section>

<?php if ($sss): ?>
<section class="band light faq" id="sss">
  <div class="wrap">
    <div class="sec-head">
      <h2>Sık sorulan sorular</h2>
      <p>Aklınıza takılan başka bir şey olursa WhatsApp'tan yazmanız yeterli.</p>
    </div>
    <div class="faq-list">
      <?php foreach ($sss as $q): ?>
        <details><summary><span><?= e($q['soru']) ?></span><span class="faq-ic"><?= $ikon('arti') ?></span></summary><div class="faq-a"><?= paragraphs($q['cevap']) ?></div></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="band light contact" id="iletisim">
  <div class="wrap">
    <div class="sec-head">
      <h2>Randevu ve fiyat bilgisi</h2>
      <p><?= e($M['iletisim_alt']) ?></p>
    </div>
    <div class="contact-grid">
      <div class="c-cards">
        <a class="c-card wa" href="<?= e($wa) ?>" target="_blank" rel="noopener"><?= $ikon('wa') ?><span><small>WhatsApp</small><b><?= e($A['telefon1']) ?></b></span></a>
        <a class="c-card" href="tel:<?= e(tel_link($A['telefon1'])) ?>"><?= $ikon('tel') ?><span><small>Telefon</small><b><?= e($A['telefon1']) ?></b></span></a>
        <?php if (!empty($A['telefon2'])): ?><a class="c-card" href="tel:<?= e(tel_link($A['telefon2'])) ?>"><?= $ikon('tel') ?><span><small>Telefon</small><b><?= e($A['telefon2']) ?></b></span></a><?php endif; ?>
        <a class="c-card" href="<?= e($A['harita']) ?>" target="_blank" rel="noopener"><?= $ikon('konum') ?><span><small>Adres · yol tarifi</small><b><?= e($A['adres']) ?></b></span></a>
        <a class="c-card" href="<?= e($A['instagram']) ?>" target="_blank" rel="noopener"><?= $ikon('insta') ?><span><small>Instagram</small><b>@5m_premium</b></span></a>
        <?php if (!empty($A['calisma'])): ?><div class="c-card"><?= $ikon('takvim') ?><span><small>Çalışma saatleri</small><b><?= e($A['calisma']) ?></b></span></div><?php endif; ?>
      </div>
      <form class="c-form" id="cform" novalidate>
        <h3>Teklif formu</h3>
        <div class="row2">
          <label>Ad soyad<input name="ad" required autocomplete="name" maxlength="80"></label>
          <label>Telefon<input name="telefon" type="tel" required autocomplete="tel" inputmode="tel" maxlength="30"></label>
        </div>
        <div class="row2">
          <label>Hizmet<select name="hizmet" id="fHizmet"><?php foreach ($hizmetler as $h): ?><option><?= e($h['baslik']) ?></option><?php endforeach; ?><option>Diğer</option></select></label>
          <label>Araç marka / model<input name="arac" maxlength="80" placeholder="Örn. Volkswagen Passat 2021"></label>
        </div>
        <label>Mesajınız<textarea name="mesaj" rows="4" maxlength="1500" placeholder="İstediğiniz uygulama, uygun olduğunuz gün…"></textarea></label>
        <input type="text" name="web" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
        <button class="btn red" type="submit">Gönder <?= $ikon('ok') ?></button>
        <div class="f-msg" id="fmsg" role="status"></div>
      </form>
    </div>
  </div>
  <div class="map">
    <iframe src="<?= e($A['harita_embed']) ?>" title="5M Premium konumu" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    <a class="btn red map-btn" href="<?= e($A['harita']) ?>" target="_blank" rel="noopener"><?= $ikon('konum') ?>Yol tarifi al</a>
  </div>
</section>

<footer class="foot">
  <div class="wrap">
    <div class="f-grid">
      <div class="f-brand">
        <img src="assets/img/site/logo-5m.png" alt="5M Premium" loading="lazy">
        <p>Araç kaplama merkezi. PPF kaplama, cam filmi, renk değişimi, seramik kaplama, pasta cila ve profesyonel yıkama.</p>
        <img class="f-ai" src="assets/img/site/logo-auto-ismail.png" alt="Auto İsmail" loading="lazy">
      </div>
      <div><h4>Menü</h4><a href="#hizmetler">Hizmetler</a><a href="#referanslar">Referanslar</a><a href="#hakkimizda">Hakkımızda</a><?php if ($sss): ?><a href="#sss">Sık sorulanlar</a><?php endif; ?><a href="#iletisim">İletişim</a></div>
      <div><h4>Hizmetler</h4><?php foreach (array_slice($hizmetler, 0, 6) as $h): ?><a href="#hizmet-<?= e($h['id']) ?>" data-hizmet="<?= e($h['id']) ?>"><?= e($h['baslik']) ?></a><?php endforeach; ?></div>
      <div><h4>İletişim</h4><a href="tel:<?= e(tel_link($A['telefon1'])) ?>"><?= e($A['telefon1']) ?></a><?php if (!empty($A['telefon2'])): ?><a href="tel:<?= e(tel_link($A['telefon2'])) ?>"><?= e($A['telefon2']) ?></a><?php endif; ?><a href="<?= e($A['harita']) ?>" target="_blank" rel="noopener"><?= e($A['adres']) ?></a><a href="<?= e($A['instagram']) ?>" target="_blank" rel="noopener">Instagram</a></div>
    </div>
    <div class="f-bot">
      <span>© <?= date('Y') ?> <?= e($A['firma']) ?>. Tüm hakları saklıdır.</span>
      <span>Tasarım: Gürlek Medya - Abdullah Talha Ateş</span>
      <a href="admin/" rel="nofollow">Yönetim</a>
    </div>
  </div>
</footer>

<div class="float">
  <a class="fl-tel" href="tel:<?= e(tel_link($A['telefon1'])) ?>" aria-label="Hemen arayın"><?= $ikon('tel') ?><span>Ara</span></a>
  <a class="fl-wa" href="<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın"><?= $ikon('wa') ?><span>WhatsApp</span></a>
</div>

<dialog class="dlg" id="svcDlg" aria-labelledby="svcT">
  <button class="x" data-close aria-label="Kapat">×</button>
  <div id="svcBody"></div>
</dialog>
<dialog class="dlg lb" id="lb" aria-label="Referans fotoğrafları">
  <button class="x" data-close aria-label="Kapat">×</button>
  <div class="lb-track" id="lbTrack"></div>
  <button class="lb-nav prev" id="lbPrev" aria-label="Önceki fotoğraf">‹</button>
  <button class="lb-nav next" id="lbNext" aria-label="Sonraki fotoğraf">›</button>
  <div class="lb-cap" id="lbCap"></div>
</dialog>

<script id="veri" type="application/json"><?= json_encode(['hizmetler' => $hizmetler, 'referanslar' => $refs, 'wa' => $A['whatsapp']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script src="assets/app.js<?= $v ?>"></script>
</body>
</html>
