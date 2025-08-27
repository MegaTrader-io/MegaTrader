const S = {
  Disclaimer: {
    AgreeCardCharge:
      "By providing your card information, you allow MyGrabber.io to charge your card for future payments in accordance with their terms.",
  },
};

//TODO: Refine naming (separate Classes from Selectors)
const Selector = {
  InvalidFieldClass: "is-invalid",
  ErrorMessageClass: "invalid-feedback",
  PaymentId: '#payment',
  NotificationsErrorGroupClass: 'woocommerce-error',
  SavedPaymentMethodRadioSelector: '.woocommerce-SavedPaymentMethods [type="radio"]',
  NewPaymentMethodRadioSelector: '.woocommerce-SavedPaymentMethods-new [type="radio"]',
  PlaceOrderBtnId: 'place_order',
}

function setStateWhenReady(stateCode) {
  if (!stateCode) return;
  const wrapper = document.getElementById("billing_state_wrapper");

  const tryApply = () => {
    const select = document.getElementById("billing_state");
    if (!select) return false;

    const match = [...select.options].find(o => o.value === stateCode);
    if (!match) return false;

    select.value = stateCode;
    select.dispatchEvent(new Event("change"));
    select.dataset.googleSet = "true";
    return true;
  };

  if (tryApply()) return;

  const obs = new MutationObserver(() => {
    if (tryApply()) obs.disconnect();
  });
  obs.observe(wrapper, { childList: true, subtree: true });
}

// Reaplica durante unos segundos por si otro script vuelve a sustituir el select
function lockStateSelection(stateCode, ttlMs = 7000) {
  if (!stateCode) return;
  const wrapper = document.getElementById("billing_state_wrapper");
  if (!wrapper) return;

  const start = Date.now();
  const apply = () => {
    const select = document.getElementById("billing_state");
    if (!select) return;
    const opt = [...select.options].find(o => o.value === stateCode);
    if (opt) {
      select.value = stateCode;
      select.dataset.googleSet = "true";
    }
  };

  apply();

  const obs = new MutationObserver(() => {
    apply();
    if (Date.now() - start > ttlMs) {
      obs.disconnect();
    }
  });
  obs.observe(wrapper, { childList: true, subtree: true });
}



document.addEventListener("DOMContentLoaded", function () {

  // ========== DETECT COUNTRY ON FIRST LOAD (IP) ==========
  const countrySelect = document.getElementById("billing_country");
  let hasBeenOverwrittenByAutocomplete = false;

  if (countrySelect) {
    const hasInitialCountry =
      !!countrySelect.value &&
      !!countrySelect.querySelector("option:checked")?.value;

    // Solo detecta por IP si NO hay país preseleccionado
    if (!hasInitialCountry) {
      fetch("https://ipapi.co/json/")
        .then((res) => res.json())
        .then((data) => {
          const detectedCountry = data.country || "US";
          // No sobrescribir si Google ya lo puso
          if (!hasBeenOverwrittenByAutocomplete && detectedCountry) {
            const opt = [...countrySelect.options].find(o => o.value === detectedCountry);
            if (opt && countrySelect.value !== detectedCountry) {
              countrySelect.value = detectedCountry;
              countrySelect.dispatchEvent(new Event("change"));
            }
          }
        })
        .catch(() => console.warn("🌎 No se pudo detectar país por IP"));
    }
  }



  // ========== GOOGLE AUTOCOMPLETE ==========
  const addressInput = document.getElementById("billing_address_1");

  if (addressInput && window.google && google.maps && google.maps.places) {
    const autocomplete = new google.maps.places.Autocomplete(addressInput, {
      types: ["address"],
      componentRestrictions: { country: ["us"] },
    });

    autocomplete.addListener("place_changed", function () {
      const place = autocomplete.getPlace();
      if (!place.address_components) return;

      const fields = {
        billing_address_1: "",
        billing_address_2: "",
        billing_city: "",
        billing_state: "",
        billing_postcode: "",
        billing_country: "",
      };

      place.address_components.forEach((component) => {
        const types = component.types;

        if (types.includes("street_number")) {
          fields.billing_address_1 =
            component.long_name + " " + fields.billing_address_1;
        }
        if (types.includes("route")) {
          fields.billing_address_1 += component.long_name;
        }
        if (types.includes("subpremise")) {
          fields.billing_address_2 = component.long_name;
        }
        if (types.includes("locality")) {
          fields.billing_city = component.long_name;
        }
        if (types.includes("administrative_area_level_1")) {
          fields.billing_state = component.short_name;
        }
        if (types.includes("postal_code")) {
          fields.billing_postcode = component.long_name;
        }
        if (types.includes("country")) {
          fields.billing_country = component.short_name;
        }
      });

      document.getElementById("billing_address_1").value =
        fields.billing_address_1;
      document.getElementById("billing_address_2").value =
        fields.billing_address_2;
      document.getElementById("billing_city").value = fields.billing_city;
      document.getElementById("billing_postcode").value =
        fields.billing_postcode;

      const countrySelect = document.getElementById("billing_country");
      if (fields.billing_country && countrySelect) {
        const option = [...countrySelect.options].find(
          (opt) => opt.value === fields.billing_country
        );
        if (option) {
          countrySelect.value = fields.billing_country;
          countrySelect.classList.add("selected-by-google");
          countrySelect.dispatchEvent(new Event("change"));
          hasBeenOverwrittenByAutocomplete = true;
        }
      }

      setStateWhenReady(fields.billing_state);
      lockStateSelection(fields.billing_state, 7000);


    });
  }

  // ========== FORCE EMPTY STATE BY DEFAULT ==========
  // (opcional, versión segura — no vacía si ya hay valor)
  const stateWrapper = document.getElementById("billing_state_wrapper");
  if (stateWrapper) {
    const observer = new MutationObserver(() => {
      const select = document.getElementById("billing_state");
      if (!select) return;

      const hasPrefilled =
        !!select.value || !!select.querySelector("option:checked")?.value;
      if (hasPrefilled) {
        observer.disconnect();
        return;
      }

      const defaultOpt = select.querySelector('option[value=""]') || select.options[0];
      if (defaultOpt) defaultOpt.selected = true;
      observer.disconnect();
    });
    observer.observe(stateWrapper, { childList: true, subtree: true });
  }



  // ========== FORMAT AND VALIDATION FOR TELEPHONE ==========
  const phoneInput = document.getElementById("billing_phone");
  if (phoneInput) {
    const isMobile = /Mobi|Android/i.test(navigator.userAgent);

    function initPhoneInput(countryCode = "us") {
      if (window.iti) {
        window.iti.destroy();
      }
      const iti = window.intlTelInput(phoneInput, {
        initialCountry: countryCode.toLowerCase(),
        nationalMode: false,
        separateDialCode: true,
      });

      window.iti = iti;

      phoneInput.addEventListener("blur", function () {
        const isValid = iti.isValidNumber();
        let errorContainer = phoneInput
          .closest(".form-group")
          ?.querySelector(".invalid-feedback");

        if (!errorContainer) {
          errorContainer = document.createElement("div");
          errorContainer.className = "invalid-feedback";
          errorContainer.textContent = "Invalid phone number.";
          phoneInput.parentNode.appendChild(errorContainer);
        }

        if (isValid) {
          phoneInput.classList.remove("is-invalid");
          errorContainer.style.display = "none";
          phoneInput.setCustomValidity("");
        } else {
          phoneInput.classList.add("is-invalid");
          errorContainer.style.display = "block";
          phoneInput.setCustomValidity("Invalid");
        }
      });
    }

    function fallbackToIP() {
      fetch("https://ipapi.co/json/")
        .then((resp) => resp.json())
        .then((resp) => {
          const countryCode = resp.country || "us";
          initPhoneInput(countryCode);
        })
        .catch(() => {
          initPhoneInput("auto");
        });
    }

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const { latitude, longitude } = position.coords;
          fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`
          )
            .then((res) => res.json())
            .then((data) => {
              const countryCode = data.address?.country_code?.toUpperCase();
              initPhoneInput(countryCode || "auto");
            })
            .catch(() => {
              fallbackToIP();
            });
        },
        (err) => {
          console.warn("⚠️ Geolocation blocked or failed:", err.message);
          fallbackToIP();
        },
        { timeout: 5000 }
      );
    } else {
      fallbackToIP();
    }
  }

  // ========== CHECKOUT FORM VALIDATION ==========
  const validationRules = {
    privacy_policy: [
      (value) =>
        value.trim() === "1" ||
        "Please accept our Terms of Service and Privacy Policy to continue.",
    ],
    billing_first_name: [
      (value) =>
        value.trim() !== "" || "Billing First Name is a required field",
    ],
    billing_last_name: [
      (value) => value.trim() !== "" || "Billing Last Name is a required field",
    ],
    billing_email: [
      (value) => value.trim() !== "" || "Please enter an Email address",
      (value) =>
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ||
        "Please enter a valid Email address",
    ],
    billing_phone: [
      (value) => value.trim() !== "" || "Billing Phone is a required field.",
    ],
    billing_address_1: [
      (value) =>
        value.trim() !== "" || "Billing Street address is a required field.",
    ],
    billing_city: [
      (value) =>
        value.trim() !== "" || "Billing Town / City is a required field.",
    ],
    billing_postcode: [
      (value) => value.trim() !== "" || "Billing ZIP code is a required field.",
    ],
    billing_country: [
      (value) => value.trim() !== "" || "Billing Country is a required field.",
    ],
    billing_state: [
      (value) => value.trim() !== "" || "Billing State is a required field.",
    ],
  };

  function validateFormFields(values, rules) {
    const errors = {};
    for (const [field, ruleSet] of Object.entries(rules)) {
      const value = values[field] || "";
      for (const rule of ruleSet) {
        const result = rule(value);
        if (result !== true) {
          errors[field] = result;
          break;
        }
      }
    }
    return errors;
  }

  function clearErrors(form) {
    form.querySelectorAll(`.${Selector.ErrorMessageClass}`).forEach((el) => el.remove());
    form
      .querySelectorAll(`.${Selector.InvalidFieldClass}`)
      .forEach((el) => el.classList.remove(Selector.InvalidFieldClass));

    protectedErrors.clear();
  }

  function showErrors(form, errors) {
    for (const [field, message] of Object.entries(errors)) {
      const input = form.querySelector(`[name="${field}"]`);
      if (!input) continue;

      const errorNode = document.createElement("div");
      errorNode.className = Selector.ErrorMessageClass;
      errorNode.textContent = message;

      // Prevent Douplicate Errors
      const existingErrorNode = input.parentNode.querySelector(`.${Selector.ErrorMessageClass}`);
      if (existingErrorNode) {
        existingErrorNode.remove()
      }

      // Insert New Error
      input.parentElement.appendChild(errorNode);
      input.classList.add(Selector.InvalidFieldClass);
      protectedErrors.set(field, errorNode);
    }
  }

  // ========== PROTECT DOM BUGS AGAINST EXTERNAL DELETION ==========
  const protectedErrors = new Map();

  function startErrorProtection() {
    const observer = new MutationObserver(() => {
      for (const [field, node] of protectedErrors.entries()) {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input) continue;

        const existing = input.parentNode.querySelector(".invalid-feedback");
        if (!existing) {
          input.parentElement.appendChild(node);
          input.classList.add("is-invalid");
        }
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });
  }

  startErrorProtection();


  function formActionHandler(e) {
    checkoutFormIsInvalid = false;
    document.activeElement?.blur();

    const fullPhone = window.iti ? window.iti.getNumber() : "";
    if (fullPhone) {
      const phoneInput = document.getElementById("billing_phone");
      if (phoneInput) phoneInput.value = fullPhone;
    }

    const formData = new FormData(checkoutForm);
    const values = Object.fromEntries(formData.entries());
    const errors = validateFormFields(values, validationRules);

    if (Object.keys(errors).length > 0) {
      checkoutFormIsInvalid = true;
      clearErrors(checkoutForm);
      showErrors(checkoutForm, errors);

      const firstInvalid = checkoutForm.querySelector(".is-invalid");
      if (firstInvalid && typeof firstInvalid.focus === "function") {
        firstInvalid.scrollIntoView({
          behavior: "smooth",
          block: "center",
          inline: "center",
        });
        setTimeout(() => {
          firstInvalid.focus();
        }, 300);
      }

    }
  }

  const checkoutForm = document.getElementById("checkout-form");
  let checkoutFormIsInvalid = false;

  if (checkoutForm) {
    checkoutForm.addEventListener("submit", (e) => {
      if (checkoutFormIsInvalid) {
        // e.stopPropagation();
      }
    }, true);
  }

  // ========== AUTO SAVE NEW CREDIT CARD ==========
  // const saveCardCheckbox = document.querySelector('.woocommerce-SavedPaymentMethods-saveNew [type="checkbox"]');
  // if (saveCardCheckbox) {
  //   const saveNewDisplayCssVar =
  //     "--woo_checkout_payment-method_save-new_display";
  //   saveCardCheckbox.checked = true;
  //   document.documentElement.style.setProperty(saveNewDisplayCssVar, "none");
  // }


  function mutationObserver(configMap = []) {
    const observer = new MutationObserver((mutations, obs) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          configMap.forEach(config => {
            if (
              node.nodeType === 1 &&
              node.matches(config.matches)
            ) {
              if (!config.completed) {
                config.callbacks.forEach(callback => {
                  callback(node);
                })

                if (config.once) {
                  config.completed = true;
                }
              }
            }
          })
        });
      });
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  // ========== PLACE ORDER BTN EVENT ==========
  function addPlaceOrderBtnListeners() {
    placeOrderBtn = document.getElementById(Selector.PlaceOrderBtnId);
    if (placeOrderBtn) {
      placeOrderBtn.addEventListener(
        "click",
        (e) => {
          clearErrors(checkoutForm);
          formActionHandler(e);
        }
      )
    }
  }

  // ========== JQUERY SLIDE ANIMATION ==========
  function slideCollapse(targetElement, isExpanded, useCustom) {
    jQuery(function ($) {
      const duration = 300;
      $targetEl = $(targetElement);
      if (isExpanded) {
        if (useCustom) {
          targetElement.style.display = '';
          targetElement.style.overflow = 'hidden';
          targetElement.style.height = `${targetElement.scrollHeight}px`;
          targetElement.style['padding-bottom'] = '';
          setTimeout(() => {
            targetElement.style.height = '';
            targetElement.style.overflow = '';
          }, duration)
        } else {
          $targetEl.slideDown(duration);
        }
      } else {
        if (useCustom) {
          requestAnimationFrame(() => {
            targetElement.style.overflow = 'hidden';
            targetElement.style.height = '0';
            targetElement.style['padding-bottom'] = '0';
          })
        } else {
          $targetEl.slideUp(duration);
        }
      }
    })
  }

  // ========== TOGGLE CC BOX COLLAPSE ==========
  function addPaymentMethodBoxToggleListeners() {
    const savedMethods = document.querySelector('.woocommerce-SavedPaymentMethods');
    if (!savedMethods || !savedMethods.getAttribute('data-count') || !parseInt(savedMethods.getAttribute('data-count')) > 0) {
      return;
    }
    const newMethodRadio = document.querySelector(Selector.NewPaymentMethodRadioSelector);
    const paymentForm = document.querySelector('.wc-payment-form');
    const saveNewCardCheckbox = document.querySelector('.woocommerce-SavedPaymentMethods-saveNew');
    let useCustom;

    // Cannot Use Animations Ending on "display: none" like jQuery toggle
    // or NMI input fields iframes won't render properly
    if (paymentForm.id.includes('nmi')) {
      useCustom = true;
      paymentForm.classList.add('collapsable');
      saveNewCardCheckbox.classList.add('collapsable');
    }

    const togglePaymentFormCollapse = () => {
      slideCollapse(paymentForm, newMethodRadio.checked, useCustom);
      slideCollapse(saveNewCardCheckbox, newMethodRadio.checked);
    }

    document.querySelectorAll(Selector.SavedPaymentMethodRadioSelector).forEach(radio =>
      radio.addEventListener("click", togglePaymentFormCollapse, true)
    )

    togglePaymentFormCollapse()
  }

  // ========== AUTO SELECT SAVED CREDIT CARD ==========
  function selectFirstSavedPaymentMethodRadio(node = document.body) {
    node.querySelector(Selector.SavedPaymentMethodRadioSelector).click();
  }

  // ========== MOVE GLOBAL ERRORS TO TARGET FORM FIELD ==========
  const errorsBlackList = new Set(
    [
      //Email
      `Your email address is invalid.`,
      // Credit Card
      'Your card number is incomplete.',
      `Your card number is invalid.`,
      // Exp Date
      `Your card’s expiration date is incomplete.`,
      `Your card’s expiration year is in the past.`,
      // CVC
      `Your card’s security code is incomplete.`,
      // Policy Checkbox
      `Please accept our Terms of Service and Privacy Policy to continue`,
    ]
  )

  function migrateGlobalFieldErrors(node) {
    const errorGroupList = [...document.getElementsByClassName(Selector.NotificationsErrorGroupClass)];

    errorGroupList.forEach(errorGroup => {
      const inputErrors = Array.from(errorGroup.children).reduce((messageByField, currentError) => {
        const fieldName = currentError.getAttribute('data-id');
        const message = currentError.textContent;

        if (fieldName) {
          messageByField[fieldName] = message;
          currentError.remove()
        } else if (errorsBlackList.has(message.trim())) {
          currentError.remove()
        }

        return messageByField;

      }, {})

      showErrors(checkoutForm, inputErrors);

      // Delete Error Group If Empty
      if (!errorGroup.children.length) {
        errorGroup.remove()
      }

    })
  }

 


  

  

  // ========= MUTATION OBSERVER POOL - ADDITION ============

  mutationObserver([
    {
      matches: Selector.PaymentId,
      callbacks: [
        addPaymentMethodBoxToggleListeners,
      ],
    },
    {
      matches: '.woocommerce-NoticeGroup, .woocommerce-error',
      callbacks: [
        migrateGlobalFieldErrors,
      ]
    },
   
    // { // Debug Added Nodes
    //   matches: '*', callbacks: [ (node)=>{ console.info('Node Added:', node) } ]
    // }
  ]);

  // ========== BLOCK ENTER TO SEND FORM ==========
  const checkoutFormEnterBlock = document.querySelector("form.checkout");
  if (checkoutFormEnterBlock) {
    checkoutFormEnterBlock.addEventListener("keydown", function (e) {
      if (e.key === "Enter" && e.target.tagName !== "TEXTAREA") {
        e.preventDefault();
        return false;
      }
    });
  }

// ===== Billing summary toggle & save =====

// --- helpers para el overlay nativo de Woo ---
function wcBlockCheckout() {
  if (typeof jQuery === 'undefined') return;
  const $form = jQuery('form.checkout, #checkout-form');
  if ($form.length && typeof $form.block === 'function') {
    $form.addClass('processing').block({
      message: null,
      overlayCSS: { background: '#000', opacity: 0.3 }
    });
  } else {
    $form.addClass('processing');
  }
}
function wcUnblockCheckout() {
  if (typeof jQuery === 'undefined') return;
  const $form = jQuery('form.checkout, #checkout-form');
  if ($form.length && typeof $form.unblock === 'function') {
    $form.removeClass('processing').unblock();
  } else {
    $form.removeClass('processing');
  }
}

// --- helpers para TU preloader (.preloader) ---
function showSitePreloader() {
  if (typeof jQuery === 'undefined') return;
  const $pre = jQuery('.preloader');
  if ($pre.length) $pre.stop(true, true).fadeIn(150);
}
function hideSitePreloader() {
  if (typeof jQuery === 'undefined') return;
  const $pre = jQuery('.preloader');
  if ($pre.length) $pre.stop(true, true).fadeOut(150);
}

// --- asegura que Woo “vea” los cambios en los inputs ---
function setWooVal(id, val) {
  const el = document.getElementById(id);
  if (!el) return;
  if (el.value !== val) el.value = val;
  el.dispatchEvent(new Event('input',  { bubbles: true }));
  el.dispatchEvent(new Event('change', { bubbles: true }));
}

const PAC_HIDE_CLASS = 'pac-hidden';

function forceClosePlaces() {
  document.body.classList.add(PAC_HIDE_CLASS);

  const addr = document.getElementById('billing_address_1');
  if (addr) addr.blur();

  document.querySelectorAll('.pac-container').forEach(el => {
    el.style.display = 'none';
    el.setAttribute('aria-hidden', 'true');
  });
}

function allowPlaces() {
  document.body.classList.remove(PAC_HIDE_CLASS);
  document.querySelectorAll('.pac-container[aria-hidden="true"]').forEach(el => {
    el.style.display = '';
    el.removeAttribute('aria-hidden');
  });
}

function initBillingSummary() {
  const summary  = document.getElementById('mt-billing-summary');
  const formBox  = document.getElementById('mt-billing-form');
  const changeLn = document.getElementById('mt-billing-change');
  const saveBtn  = document.getElementById('mt-save-billing');

  function showForm() {
  if (!summary || !formBox) return;
  allowPlaces(); // <- permitir dropdown al entrar al form
  formBox.classList.remove('d-none');
  summary.classList.add('d-none');
  if (typeof jQuery !== 'undefined' && jQuery.fn && jQuery.fn.slideDown) {
    jQuery(formBox).stop(true, true).hide().slideDown(200);
  }
}
 function showSummary() {
  if (!summary || !formBox) return;
  forceClosePlaces(); // <- cerrarlo al volver al resumen
  summary.classList.remove('d-none');
  formBox.classList.add('d-none');
  if (typeof jQuery !== 'undefined' && jQuery.fn && jQuery.fn.slideDown) {
    jQuery(summary).stop(true, true).hide().slideDown(200);
  }
}

  // evita listeners duplicados cuando Woo refresca fragmentos
  if (changeLn && !changeLn.dataset.bound) {
    changeLn.dataset.bound = '1';
    changeLn.addEventListener('click', (e) => { e.preventDefault(); showForm(); });
  }

  if (saveBtn && !saveBtn.dataset.bound) {
    saveBtn.dataset.bound = '1';
    saveBtn.addEventListener('click', async (e) => {
      e.preventDefault();

      // 🔥 levantar preloader + overlay
      showSitePreloader();
      wcBlockCheckout();

      const originalTxt = saveBtn.textContent;
      saveBtn.disabled = true;
      saveBtn.textContent = 'Saving…';

      const v = id => document.getElementById(id)?.value?.trim() || '';
      const payload = {
        action: 'mt_save_billing_profile',
        nonce: document.getElementById('mt_save_billing_nonce')?.value || '',
        billing_first_name: v('billing_first_name'),
        billing_last_name:  v('billing_last_name'),
        billing_email:      v('billing_email'),
        billing_phone:      v('billing_phone'),
        billing_address_1:  v('billing_address_1'),
        billing_address_2:  v('billing_address_2'),
        billing_city:       v('billing_city'),
        billing_state:      v('billing_state'),
        billing_postcode:   v('billing_postcode'),
        billing_country:    v('billing_country'),
      };

      const ajaxUrl =
        (window.wc_checkout_params && window.wc_checkout_params.ajax_url) ||
        '/wp-admin/admin-ajax.php';

      try {
        const resp = await fetch(ajaxUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
          body: new URLSearchParams(payload).toString(),
        });
        const json = await resp.json();
        if (!json?.success) throw new Error(json?.data?.message || 'Could not save billing details.');

        // actualizar la tarjeta resumen
        const name = `${payload.billing_first_name} ${payload.billing_last_name}`.trim() || '—';
        const countryText = json.data?.country_name || '';
        const stateText   = json.data?.state_name || '';
        const cityLine = [payload.billing_city, stateText, payload.billing_postcode].filter(Boolean).join(', ');
        const addrLines = [
          [payload.billing_address_1, payload.billing_address_2].filter(Boolean).join(', '),
          cityLine,
          countryText
        ].filter(Boolean).join('\n');

        const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || '—'; };
        setText('mt-sum-name', name);
        setText('mt-sum-email', payload.billing_email);
        setText('mt-sum-phone', payload.billing_phone);
        setText('mt-sum-address', addrLines);

        // sincroniza inputs de Woo (para Place order)
        setWooVal('billing_first_name', payload.billing_first_name);
        setWooVal('billing_last_name',  payload.billing_last_name);
        setWooVal('billing_email',      payload.billing_email);
        setWooVal('billing_phone',      payload.billing_phone);
        setWooVal('billing_address_1',  payload.billing_address_1);
        setWooVal('billing_address_2',  payload.billing_address_2);
        setWooVal('billing_city',       payload.billing_city);
        setWooVal('billing_state',      payload.billing_state);
        setWooVal('billing_postcode',   payload.billing_postcode);
        setWooVal('billing_country',    payload.billing_country);

        // recalcular checkout (impuestos/totales)
        if (typeof jQuery !== 'undefined') {
          jQuery(document.body).trigger('update_checkout');
        }

        // volver a la vista resumen
        showSummary();

      } catch (err) {
        alert(err.message || 'Error saving billing.');
      } finally {
        // 🔥 ocultar preloader + overlay y restaurar botón
        wcUnblockCheckout();
        hideSitePreloader();
        saveBtn.disabled = false;
        saveBtn.textContent = originalTxt;
      }
    });
  }
}

// Inicializa y reata tras fragment refresh
initBillingSummary();
if (typeof jQuery !== 'undefined') {
  jQuery(document.body).on('updated_checkout', initBillingSummary);
}

(function($){
  function patchNewPaymentLabel(){
    // Puede haber varios gateways; parchea todos los "NEW"
    document.querySelectorAll('li.woocommerce-SavedPaymentMethods-new label[for$="-payment-token-new"]').forEach(label => {
      if (!label || label.dataset.patched) return;

      const title = (label.textContent || 'Use a new payment method').trim();

      // Construimos el contenido con el subtítulo
      const wrap  = document.createElement('div');
      const line1 = document.createElement('div');
      const line2 = document.createElement('div');

      line1.textContent = title;
      line2.textContent = 'Securely pay with a new card';
      line2.className   = 'fw-bold text-14px-line-20px text-a8a29e';

      wrap.appendChild(line1);
      wrap.appendChild(line2);

      label.textContent = '';    // limpiamos el texto original
      label.appendChild(wrap);   // insertamos nuestro bloque
      label.dataset.patched = '1';
    });
  }

  // 1) Primera pasada
  patchNewPaymentLabel();

  // 2) Cuando Woo refresca el checkout o re-inicializa tarjetas
  $(document.body).on('updated_checkout wc-credit-card-form-init payment_method_selected', patchNewPaymentLabel);
})(jQuery);



  // ========== HIDE AUTOMATIC WOOCOMMERCE ERRORS ==========
  // TODO: remove block
  /* 
  const bodyObserver = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (
          node.nodeType === 1 &&
          node.classList.contains("woocommerce-NoticeGroup-checkout")
        ) {
          node.remove(); // O node.style.display = "none";
        }
      });
    });
  });

  bodyObserver.observe(document.body, {
    childList: true,
    subtree: true,
  });
  */
});
