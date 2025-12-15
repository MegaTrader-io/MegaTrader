(function PM() {
  'use strict';

  // ===== DEBUG =====
  const DEBUG = true;
  const log = (...a) => { if (DEBUG) console.log('[PM]', ...a); };
  window.addEventListener('error', (e) => {
    console.error('[PM] JS error capturado:', e.message, e.filename, e.lineno);
  });

  // ===== Selectores (namespaced) =====
  const PM_SEL = {
    paymentRoot: '#payment',
    savedRadio: '.woocommerce-SavedPaymentMethods [type="radio"]',
    newRadio: '.woocommerce-SavedPaymentMethods-new [type="radio"]',
    placeOrderBtn: 'button[name="woocommerce_checkout_place_order"]',
    termsField: '#privacy_policy_field',
    termsCheckbox: '#privacy_policy',
  };

  const ERR_BLACKLIST = [
    'Your card number is incomplete.',
    'Your card number is invalid.',
    'Your card’s expiration year is in the past.',
    'Your card’s expiration year is invalid.',
    'Your card’s security code is incomplete.',
    'Your ZIP is invalid.',
  ];

  // ========== Helpers ==========
  function pmMutationObserver(configMap = []) {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          configMap.forEach((config) => {
            if (node.nodeType === 1 && node.matches(config.matches)) {
              if (!config.completed) {
                try {
                  config.callbacks.forEach((cb) => cb(node));
                } catch (err) {
                  console.error('[PM] callback error:', err);
                }
                if (config.once) config.completed = true;
              }
            }
          });
        });
      });
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  function filterErrors(stringsToRemove) {
    return (node) => {
      if (!node) return;
      try {
        // Borra <li> concretos si existen; si solo hay 1 <li>, borrar el UL entero
        const items = node.querySelectorAll ? node.querySelectorAll('li') : [];
        if (items.length) {
          items.forEach(li => {
            if (stringsToRemove.includes(li.textContent.trim())) li.remove();
          });
          if (!node.querySelector('li')) node.remove();
        } else if (stringsToRemove.includes(node.textContent.trim())) {
          node.remove();
        }
        log('Filtro de errores aplicado');
      } catch (e) { }
    };
  }

  function scrollToEl(el, offset = 120) {
    if (!el) return;
    if (window.jQuery) jQuery('html, body').stop(true);
    const y = el.getBoundingClientRect().top + window.pageYOffset - offset;
    window.scrollTo({ top: y, behavior: 'smooth' });
  }

  function getTermsField() { return document.querySelector(PM_SEL.termsField); }
  function getTermsCheckbox() { return document.querySelector(PM_SEL.termsCheckbox); }

  function ensureTermsError() {
    const field = getTermsField();
    const cb = getTermsCheckbox();
    if (!field || !cb) { log('No encontré términos'); return null; }

    field.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
    cb.classList.add('is-invalid');
    cb.setAttribute('aria-invalid', 'true');

    let msg = field.querySelector('.invalid-feedback.privacy-policy-error');
    if (!msg) {
      msg = document.createElement('div');
      msg.className = 'invalid-feedback privacy-policy-error';
      msg.style.display = 'block';
      msg.textContent = 'Please accept our Terms of Service and Privacy Policy to continue.';
      field.appendChild(msg);
      log('Inserté mensaje inline de términos');
    } else {
      msg.style.display = 'block';
      log('Mostré mensaje inline de términos (existente)');
    }

    const id = 'privacy-policy-error';
    msg.id = id;
    cb.setAttribute('aria-describedby', id);
    return field;
  }

  function clearTermsError() {
    const field = getTermsField();
    const cb = getTermsCheckbox();
    if (!field || !cb) return;
    field.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
    cb.classList.remove('is-invalid');
    cb.removeAttribute('aria-invalid');
    cb.removeAttribute('aria-describedby');
    const msg = field.querySelector('.invalid-feedback.privacy-policy-error');
    if (msg) msg.style.display = 'none';
    log('Limpio error inline de términos');
  }

  function shouldBlockForTerms() {
    const cb = getTermsCheckbox();
    if (!cb) return false; // si no existe, no bloquees
    if (!cb.checked) {
      const tgt = ensureTermsError();
      scrollToEl(tgt);
      const cbi = tgt ? tgt.querySelector('input[type="checkbox"]') : null;
      setTimeout(() => { try { cbi && cbi.focus(); } catch (_) { } }, 200);
      log('Bloqueo por términos no aceptados');
      return true;
    }
    clearTermsError();
    return false;
  }

  // ========== Interceptores ==========
  // A) Click en Place Order (capturing)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest(PM_SEL.placeOrderBtn);
    if (!btn) return;
    log('Click en Place Order');
    if (shouldBlockForTerms()) {
      e.preventDefault();
      e.stopImmediatePropagation();
      e.stopPropagation();
      log('Submit BLOQUEADO en CLICK');
    }
  }, true);

  // B) Submit del form (por si Woo dispara submit)
  (function hookSubmit() {
    const form = document.querySelector('form.checkout, #checkout-form');
    if (!form) { log('Form no encontrado aún'); return; }
    form.addEventListener('submit', (e) => {
      log('Submit del form detectado');
      if (shouldBlockForTerms()) {
        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();
        log('Submit BLOQUEADO en SUBMIT');
      }
    }, true);
  })();

  // C) Cambios sobre el checkbox de términos (mostrar/ocultar inline al instante)
  document.addEventListener('change', (e) => {
    if (!e.target || !e.target.matches(PM_SEL.termsCheckbox)) return;
    if (e.target.checked) {
      clearTermsError();
    } else {
      ensureTermsError();
    }
  });

  // D) Eventos jQuery propios de Woo
  if (window.jQuery) {
    jQuery(document.body).on('checkout_place_order', function () {
      log('Evento checkout_place_order');
      if (shouldBlockForTerms()) {
        log('Retorno FALSE en checkout_place_order');
        return false;
      }
    });

    jQuery(document.body).on('checkout_error', function () {
      console.log('🛑 Error detectado (tarjeta u otro) → ocultando preloader');
      try { window.wcUnblockCheckout && window.wcUnblockCheckout(); } catch (_) { }
      try { window.hideSitePreloader && window.hideSitePreloader(); } catch (_) { }

      const cb = getTermsCheckbox();
      if (cb && !cb.checked) {
        const tgt = ensureTermsError();
        scrollToEl(tgt);
        log('checkout_error → refuerzo términos y scroll al bloque');
      }
    });
  }

  // ========== Filtro de errores globales que no queremos mostrar ==========
  pmMutationObserver([
    { matches: PM_SEL.paymentRoot, callbacks: [addPaymentMethodBoxToggleListeners] },
    { matches: '.woocommerce-error', callbacks: [filterErrors(ERR_BLACKLIST)] },
  ]);

  // Llamada inicial por si #payment ya está en el DOM
  try {
    if (document.querySelector(PM_SEL.paymentRoot)) {
      addPaymentMethodBoxToggleListeners();
    }
  } catch (e) { log('init toggle error', e); }

  // ========== Animación y toggle de métodos de pago ==========
  function slideCollapse(targetElement, isExpanded, useCustom) {
    if (!window.jQuery) return;
    jQuery(function ($) {
      const duration = 300, $el = $(targetElement);
      if (isExpanded) {
        if (useCustom) {
          targetElement.style.display = '';
          targetElement.style.overflow = 'hidden';
          targetElement.style.height = `${targetElement.scrollHeight}px`;
          targetElement.style['padding-bottom'] = '';
          setTimeout(() => {
            targetElement.style.height = '';
            targetElement.style.overflow = '';
          }, duration);
        } else {
          $el.slideDown(duration);
        }
      } else {
        if (useCustom) {
          requestAnimationFrame(() => {
            targetElement.style.overflow = 'hidden';
            targetElement.style.height = '0';
            targetElement.style['padding-bottom'] = '0';
          });
        } else {
          $el.slideUp(duration);
        }
      }
    });
  }

  function addPaymentMethodBoxToggleListeners() {
    const newMethodRadio = document.querySelector(PM_SEL.newRadio);
    const savedMethods = document.querySelector('.woocommerce-SavedPaymentMethods');
    if (!savedMethods || !savedMethods.getAttribute('data-count') || !(parseInt(savedMethods.getAttribute('data-count')) > 0)) {
      if (newMethodRadio) newMethodRadio.checked = true;
      return;
    }
    const paymentForm = document.querySelector('.wc-payment-form');
    const saveNewCardCheckbox = document.querySelector('.woocommerce-SavedPaymentMethods-saveNew');
    let useCustom;

    if (paymentForm && paymentForm.id && paymentForm.id.includes('nmi')) {
      useCustom = true;
      paymentForm.classList.add('collapsable');
      if (saveNewCardCheckbox) saveNewCardCheckbox.classList.add('collapsable');
    }

    const toggle = () => {
      slideCollapse(paymentForm, newMethodRadio && newMethodRadio.checked, useCustom);
      slideCollapse(saveNewCardCheckbox, newMethodRadio && newMethodRadio.checked);
    };

    document.querySelectorAll(PM_SEL.savedRadio).forEach(r =>
      r.addEventListener('click', toggle, true)
    );
    toggle();
  }

  // ===== Mover “Save payment method” dentro del UPE form (robusto) =====
  (function () {
    const DEST_SEL = '#wc-stripe-upe-form, #wc-stripe-cc-form, .wc-payment-form';
    const SAVE_SEL = '.woocommerce-SavedPaymentMethods-saveNew'; // <p> dentro de un fieldset

    function moveSaveCheckboxInside(reason) {
      const dest = document.querySelector(DEST_SEL);
      const saveP = document.querySelector(SAVE_SEL);
      if (!dest || !saveP) return;

      saveP.classList.add('p-0');
      const saveFs = saveP.closest('fieldset') || saveP;
      saveFs.classList.add('payment-save-card', 'pt-2');

      // si el <p> está oculto (display:none), ocultar también su padre (fieldset) para que no deje espacio
      try {
        const cs = window.getComputedStyle(saveP);
        const isHidden = cs.display === 'none'
          || saveP.hidden
          || saveP.getAttribute('aria-hidden') === 'true';

        saveFs.style.display = isHidden ? 'none' : '';
      } catch (e) { }


      if (dest.contains(saveFs)) return;

      const newRadio = document.querySelector('li.woocommerce-SavedPaymentMethods-new input[type="radio"]');
      if (newRadio && !newRadio.checked) return;

      //saveFs.style.marginTop = '12px';
      dest.appendChild(saveFs);
    }


    // 1) Initial load
    document.addEventListener('DOMContentLoaded', () => moveSaveCheckboxInside('DOMContentLoaded'));

    // 2) Woo/Stripe Refreshments
    if (typeof jQuery !== 'undefined') {
      jQuery(document.body).on('updated_checkout wc-credit-card-form-init payment_method_selected', () => {
        setTimeout(() => moveSaveCheckboxInside('updated_checkout'), 0);
      });
    }

    // 3) Click on “Use a new payment method”
    document.addEventListener('click', (e) => {
      if (e.target && e.target.matches('li.woocommerce-SavedPaymentMethods-new input[type="radio"]')) {
        setTimeout(() => moveSaveCheckboxInside('radio:new-click'), 0);
      }
    });

    // 4) Fallback: appearance of the form in the DOM
    const obs = new MutationObserver(() => setTimeout(() => moveSaveCheckboxInside('mutation'), 0));
    obs.observe(document.body, { childList: true, subtree: true });
  })();

  // Start signal
  log('payment-methods initialized');
})();
