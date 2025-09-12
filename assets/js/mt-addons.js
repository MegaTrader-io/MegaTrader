// mt-addons.js
(function () {
  "use strict";

  /* ======================= Helpers ======================= */
  function formatCurrencySafe(value) {
    try {
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
      }).format(value);
    } catch {
      return "$" + String(value);
    }
  }
  function currencyToNumber(text) {
    if (!text) return NaN;
    const num = parseFloat(String(text).replace(/[^\d.-]+/g, ""));
    return isNaN(num) ? NaN : num;
  }
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  /* =========================================================
     1) (MOVIDO DESDE billing-validation.js)
        UPDATE PLAN DESCRIPTION WITH ADD-ONS
     ========================================================= */
  function addonsConfigRun(addonsNode) {
    const AddonAction = {
      subtract: subtractActionAdd,
      add: addonActionAdd,
      update: addonActionUpdateValue,
    };

    const AddonClass = {
      planItemAddonApplied: "addon-applied",
      planItemAddonValue: "metaInfo__addon",
    };

    function planItemAppendAddonValue(planItemEl, value) {
      const newEl = document.createElement("span");
      newEl.classList.add("metaInfo__addon");
      newEl.innerText = value;
      planItemEl.appendChild(newEl);
    }
    function planApplyAddon(planItemEl, value) {
      planItemEl.classList.add(AddonClass.planItemAddonApplied);
      planItemAppendAddonValue(planItemEl, value);
    }
    function planClearAddon(planItemEl) {
      planItemEl.classList.remove(AddonClass.planItemAddonApplied);
      planItemEl.querySelector(`.${AddonClass.planItemAddonValue}`)?.remove();
    }
    function planGetDefaulValue(planItemEl) {
      return (
        planItemEl.querySelector(`span.metaInfo__value`)?.textContent ?? ""
      );
    }
    function addonActionAdd(planItemEl, { value }, sign = 1) {
      const defaultValue = planGetDefaulValue(planItemEl);
      const number = parseFloat(defaultValue.replace(/[^0-9.-]+/g, ""));
      const result = number + value * sign;
      const newValue = defaultValue.includes("$")
        ? formatCurrencySafe(result)
        : result;
      planApplyAddon(planItemEl, newValue);
    }
    function subtractActionAdd(planItemEl, { value }) {
      addonActionAdd(planItemEl, { value }, -1);
    }
    function addonActionUpdateValue(planItemEl, { value, labelValue }) {
      const newValue = planGetDefaulValue(planItemEl)
        ? value
        : labelValue ?? value;
      planApplyAddon(planItemEl, newValue);
    }
    function updatePlan(planConfig, active) {
      const planItemEl = document.querySelector(
        `.metaInfo .${planConfig.field}`
      );
      if (!planItemEl || !(planConfig.action in AddonAction)) return;
      planClearAddon(planItemEl);
      if (active) {
        AddonAction[planConfig.action](planItemEl, planConfig.params);
      }
    }

    function getAddonsMeta(checkboxEl) {
      const name = checkboxEl.value;
      const dataMeta = checkboxEl.getAttribute("data-meta");
      const config = dataMeta ? JSON.parse(dataMeta) ?? null : null;
      if (name && config) {
        config.name = name;
        config.active = checkboxEl.checked;
      }
      return config;
    }

    // Ejecuta una pasada
    addonsNode.querySelectorAll('[type="checkbox"]').forEach((checkboxEl) => {
      const config = getAddonsMeta(checkboxEl);
      if (!config) return;
      if (config.plan) {
        updatePlan(config.plan, config.active);
      }
    });
  }

  /* =========================================================
     2) (MOVIDO DESDE billing-validation.js)
        ADDONS UI INIT/SYNC (texto/porcentaje y descripción)
     ========================================================= */
  function updateAddonsCard(addonsNode) {
    const addons = {};

    // Precio base desde el carrito
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
      const staticEl = document.querySelector(`.addons-item.${addonKey} .title`);

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
      const staticDescEl = document.querySelector(
        `.addons-item.${addonKey} .addons-des`
      );
      if (descriptionEl && staticDescEl) {
        staticDescEl.textContent = descriptionEl.textContent.trim();
      }
    });
  }

  /* =========================================================
     3) LÓGICA EXTRA (Drawdown Buffer & Anytime Payouts)
     ========================================================= */
  const DRAW_BUFFER_DELTA = 500; // +500

  // Busca SOLO el meta real (ignora inyectados)
  function findTrailingMaxDrawdownItem(root = document) {
    let el = root.querySelector(
      '.mt-meta-item[data-meta-key="trailing_max_drawdown"]:not([data-injected])'
    );
    if (el) return el;
    for (const item of $$(".mt-meta-item:not([data-injected])", root)) {
      const labelTxt =
        $(".mt-meta-label", item)?.textContent?.trim().toLowerCase() || "";
      if (labelTxt && /trailing\s*max\s*drawdown/.test(labelTxt)) return item;
    }
    return null;
  }
  function isDrawdownBufferActive(root = document) {
    return !!$(".addons-item.drawdown-buffer.active", root);
  }
  function updateTrailingMaxDrawdownUI(root = document) {
    const item = findTrailingMaxDrawdownItem(root);

    // Si NO existe el meta real y el addon está activo → inyectar texto fijo
    if (!item) {
      if (isDrawdownBufferActive(root)) {
        ensureDrawdownInjected();   // "Extra +500" SIN cálculos
      } else {
        removeDrawdownInjected();
      }
      return; // importante: no continuar, así no sumamos sobre el inyectado
    }

    // Si SÍ existe el meta real, sumar +500 cuando el addon esté activo
    const valueEl = $(".mt-meta-value", item);
    if (!valueEl) return;

    if (!valueEl.dataset.base) {
      valueEl.dataset.base = valueEl.textContent.trim();
    }

    const baseText = valueEl.dataset.base;
    const baseNumber = currencyToNumber(baseText);

    if (isDrawdownBufferActive(root) && !isNaN(baseNumber)) {
      const newNumber = baseNumber + DRAW_BUFFER_DELTA;
      const newText = formatCurrencySafe(newNumber);
      valueEl.innerHTML =
        `<span class="text-decoration-line-through me-2">${baseText}</span>` +
        `<span class="text-primary">${newText}</span>`;
      item.classList.add("addon-applied");
      removeDrawdownInjected(); // limpiar posibles inyectados
    } else {
      valueEl.textContent = baseText;
      item.classList.remove("addon-applied");
      removeDrawdownInjected();
    }
  }

  function findMinTradingDaysToPayoutItem(root = document) {
    let el = root.querySelector(
      '.mt-meta-item[data-meta-key="min_trading_days_to_payout"]'
    );
    if (el) return el;
    const candidates = [
      "min trading days payout",
      "min trading days to payout",
    ];
    for (const item of $$(".mt-meta-item", root)) {
      const lbl = $(".mt-meta-label", item);
      const txt = lbl?.textContent?.trim().toLowerCase() || "";
      if (candidates.includes(txt)) return item;
    }
    return null;
  }
  function isAnytimePayoutsActive(root = document) {
    return !!$(".addons-item.addons-item-new.anytime-payouts.active", root);
  }
  function ensureMetaGrid(root = document) {
    return $(".mt-meta-grid", root) || null;
  }
  function ensureAnytimeInjected(root = document) {
    let injected = $(".mt-meta-item.anytime-injected", root);
    if (injected) return injected;
    const grid = ensureMetaGrid(root);
    if (!grid) return null;

    injected = document.createElement("div");
    injected.className = "mt-meta-item anytime-injected";
    injected.innerHTML = `
      <i class="mt-icon mt-icon_paid"></i>
      <div class="mt-meta-text">
        <span class="mt-meta-label text-primary">Anytime Payouts</span>
        <span class="mt-meta-value">7-Day Rule Waived</span>
      </div>
    `;
    grid.appendChild(injected);
    return injected;
  }

  // ===== Drawdown Buffer: inyección si no existe el meta (texto fijo) =====
  function ensureDrawdownInjected() {
    const grid = document.querySelector(".mt-meta-grid");
    if (!grid) return;
    if (grid.querySelector('.mt-meta-item[data-injected="drawdown-buffer"]'))
      return;

    const el = document.createElement("div");
    el.className = "mt-meta-item";
    el.setAttribute("data-meta-key", "trailing_max_drawdown");
    el.setAttribute("data-injected", "drawdown-buffer");
    el.innerHTML = `
      <i class="mt-icon mt-icon-white mt-icon_max-drawdown"></i>
      <div class="mt-meta-text">
        <span class="mt-meta-label text-primary">Trailing Max Drawdown</span>
        <span class="mt-meta-value">Extra +500</span>
      </div>
    `;
    grid.appendChild(el);
  }
  function removeDrawdownInjected() {
    document
      .querySelectorAll('.mt-meta-item[data-injected="drawdown-buffer"]')
      .forEach((n) => n.remove());
  }

  function applyAnytimeToExisting(item) {
    const iconEl = $(".mt-icon", item);
    const labelEl = $(".mt-meta-label", item);
    const valueEl = $(".mt-meta-value", item);
    if (!iconEl || !labelEl || !valueEl) return;

    if (!item.dataset.anytimeBaseSaved) {
      item.dataset.anytimeBaseSaved = "1";
      labelEl.dataset.baseText = labelEl.textContent.trim();
      labelEl.dataset.baseClass = labelEl.className;
      valueEl.dataset.base = valueEl.textContent.trim();
      iconEl.dataset.baseClass = iconEl.className;
    }
    labelEl.textContent = "Anytime Payouts";
    labelEl.classList.add("text-primary");
    valueEl.textContent = "7-Day Rule Waived";
    iconEl.className = "mt-icon mt-icon_paid";
    item.classList.add("anytime-applied");
  }
  function revertAnytimeFromExisting(item) {
    const iconEl = $(".mt-icon", item);
    const labelEl = $(".mt-meta-label", item);
    const valueEl = $(".mt-meta-value", item);
    if (!iconEl || !labelEl || !valueEl) return;
    if (!item.dataset.anytimeBaseSaved) return;

    if (labelEl.dataset.baseText) labelEl.textContent = labelEl.dataset.baseText;
    if (labelEl.dataset.baseClass) labelEl.className = labelEl.dataset.baseClass;
    if (valueEl.dataset.base) valueEl.textContent = valueEl.dataset.base;
    if (iconEl.dataset.baseClass) iconEl.className = iconEl.dataset.baseClass;
    item.classList.remove("anytime-applied");
  }
  function updateAnytimePayoutsUI(root = document) {
    const active = isAnytimePayoutsActive(root);
    const existing = findMinTradingDaysToPayoutItem(root);
    const injected = $(".mt-meta-item.anytime-injected", root);

    if (active) {
      if (existing) {
        applyAnytimeToExisting(existing);
        if (injected) injected.remove();
      } else {
        ensureAnytimeInjected(root);
      }
    } else {
      if (existing) revertAnytimeFromExisting(existing);
      if (injected) injected.remove();
    }
  }

  /* ======================= Orquestador ======================= */
  function applyAll(root = document) {
    // 1) reflejo/config (addons del plugin → UI)
    const addonsNode =
      $("#wc_checkout_add_ons", root) || $(".checkout-addons", root) || root;
    if (addonsNode) {
      try {
        addonsConfigRun(addonsNode);
      } catch {}
      try {
        updateAddonsCard(addonsNode);
      } catch {}
    }
    // 2) drawdown buffer / anytime payouts
    updateTrailingMaxDrawdownUI(root);
    updateAnytimePayoutsUI(root);
  }

  function watchClassToggle(selector, onChange) {
    const node = $(selector);
    if (!node) return;
    const mo = new MutationObserver((muts) => {
      for (const m of muts) {
        if (m.type === "attributes" && m.attributeName === "class") {
          onChange(document);
          break;
        }
      }
    });
    mo.observe(node, { attributes: true, attributeFilter: ["class"] });
  }

  function bindAddonsNode(addonsNode) {
    if (!addonsNode || addonsNode.dataset.mtAddonsBound) return;
    addonsNode.dataset.mtAddonsBound = "1";
    addonsNode.addEventListener("change", (e) => {
      if (
        e.target &&
        (e.target.matches('input[type="checkbox"]') ||
          e.target.matches('input[type="radio"]'))
      ) {
        applyAll(document);
      }
    });
  }

  function bootstrap() {
    applyAll(document);

    // Reaccionar a toggles de las tarjetas estáticas
    watchClassToggle(".addons-item.drawdown-buffer", applyAll);
    watchClassToggle(".addons-item.addons-item-new.anytime-payouts", applyAll);

    // Enlazar cambios del bloque del plugin (#wc_checkout_add_ons) si existe
    const pluginAddons = $("#wc_checkout_add_ons");
    if (pluginAddons) bindAddonsNode(pluginAddons);

    // Observar DOM por si Woo refresca fragmentos o llegan tarde
    const mo = new MutationObserver((muts) => {
      let need = false;
      muts.forEach((m) => {
        m.addedNodes.forEach((n) => {
          if (
            n.nodeType === 1 &&
            (n.id === "wc_checkout_add_ons" ||
              n.matches?.(".checkout-addons") ||
              n.querySelector?.("#wc_checkout_add_ons, .checkout-addons"))
          ) {
            need = true;
          }
        });
      });
      if (need) {
        const node =
          $("#wc_checkout_add_ons") || $(".checkout-addons") || document;
        bindAddonsNode(node);
        applyAll(document);
      }
    });
    mo.observe(document.body, { childList: true, subtree: true });

    // WooCommerce fragments
    if (window.jQuery && jQuery(document.body)) {
      jQuery(document.body).on("updated_checkout", () => {
        const node =
          $("#wc_checkout_add_ons") || $(".checkout-addons") || document;
        bindAddonsNode(node);
        applyAll(document);
      });
    }
  }

  document.addEventListener("DOMContentLoaded", bootstrap);

  // Opcional: expone para debugging en consola
  window.MTAddons = {
    applyAll,
    addonsConfigRun,
    updateAddonsCard,
  };
})();
