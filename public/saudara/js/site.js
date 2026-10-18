(() => {
  'use strict';
  const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  const prefersReducedMotion = () => motionQuery.matches;
  const toggle = document.querySelector('.menu-toggle');
  const menu = document.querySelector('.mobile-nav');

  if (toggle && menu) {
    const mobileQuery = window.matchMedia('(max-width: 760px)');
    let closeTimer;

    const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';
    const setToggleState = open => {
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    };

    const closeMenu = ({ restoreFocus = false, immediate = false } = {}) => {
      window.clearTimeout(closeTimer);
      setToggleState(false);
      menu.setAttribute('aria-hidden', 'true');
      if ('inert' in menu) menu.inert = true;

      if (immediate || prefersReducedMotion()) {
        menu.hidden = true;
      } else {
        closeTimer = window.setTimeout(() => {
          if (!isOpen()) menu.hidden = true;
        }, 240);
      }

      if (restoreFocus) toggle.focus();
    };

    const openMenu = () => {
      window.clearTimeout(closeTimer);
      menu.hidden = false;
      menu.setAttribute('aria-hidden', 'true');
      setToggleState(true);
      if ('inert' in menu) menu.inert = false;

      window.requestAnimationFrame(() => {
        if (isOpen()) menu.setAttribute('aria-hidden', 'false');
      });
    };

    closeMenu({ immediate: true });
    toggle.addEventListener('click', () => {
      if (isOpen()) closeMenu();
      else openMenu();
    });
    menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => closeMenu()));

    document.addEventListener('click', event => {
      if (isOpen() && !menu.contains(event.target) && !toggle.contains(event.target)) closeMenu();
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && isOpen()) closeMenu({ restoreFocus: true });
    });

    const resetForViewport = event => {
      if (!event.matches) closeMenu({ immediate: true });
    };
    if (typeof mobileQuery.addEventListener === 'function') {
      mobileQuery.addEventListener('change', resetForViewport);
    } else {
      mobileQuery.addListener(resetForViewport);
    }
  }

  const reveals = document.querySelectorAll('.reveal');
  if (!('IntersectionObserver' in window) || prefersReducedMotion()) {
    reveals.forEach(el => el.classList.add('is-visible'));
  } else {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); } });
    }, { threshold: 0.08, rootMargin: '0px 0px 40px 0px' });
    reveals.forEach(el => observer.observe(el));
  }

  const formatRupiah = value => {
    try {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
      }).format(value);
    } catch (error) {
      return `Rp${Number(value).toLocaleString('id-ID')}`;
    }
  };

  // Semua interaksi berikut hanya pratinjau lokal. Tidak ada data yang dikirim atau disimpan.
  const orderForm = document.querySelector('#prototype-form');
  if (orderForm) {
    const nameInput = orderForm.querySelector('#customer-name');
    const phoneInput = orderForm.querySelector('#customer-phone');
    const quantityInput = orderForm.querySelector('#order-quantity');
    const notesInput = orderForm.querySelector('#order-notes');
    const paymentInputs = Array.from(orderForm.querySelectorAll('input[name=payment_type]'));
    const decreaseButton = orderForm.querySelector('[data-decrease]');
    const increaseButton = orderForm.querySelector('[data-increase]');
    const quantityDisplay = orderForm.querySelector('[data-count]');
    const noteCount = orderForm.querySelector('[data-note-count]');
    const validateButton = orderForm.querySelector('[data-validate-order]');
    const feedback = orderForm.querySelector('[data-form-message], [data-form-feedback]');
    const summaryCount = document.querySelector('[data-summary-count]');
    const summaryPayment = document.querySelector('[data-summary-payment]');
    const total = document.querySelector('[data-total]');
    const parsedPrice = Number(orderForm.dataset.unitPrice);
    const unitPrice = Number.isFinite(parsedPrice) && parsedPrice > 0 ? parsedPrice : 5000;
    const parsedMin = quantityInput ? Number(quantityInput.min) : 1;
    const parsedMax = quantityInput ? Number(quantityInput.max) : 999;
    const minimum = Number.isInteger(parsedMin) && parsedMin > 0 ? parsedMin : 1;
    const maximum = Number.isInteger(parsedMax) && parsedMax >= minimum ? parsedMax : 999;
    let validationAttempted = false;

    const setFieldError = (field, errorId, message = '') => {
      if (field) {
        if (message) field.setAttribute('aria-invalid', 'true');
        else field.removeAttribute('aria-invalid');
      }
      const error = document.getElementById(errorId);
      if (error) {
        error.textContent = message;
        error.hidden = !message;
      }
      return !message;
    };

    const setPaymentError = (message = '') => {
      paymentInputs.forEach(input => {
        if (message) input.setAttribute('aria-invalid', 'true');
        else input.removeAttribute('aria-invalid');
      });
      const error = document.getElementById('payment-type-error');
      if (error) {
        error.textContent = message;
        error.hidden = !message;
      }
      return !message;
    };

    const showFeedback = (message, status) => {
      if (!feedback) return;
      feedback.textContent = message;
      feedback.dataset.status = status;
      feedback.setAttribute('role', status === 'error' ? 'alert' : 'status');
      feedback.hidden = false;
    };

    const clearFeedback = () => {
      if (!feedback || feedback.hidden) return;
      feedback.hidden = true;
      feedback.textContent = '';
      delete feedback.dataset.status;
    };

    const getQuantity = () => {
      if (!quantityInput || quantityInput.value.trim() === '') return Number.NaN;
      return Number(quantityInput.value);
    };
    const selectedPayment = () => paymentInputs.find(input => input.checked);
    const paymentLabel = input => {
      if (!input) return 'Belum dipilih';
      if (input.dataset.paymentLabel) return input.dataset.paymentLabel;
      const key = input.value.toLowerCase().replace(/[\s-]+/g, '_');
      const labels = {
        transfer: 'Transfer manual',
        manual: 'Transfer manual',
        transfer_manual: 'Transfer manual',
        manual_transfer: 'Transfer manual',
        pickup: 'Bayar saat ambil',
        cash: 'Bayar saat ambil',
        cod: 'Bayar saat ambil',
        pay_on_pickup: 'Bayar saat ambil',
        bayar_saat_ambil: 'Bayar saat ambil',
      };
      return labels[key] || (input.labels && input.labels[0]
        ? input.labels[0].textContent.trim().replace(/\s+/g, ' ')
        : input.value);
    };

    const renderQuantity = () => {
      const quantity = getQuantity();
      const isInteger = Number.isInteger(quantity);
      const isValid = isInteger && quantity >= minimum && quantity <= maximum;
      if (quantityDisplay && quantityDisplay !== quantityInput) {
        if ('value' in quantityDisplay) quantityDisplay.value = isInteger ? String(quantity) : '';
        else quantityDisplay.textContent = isInteger ? String(quantity) : '—';
      }
      if (summaryCount) summaryCount.textContent = isValid ? String(quantity) : '—';
      if (total) total.textContent = formatRupiah(isValid ? quantity * unitPrice : 0);
      if (decreaseButton) {
        decreaseButton.disabled = !isInteger || quantity <= minimum;
        decreaseButton.setAttribute('aria-disabled', String(decreaseButton.disabled));
      }
      if (increaseButton) {
        increaseButton.disabled = isInteger && quantity >= maximum;
        increaseButton.setAttribute('aria-disabled', String(increaseButton.disabled));
      }
    };

    const setQuantity = value => {
      if (!quantityInput) return;
      quantityInput.value = String(Math.min(maximum, Math.max(minimum, value)));
      renderQuantity();
    };
    const renderPayment = () => {
      if (summaryPayment) summaryPayment.textContent = paymentLabel(selectedPayment());
    };
    const renderNoteCount = () => {
      if (!notesInput || !noteCount) return;
      const limit = notesInput.maxLength > 0 ? notesInput.maxLength : 300;
      noteCount.textContent = `${notesInput.value.length} / ${limit}`;
    };

    const validateName = () => {
      if (!nameInput) return true;
      const value = nameInput.value.trim();
      const limit = nameInput.maxLength > 0 ? nameInput.maxLength : 80;
      let message = '';
      if (!value) message = 'Nama pelanggan wajib diisi.';
      else if (Array.from(value.replace(/\s/g, '')).length < 2) message = 'Nama pelanggan minimal 2 karakter.';
      else if (value.length > limit) message = `Nama pelanggan maksimal ${limit} karakter.`;
      return setFieldError(nameInput, 'customer-name-error', message);
    };

    const validatePhone = () => {
      if (!phoneInput) return true;
      const value = phoneInput.value.trim();
      const normalized = value.replace(/[\s().-]/g, '');
      let message = '';
      if (!value) message = 'Nomor WhatsApp wajib diisi.';
      else if (!/^(?:\+62|62|0)8[1-9]\d{7,10}$/.test(normalized)) {
        message = 'Gunakan nomor WhatsApp Indonesia yang valid, diawali 08 atau +628.';
      }
      return setFieldError(phoneInput, 'customer-phone-error', message);
    };

    const validateQuantity = () => {
      if (!quantityInput) return true;
      const quantity = getQuantity();
      let message = '';
      if (Number.isNaN(quantity)) message = 'Jumlah pouch wajib diisi.';
      else if (!Number.isInteger(quantity)) message = 'Jumlah pouch harus berupa angka bulat.';
      else if (quantity < minimum) message = `Jumlah minimal ${minimum} pouch.`;
      else if (quantity > maximum) message = `Jumlah maksimal ${maximum} pouch.`;
      return setFieldError(quantityInput, 'quantity-error', message);
    };

    const validatePayment = () => setPaymentError(
      selectedPayment() ? '' : 'Pilih transfer manual atau bayar saat ambil.',
    );

    const validateNotes = () => {
      if (!notesInput) return true;
      const limit = notesInput.maxLength > 0 ? notesInput.maxLength : 300;
      const message = notesInput.value.length > limit ? `Catatan maksimal ${limit} karakter.` : '';
      return setFieldError(notesInput, 'order-notes-error', message);
    };

    const validateOrder = () => {
      validationAttempted = true;
      const checks = [
        [nameInput, validateName()],
        [phoneInput, validatePhone()],
        [quantityInput, validateQuantity()],
        [paymentInputs[0], validatePayment()],
        [notesInput, validateNotes()],
      ];
      const firstInvalid = checks.find(([, valid]) => !valid);
      if (firstInvalid) {
        showFeedback('Periksa kembali kolom yang ditandai. Tidak ada data yang dikirim.', 'error');
        if (firstInvalid[0]) firstInvalid[0].focus();
        return false;
      }
      showFeedback(
        'Data sudah lengkap untuk pratinjau. Pesanan belum dikirim atau disimpan karena backend belum aktif.',
        'prototype',
      );
      return true;
    };

    const bindTextValidation = (input, validator, renderExtra) => {
      if (!input) return;
      input.addEventListener('input', () => {
        clearFeedback();
        if (renderExtra) renderExtra();
        if (validationAttempted || input.getAttribute('aria-invalid') === 'true') validator();
      });
      input.addEventListener('blur', validator);
    };

    bindTextValidation(nameInput, validateName);
    bindTextValidation(phoneInput, validatePhone);
    bindTextValidation(notesInput, validateNotes, renderNoteCount);

    if (quantityInput) {
      if (quantityInput.value.trim() === '') quantityInput.value = String(minimum);
      quantityInput.addEventListener('input', () => {
        clearFeedback();
        renderQuantity();
        if (validationAttempted || quantityInput.getAttribute('aria-invalid') === 'true') validateQuantity();
      });
      quantityInput.addEventListener('blur', validateQuantity);
    }

    if (decreaseButton) {
      decreaseButton.addEventListener('click', () => {
        const quantity = getQuantity();
        setQuantity(Number.isInteger(quantity) ? quantity - 1 : minimum);
        clearFeedback();
        if (validationAttempted) validateQuantity();
      });
    }
    if (increaseButton) {
      increaseButton.addEventListener('click', () => {
        const quantity = getQuantity();
        setQuantity(Number.isInteger(quantity) ? quantity + 1 : minimum);
        clearFeedback();
        if (validationAttempted) validateQuantity();
      });
    }

    paymentInputs.forEach(input => {
      input.addEventListener('change', () => {
        clearFeedback();
        renderPayment();
        if (validationAttempted) validatePayment();
      });
    });

    if (validateButton) {
      validateButton.addEventListener('click', event => {
        event.preventDefault();
        validateOrder();
      });
    }
    orderForm.addEventListener('submit', event => {
      event.preventDefault();
      validateOrder();
    });

    [
      'customer-name-error',
      'customer-phone-error',
      'quantity-error',
      'payment-type-error',
      'order-notes-error',
    ].forEach(id => {
      const error = document.getElementById(id);
      if (error && !error.textContent.trim()) error.hidden = true;
    });

    renderQuantity();
    renderPayment();
    renderNoteCount();
  }

  const trackingForm = document.querySelector('#tracking-form');
  if (trackingForm) {
    const accessInput = trackingForm.querySelector('#tracking-access');
    const accessError = trackingForm.querySelector('#tracking-access-error');
    const checkButton = trackingForm.querySelector('[data-track-check]');
    const scope = trackingForm.closest('.tracking-main')
      || trackingForm.closest('.tracking-card')
      || document;
    const statePanels = Array.from(scope.querySelectorAll('[data-state]'));
    let stateTimer;

    const showTrackingState = (state, message = '') => {
      let activePanel;
      statePanels.forEach(panel => {
        const active = panel.dataset.state === state;
        panel.hidden = !active;
        panel.setAttribute('aria-hidden', String(!active));
        panel.classList.toggle('is-active', active);
        if (active) activePanel = panel;
      });

      if (message) {
        const messageElement = activePanel
          ? activePanel.querySelector('[data-state-message]')
          : scope.querySelector('[data-state-message]');
        if (messageElement) messageElement.textContent = message;
      }
    };

    const setTrackingBusy = busy => {
      trackingForm.setAttribute('aria-busy', String(busy));
      if (checkButton) {
        checkButton.disabled = busy;
        checkButton.setAttribute('aria-disabled', String(busy));
      }
    };

    const setAccessError = (message = '') => {
      if (accessInput) {
        if (message) accessInput.setAttribute('aria-invalid', 'true');
        else accessInput.removeAttribute('aria-invalid');
      }
      if (accessError) {
        accessError.textContent = message;
        accessError.hidden = !message;
      }
    };

    const looksLikePrivateAccess = value => {
      if (value.length < 32) return false;
      if (/^[A-Za-z0-9_-]{32,}$/.test(value)) return true;
      try {
        const url = new URL(value);
        return url.protocol === 'https:' || url.protocol === 'http:';
      } catch (error) {
        return false;
      }
    };

    const checkTrackingAccess = () => {
      if (!accessInput) return;
      window.clearTimeout(stateTimer);
      setTrackingBusy(false);
      const value = accessInput.value.trim();

      if (!value) {
        setAccessError('Tautan atau kunci akses pribadi wajib diisi.');
        showTrackingState('empty', 'Tempel tautan atau token akses pribadi untuk memulai.');
        accessInput.focus();
        return;
      }

      if (!looksLikePrivateAccess(value)) {
        setAccessError('Kode biasa tidak dapat digunakan. Masukkan akses pribadi minimal 32 karakter.');
        showTrackingState(
          'error',
          'Kode pesanan biasa tidak dapat digunakan. Gunakan tautan atau token akses pribadi minimal 32 karakter.',
        );
        accessInput.focus();
        return;
      }

      setAccessError();
      setTrackingBusy(true);
      showTrackingState('loading', 'Memeriksa format akses secara lokal...');
      stateTimer = window.setTimeout(() => {
        setTrackingBusy(false);
        showTrackingState(
          'prototype',
          'Format akses dikenali, tetapi pelacakan belum tersedia karena backend belum aktif. Tidak ada data yang dikirim atau dicari.',
        );
      }, prefersReducedMotion() ? 0 : 650);
    };

    if (accessInput) {
      accessInput.addEventListener('input', () => {
        window.clearTimeout(stateTimer);
        setTrackingBusy(false);
        setAccessError();
        showTrackingState(
          'empty',
          'Siapkan tautan atau token akses pribadi yang diberikan setelah pemesanan.',
        );
      });
    }

    if (checkButton) {
      checkButton.addEventListener('click', event => {
        event.preventDefault();
        checkTrackingAccess();
      });
    }
    trackingForm.addEventListener('submit', event => {
      event.preventDefault();
      checkTrackingAccess();
    });

    setTrackingBusy(false);
    setAccessError();
    showTrackingState('empty');
  }
})();
