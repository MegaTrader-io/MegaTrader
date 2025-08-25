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

  // ========== UTILS ==========

  function formatCurrency(value, format = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  })) {

    return format.format(value);
  }


  // ========== UPDATE PLAN DESCRIPTION WITH ADD-ONS ==========

  function addonsConfigRun(addonsNode) {

    const AddonAction = {
      subtract: subtractActionAdd,
      add: addonActionAdd,
      update: addonActionUpdateValue,
    }

    const AddonClass = {
      planItemAddonApplied: 'addon-applied',
      planItemAddonValue: 'metaInfo__addon',
    }

    function planItemAppendAddonValue(planItemEl, value) {
      const newEl = document.createElement('span');
      newEl.classList.add('metaInfo__addon');
      newEl.innerText = value;
      planItemEl.appendChild(newEl);
    }

    function planApplyAddon(planItemEl, value) {
      planItemEl.classList.add(AddonClass.planItemAddonApplied);
      planItemAppendAddonValue(planItemEl, value)
    }

    function planClearAddon(planItemEl) {
      planItemEl.classList.remove(AddonClass.planItemAddonApplied);
      planItemEl.querySelector(`.${AddonClass.planItemAddonValue}`)?.remove();
    }

    function planGetDefaulValue(planItemEl) {
      return planItemEl.querySelector(`span.metaInfo__value`)?.textContent ?? '';
    }

    function addonActionAdd(planItemEl, { value }, sign = 1) {
      const defaultValue = planGetDefaulValue(planItemEl)
      const number = parseFloat(defaultValue.replace(/[^0-9.-]+/g, ""));
      const result = number + (value * sign);

      const newValue = defaultValue.includes('$') ? formatCurrency(result) : result;
      planApplyAddon(planItemEl, newValue);
    }

    function subtractActionAdd(planItemEl, { value }) {
      addonActionAdd(planItemEl, { value }, -1)
    }

    function addonActionUpdateValue(planItemEl, { value, labelValue }) {
      newValue = planGetDefaulValue(planItemEl) ? value : labelValue ?? value;
      planApplyAddon(planItemEl, newValue);
    }

    function updatePlan(planConfig, active) {
      const planItemEl = document.querySelector(`.metaInfo .${planConfig.field}`);

      if (!planItemEl || !(planConfig.action in AddonAction)) { return; }

      planClearAddon(planItemEl);

      if (active) {
        AddonAction[planConfig.action](planItemEl, planConfig.params);
      }
    }

    function getAddonsMeta(checkboxEl) {
      const name = checkboxEl.value;
      const dataMeta = checkboxEl.getAttribute('data-meta');
      const config = dataMeta ? JSON.parse(dataMeta) ?? null : null
      if (name && config) {
        config.name = name;
        config.active = checkboxEl.checked;
      }

      return config;
    }

    function run() {
      addonsNode.querySelectorAll('[type="checkbox"]').forEach(checkboxEl => {
        const config = getAddonsMeta(checkboxEl);

        if (!config) { return; }

        if (config.plan) {
          updatePlan(config.plan, config.active);
        }
      })
    }

    run();
  }

  // ========== ADDONS UI INIT/SYNC ==========

  function updateAddonsCard(addonsNode) {
    const addons = {};

    // 1. Obtener precio base desde el carrito
    const basePriceEl = document.querySelector(
      ".cart_item .product-total .woocommerce-Price-amount.amount bdi"
    );
    const basePriceText = basePriceEl?.textContent
      ?.replace(/[^\d.,]/g, "")
      .replace(",", "");
    const basePrice = basePriceText ? parseFloat(basePriceText) : null;

    addonsNode.querySelectorAll('[type="checkbox"]').forEach((checkboxEl) => {
      addons[checkboxEl.value] = checkboxEl.checked;
    });

    Object.entries(addons).forEach(([addonKey]) => {
      const labelEl = addonsNode.querySelector(
        `label[for^="e0e87f1_${addonKey}"]`
      );
      const staticEl = document.querySelector(
        `.addons-item.${addonKey} .title`
      );

      if (labelEl && staticEl) {
        const labelText = labelEl.childNodes[0]?.textContent
          ?.trim()
          .split("(")[0]
          .trim();
        const priceText = labelEl
          .querySelector(".woocommerce-Price-amount")
          ?.textContent?.trim()
          ?.replace(/[^\d.,]/g, "")
          ?.replace(",", "");
        const price = priceText ? parseFloat(priceText) : null;

        // 2. Si hay precio base y del addon, calcular %
        let displayText = "";
        if (labelText) {
          if (basePrice && price) {
            const percent = Math.round((price / basePrice) * 100);
            displayText = `<i>${percent}%</i>`;
          } else if (priceText) {
            displayText = `${labelText} <i>$${priceText}</i>`;
          }
          staticEl.innerHTML = displayText;
        }
      }
      const descriptionEl = labelEl?.nextElementSibling;
      const staticDescEl = document.querySelector(`.addons-item.${addonKey} .addons-des`);

      if (descriptionEl && staticDescEl) {
        staticDescEl.textContent = descriptionEl.textContent.trim();
      }
    });
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
    {
      matches: '#wc_checkout_add_ons',
      callbacks: [
        addonsConfigRun,
        updateAddonsCard,
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

  // === BILLING SUMMARY: TOGGLE & SAVE (AJAX) ===
(function billingToggleAndSave() {
  const summary  = document.getElementById('mt-billing-summary');
  const formWrap = document.getElementById('mt-billing-form');
  const changeLink = document.getElementById('mt-billing-change');
  const saveBtn  = document.getElementById('mt-save-billing');

  // Mostrar formulario al hacer "Change"
  if (changeLink && summary && formWrap) {
    changeLink.addEventListener('click', function(e) {
      e.preventDefault();
      summary.style.display = 'none';
      formWrap.style.display = '';
    });
  }

  // Guardar billing en el perfil y volver al resumen
  if (saveBtn) {
    saveBtn.addEventListener('click', async function() {
      const nonce = document.getElementById('mt_save_billing_nonce')?.value || '';
      const fields = ['first_name','last_name','email','phone','address_1','address_2','city','state','postcode','country'];

      const fd = new FormData();
      fd.append('action', 'mt_save_billing_profile');
      fd.append('nonce', nonce);
      fields.forEach(k => {
        const el = document.getElementById('billing_' + k);
        if (el) fd.append(`fields[${k}]`, el.value);
      });

      const ajaxUrl =
        (window.wc_checkout_params && wc_checkout_params.ajax_url) ||
        window.ajaxurl ||
        '/wp-admin/admin-ajax.php';

      try {
        const res = await fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd });
        const json = await res.json();

        if (json && json.success) {
          const d = json.data || {};
          const name  = ((d.first_name || '') + ' ' + (d.last_name || '')).trim() || '—';
          const email = d.email || '—';
          const phone = d.phone || '—';
          const addr  = d.formatted_address ||
                        [d.address_1, d.address_2, [d.city, d.state, d.postcode].filter(Boolean).join(', '), d.country]
                        .filter(Boolean).join(' ');

          const setText = (id, val) => { const n = document.getElementById(id); if (n) n.textContent = val; };
          setText('mt-sum-name', name);
          setText('mt-sum-email', email);
          setText('mt-sum-phone', phone);
          setText('mt-sum-address', addr);

          // Toggle back
          if (formWrap) formWrap.style.display = 'none';
          if (summary) summary.style.display = '';
        } else {
          const msg = (json && json.data && json.data.message) ? json.data.message : 'Could not save billing details.';
          alert(msg);
        }
      } catch (err) {
        console.error(err);
        alert('Network error saving billing.');
      }
    });
  }
})();


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
