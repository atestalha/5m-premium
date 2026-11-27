{
const V = JSON.parse(document.getElementById('veri').textContent);
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const wa = t => 'https://wa.me/' + V.wa + '?text=' + encodeURIComponent(t);
const icon = n => `<svg class="ic" aria-hidden="true"><use href="#i-${n}"/></svg>`;
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

/* header */
const top = $('#top');
const onScroll = () => top.classList.toggle('solid', scrollY > 40);
addEventListener('scroll', onScroll, {passive: true}); onScroll();
const burger = $('#burger'), nav = $('#nav');
burger.addEventListener('click', () => {
  const o = nav.classList.toggle('open');
  burger.setAttribute('aria-expanded', o);
  top.classList.add('solid');
});
$$('#nav a').forEach(a => a.addEventListener('click', () => { nav.classList.remove('open'); burger.setAttribute('aria-expanded', 'false'); }));

/* hero slideshow */
const slides = $$('#heroBg img');
if (slides.length > 1 && !reduce) {
  let i = 0;
  setInterval(() => { slides[i].classList.remove('on'); i = (i + 1) % slides.length; slides[i].classList.add('on'); }, 6000);
}

/* dialogs */
const openDlg = d => { if (typeof d.showModal === 'function') d.showModal(); else d.setAttribute('open', ''); };
const closeDlg = d => { if (d.close) d.close(); else d.removeAttribute('open'); };
$$('.dlg').forEach(d => d.addEventListener('click', e => { if (e.target === d || e.target.closest('[data-close]')) closeDlg(d); }));

/* service detail */
const svcDlg = $('#svcDlg'), svcBody = $('#svcBody');
const norm = s => s.toLocaleLowerCase('tr').replace(/\s*\(.*\)$/, '').replace(/^profesyonel /, '').trim();
function openService(id) {
  const h = V.hizmetler.find(x => x.id === id); if (!h) return;
  const key = norm(h.baslik);
  const rel = V.referanslar.filter(r => r.gorseller?.length && r.islemler.some(t => {
    const a = norm(t); return a.startsWith(key.slice(0, 6)) || key.startsWith(a.slice(0, 6));
  })).slice(0, 8);
  const items = (h.maddeler || []).filter(Boolean);
  svcBody.innerHTML =
    (h.gorsel ? `<div class="d-img"><img src="${esc(h.gorsel)}" alt="${esc(h.baslik)}"></div>` : '') +
    `<div class="d-body"><small>${esc(h.grup)}</small><h3 id="svcT">${esc(h.baslik)}</h3><p>${esc(h.detay)}</p>` +
    (items.length ? `<ul class="d-list">${items.map(m => `<li>${esc(m)}</li>`).join('')}</ul>` : '') +
    (rel.length ? `<div><p style="font-size:14px;margin-bottom:10px;color:rgba(255,255,255,.6)">Bu hizmeti uyguladığımız araçlar</p><div class="d-refs">${rel.map(r => `<button data-ref="${esc(r.id)}" aria-label="${esc(r.arac)}"><img src="${esc(r.gorseller[0])}" alt="" loading="lazy"></button>`).join('')}</div></div>` : '') +
    `<div class="d-btns"><a class="btn red" target="_blank" rel="noopener" href="${esc(wa('Merhaba, ' + h.baslik + ' hakkında bilgi ve fiyat almak istiyorum. Aracım: '))}">${icon('wa')}Bu hizmet için teklif alın</a><a class="btn ghost" href="#iletisim" data-close data-pick="${esc(h.baslik)}">Form ile yazın</a></div></div>`;
  openDlg(svcDlg);
}
document.addEventListener('click', e => {
  const s = e.target.closest('[data-hizmet]');
  if (s) { e.preventDefault(); openService(s.dataset.hizmet); return; }
  const r = e.target.closest('[data-ref]');
  if (r) { e.preventDefault(); if (svcDlg.open) closeDlg(svcDlg); openRef(r.dataset.ref); return; }
  const p = e.target.closest('[data-pick]');
  if (p) { const sel = $('#fHizmet'); [...sel.options].forEach(o => { if (o.text === p.dataset.pick) sel.value = o.value; }); }
});

/* references filter */
const chips = $('#chips'), cards = $$('.ref');
const tags = ['Tümü', ...new Set(V.referanslar.filter(r => r.gorseller?.length).flatMap(r => r.islemler))];
chips.innerHTML = tags.map((t, i) => `<button class="chip" aria-pressed="${i === 0}">${esc(t)}</button>`).join('');
chips.addEventListener('click', e => {
  const b = e.target.closest('.chip'); if (!b) return;
  $$('.chip', chips).forEach(c => c.setAttribute('aria-pressed', c === b));
  const t = b.textContent;
  cards.forEach(c => c.classList.toggle('hide', t !== 'Tümü' && !c.dataset.tags.split('|').includes(t)));
});

/* lightbox */
const lb = $('#lb'), track = $('#lbTrack'), cap = $('#lbCap'), prev = $('#lbPrev'), next = $('#lbNext');
function openRef(id) {
  const r = V.referanslar.find(x => x.id === id); if (!r) return;
  track.innerHTML = r.gorseller.map((g, i) => `<figure><img src="${esc(g)}" alt="${esc(r.arac)} – fotoğraf ${i + 1}"></figure>`).join('');
  cap.innerHTML = `${esc(r.arac)}<span>${esc(r.islemler.join(' · '))}</span>`;
  prev.hidden = next.hidden = r.gorseller.length < 2;
  openDlg(lb); track.scrollLeft = 0;
}
const go = d => track.scrollBy({left: d * track.clientWidth, behavior: reduce ? 'auto' : 'smooth'});
prev.addEventListener('click', () => go(-1));
next.addEventListener('click', () => go(1));
lb.addEventListener('keydown', e => { if (e.key === 'ArrowLeft') go(-1); if (e.key === 'ArrowRight') go(1); });

/* faq: one open at a time */
$$('.faq details').forEach(d => d.addEventListener('toggle', () => { if (d.open) $$('.faq details').forEach(o => { if (o !== d) o.open = false; }); }));

/* contact form */
const form = $('#cform'), msg = $('#fmsg');
form.addEventListener('submit', async e => {
  e.preventDefault();
  const f = Object.fromEntries(new FormData(form));
  let bad = false;
  ['ad', 'telefon'].forEach(n => { const el = form.elements[n]; const b = !String(f[n] || '').trim(); el.classList.toggle('bad', b); bad ||= b; });
  if (bad) { msg.className = 'f-msg on'; msg.textContent = 'Lütfen adınızı ve telefon numaranızı yazın.'; return; }
  const text = `Merhaba, web sitenizden yazıyorum.\nAd soyad: ${f.ad}\nTelefon: ${f.telefon}\nHizmet: ${f.hizmet}\nAraç: ${f.arac || '-'}${f.mesaj ? '\n' + f.mesaj : ''}`;
  const btn = form.querySelector('button[type=submit]'); btn.disabled = true;
  let ok = false;
  try {
    const r = await fetch('iletisim.php', {method: 'POST', body: new FormData(form), headers: {'Accept': 'application/json'}});
    ok = r.ok && (await r.json()).ok;
  } catch (_) { ok = false; }
  btn.disabled = false;
  if (ok) {
    msg.className = 'f-msg on ok';
    msg.innerHTML = `Teşekkürler, mesajınız bize ulaştı. En kısa sürede sizi arayacağız. Beklemek istemezseniz <a href="${esc(wa(text))}" target="_blank" rel="noopener">WhatsApp'tan da yazabilirsiniz →</a>`;
    form.reset();
  } else {
    msg.className = 'f-msg on';
    msg.innerHTML = `Mesajınız hazır. <a href="${esc(wa(text))}" target="_blank" rel="noopener">WhatsApp'ta açıp gönderin →</a>`;
  }
});

/* reveal on scroll */
if ('IntersectionObserver' in window && !reduce) {
  const els = $$('.sec-head, .svc, .ref, .steps li, .about-pic, .about-txt, .faq details, .c-card, .c-form');
  els.forEach(el => el.classList.add('rv'));
  const io = new IntersectionObserver(es => es.forEach(x => { if (x.isIntersecting) { x.target.classList.add('in'); io.unobserve(x.target); } }), {rootMargin: '0px 0px -8% 0px'});
  els.forEach(el => io.observe(el));
}
}
