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
  const links = $$('.nav a[href^="#"]');
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
    if (reduce) { el.textContent = prefix + target; return; }
    const dur = 1600;
    const t0 = performance.now();
    const tick = (t) => {
      const p = Math.min((t - t0) / dur, 1);
      const eased = 1 - Math.pow(1 - p, 4);
      el.textContent = prefix + Math.round(target * eased);
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
  let current = 0;
  const show = (i) => {
    current = (i + items.length) % items.length;
    lbImg.src = items[current].getAttribute('href');
    lbImg.alt = $('img', items[current]).alt;
    // riavvia l'animazione d'ingresso
    lbImg.style.animation = 'none'; void lbImg.offsetWidth; lbImg.style.animation = '';
  };
  const openLb = (i) => { show(i); lb.hidden = false; document.body.style.overflow = 'hidden'; $('.lightbox__close', lb).focus(); };
  const closeLb = () => { lb.hidden = true; document.body.style.overflow = ''; items[current].focus(); };
  items.forEach((a, i) => a.addEventListener('click', (e) => { e.preventDefault(); openLb(i); }));
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
  $('#year').textContent = new Date().getFullYear();

  /* ---------- ciclo di scorrimento ---------- */
  let ticking = false;
  window.addEventListener('scroll', () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => { onScroll(); if (!reduce) parallax(); ticking = false; });
  }, { passive: true });
  onScroll();
})();
