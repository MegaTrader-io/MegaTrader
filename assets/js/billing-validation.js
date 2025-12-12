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
  PaymentId: "#payment",
  NotificationsErrorGroupClass: "woocommerce-error",
  SavedPaymentMethodRadioSelector:
    '.woocommerce-SavedPaymentMethods [type="radio"]',
  NewPaymentMethodRadioSelector:
    '.woocommerce-SavedPaymentMethods-new [type="radio"]',
  PlaceOrderBtnId: "place_order",
  CheckoutForm: "#checkout-form",
};

function setStateWhenReady(stateCode) {
  if (!stateCode) return;
  const wrapper = document.getElementById("billing_state_wrapper");

  const tryApply = () => {
    const select = document.getElementById("billing_state");
    if (!select) return false;

    const match = [...select.options].find((o) => o.value === stateCode);
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
    const opt = [...select.options].find((o) => o.value === stateCode);
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
  // --- DEBUG LOGGER ---
  //const MT_DBG = true;

  function mtNow() {
    return `[MT ${performance.now().toFixed(1)}ms]`;
  }
  function mtLog() {
    if (!window.MT_DBG) return;
    try {
      console.log.apply(console, [mtNow(), ...arguments]);
    } catch (e) {}
  }
  window.mtLog = mtLog;

  // Log global de AJAX (solo lo que nos interesa)
  if (typeof jQuery !== "undefined") {
    jQuery(document).ajaxSend(function (_e, _xhr, o) {
      const mark = (o.url || "") + " " + (o.data || "");
      if (
        mark.includes("wc-ajax=checkout")
        // ||
        // mark.includes("mt_render_order_success_modal")
      ) {
        $.preloader.show();
        mtLog(
          "ajaxSend →",
          o.type || "POST",
          o.url || "(admin-ajax)",
          o.data || ""
        );
      }
    });
    jQuery(document).ajaxComplete(function (_e, xhr, o) {
      const mark = (o.url || "") + " " + (o.data || "");
      if (
        mark.includes("wc-ajax=checkout")
        // ||
        // mark.includes("mt_render_order_success_modal")
      ) {
        mtLog("ajaxComplete ✓", xhr.status, o.url || "(admin-ajax)");
      }
    });
  }

  const debouncedUpdate = (() => {
    let t;
    return () => {
      clearTimeout(t);
      t = setTimeout(() => {
        if (typeof jQuery !== "undefined") {
          jQuery(document.body).trigger("update_checkout");
        }
      }, 300);
    };
  })();

  // ========== DETECT COUNTRY ON FIRST LOAD (IP) ==========

  // --- Helpers para controlar el dropdown de Google Places (deben ir antes de usarse) ---
  const PAC_HIDE_CLASS = "pac-hidden";

  function forceClosePlaces() {
    document.body.classList.add(PAC_HIDE_CLASS);

    const addr = document.getElementById("billing_address_1");
    if (addr) addr.blur();

    document.querySelectorAll(".pac-container").forEach((el) => {
      el.style.display = "none";
      el.setAttribute("aria-hidden", "true");
    });
  }

  function allowPlaces() {
    document.body.classList.remove(PAC_HIDE_CLASS);
    document
      .querySelectorAll('.pac-container[aria-hidden="true"]')
      .forEach((el) => {
        el.style.display = "";
        el.removeAttribute("aria-hidden");
      });
  }

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
            const opt = [...countrySelect.options].find(
              (o) => o.value === detectedCountry
            );
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

    // Mantener cerrado de inicio (evita que se abra al re-entrar en "Change")
    forceClosePlaces();

    // Abrir SOLO cuando el usuario interactúa con el input
    const enablePacOnUserInput = () => allowPlaces();
    addressInput.addEventListener("focus", enablePacOnUserInput);
    addressInput.addEventListener("keydown", enablePacOnUserInput);
    addressInput.addEventListener("input", enablePacOnUserInput);

    // Cerrar al salir del input (con pequeño delay para permitir click en la sugerencia)
    addressInput.addEventListener("blur", () => {
      setTimeout(forceClosePlaces, 150);
    });

    // Cerrar si el usuario hace click fuera del input y del dropdown
    document.addEventListener("click", (e) => {
      const pac = document.querySelector(".pac-container");
      if (!pac) return;
      if (
        e.target?.name === "api_billing_address_1" ||
        e.target === addressInput ||
        pac.contains(e.target)
      )
        return;
      forceClosePlaces();
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
      const addr2 = document.getElementById("billing_address_2");
      if (addr2) {
        addr2.value = fields.billing_address_2 || "";
      }
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

      // Cerrar el dropdown tras seleccionar
      forceClosePlaces();
      debouncedUpdate();
    });
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
    account_username: [
      (value) => value.trim() !== "" || "Email address is a required field.",
      (value) =>
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ||
        "Please enter a valid email address.",
    ],
    account_password: [
      (value) => value.trim() !== "" || "Password is a required field.",
      (value) =>
        value.trim().length >= 8 ||
        "Password must be at least 8 characters long.",
    ],
  };

  function findFieldElement(field) {
    const elByName = document.querySelector(`[name="${field}"]`);
    const elById = document.getElementById(field);
    const finalElement = elByName || elById || null;
    return finalElement;
  }

  function isVisibleField(el) {
    if (!el) return false;
    const rects = el.getClientRects();
    return !!(
      (el.offsetWidth || el.offsetHeight || rects.length) &&
      window.getComputedStyle(el).visibility !== "hidden"
    );
  }

  function validateFormFields(values, rules) {
    const errors = {};

    for (const [field, ruleSet] of Object.entries(rules)) {
      const inputEl = findFieldElement(field);
      const value = (values[field] ?? "").trim();

      // Si el campo no existe o está oculto, lo saltamos
      if (!inputEl) continue;
      if (!isVisibleField(inputEl)) continue;

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
    form
      .querySelectorAll(`.${Selector.ErrorMessageClass}`)
      .forEach((el) => el.remove());
    form
      .querySelectorAll(`.${Selector.InvalidFieldClass}`)
      .forEach((el) => el.classList.remove(Selector.InvalidFieldClass));

    protectedErrors.clear();
  }

  function showErrors(form, errors) {
    for (const [field, message] of Object.entries(errors)) {
      const input = findFieldElement(field);
      if (!input) continue;

      const container =
        input.closest(".form-group") || input.parentElement || input;

      const errorNode = document.createElement("div");
      errorNode.className = Selector.ErrorMessageClass;
      errorNode.textContent = message;

      const existingErrorNode = container.querySelector(
        `.${Selector.ErrorMessageClass}`
      );
      if (existingErrorNode) {
        existingErrorNode.remove();
      }

      container.appendChild(errorNode);
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

  // --- VALIDACIÓN SOLO DEL BLOQUE DE BILLING (para el botón "Save details")
  function validateBillingFormOnly() {
    const container = document.getElementById("mt-billing-form") || document;

    // helper local para leer valores por id
    const val = (id) => document.getElementById(id)?.value?.trim() || "";

    // limpia errores previos solo dentro del bloque de billing
    clearErrors(container);

    // construye valores que vamos a validar
    const values = {
      billing_first_name: val("billing_first_name"),
      billing_last_name: val("billing_last_name"),
      billing_email: val("billing_email"),
      billing_phone: val("billing_phone"),
      billing_address_1: val("billing_address_1"),
      billing_city: val("billing_city"),
      billing_postcode: val("billing_postcode"),
      billing_country: val("billing_country"),
      billing_state: val("billing_state"),
      account_username: val("account_username"),
      account_password: val("account_password"),
    };

    // usa tus mismas reglas, pero sin privacy_policy
    const rules = { ...validationRules };
    delete rules.privacy_policy;

    const errors = validateFormFields(values, rules);

    // Validación extra con intl-tel-input (si está activo)
    try {
      const telInput = document.getElementById("billing_phone");
      const iti = window.iti;
      if (telInput && iti && typeof iti.isValidNumber === "function") {
        if (!iti.isValidNumber()) {
          errors.billing_phone = "Please enter a valid phone number.";
        } else {
          // guarda en formato E.164 para el servidor
          const full = iti.getNumber();
          if (full) telInput.value = full;
        }
      }
    } catch (_) {}

    if (Object.keys(errors).length) {
      showErrors(container, errors);

      // focus/scroll al primer inválido
      const firstInvalid =
        container.querySelector("." + Selector.InvalidFieldClass) ||
        container.querySelector("." + Selector.ErrorMessageClass)
          ?.previousElementSibling;

      if (firstInvalid && typeof firstInvalid.scrollIntoView === "function") {
        firstInvalid.scrollIntoView({ behavior: "smooth", block: "center" });
        setTimeout(() => firstInvalid.focus && firstInvalid.focus(), 250);
      }
      return false;
    }

    return true;
  }

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
    checkoutForm.addEventListener(
      "submit",
      (e) => {
        if (checkoutFormIsInvalid) {
          e.preventDefault();
          if (typeof wcUnblockCheckout === "function") wcUnblockCheckout();
          return false;
        }
        // por si llega a submit sin pasar por el click
        if (!ensurePaymentSelection(e)) {
          e.preventDefault();
          return false;
        }
      },
      true
    );
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
          configMap.forEach((config) => {
            if (node.nodeType === 1 && node.matches(config.matches)) {
              if (!config.completed) {
                config.callbacks.forEach((callback) => {
                  callback(node);
                });

                if (config.once) {
                  config.completed = true;
                }
              }
            }
          });
        });
      });
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  // Exige que haya método de pago "activo", contemplando el 1er checkout sin radios
  function ensurePaymentSelection(e) {
    const savedChecked = document.querySelector(
      Selector.SavedPaymentMethodRadioSelector + ":checked"
    );
    const newChecked = document.querySelector(
      Selector.NewPaymentMethodRadioSelector + ":checked"
    );
    const savedRadios = document.querySelectorAll(
      Selector.SavedPaymentMethodRadioSelector
    );
    const newRadios = document.querySelectorAll(
      Selector.NewPaymentMethodRadioSelector
    );
    const hasRadioUI = savedRadios.length + newRadios.length > 0;

    const paymentRoot = document.querySelector("#payment");
    const ccForms = document.querySelectorAll(
      ".wc-payment-form, .wc-credit-card-form"
    );
    const hasVisibleCC = Array.from(ccForms).some((el) => {
      if (!el) return false;
      const style = window.getComputedStyle(el);
      const visible =
        style.display !== "none" &&
        style.visibility !== "hidden" &&
        el.getClientRects().length > 0;
      return visible;
    });

    // Si hay UI con radios (saved/new), exige que uno esté marcado
    if (hasRadioUI) {
      if (!savedChecked && !newChecked) {
        const host = paymentRoot || document.body;
        let msg = host.querySelector(".invalid-feedback.payment-required");
        if (!msg) {
          msg = document.createElement("div");
          msg.className = "invalid-feedback payment-required";
          msg.textContent =
            "Please select a saved card or choose “Use a new payment method”.";
          (
            document.querySelector(".woocommerce-SavedPaymentMethods") ||
            document.querySelector(".wc-payment-form") ||
            host
          ).appendChild(msg);
        } else {
          msg.style.display = "block";
        }

        // Desbloquea si ya hay overlay
        try {
          if (typeof jQuery !== "undefined") {
            const $form = jQuery("form.checkout, #checkout-form");
            $form.removeClass("processing");
            if (typeof $form.unblock === "function") $form.unblock();
          }
        } catch (_) {}

        (
          document.querySelector(".woocommerce-SavedPaymentMethods") ||
          document.querySelector(".wc-payment-form") ||
          paymentRoot ||
          document.body
        ).scrollIntoView({ behavior: "smooth", block: "center" });

        if (e && typeof e.preventDefault === "function") e.preventDefault();
        return false;
      }
      return true;
    }

    // SIN radios: si hay un form de tarjeta visible, lo tomamos como método activo (primer checkout)
    if (hasVisibleCC) {
      const old = (paymentRoot || document.body).querySelector(
        ".invalid-feedback.payment-required"
      );
      if (old) old.style.display = "none";
      return true;
    }

    // Fallback: si hay un único input payment_method (oculto por el gateway/tema), acéptalo
    const pmRadios = document.querySelectorAll('input[name="payment_method"]');
    if (pmRadios.length === 1) return true;

    // Si no podemos determinar método, bloqueamos y avisamos
    const host = paymentRoot || document.body;
    let msg = host.querySelector(".invalid-feedback.payment-required");
    if (!msg) {
      msg = document.createElement("div");
      msg.className = "invalid-feedback payment-required";
      msg.textContent = "Please select a payment method.";
      (document.querySelector(".wc-payment-form") || host).appendChild(msg);
    } else {
      msg.style.display = "block";
    }
    if (e && typeof e.preventDefault === "function") e.preventDefault();
    return false;
  }

  // ========== PLACE ORDER BTN EVENT ==========
  function addPlaceOrderBtnListeners() {
    const placeOrderBtn = document.getElementById(Selector.PlaceOrderBtnId);
    const checkoutForm = document.querySelector(Selector.CheckoutForm);
    if (!placeOrderBtn || !checkoutForm) return;

    placeOrderBtn.addEventListener(
      "click",
      (e) => {
        mtLog("CLICK place_order");

        // anti-doble click
        if (placeOrderBtn.dataset.mtLocked === "1") {
          e.preventDefault();
          e.stopImmediatePropagation();
          e.stopPropagation();
          return false;
        }

        // limpiar overlays/errores previos
        try {
          if (typeof wcUnblockCheckout === "function") wcUnblockCheckout();
        } catch (_) {}
        if (typeof clearErrors === "function") clearErrors(checkoutForm);
        if (typeof formActionHandler === "function") formActionHandler(e);

        // valida formulario (usa tu flag/función real)
        if (
          typeof checkoutFormIsInvalid !== "undefined" &&
          checkoutFormIsInvalid
        ) {
          e.preventDefault();
          mtLog("VALIDATION → INVALID (no preloader)");
          try {
            if (typeof wcUnblockCheckout === "function") wcUnblockCheckout();
          } catch (_) {}
          try {
            if (typeof hideSitePreloader === "function") hideSitePreloader();
          } catch (_) {}
          // no bloqueamos ni mostramos preloader si es inválido
          return false;
        }

        // asegura método de pago (usa tu ensurePaymentSelection real)
        if (
          typeof ensurePaymentSelection === "function" &&
          !ensurePaymentSelection(e)
        ) {
          mtLog("PAYMENT METHOD → MISSING (no preloader)");
          try {
            if (typeof hideSitePreloader === "function") hideSitePreloader();
          } catch (_) {}
          return false;
        }
        mtLog("PAYMENT METHOD → OK");

        // ✅ bloqueo + freeze + preloader + overlay WC
        try {
          lockPlaceOrderBtn && lockPlaceOrderBtn();
        } catch (_) {}
        try {
          showSitePreloader && showSitePreloader();
        } catch (_) {}
        try {
          wcBlockCheckout && wcBlockCheckout();
        } catch (_) {}

        mtLog(
          "VALIDATION OK → lock + preloader + block (esperando respuesta gateway)"
        );
        // Deja que Woo siga con su submit/AJAX normal
        return true;
      },
      true
    );
  }

  // ========== JQUERY SLIDE ANIMATION ==========
  function slideCollapse(targetElement, isExpanded, useCustom) {
    jQuery(function ($) {
      const duration = 300;
      const $targetEl = $(targetElement);
      if (isExpanded) {
        if (useCustom) {
          targetElement.style.display = "";
          targetElement.style.overflow = "hidden";
          targetElement.style.height = `${targetElement.scrollHeight}px`;
          targetElement.style["padding-bottom"] = "";
          setTimeout(() => {
            targetElement.style.height = "";
            targetElement.style.overflow = "";
          }, duration);
        } else {
          $targetEl.slideDown(duration);
        }
      } else {
        if (useCustom) {
          requestAnimationFrame(() => {
            targetElement.style.overflow = "hidden";
            targetElement.style.height = "0";
            targetElement.style["padding-bottom"] = "0";
          });
        } else {
          $targetEl.slideUp(duration);
        }
      }
    });
  }

  // ========== TOGGLE CC BOX COLLAPSE ==========
  function addPaymentMethodBoxToggleListeners() {
    const savedMethods = document.querySelector(
      ".woocommerce-SavedPaymentMethods"
    );
    if (
      !savedMethods ||
      !savedMethods.getAttribute("data-count") ||
      !parseInt(savedMethods.getAttribute("data-count")) > 0
    ) {
      return;
    }
    const newMethodRadio = document.querySelector(
      Selector.NewPaymentMethodRadioSelector
    );
    const paymentForm = document.querySelector(".wc-payment-form");
    const saveNewCardCheckbox = document.querySelector(
      ".woocommerce-SavedPaymentMethods-saveNew"
    );
    let useCustom;

    // Si no hay elementos, salimos
    if (!newMethodRadio || !paymentForm || !saveNewCardCheckbox) return;

    // Cannot Use Animations Ending on "display: none" like jQuery toggle
    // or NMI input fields iframes won't render properly
    if (paymentForm && paymentForm.id && paymentForm.id.includes("nmi")) {
      useCustom = true;
      paymentForm.classList.add("collapsable");
      saveNewCardCheckbox.classList.add("collapsable");
    }

    const togglePaymentFormCollapse = () => {
      slideCollapse(paymentForm, newMethodRadio.checked, useCustom);
      slideCollapse(saveNewCardCheckbox, newMethodRadio.checked);
    };

    document
      .querySelectorAll(Selector.SavedPaymentMethodRadioSelector)
      .forEach((radio) =>
        radio.addEventListener("click", togglePaymentFormCollapse, true)
      );

    togglePaymentFormCollapse();
  }

  // ========== AUTO SELECT SAVED CREDIT CARD ==========
  function selectFirstSavedPaymentMethodRadio(node = document.body) {
    node.querySelector(Selector.SavedPaymentMethodRadioSelector)?.click();
  }

  // ========== MOVE GLOBAL ERRORS TO TARGET FORM FIELD ==========
  const errorsBlackList = new Set([
    //Email
    `Your email address is invalid.`,
    // Credit Card
    "Your card number is incomplete.",
    `Your card number is invalid.`,
    // Exp Date
    `Your card’s expiration date is incomplete.`,
    `Your card’s expiration year is in the past.`,
    // CVC
    `Your card’s security code is incomplete.`,
    // Policy Checkbox
    `Please accept our Terms of Service and Privacy Policy to continue.`,
  ]);

  function migrateGlobalFieldErrors(node) {
    const errorGroupList = [
      ...document.getElementsByClassName(Selector.NotificationsErrorGroupClass),
    ];

    errorGroupList.forEach((errorGroup) => {
      const inputErrors = Array.from(errorGroup.children).reduce(
        (messageByField, currentError) => {
          const fieldName = currentError.getAttribute("data-id");
          const message = currentError.textContent;

          if (fieldName) {
            messageByField[fieldName] = message;
            currentError.remove();
          } else if (errorsBlackList.has(message.trim())) {
            currentError.remove();
          }

          return messageByField;
        },
        {}
      );

      if (typeof checkoutForm !== "undefined" && checkoutForm) {
        const formData = new FormData(checkoutForm);
        const values = Object.fromEntries(formData.entries());
        const jsErrors = validateFormFields(values, validationRules);

        ["account_username", "account_password"].forEach((field) => {
          if (!inputErrors[field] && jsErrors[field]) {
            inputErrors[field] = jsErrors[field];
          }
        });
      }

      showErrors(checkoutForm, inputErrors);

      if (!errorGroup.children.length) {
        errorGroup.remove();
        stripEmptyNoticeGroups();
      }
    });
  }

  // Elimina grupos de notices vacíos (incluye <div role="alert"></div> sin mensajes)
  function stripEmptyNoticeGroups() {
    document
      .querySelectorAll(".woocommerce-NoticeGroup-checkout")
      .forEach((group) => {
        group.querySelectorAll('[role="alert"]').forEach((alertEl) => {
          const hasMsgs = alertEl.querySelector(
            ".woocommerce-error, .woocommerce-info, .woocommerce-message"
          );
          if (!alertEl.textContent.trim() && !hasMsgs) {
            alertEl.remove();
          }
        });
        const stillHasMsgs = group.querySelector(
          ".woocommerce-error, .woocommerce-info, .woocommerce-message"
        );
        if (!stillHasMsgs) group.remove();
      });
  }

  // ========= MUTATION OBSERVER POOL - ADDITION ============
  mutationObserver([
    {
      matches: Selector.PaymentId,
      callbacks: [addPaymentMethodBoxToggleListeners],
    },
    {
      matches: ".woocommerce-NoticeGroup, .woocommerce-error",
      callbacks: [migrateGlobalFieldErrors],
    },
    {
      matches: `#${Selector.PlaceOrderBtnId}`,
      callbacks: [addPlaceOrderBtnListeners],
    },
  ]);

  // Llamada inicial por si el botón ya está presente
  addPlaceOrderBtnListeners();

  // Limpieza de wrappers vacíos al cargar y en eventos de checkout
  stripEmptyNoticeGroups();
  if (typeof jQuery !== "undefined") {
    jQuery(document.body).on(
      "updated_checkout checkout_error payment_method_selected wc-credit-card-form-init",
      function () {}
    );
  }

  // Intercepta el submit antes del AJAX: si retornamos false, Woo quita el preloader automáticamente
  if (typeof jQuery !== "undefined") {
    jQuery(function ($) {
      $(document.body).on("checkout_place_order", function () {
        // Normaliza teléfono (E.164) si hay intl-tel-input
        try {
          const telInput = document.getElementById("billing_phone");
          if (window.iti && telInput) {
            const full = window.iti.getNumber();
            if (full) telInput.value = full;
          }
        } catch (_) {}

        // Permite no-radios con form de tarjeta visible (primer checkout)
        if (!ensurePaymentSelection()) {
          if (typeof wcUnblockCheckout === "function") wcUnblockCheckout();
          return false;
        }

        // Valida campos requeridos
        const formData = new FormData(checkoutForm);
        const values = Object.fromEntries(formData.entries());
        const errors = validateFormFields(values, validationRules);

        try {
          if (window.iti && !window.iti.isValidNumber()) {
            errors.billing_phone = "Please enter a valid phone number.";
          }
        } catch (_) {}

        if (Object.keys(errors).length) {
          clearErrors(checkoutForm);
          showErrors(checkoutForm, errors);

          const firstInvalid = checkoutForm.querySelector(".is-invalid");
          if (firstInvalid) {
            firstInvalid.scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
            setTimeout(() => firstInvalid.focus && firstInvalid.focus(), 200);
          }

          if (typeof wcUnblockCheckout === "function") wcUnblockCheckout();
          return false;
        }
        return true; // OK
      });

      // Fallback por si algún gateway emite checkout_error
      $(document.body).on("checkout_error", function () {
        try {
          if (typeof wcUnblockCheckout === "function") wcUnblockCheckout();
        } catch (_) {}
        try {
          if (typeof hideSitePreloader === "function") hideSitePreloader();
        } catch (_) {}
        // Desbloquear botón aunque no exista helper global
        try {
          if (typeof unlockPlaceOrderBtn === "function") {
            unlockPlaceOrderBtn();
          } else {
            var btn = document.getElementById("place_order");
            if (btn) btn.disabled = false;
            document.body.classList.remove("mt-placing");
          }
        } catch (_) {}
      });
    });
  }

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
    if (typeof jQuery === "undefined") return;
    const $form = jQuery("form.checkout, #checkout-form");
    if ($form.length && typeof $form.block === "function") {
      $form.addClass("processing").block({
        message: null,
        overlayCSS: { background: "#000", opacity: 0.3 },
      });
    } else {
      $form.addClass("processing");
    }
  }
  function wcUnblockCheckout() {
    if (typeof jQuery === "undefined") return;
    const $form = jQuery("form.checkout, #checkout-form");
    if ($form.length && typeof $form.unblock === "function") {
      $form.removeClass("processing").unblock();
    } else {
      $form.removeClass("processing");
    }
  }

  function forceUnblockCheckout() {
    if (typeof jQuery === "undefined") return;
    try {
      const $form = jQuery("form.checkout, #checkout-form");
      $form.removeClass("processing");
      if ($form.length && typeof $form.unblock === "function") {
        $form.unblock();
      }
      // Por si quedaron overlays huérfanos
      jQuery(".blockUI, .blockOverlay").remove();
    } catch (_) {}
  }

  function lockPlaceOrderBtn() {
    const btn = document.getElementById("place_order");
    if (!btn) return;
    btn.disabled = true;
    btn.dataset.mtLocked = "1";
    document.body.classList.add("mt-placing");
  }
  function unlockPlaceOrderBtn() {
    const btn = document.getElementById("place_order");
    if (!btn) return;
    btn.disabled = false;
    delete btn.dataset.mtLocked;
    document.body.classList.remove("mt-placing");
  }

  // Watchdog: si el overlay queda puesto y no hay AJAX activo, lo quitamos
  // Watchdog: reintenta desbloquear si no hay AJAX activo
  function processingWatchdog(delay = 900, repeats = 4, gap = 900) {
    if (typeof jQuery === "undefined") return;

    const checkOnce = () => {
      try {
        const $form = jQuery("form.checkout, #checkout-form");
        const stillProcessing = $form.hasClass("processing");
        const ajaxActive = typeof jQuery !== "undefined" && jQuery.active > 0;
        if (stillProcessing && !ajaxActive) {
          forceUnblockCheckout();
        }
      } catch (_) {}
    };

    setTimeout(function run() {
      checkOnce();
      if (--repeats > 0) setTimeout(run, gap);
    }, delay);
  }

  // --- helpers para TU preloader (.preloader) ---
  function showSitePreloader() {
    if (typeof jQuery === "undefined") return;
    const $pre = jQuery(".preloader");
    if ($pre.length) $pre.stop(true, true).fadeIn(150);
  }
  function hideSitePreloader() {
    if (typeof jQuery === "undefined") return;
    const $pre = jQuery(".preloader");
    if ($pre.length) $pre.stop(true, true).fadeOut(150);
  }

  // Exponer helpers para thankyou-modal.js
  window.showSitePreloader = showSitePreloader;
  window.hideSitePreloader = hideSitePreloader;
  window.wcBlockCheckout = wcBlockCheckout;
  window.wcUnblockCheckout = wcUnblockCheckout;

  // Exponer helpers + envolver con logs
  const __showPre = showSitePreloader;
  const __hidePre = hideSitePreloader;
  const __blkWC = wcBlockCheckout;
  const __ubWC = wcUnblockCheckout;

  showSitePreloader = function () {
    mtLog("showSitePreloader()");
    return __showPre.apply(this, arguments);
  };
  hideSitePreloader = function () {
    mtLog("hideSitePreloader()");
    return __hidePre.apply(this, arguments);
  };
  wcBlockCheckout = function () {
    mtLog("wcBlockCheckout()");
    return __blkWC.apply(this, arguments);
  };
  wcUnblockCheckout = function () {
    mtLog("wcUnblockCheckout()");
    return __ubWC.apply(this, arguments);
  };

  window.showSitePreloader = showSitePreloader;
  window.hideSitePreloader = hideSitePreloader;
  window.wcBlockCheckout = wcBlockCheckout;
  window.wcUnblockCheckout = wcUnblockCheckout;

  // --- asegura que Woo “vea” los cambios en los inputs ---
  function setWooVal(id, val) {
    const el = document.getElementById(id);
    if (!el) return;
    if (el.value !== val) el.value = val;
    el.dispatchEvent(new Event("input", { bubbles: true }));
    el.dispatchEvent(new Event("change", { bubbles: true }));
  }

  function setAddressHTML(id, linesArray) {
    const el = document.getElementById(id);
    if (!el) return;
    const esc = (s) =>
      String(s)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
    const html = (linesArray || []).filter(Boolean).map(esc).join("<br>");
    el.innerHTML = html;
  }

  function initBillingSummary() {
    const summary = document.getElementById("mt-billing-summary");
    const formBox = document.getElementById("mt-billing-form");
    const changeLn = document.getElementById("mt-billing-change");
    const saveBtn = document.getElementById("mt-save-billing");

    function getStoredState() {
      try {
        return localStorage.getItem("mt_billing_state") || "";
      } catch (_) {
        return "";
      }
    }

    function restoreStateIfEmpty() {
      const el = document.getElementById("billing_state");
      if (!el) return;

      // usa lo que haya guardado; si no hay, no toques nada
      const saved = getStoredState();
      if (!saved) return;

      // Fuerza el valor guardado aunque el select ya tenga otro (Woo lo puede haber reescrito)
      if (el.tagName === "SELECT") {
        if (el.value !== saved) {
          setStateWhenReady(saved);
          // Mantén el lock un poco más (Woo puede refrescar fragmentos con retraso)
          lockStateSelection(saved, 7000);
        }
      } else {
        if (el.value !== saved) {
          el.value = saved;
          el.dispatchEvent(new Event("change", { bubbles: true }));
        }
      }
    }

    const stEl = document.getElementById("billing_state");
    if (stEl && !stEl.dataset.mtRemember) {
      stEl.dataset.mtRemember = "1";
      stEl.addEventListener("change", () => {
        try {
          localStorage.setItem("mt_billing_state", stEl.value || "");
        } catch (_) {}
        // Recalcula cuando cambie el estado
        debouncedUpdate();
      });
    }

    const stOrig = document.getElementById("billing_state_current");
    if (stEl && stOrig && !stEl.value) {
      setStateWhenReady(stOrig.value);
      lockStateSelection(stOrig.value, 2000);
    }

    const countryEl = document.getElementById("billing_country");
    if (countryEl && !countryEl.dataset.mtReset) {
      countryEl.dataset.mtReset = "1";
      countryEl.addEventListener("change", () => {
        // Olvida el state guardado
        try {
          localStorage.removeItem("mt_billing_state");
        } catch (_) {}

        // Limpia el campo state para que Woo repueble según el país
        const st = document.getElementById("billing_state");
        if (st) {
          st.value = "";
          st.dispatchEvent(new Event("change", { bubbles: true }));
        }

        // Vuelve a calcular impuestos/totales
        debouncedUpdate();
      });
    }

    // Recalcular al editar manualmente ZIP, City y Address 1
    ["billing_postcode", "billing_city", "billing_address_1"].forEach((id) => {
      const el = document.getElementById(id);
      if (el && !el.dataset.mtDebounced) {
        el.dataset.mtDebounced = "1";
        el.addEventListener("input", debouncedUpdate);
        el.addEventListener("change", debouncedUpdate);
      }
    });

    function showForm() {
      if (!summary || !formBox) return;
      formBox.classList.remove("d-none");
      summary.classList.add("d-none");
      restoreStateIfEmpty();

      if (typeof jQuery !== "undefined" && jQuery.fn && jQuery.fn.slideDown) {
        jQuery(formBox).stop(true, true).hide().slideDown(200);
      }
    }
    function showSummary() {
      if (!summary || !formBox) return;
      forceClosePlaces(); // <- cerrarlo al volver al resumen
      summary.classList.remove("d-none");
      formBox.classList.add("d-none");
      if (typeof jQuery !== "undefined" && jQuery.fn && jQuery.fn.slideDown) {
        jQuery(summary).stop(true, true).hide().slideDown(200);
      }
    }

    // evita listeners duplicados cuando Woo refresca fragmentos
    if (changeLn && !changeLn.dataset.bound) {
      changeLn.dataset.bound = "1";
      changeLn.addEventListener("click", (e) => {
        e.preventDefault();
        showForm();
      });
    }

    if (saveBtn && !saveBtn.dataset.bound) {
      saveBtn.dataset.bound = "1";
      saveBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        if (!validateBillingFormOnly()) {
          return;
        }

        // 🔥 levantar preloader + overlay
        showSitePreloader();
        wcBlockCheckout();

        const originalTxt = saveBtn.textContent;
        saveBtn.disabled = true;
        saveBtn.textContent = "Saving…";

        const v = (id) => document.getElementById(id)?.value?.trim() || "";
        const payload = {
          action: "mt_save_billing_profile",
          nonce: document.getElementById("mt_save_billing_nonce")?.value || "",
          billing_first_name: v("billing_first_name"),
          billing_last_name: v("billing_last_name"),
          billing_email: v("billing_email"),
          billing_phone: v("billing_phone"),
          billing_address_1: v("billing_address_1"),
          billing_address_2: v("billing_address_2"),
          billing_city: v("billing_city"),
          billing_state: v("billing_state"),
          billing_postcode: v("billing_postcode"),
          billing_country: v("billing_country"),
          account_username: v("account_username"),
          account_password: v("account_password"),
        };

        const ajaxUrl =
          (window.wc_checkout_params && window.wc_checkout_params.ajax_url) ||
          "/wp-admin/admin-ajax.php";

        try {
          const resp = await fetch(ajaxUrl, {
            method: "POST",
            headers: {
              "Content-Type":
                "application/x-www-form-urlencoded; charset=UTF-8",
            },
            body: new URLSearchParams(payload).toString(),
          });
          const json = await resp.json();
          if (!json?.success)
            throw new Error(
              json?.data?.message || "Could not save billing details."
            );

          // actualizar la tarjeta resumen
          const name =
            `${payload.billing_first_name} ${payload.billing_last_name}`.trim() ||
            "—";
          const countryText = json.data?.country_name || "";
          const stateText = json.data?.state_name || "";
          const cityLine = [
            payload.billing_city,
            stateText,
            payload.billing_postcode,
          ]
            .filter(Boolean)
            .join(", ");
          const line1 = [payload.billing_address_1, payload.billing_address_2]
            .filter(Boolean)
            .join(", ");
          setAddressHTML("mt-sum-address", [line1, cityLine, countryText]);

          const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val || "—";
          };
          setText("mt-sum-name", name);
          setText("mt-sum-email", payload.billing_email);
          setText("mt-sum-phone", payload.billing_phone);
          setAddressHTML("mt-sum-address", [line1, cityLine, countryText]);

          // sincroniza inputs de Woo (para Place order)
          setWooVal("billing_first_name", payload.billing_first_name);
          setWooVal("billing_last_name", payload.billing_last_name);
          setWooVal("billing_email", payload.billing_email);
          setWooVal("billing_phone", payload.billing_phone);
          setWooVal("billing_address_1", payload.billing_address_1);
          setWooVal("billing_address_2", payload.billing_address_2);
          setWooVal("billing_city", payload.billing_city);
          setWooVal("billing_state", payload.billing_state);
          setWooVal("billing_postcode", payload.billing_postcode);
          setWooVal("billing_country", payload.billing_country);

          // guarda el último estado válido
          try {
            localStorage.setItem(
              "mt_billing_state",
              payload.billing_state || ""
            );
          } catch (_) {}

          // recalcular checkout (impuestos/totales)
          if (typeof jQuery !== "undefined") {
            jQuery(document.body).trigger("update_checkout");
          }

          // volver a la vista resumen
          forceClosePlaces();
          showSummary();
        } catch (err) {
          alert(err.message || "Error saving billing.");
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
  if (typeof jQuery !== "undefined") {
    jQuery(document.body).on("updated_checkout", function () {
      try {
        const saved = localStorage.getItem("mt_billing_state") || "";
        if (saved) {
          setStateWhenReady(saved);
          lockStateSelection(saved, 7000);
        }
      } catch (_) {}
    });
  }

  (function ($) {
    function patchNewPaymentLabel() {
      // Puede haber varios gateways; parchea todos los "NEW"
      document
        .querySelectorAll(
          'li.woocommerce-SavedPaymentMethods-new label[for$="-payment-token-new"]'
        )
        .forEach((label) => {
          if (!label || label.dataset.patched) return;

          const title = (
            label.textContent || "Use a new payment method"
          ).trim();

          // Construimos el contenido con el subtítulo
          const wrap = document.createElement("div");
          const line1 = document.createElement("div");
          const line2 = document.createElement("div");

          line1.textContent = title;
          line2.textContent = "Securely pay with a new card";
          line2.className = "fw-bold text-14px-line-20px text-a8a29e";

          wrap.appendChild(line1);
          wrap.appendChild(line2);

          label.textContent = ""; // limpiamos el texto original
          label.appendChild(wrap); // insertamos nuestro bloque
          label.dataset.patched = "1";
        });
    }

    // 1) Primera pasada
    patchNewPaymentLabel();

    // 2) Cuando Woo refresca el checkout o re-inicializa tarjetas
    $(document.body).on(
      "updated_checkout wc-credit-card-form-init payment_method_selected",
      patchNewPaymentLabel
    );
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

  /* TODO: Code Block for Order Success Modal (Thank You)
  function renderOrderSuccessModal(orderId, redirectUrl, orderKey) {
    const holder = document.getElementById("mt-order-success-nonce");
    const ajaxUrl =
      (window.wc_checkout_params && window.wc_checkout_params.ajax_url) ||
      "/wp-admin/admin-ajax.php";
    const nonce = holder ? holder.getAttribute("data-nonce") : "";

    // Limpia un modal previo
    const prev = document.getElementById("orderSuccessModal");
    if (prev) prev.remove();

    // LOG
    mtLog("modal FETCH start", { orderId, orderKey, redirectUrl });

    fetch(ajaxUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: new URLSearchParams({
        action: "mt_render_order_success_modal",
        order_id: String(orderId || ""),
        order_key: String(orderKey || ""),
        nonce,
      }).toString(),
    })
      .then((r) => r.json())
      .then((json) => {
        if (!json || !json.success || !json.data || !json.data.html) {
          throw new Error(
            json?.data?.message || "Could not load confirmation."
          );
        }

        // Inyecta HTML del modal
        document.body.insertAdjacentHTML("beforeend", json.data.html);
        mtLog("modal HTML injected");

        const modalEl = document.getElementById("orderSuccessModal");
        if (!modalEl) throw new Error("Modal element not found.");

        // UX: desactivar beforeunload y congelar fondo (no navegamos desde billing)
        try {
          window.onbeforeunload = null;
        } catch (_) {}
        try {
          jQuery(window).off("beforeunload").off("beforeunload.checkout");
        } catch (_) {}
        document.body.classList.add("mt-checkout-frozen");

        // Inicializa lógica del modal externo (redirecciones propias del modal)
        if (
          window.MTSuccessModal &&
          typeof window.MTSuccessModal.init === "function"
        ) {
          window.MTSuccessModal.init(modalEl, {
            redirectUrlFromCheckout: redirectUrl,
          });
        }

        // Mostrar modal: apagamos preloader SOLO cuando el modal está visible
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
          const bs = bootstrap.Modal.getOrCreateInstance(modalEl, {
            backdrop: true,
            keyboard: true,
          });

          modalEl.addEventListener(
            "shown.bs.modal",
            () => {
              mtLog("modal SHOWN → hide preloader + unblock");
              try {
                if (typeof wcUnblockCheckout === "function")
                  wcUnblockCheckout();
              } catch (_) {}
              try {
                if (typeof hideSitePreloader === "function")
                  hideSitePreloader();
              } catch (_) {}
            },
            { once: true }
          );

          modalEl.addEventListener(
            "hidden.bs.modal",
            () => {
              mtLog("modal HIDDEN → unfreeze");
              document.body.classList.remove("mt-checkout-frozen");
              document.body.style.overflow = "";
            },
            { once: true }
          );

          mtLog("modal SHOW() (bootstrap)");
          bs.show();
        } else {
          // Fallback sin Bootstrap
          modalEl.style.display = "block";
          modalEl.classList.add("show");
          document.body.style.overflow = "hidden";

          try {
            if (typeof wcUnblockCheckout === "function") wcUnblockCheckout();
          } catch (_) {}
          try {
            if (typeof hideSitePreloader === "function") hideSitePreloader();
          } catch (_) {}
          mtLog("modal SHOW() (fallback)");

          const closeBtn = modalEl.querySelector("[data-bs-dismiss='modal']");
          if (closeBtn) {
            closeBtn.addEventListener("click", (e) => {
              e.preventDefault();
              modalEl.classList.remove("show");
              modalEl.style.display = "none";
              document.body.classList.remove("mt-checkout-frozen");
              document.body.style.overflow = "";
            });
          }
        }
      })
      .catch((err) => {
        mtLog("modal FETCH ERROR", err && (err.message || err));
        try {
          if (typeof hideSitePreloader === "function") hideSitePreloader();
        } catch (_) {}
        try {
          if (typeof unlockPlaceOrderBtn === "function") unlockPlaceOrderBtn();
        } catch (_) {}
        alert(
          err.message ||
            "Order placed, but we could not show the receipt. Check your email."
        );
      });
  }

  // Intercepta wc-ajax=checkout para NO redirigir y abrir el modal
  
  if (typeof jQuery !== "undefined") {
    jQuery.ajaxPrefilter(function (options, originalOptions, jqXHR) {
      const url = String(options.url || "");
      if (url.indexOf("wc-ajax=checkout") !== -1) {
        mtLog("prefilter HIT", url);
      }

      const origSuccess = options.success;
      options.success = function (data, textStatus, jqXHR2) {
        try {
          if (
            data &&
            data.result === "success" &&
            typeof data.redirect === "string"
          ) {
            // order_id por path o query
            const mPath = data.redirect.match(/order-received\/(\d+)/);
            const mQuery = data.redirect.match(/[?&]order-received=(\d+)/);
            const orderId =
              data.order_id || (mPath ? mPath[1] : mQuery ? mQuery[1] : null);

            // order_key (wc_order_…)
            const k = data.redirect.match(/[?&]key=([^&]+)/);
            const orderKey = k ? k[1] : null;

            mtLog("prefilter SUCCESS → will render modal", {
              orderId,
              orderKey,
              redirect: data.redirect,
            });

            // Desbloquea overlay nativo; NO apagamos preloader aquí
            try {
              if (typeof wcUnblockCheckout === "function") wcUnblockCheckout();
            } catch (_) {}
            try {
              jQuery(
                ".woocommerce-NoticeGroup, .woocommerce-error, .woocommerce-message"
              ).remove();
            } catch (_) {}

            // Evita popup “Leave site?” y congela fondo
            try {
              window.onbeforeunload = null;
            } catch (_) {}
            try {
              jQuery(window).off("beforeunload").off("beforeunload.checkout");
            } catch (_) {}
            document.body.classList.add("mt-checkout-frozen");

            // Abrir modal (el JS del modal controla redirecciones)
            renderOrderSuccessModal(orderId, data.redirect, orderKey);
            return; // NO redirigir
          }
        } catch (e) {
          mtLog("prefilter ERROR", e && (e.message || e));
        }

        if (typeof origSuccess === "function")
          return origSuccess.apply(this, arguments);
      };
    });
  }
  */
});
