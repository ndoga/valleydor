/* Valley d'Or Padel – interazioni */
(() => {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = (s, el = document) => el.querySelector(s);
  const $$ = (s, el = document) => [...el.querySelectorAll(s)];

  /* ---------- header: sfondo allo scorrimento, si nasconde scendendo ---------- */
  const header = $('.header');
  const fab = $('.fab');
  let lastY = 0;
  const onScroll = () => {
    const y = window.scrollY;
    header.classList.toggle('is-scrolled', y > 40);
    header.classList.toggle('is-hidden', y > 400 && y > lastY && !nav.classList.contains('is-open'));
    fab.classList.toggle('is-visible', y > window.innerHeight * .8);
    lastY = y;
  };

  /* ---------- menu mobile ---------- */
  const nav = $('#nav');
  const burger = $('#burger');
  const toggleMenu = (open) => {
    nav.classList.toggle('is-open', open);
    burger.setAttribute('aria-expanded', open);
    burger.setAttribute('aria-label', open ? 'Chiudi il menu' : 'Apri il menu');
    document.body.style.overflow = open ? 'hidden' : '';
  };
  burger.addEventListener('click', () => toggleMenu(!nav.classList.contains('is-open')));
  $$('a', nav).forEach(a => a.addEventListener('click', () => toggleMenu(false)));

  /* ---------- voce di menu attiva ---------- */
  const links = $$('.nav a[href^="#"]:not([data-finestra])'); // le voci che aprono una finestra non sono sezioni
  const sections = links.map(a => $(a.getAttribute('href')));
  const spy = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      links.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === '#' + e.target.id));
    });
  }, { rootMargin: '-45% 0px -50% 0px' });
  sections.forEach(s => s && spy.observe(s));

  /* ---------- comparsa degli elementi ---------- */
  const reveals = $$('.reveal');
  if (reduce || !('IntersectionObserver' in window)) {
    reveals.forEach(el => el.classList.add('is-in'));
  } else {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        // piccolo ritardo a cascata tra elementi fratelli
        const siblings = $$('.reveal', e.target.parentElement);
        e.target.style.transitionDelay = Math.min(siblings.indexOf(e.target), 6) * 90 + 'ms';
        e.target.classList.add('is-in');
        io.unobserve(e.target);
      });
    }, { threshold: .15, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach(el => io.observe(el));
  }

  /* ---------- contatori ---------- */
  const counters = $$('[data-count]');
  const runCounter = (el) => {
    const target = +el.dataset.count;
    const prefix = el.dataset.prefix || '';
    const suffix = el.dataset.suffix || '';
    if (reduce) { el.textContent = prefix + target + suffix; return; }
    const dur = 1600;
    const t0 = performance.now();
    const tick = (t) => {
      const p = Math.min((t - t0) / dur, 1);
      const eased = 1 - Math.pow(1 - p, 4);
      el.textContent = prefix + Math.round(target * eased) + suffix;
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };
  const co = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { runCounter(e.target); co.unobserve(e.target); } });
  }, { threshold: .6 });
  counters.forEach(el => co.observe(el));

  /* ---------- parallasse leggera ---------- */
  const para = $$('[data-parallax]');
  const parallax = () => {
    para.forEach(el => {
      const r = el.parentElement.getBoundingClientRect();
      if (r.bottom < 0 || r.top > window.innerHeight) return;
      el.style.transform = `translate3d(0, ${-r.top * +el.dataset.parallax}px, 0)`;
    });
  };

  /* ---------- schede prezzi in 3D al passaggio del mouse ---------- */
  if (!reduce && window.matchMedia('(hover: hover)').matches) {
    $$('[data-tilt]').forEach(card => {
      card.addEventListener('mousemove', (e) => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - .5;
        const y = (e.clientY - r.top) / r.height - .5;
        card.style.transform = `perspective(900px) rotateY(${x * 10}deg) rotateX(${-y * 10}deg) translateY(-6px)`;
      });
      card.addEventListener('mouseleave', () => { card.style.transform = ''; });
    });
  }

  /* ---------- sponsor: duplica la fila per lo scorrimento infinito ---------- */
  const track = $('#sponsorTrack');
  if (track) {
    $$('img', track).forEach(img => {
      const c = img.cloneNode();
      c.alt = '';
      c.setAttribute('aria-hidden', 'true');
      track.appendChild(c);
    });
  }

  /* ---------- galleria con lightbox ---------- */
  const items = $$('.gallery__item');
  const lb = $('#lightbox');
  const lbImg = $('img', lb);
  const lbVideo = $('.lightbox__video', lb);
  let current = 0;
  const show = (i) => {
    current = (i + items.length) % items.length;
    const it = items[current];
    const video = it.dataset.tipo === 'video';
    // foto o video: si mostra uno dei due
    lbImg.hidden = video;
    if (lbVideo) {
      lbVideo.pause();
      lbVideo.hidden = !video;
      if (video) {
        lbVideo.src = it.getAttribute('href');
        lbVideo.poster = it.dataset.poster || '';
        lbVideo.setAttribute('aria-label', it.dataset.alt || 'Video');
        lbVideo.play().catch(() => {}); // senza audio bloccato: parte al primo tocco
      } else {
        lbVideo.removeAttribute('src');
      }
    }
    if (!video) {
      lbImg.src = it.getAttribute('href');
      lbImg.alt = it.dataset.alt || '';
    }
    const el = video ? lbVideo : lbImg;
    // riavvia l'animazione d'ingresso
    el.style.animation = 'none'; void el.offsetWidth; el.style.animation = '';
  };
  let lastFocus = null;
  const openLb = (i) => { lastFocus = document.activeElement; show(i); lb.hidden = false; document.body.style.overflow = 'hidden'; $('.lightbox__close', lb).focus(); };
  const closeLb = () => {
    if (lbVideo) { lbVideo.pause(); lbVideo.removeAttribute('src'); lbVideo.load(); }
    lb.hidden = true; document.body.style.overflow = ''; (items[current].hidden ? lastFocus : items[current]).focus();
  };

  /* ---------- video della galleria: partono da soli, senza audio, quando sono visibili ---------- */
  const anteprime = $$('.gallery__item--video video');
  if (anteprime.length && 'IntersectionObserver' in window && !reduce) {
    const vo = new IntersectionObserver(entries => entries.forEach(e => {
      const v = e.target;
      if (e.isIntersecting) { v.preload = 'auto'; v.play().catch(() => {}); } else { v.pause(); }
    }), { threshold: .35 });
    anteprime.forEach(v => vo.observe(v));
  }
  items.forEach((a, i) => a.addEventListener('click', (e) => { e.preventDefault(); openLb(i); }));
  $$('[data-open-gallery]').forEach(b => b.addEventListener('click', () => openLb(+b.dataset.openGallery)));
  $('.lightbox__close', lb).addEventListener('click', closeLb);
  $('.lightbox__prev', lb).addEventListener('click', () => show(current - 1));
  $('.lightbox__next', lb).addEventListener('click', () => show(current + 1));
  lb.addEventListener('click', (e) => { if (e.target === lb) closeLb(); });
  document.addEventListener('keydown', (e) => {
    if (lb.hidden) { if (e.key === 'Escape' && nav.classList.contains('is-open')) toggleMenu(false); return; }
    if (e.key === 'Escape') closeLb();
    if (e.key === 'ArrowLeft') show(current - 1);
    if (e.key === 'ArrowRight') show(current + 1);
  });
  // scorrimento con il dito
  let touchX = null;
  lb.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; }, { passive: true });
  lb.addEventListener('touchend', (e) => {
    if (touchX === null) return;
    const dx = e.changedTouches[0].clientX - touchX;
    if (Math.abs(dx) > 50) show(current + (dx < 0 ? 1 : -1));
    touchX = null;
  });

  /* ---------- anno nel footer ---------- */
  const year = $('#year');
  if (year) year.textContent = new Date().getFullYear();

  /* ---------- finestra della novità (es. Padel Clinic) ---------- */
  // Si apre dal cartello, dalla voce del menu, o arrivando da un'altra pagina con #clinic.
  $$('dialog.clinic').forEach(dlg => {
    if (typeof dlg.showModal !== 'function') return; // senza supporto restano i link normali
    let origine = null;
    const apri = (da) => {
      origine = da || null;
      dlg.showModal();
      document.body.style.overflow = 'hidden';
      dlg.querySelector('.clinic__box').scrollTop = 0;
    };
    const chiudi = () => {
      dlg.classList.add('is-closing');
      setTimeout(() => { dlg.classList.remove('is-closing'); dlg.close(); }, reduce ? 0 : 220);
    };
    $$('[data-finestra="' + dlg.id + '"]').forEach(btn => btn.addEventListener('click', (e) => {
      e.preventDefault();
      apri(btn);
    }));
    $$('[data-chiudi]', dlg).forEach(b => b.addEventListener('click', chiudi));
    // clic sullo sfondo scuro, fuori dal riquadro
    dlg.addEventListener('click', (e) => { if (e.target === dlg) chiudi(); });
    dlg.addEventListener('cancel', (e) => { e.preventDefault(); chiudi(); });
    dlg.addEventListener('close', () => {
      document.body.style.overflow = '';
      if (origine && origine.offsetParent !== null) origine.focus();
      if (location.hash === '#' + dlg.id) history.replaceState(null, '', location.pathname + location.search);
    });
    if (location.hash === '#' + dlg.id) setTimeout(() => apri(null), 400);
  });

  /* ---------- ciclo di scorrimento ---------- */
  let ticking = false;
  window.addEventListener('scroll', () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => { onScroll(); if (!reduce) parallax(); ticking = false; });
  }, { passive: true });
  onScroll();
})();
