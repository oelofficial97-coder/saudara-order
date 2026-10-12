(() => {
  'use strict';
  const toggle = document.querySelector('.menu-toggle');
  const menu = document.querySelector('.mobile-nav');
  if (toggle && menu) {
    const close = () => { menu.hidden = true; toggle.setAttribute('aria-expanded', 'false'); toggle.setAttribute('aria-label', 'Buka menu navigasi'); };
    toggle.addEventListener('click', () => {
      const willOpen = menu.hidden;
      menu.hidden = !willOpen;
      toggle.setAttribute('aria-expanded', String(willOpen));
      toggle.setAttribute('aria-label', willOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    });
    menu.querySelectorAll('a').forEach(link => link.addEventListener('click', close));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
  }

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const reveals = document.querySelectorAll('.reveal');
  if (!('IntersectionObserver' in window) || reduced) {
    reveals.forEach(el => el.classList.add('is-visible'));
  } else {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); } });
    }, { threshold: 0.08, rootMargin: '0px 0px 40px 0px' });
    reveals.forEach(el => observer.observe(el));
  }

  // Ini hanya perhitungan pratinjau UI; seluruh harga dan kuota wajib dihitung ulang oleh Laravel saat fitur pemesanan dibangun.
  const quantityPicker = document.querySelector('[data-quantity]');
  if (quantityPicker) {
    let quantity = 1;
    const price = 5000;
    const count = quantityPicker.querySelector('[data-count]');
    const countSummary = document.querySelector('[data-summary-count]');
    const total = document.querySelector('[data-total]');
    const render = () => {
      count.textContent = String(quantity);
      if (countSummary) countSummary.textContent = String(quantity);
      if (total) total.textContent = `Rp${(quantity * price).toLocaleString('id-ID')}`;
    };
    quantityPicker.querySelector('[data-decrease]').addEventListener('click', () => { quantity = Math.max(1, quantity - 1); render(); });
    quantityPicker.querySelector('[data-increase]').addEventListener('click', () => { quantity = Math.min(999, quantity + 1); render(); });
    render();
  }
})();
