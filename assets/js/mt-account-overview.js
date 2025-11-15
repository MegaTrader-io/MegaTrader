/* mt-account-overview.js */

(function () {
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const clamp = (n, min, max) =>
    Math.max(min, Math.min(max, parseInt(n, 10) || 0));

  /* ========= Donuts ========= */
  function initDonuts(root = document) {
    $$(".mt-donut", root).forEach((d) => {
      const v = clamp(d.dataset.donutValue, 0, 100);
      d.style.setProperty("--mt-donut-value", v);
      d.classList.toggle("is-empty", v === 0);
      const t = $(".mt-donut__percent", d);
      if (t) t.textContent = v ? v + "%" : "--";
    });
  }

  /* ========= Dual bar (Reward/Risk con separador 4px) ========= */
  function initDualBars(root = document) {
    $$(".mt-dualbar", root).forEach((bar) => {
      const reward = clamp(bar.dataset.reward, 0, 100);
      const risk = clamp(bar.dataset.risk, 0, 100);
      const hasAny = reward > 0 || risk > 0;

      bar.classList.toggle("is-empty", !hasAny);
      bar.classList.toggle("has-data", hasAny);

      if (hasAny) {
        bar.style.setProperty("--split", reward + "%");
      } else {
        bar.style.removeProperty("--split");
      }

      const left = $(".mt-dualbar__seg--reward", bar);
      const right = $(".mt-dualbar__seg--risk", bar);

      if (left) {
        left.style.width = hasAny ? reward + "%" : "0%";
        left.classList.toggle("is-full", reward === 100);
      }
      if (right) {
        right.style.width = hasAny ? risk + "%" : "0%";
        right.classList.toggle("is-full", risk === 100);
      }
    });
  }

  /* ========= Progress genérico ========= */
  function initProgressBars(root = document) {
    $$(".mt-progress-bar", root).forEach((el) => {
      if (!el.hasAttribute("data-progress")) return;
      const v = clamp(el.dataset.progress, 0, 100);
      el.style.setProperty("--mt-progress-value", v + "%");
    });
  }

  /* ========= Tabs + carrusel + gradientes ========= */
  function setupTabsCarousel(root) {
    const viewport = $(".mt-tabs-viewport", root);
    const group = $(".mt-toggle-group", root);
    const gradL = $(".mt-tabs-gradient--left", root);
    const gradR = $(".mt-tabs-gradient--right", root);
    const btnPrev = $(".js-tabs-prev", root);
    const btnNext = $(".js-tabs-next", root);

    if (!viewport || !group) return;

    const SCROLL_STEP = () => Math.max(120, group.clientWidth * 0.7);

    function updateGradients() {
      const max = group.scrollWidth - group.clientWidth;

      if (max <= 1) {
        if (gradL) gradL.style.opacity = "0";
        if (gradR) gradR.style.opacity = "0";
        if (btnPrev) btnPrev.disabled = true;
        if (btnNext) btnNext.disabled = true;
        return;
      }

      const x = group.scrollLeft;

      if (gradL) gradL.style.opacity = x > 1 ? "1" : "0";
      if (gradR) gradR.style.opacity = x < max - 1 ? "1" : "0";

      if (btnPrev) btnPrev.disabled = x <= 1;
      if (btnNext) btnNext.disabled = x >= max - 1;
    }

    group.addEventListener("scroll", updateGradients);
    window.addEventListener("resize", updateGradients);

    btnPrev &&
      btnPrev.addEventListener("click", () => {
        group.scrollBy({ left: -SCROLL_STEP(), behavior: "smooth" });
      });
    btnNext &&
      btnNext.addEventListener("click", () => {
        group.scrollBy({ left: SCROLL_STEP(), behavior: "smooth" });
      });

    const activeChip =
      $("[data-fc-tab].active", root) ||
      $('[data-fc-tab][aria-selected="true"]', root);
    if (activeChip) {
      activeChip.scrollIntoView({ inline: "center", block: "nearest" });
    }

    updateGradients();
    return updateGradients;
  }

  function initFeatureTabs() {
    $$(".mt-feature-tabs").forEach((tabsRoot) => {
      const tablist = $("[data-fc-tabs]", tabsRoot);
      const panels = $$(".mt-feature-panel", tabsRoot);
      const refreshGradients = setupTabsCarousel(tabsRoot);

      tablist?.addEventListener("click", (ev) => {
        const chip = ev.target.closest("[data-fc-tab]");
        if (!chip) return;

        const id = chip.getAttribute("data-id");

        $$(".mt-toggle-button", tablist).forEach((c) => {
          const active = c === chip;
          c.classList.toggle("active", active);
          c.setAttribute("aria-selected", active ? "true" : "false");
          c.tabIndex = active ? 0 : -1;
        });

        chip.scrollIntoView({
          behavior: "smooth",
          block: "nearest",
          inline: "center",
        });

        panels.forEach((p) => {
          const match = p.getAttribute("data-panel-for") === id;
          if (match) {
            p.removeAttribute("hidden");
            initDonuts(p);
            initDualBars(p);
            initProgressBars(p);
          } else {
            p.setAttribute("hidden", "");
          }
        });

        if (typeof refreshGradients === "function") refreshGradients();
      });
    });
  }

  function init() {
    initFeatureTabs();
    initDonuts();
    initDualBars();
    initProgressBars();
  }

  if (document.readyState === "loading") {
    console.info("DOM not ready, waiting for it...");
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

/* ======= Interacciones de cuenta (copy + toggle pwd + AJAX) ======= */
(function () {
  /* --- Toast “Copied to clipboard” anclado sobre el click --- */
  const COPY_FEEDBACK_MS = 5000; // 5
  function ensureCopyToastStyle() {
    if (window.__mtCopyToastStyle) return;
    const css = `
      #mt-copy-toast{
        position:fixed;
        left:0; top:0; /* dinámico */
        transform:translate(-50%,-100%);
        background:#000;color:#A8A29E;padding:8px 12px;border-radius:8px;
        font-size:12px;line-height:1;z-index:9999;box-shadow:0 6px 20px rgba(0,0,0,.3);
        opacity:0;transition:opacity .18s ease;pointer-events:none;
        white-space:nowrap;
      }
      #mt-copy-toast.is-visible{opacity:1}
    `;
    const style = document.createElement("style");
    style.textContent = css;
    document.head.appendChild(style);
    window.__mtCopyToastStyle = true;
  }
  function showCopyToast(text, anchorEl) {
    ensureCopyToastStyle();
    let toast = document.getElementById("mt-copy-toast");
    if (!toast) {
      toast = document.createElement("div");
      toast.id = "mt-copy-toast";
      document.body.appendChild(toast);
    }
    toast.textContent = text || "Copied to clipboard";

    const rect =
      anchorEl && anchorEl.getBoundingClientRect
        ? anchorEl.getBoundingClientRect()
        : {
            left: window.innerWidth / 2,
            top: window.innerHeight - 24,
            width: 0,
          };

    const clampNum = (n, min, max) => Math.max(min, Math.min(max, n));
    const centerX = clampNum(
      rect.left + rect.width / 2,
      16,
      window.innerWidth - 16
    );
    const aboveY = clampNum(rect.top - 8, 16, window.innerHeight - 16);

    toast.style.left = `${Math.round(centerX)}px`;
    toast.style.top = `${Math.round(aboveY)}px`;

    toast.classList.add("is-visible");
    clearTimeout(window.__mtCopyToastTimer);
    window.__mtCopyToastTimer = setTimeout(() => {
      toast.classList.remove("is-visible");
    }, COPY_FEEDBACK_MS);
  }

  // Copiar (con toast anclado)
  document.addEventListener("click", (e) => {
    const t = e.target.closest("[data-copy]");
    if (!t) return;
    const v = t.getAttribute("data-copy") || "";
    if (!v) return;

    const onDone = () => {
      t.classList.add("is-copied");
      showCopyToast("Copied to clipboard", t);
      setTimeout(() => t.classList.remove("is-copied"), 1200);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(v).then(onDone).catch(onDone);
    } else {
      const ta = document.createElement("textarea");
      ta.value = v;
      document.body.appendChild(ta);
      ta.select();
      try {
        document.execCommand("copy");
      } catch (err) {}
      document.body.removeChild(ta);
      onDone();
    }
  });

  // Toggle password
  document.addEventListener("click", (e) => {
    const t = e.target.closest(".js-pwd-toggle");
    if (!t) return;

    const row =
      t.closest("[data-pwd-row]") || t.closest(".mt-account-data") || document;
    const mask = row.querySelector(".js-pwd-mask");
    if (!mask) return;

    const real = t.getAttribute("data-pwd") || "";
    const hidden =
      mask.textContent.trim().startsWith("•") ||
      mask.textContent.trim() === "--";

    mask.textContent = hidden ? real : "••••••••••••";
    t.setAttribute("aria-expanded", String(hidden));

    const ic = t.querySelector(".mt-icon");
    if (ic) {
      ic.classList.toggle("mt-icon_visibility");
      ic.classList.toggle("mt-icon_visibility-off");
    }
  });

  // Refrescar Account Data (global)
  window.mtRefreshAccountData = function (accountId) {
    const container =
      document.getElementById("mt-account-data-container") ||
      document.getElementById("mt-performance-data");
    if (!container) return;
    const url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
    const rootNonce =
      document.querySelector("#mt-daily-journal")?.dataset?.nonce;
    const nonce = (window.mtAccounts && mtAccounts.nonce) || rootNonce || "";

    document.querySelector(".preloader")?.classList.add("is-active");

    const body = new URLSearchParams();
    body.set("action", "mt_accounts_data");
    body.set("nonce", nonce);
    body.set("account_id", String(accountId || 0));

    window.MEGATRADER.fetchJSON(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    })
      .then((j) => {
        if (j && j.success && j.data && j.data.html)
          container.innerHTML = j.data.html;
      })
      .catch((err) => {
        window.MEGATRADER?.showError?.(
          "Account Data Error",
          err?.message || "Unable to load account data."
        );
      })
      .finally(() =>
        document.querySelector(".preloader")?.classList.remove("is-active")
      );
  };
})();

// ===== Refresh Bus (centraliza todos los componentes) =====
window.mtRefresh = (function () {
  const handlers = {};
  let inflight = 0;

  // Spinner con retardo para evitar flicker
  let showTimer = null;
  const show = () => {
    if (showTimer) return;
    showTimer = setTimeout(() => {
      document.querySelector(".preloader")?.classList.add("is-active");
    }, 120);
  };
  const hide = () => {
    if (showTimer) {
      clearTimeout(showTimer);
      showTimer = null;
    }
    document.querySelector(".preloader")?.classList.remove("is-active");
  };

  // Track de operaciones (acepta promesas o sync)
  function track(maybePromise) {
    inflight++;
    show();
    const done = () => {
      if (--inflight <= 0) hide();
    };
    if (maybePromise && typeof maybePromise.finally === "function") {
      return maybePromise.finally(done);
    }
    setTimeout(done, 200);
    return undefined;
  }

  return {
    register(name, fn) {
      handlers[name] = fn;
    },

    refreshAll(id) {
      Object.entries(handlers).forEach(([key, fn]) => {
        if (!fn) return;
        const t0 = performance.now();
        track(
          Promise.resolve(fn(id)).finally(() => {
            const t1 = performance.now();
          })
        );
      });
    },

    refreshAllSequence(id) {
      const seq = [];
      const prime = "accountData";
      const heavy = Object.keys(handlers).filter((k) => k !== prime);

      // Step 1: rápido (accountData)
      if (handlers[prime]) {
        const t0 = performance.now();
        seq.push(
          Promise.resolve(handlers[prime](id)).finally(() => {
            const t1 = performance.now();
          })
        );
      }

      // Step 2: el resto en paralelo
      const rest = heavy.map((key) => {
        if (!handlers[key]) return null;
        const t0 = performance.now();
        return Promise.resolve(handlers[key](id)).finally(() => {
          const t1 = performance.now();
        });
      });

      return Promise.all(seq.concat(rest.filter(Boolean)));
    },
  };
})();

/* === Consolidated accountSelected handler (único y seguro) === */
document.addEventListener("mt:accountSelected", (e) => {
  const id = (e && e.detail && (e.detail.accountId || e.detail.id)) || "";
  if (!id) return;

  // Paso 1: refresca todos los módulos (o usa refreshAllSequence si lo tienes)
  // window.mtRefresh?.refreshAll?.(id);
  window.mtRefresh?.refreshAllSequence?.(id); // pinta Account Data primero (si está disponible)

  // Paso 2: chequeo de breach (throttle propio)
  try {
    breachGuardCheck?.(id);
  } catch (_) {}

  // Paso 3: re-sincroniza Manage Subscription
  try {
    syncManageSubscription?.();
  } catch (_) {}

  // Paso 4/5: re-render del breach modal (si existe)
  try {
    const modalRef =
      window.MT_BREACH_MODAL ||
      document.getElementById("mt-breach-modal") ||
      document.querySelector(".mt-breach-modal") ||
      undefined;
    refreshBreachModalUI?.(modalRef);
  } catch (_) {}
});

// ===== Overlay utils (genérico) =====
window.mtOverlay = (function () {
  // Encuentra overlays dentro de un root (varios nombres compatibles)
  function find(root) {
    return Array.from(
      (root || document).querySelectorAll(
        ".account-performance-chart__overlay, .daily-journal__overlay"
      )
    );
  }
  function toggle(root, show) {
    find(root).forEach((el) => {
      if (show) {
        el.removeAttribute("hidden");
        el.setAttribute("aria-hidden", "false");
      } else {
        el.setAttribute("hidden", "");
        el.setAttribute("aria-hidden", "true");
      }
    });
  }
  return { toggle };
})();

// ===== Account Performance (AJAX refresh) =====
(function () {
  if (!window.mtRefresh || typeof window.mtRefresh.register !== "function")
    return;

  window.mtRefresh.register("accountPerformance", function (accountId) {
    var wrap =
      document.querySelector(".mt-account-performance") ||
      document.getElementById("mt-performance-container");
    if (!wrap) return;

    var url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
    var nonce = (window.mtAccounts && mtAccounts.nonce) || "";

    var body = new URLSearchParams();
    body.set("action", "mt_accounts_performance");
    body.set("nonce", nonce);
    body.set("accountId", String(accountId || ""));

    // Cierra tooltips antes de reemplazar (igual que en otros módulos)
    if (window.mtTooltips && typeof window.mtTooltips.closeAll === "function") {
      window.mtTooltips.closeAll();
    }

    return window.MEGATRADER.fetchJSON(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body,
    })
      .then(function (j) {
        if (!j || !j.success || !j.data || typeof j.data.html !== "string")
          return;
        wrap.innerHTML = j.data.html;

        // Re-init UI pequeña (progress bars/donuts) por si hace falta
        try {
          // mismas utilidades que usas arriba
          (function reinit(root) {
            root
              .querySelectorAll(".mt-progress-bar[data-progress]")
              .forEach(function (el) {
                var v = parseInt(el.getAttribute("data-progress"), 10) || 0;
                v = Math.max(0, Math.min(100, v));
                el.style.setProperty("--mt-progress-value", v + "%");
              });
          })(wrap);
        } catch (_) {}

        // Re-init tooltips
        if (
          window.mtTooltips &&
          typeof window.mtTooltips.refresh === "function"
        ) {
          window.mtTooltips.refresh(wrap);
        }
      })
      .catch(function (err) {
        window.MEGATRADER?.showError?.(
          "Account Performance Error",
          err?.message || "Unable to load account performance."
        );
      });
  });
})();

/* ===== Daily Journal (GRID + AJAX + paginación dinámica) ===== */
(function () {
  const qs = (s, r = document) => r.querySelector(s);
  const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));

  function getRoot() {
    return document.getElementById("mt-daily-journal");
  }
  function stateFrom(root) {
    const per = parseInt(root.getAttribute("data-per-page"), 10) || 7;
    return { perPage: per, totalPages: 1, totalRows: 0, currentPage: 1 };
  }
  function findRows(root) {
    return qsa(".dj-row", root);
  }

  function rebuildNumericPager(root, st) {
    const pager = qs(".dj-pager", root);
    if (!pager) return;

    const prev = qs(".mt-dj-prev", pager);
    const next = qs(".mt-dj-next", pager);
    let holder = qs(".dj-pages", pager);
    if (!holder) {
      holder = document.createElement("span");
      holder.className = "dj-pages";
      next ? pager.insertBefore(holder, next) : pager.appendChild(holder);
    }
    holder.innerHTML = "";

    for (let i = 1; i <= st.totalPages; i++) {
      const b = document.createElement("button");
      b.type = "button";
      b.className = "dj-btn dj-num";
      b.textContent = String(i);
      b.addEventListener("click", () => {
        goTo(root, st, i);
      });
      holder.appendChild(b);
    }
  }

  function updatePagerUI(root, st) {
    const pager = qs(".dj-pager", root);
    if (!pager) return;
    const prev = qs(".mt-dj-prev", pager);
    const next = qs(".mt-dj-next", pager);

    prev && (prev.disabled = st.currentPage <= 1);
    next && (next.disabled = st.currentPage >= st.totalPages);

    const nums = qsa(".dj-pages .dj-btn", pager);
    nums.forEach((b, i) =>
      b.classList.toggle("active", i + 1 === st.currentPage)
    );
  }

  function applyPageVisibility(root, st) {
    const rows = findRows(root);
    rows.forEach((row, idx) => {
      const p = Math.floor(idx / st.perPage) + 1;
      const visible = p === st.currentPage;
      row.style.display = visible ? "grid" : "none";
      row.classList.toggle("is-visible", visible);
      row.classList.remove("is-last");
    });
    const visibles = rows.filter((r) => r.classList.contains("is-visible"));
    if (visibles.length) visibles[visibles.length - 1].classList.add("is-last"); // sin borde
  }

  function applyEmptyState(root, st) {
    const has = (st.totalRows || 0) > 0;

    // marca visual para estilos
    root.classList.toggle("is-empty", !has);

    // aria-busy del viewport
    const vp = root.querySelector(".dj-viewport");
    if (vp) vp.setAttribute("aria-busy", has ? "false" : "true");

    // pager visible solo si hay filas
    const pager = root.querySelector(".dj-pager");
    if (pager) {
      if (has) {
        pager.removeAttribute("hidden");
        pager.removeAttribute("aria-hidden");
      } else {
        pager.setAttribute("hidden", "");
        pager.setAttribute("aria-hidden", "true");
      }
    }

    // overlay genérico ON si no hay filas
    if (window.mtOverlay) window.mtOverlay.toggle(root, !has);
  }

  function goTo(root, st, page) {
    st.currentPage = Math.max(1, Math.min(st.totalPages, page));
    applyPageVisibility(root, st);
    updatePagerUI(root, st);
    applyEmptyState(root, st);
  }

  function recalcAndRender(root, st) {
    const rows = findRows(root);
    st.totalRows = rows.length;
    st.totalPages = Math.max(1, Math.ceil(st.totalRows / st.perPage));
    st.currentPage = Math.min(st.currentPage, st.totalPages) || 1;
    rebuildNumericPager(root, st);
    applyPageVisibility(root, st);
    updatePagerUI(root, st);
    applyEmptyState(root, st);
  }

  function fetchDailyJournal(root, accountId) {
    const url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
    const rootNonce =
      document.querySelector("#mt-daily-journal")?.dataset?.nonce;
    const nonce = (window.mtAccounts && mtAccounts.nonce) || rootNonce || "";

    const body = new URLSearchParams();
    body.set("action", "mt_account_daily_journal");
    body.set("nonce", nonce);
    body.set("account_id", String(accountId || ""));

    const scroll = qs(".dj-scroll", root);
    const vp = qs(".dj-viewport", root);
    if (vp) vp.setAttribute("aria-busy", "true");

    return window.MEGATRADER.fetchJSON(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    })
      .then((j) => {
        if (!j?.success || !j?.data) return;
        const { rowsHtml, per_page } = j.data;

        // 1) Reinyectar filas (manteniendo el header)
        if (scroll && rowsHtml != null) {
          Array.from(scroll.querySelectorAll(".dj-row")).forEach((n) =>
            n.remove()
          );
          const tmp = document.createElement("div");
          tmp.innerHTML = rowsHtml;
          Array.from(tmp.querySelectorAll(".dj-row")).forEach((n) =>
            scroll.appendChild(n)
          );
        }

        // 2) Actualizar atributos del ROOT (id y per-page)
        root.setAttribute("data-account-id", String(accountId || ""));
        if (per_page) root.setAttribute("data-per-page", String(per_page));

        // 3) Recalcular estado (y reset a página 1)
        const st = root.__djState || stateFrom(root);
        if (per_page) st.perPage = parseInt(per_page, 10) || st.perPage;
        st.currentPage = 1;

        recalcAndRender(root, st);
        applyEmptyState(root, st);
        root.__djState = st;
      })
      .catch((err) => {
        window.MEGATRADER?.showError?.(
          "Daily Journal Error",
          err?.message || "Unable to load daily journal."
        );
      });
  }

  function bindDailyJournal(root) {
    if (!root || root.__djBound) return;
    root.__djBound = true;

    const st = root.__djState || stateFrom(root);
    const pager = qs(".dj-pager", root);
    const prev = qs(".mt-dj-prev", pager);
    const next = qs(".mt-dj-next", pager);

    prev &&
      prev.addEventListener("click", () => goTo(root, st, st.currentPage - 1));
    next &&
      next.addEventListener("click", () => goTo(root, st, st.currentPage + 1));

    recalcAndRender(root, st);
    root.__djState = st;
    applyEmptyState(root, st);
  }

  function initDJ() {
    const root = getRoot();
    if (!root) return;
    bindDailyJournal(root);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initDJ);
  } else {
    initDJ();
  }

  // Bus global para AJAX futuro
  if (window.mtRefresh && typeof window.mtRefresh.register === "function") {
    window.mtRefresh.register("dailyJournal", function (accountId) {
      const root = getRoot();
      if (!root) return;
      bindDailyJournal(root);
      return fetchDailyJournal(root, accountId);
    });
  }
})();

// ===== Feature Content (AJAX refresh) =====
(function () {
  if (!window.mtRefresh || typeof window.mtRefresh.register !== "function")
    return;

  function clamp(n, min, max) {
    n = parseInt(n, 10) || 0;
    return Math.max(min, Math.min(max, n));
  }

  // Re-init de TODO el feature (donuts + barras) tras inyectar HTML
  function initFeatureContent(root) {
    if (!root) return;

    // Donuts
    root.querySelectorAll(".mt-donut").forEach(function (d) {
      var v = clamp(d.dataset.donutValue, 0, 100);
      d.style.setProperty("--mt-donut-value", v);
      d.classList.toggle("is-empty", v === 0);
      var t = d.querySelector(".mt-donut__percent, .mt-donut__value");
      if (t) t.textContent = v ? v + "%" : "0%";
    });

    // Barras
    root.querySelectorAll(".mt-bar").forEach(function (el) {
      var raw = el.getAttribute("data-bar-value");
      if (raw == null || raw === "") {
        var m = (el.getAttribute("style") || "").match(/--w:\s*([0-9.]+)%/);
        raw = m ? m[1] : 0;
      }
      var v = clamp(raw, 0, 100);
      el.style.setProperty("--w", v + "%");
      el.classList.toggle("is-empty", v === 0);
    });

    // Dualbar
    root.querySelectorAll(".mt-dualbar").forEach(function (db) {
      var reward = clamp(db.getAttribute("data-reward"), 0, 100);
      var risk = clamp(db.getAttribute("data-risk"), 0, 100);
      var segReward = db.querySelector(".mt-dualbar__seg--reward");
      var segRisk = db.querySelector(".mt-dualbar__seg--risk");
      if (segReward) segReward.style.width = reward + "%";
      if (segRisk) segRisk.style.width = risk + "%";
      db.style.setProperty("--split", reward + "%");

      db.classList.toggle("is-empty", reward === 0 && risk === 0);
    });
  }

  window.mtRefresh.register("featureContent", function (accountId) {
    var wrap = document.querySelector(".mt-account-feature-content");
    if (!wrap) return;

    var url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
    var nonce = (window.mtAccounts && mtAccounts.nonce) || "";
    var body = new URLSearchParams();
    body.set("action", "mt_account_feature_content");
    body.set("nonce", nonce);
    body.set("accountId", String(accountId || ""));

    if (window.mtTooltips && typeof window.mtTooltips.closeAll === "function") {
      window.mtTooltips.closeAll();
    }

    return window.MEGATRADER.fetchJSON(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body,
    })
      .then(function (j) {
        if (!j || !j.success || !j.data || j.data.html == null) return;

        wrap.innerHTML = j.data.html;
        initFeatureContent(wrap);

        if (
          window.mtTooltips &&
          typeof window.mtTooltips.refresh === "function"
        ) {
          window.mtTooltips.refresh(wrap);
        }
      })
      .catch(function (err) {
        window.MEGATRADER?.showError?.(
          "Feature Content Error",
          err?.message || "Unable to load feature content."
        );
      });
  });
})();

// ===== Performance Chart (AJAX refresh) =====
(function () {
  if (!window.mtRefresh || typeof window.mtRefresh.register !== "function")
    return;

  // Ejecuta <script> inline dentro del contenedor
  function runInlineScripts(container) {
    container.querySelectorAll("script:not([src])").forEach(function (old) {
      var s = document.createElement("script");
      if (old.type) s.type = old.type;
      s.text = "(function(){\n" + (old.textContent || "") + "\n})();";
      document.body.appendChild(s);
      document.body.removeChild(s);
    });
    var sel = container.querySelector("#lastDaysSelect");
    if (sel && sel.options.length === 1) sel.disabled = true;
  }

  // (Compat) Info para overlay: usa solo data-attrs
  function chartDataInfo(wrap) {
    const carrier =
      wrap.querySelector("[data-points],[data-min-points]") || wrap;
    const rawPts = carrier.getAttribute("data-points");
    const rawMin = carrier.getAttribute("data-min-points");
    const points = Number.isFinite(parseInt(rawPts, 10))
      ? parseInt(rawPts, 10)
      : null;
    const minPts = Number.isFinite(parseInt(rawMin, 10))
      ? parseInt(rawMin, 10)
      : 7;
    return { points, minPoints: minPts };
  }

  function applyChartOverlay(wrap) {
    const overlay = document.querySelector(
      ".account-performance-chart__overlay"
    );
    if (!overlay) return;
    const info = chartDataInfo(wrap);
    const enough = info.points == null ? true : info.points >= info.minPoints;
    const show = !enough;
    overlay.hidden = !show;
    if (window.mtOverlay) window.mtOverlay.toggle(wrap, show);
  }

  // === AJAX ===
  let ctrl = null;
  let reqToken = 0;

  window.mtRefresh.register("performanceChart", function (accountId) {
    var wrap =
      document.querySelector(".mt-account-performance-chart-content") ||
      document;
    var url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
    var nonce = (window.mtAccounts && mtAccounts.nonce) || "";

    try {
      ctrl?.abort();
    } catch (_) {}
    ctrl = new AbortController();
    const myToken = ++reqToken;

    var body = new URLSearchParams();
    body.set("action", "mt_account_performance_chart");
    body.set("nonce", nonce);
    body.set("accountId", String(accountId || ""));

    return window.MEGATRADER.fetchJSON(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body,
      signal: ctrl.signal,
    })
      .then((j) => {
        if (myToken !== reqToken) return;

        const ok = !!(
          j &&
          j.success &&
          j.data &&
          typeof j.data.html === "string"
        );
        if (!ok) {
          const msg =
            (j && j.data && (j.data.message || j.data.error)) ||
            "Unable to load the performance chart.";
          window.MEGATRADER?.showError?.("Chart Error", msg, {
            iconSrc:
              "/wp-content/themes/megatrader-addons/assets/img/error.svg",
            headline: "Oops!",
          });
          return;
        }

        // Sustituye el HTML y deja que el inline se auto-inicialice (con su loader LWC)
        const host =
          document.querySelector(".account-performance-chart.mt-card")
            ?.parentElement || document;
        host.innerHTML = j.data.html;

        const newWrap =
          document.querySelector(".mt-account-performance-chart-content") ||
          host;
        applyChartOverlay(newWrap);

        // Ejecuta scripts inline
        runInlineScripts(newWrap);

        // Reevalúa overlay por si el init actualiza data-points
        setTimeout(function () {
          applyChartOverlay(newWrap);
        }, 150);
      })
      .catch(function (err) {
        if (err?.name === "AbortError") {
          console.warn("[MT] chart AJAX aborted");
          return;
        }
        console.error("[MT] chart AJAX error:", err);
        const msg =
          (err && (err.message || err.statusText)) ||
          "Network or server error while loading the performance chart.";
        window.MEGATRADER?.showError?.("Chart Error", msg, {
          iconSrc: "/wp-content/themes/megatrader-addons/assets/img/error.svg",
          headline: "Oops!",
        });
      });
  });
})();

// ===== Account Data (AJAX refresh) =====
(function () {
  if (!window.mtRefresh || typeof window.mtRefresh.register !== "function")
    return;

  window.mtRefresh.register("accountData", function (accountId) {
    var wrap =
      document.querySelector(".mt-account-data") ||
      document.querySelector("#mt-performance-container");
    if (!wrap) return;

    var url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
    var nonce = (window.mtAccounts && mtAccounts.nonce) || "";

    var body = new URLSearchParams();
    body.set("action", "mt_account_data");
    body.set("nonce", nonce);
    body.set("accountId", String(accountId || ""));

    if (window.mtTooltips && typeof window.mtTooltips.closeAll === "function") {
      window.mtTooltips.closeAll();
    }

    return window.MEGATRADER.fetchJSON(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body,
    })
      .then(function (j) {
        if (!j || !j.success || !j.data || j.data.html == null) return;

        wrap.innerHTML = j.data.html;

        try {
          // initDonuts?.(wrap); initDualBars?.(wrap); initProgressBars?.(wrap);
        } catch (_) {}

        if (
          window.mtTooltips &&
          typeof window.mtTooltips.refresh === "function"
        ) {
          window.mtTooltips.refresh(wrap);
        }
      })
      .catch(function (err) {
        window.MEGATRADER?.showError?.(
          "Account Data Error",
          err?.message || "Unable to load account data."
        );
      });
  });
})();

//======= Feedback Tooltip (popover) =======
(function () {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  const pop = $("#mt-feedback-modal");
  if (!pop) return;
  const panel = $(".mt-modal__panel", pop);
  const arrow = $(".mtfb-arrow", panel) || $(".mtfb-arrow", pop);
  const btnSave = $(".mtfb-save", panel);
  const btnEdit = $(".mtfb-edit", panel);
  const btnClose = $(".mt-modal__close", panel);

  const NOTE_MAX = 58;

  if (panel && !panel.hasAttribute("tabindex"))
    panel.setAttribute("tabindex", "-1");

  let current = { accountId: 0, tradeDate: "", anchor: null, rowSel: "" };
  let isOpen = false;

  function updateSaveEnabled() {
    const hasMood = !!$(".mtfb-mood [data-mood].is-active", panel);
    const hasPlan = !!$('input[name="mtfb-plan"]:checked', panel);
    if (btnSave) btnSave.disabled = !(hasMood && hasPlan);
  }

  function setReadOnly(ro) {
    $$(".mtfb-mood [data-mood]", panel).forEach((b) => (b.disabled = !!ro));
    $$('input[name="mtfb-plan"]', panel).forEach((r) => (r.disabled = !!ro));
    const noteEl = $("#mtfb-note", panel);
    if (noteEl) noteEl.disabled = !!ro;

    if (btnSave) btnSave.hidden = !!ro;
    if (btnEdit) btnEdit.hidden = !ro;

    if (!ro) updateSaveEnabled();
  }

  function positionTo(anchorEl, prefer = "bottom") {
    if (!anchorEl) return;
    pop.hidden = false;
    pop.style.visibility = "hidden";

    const rect = anchorEl.getBoundingClientRect();
    const pw = panel.offsetWidth,
      ph = panel.offsetHeight;
    const gap = 12,
      margin = 8;

    let top = rect.bottom + gap;
    let side = "top";
    if (top + ph + margin > window.innerHeight) {
      top = rect.top - ph - gap;
      side = "bottom";
    }
    let left = rect.left + rect.width / 2 - pw / 2;
    left = Math.max(margin, Math.min(left, window.innerWidth - pw - margin));

    panel.style.top = `${Math.round(top)}px`;
    panel.style.left = `${Math.round(left)}px`;

    if (arrow) {
      arrow.dataset.side = side;
      const ax = rect.left + rect.width / 2 - left - 8;
      arrow.style.left = `${Math.round(Math.max(12, Math.min(ax, pw - 28)))}px`;
    }
    pop.style.visibility = "visible";
  }

  function applyNotePlaceholderPolicy(preset) {
    const noteEl = $("#mtfb-note", panel);
    if (!noteEl) return;
    if (!noteEl.dataset.ph)
      noteEl.dataset.ph = noteEl.getAttribute("placeholder") || "";
    if (preset && (preset.note == null || preset.note === "")) {
      noteEl.setAttribute("placeholder", "");
    } else {
      noteEl.setAttribute("placeholder", noteEl.dataset.ph);
    }
  }

  function resetMoodUI(preset) {
    $$(".mtfb-mood [data-mood]", panel).forEach((btn) => {
      btn.classList.remove("is-active", "mt-icon-primary");
      if (!btn.classList.contains("mt-icon-base"))
        btn.classList.add("mt-icon-base");
    });
    $$('input[name="mtfb-plan"]', panel).forEach((r) => {
      r.checked = false;
      r.removeAttribute("checked");
    });
    $("#mtfb-note", panel).value = "";

    if (preset) {
      const m = Math.max(1, Math.min(5, parseInt(preset.mood || 0, 10)));
      if (m) {
        const b = panel.querySelector(`.mtfb-mood [data-mood="${m}"]`);
        if (b) {
          b.classList.add("is-active", "mt-icon-primary");
          b.classList.remove("mt-icon-base");
        }
      }
      const rp = preset.followed_plan ? "1" : "0";
      const r = panel.querySelector(`input[name="mtfb-plan"][value="${rp}"]`);
      if (r) {
        r.checked = true;
        r.dispatchEvent(new Event("change", { bubbles: true }));
      }
      if (preset.note) $("#mtfb-note", panel).value = preset.note;
    }
    applyNotePlaceholderPolicy(preset);
    updateSaveEnabled();
  }

  function openPopover(anchorEl, { accountId, tradeDate, preset } = {}) {
    current = {
      accountId,
      tradeDate,
      anchor: anchorEl,
      rowSel: `#dj-row-${tradeDate}`,
    };
    resetMoodUI(preset);
    const isViewing = !!preset;
    setReadOnly(isViewing);
    updateSaveEnabled();
    positionTo(anchorEl, "bottom");
    pop.setAttribute("aria-hidden", "false");
    isOpen = true;
    panel?.focus();
  }

  function closePopover() {
    setReadOnly(false);
    pop.hidden = true;
    pop.setAttribute("aria-hidden", "true");
    isOpen = false;
    current.anchor = null;
  }

  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".mt-dj-visibility");
    if (!btn) return;

    const row = btn.closest(".dj-row");
    const tradeDate = row?.dataset.tradeDate || "";
    const root = document.querySelector("#mt-daily-journal");
    const accountId = root?.dataset.accountId || "";
    if (!accountId || !tradeDate) return;

    let preset;
    if (row?.dataset.hasFb === "1") {
      preset = {
        mood: parseInt(row.dataset.mood || "0", 10),
        followed_plan: row.dataset.followed === "1",
        note: row.dataset.note || "",
      };
    }
    openPopover(btn, { accountId, tradeDate, preset });
  });

  document.addEventListener("click", (e) => {
    if (!isOpen) return;
    const inside =
      e.target.closest(".mt-modal__panel") ||
      e.target.closest(".mt-dj-visibility");
    if (!inside) closePopover();
  });
  btnClose?.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    closePopover();
  });

  btnEdit?.addEventListener("click", () => {
    const noteEl = $("#mtfb-note", panel);
    if (noteEl) noteEl.setAttribute("placeholder", noteEl.dataset.ph || "");
    setReadOnly(false);
  });

  ["scroll", "resize"].forEach((ev) => {
    window.addEventListener(
      ev,
      () => {
        if (isOpen && current.anchor) positionTo(current.anchor);
      },
      { passive: true }
    );
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && isOpen) closePopover();
  });

  panel.addEventListener("click", (e) => {
    const b = e.target.closest(".mtfb-mood [data-mood]");
    if (b) {
      if (b.disabled) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return;
      }
      $$(".mtfb-mood [data-mood]", panel).forEach((x) => {
        x.classList.remove("is-active", "mt-icon-primary");
        if (!x.classList.contains("mt-icon-base"))
          x.classList.add("mt-icon-base");
      });
      b.classList.add("is-active", "mt-icon-primary");
      b.classList.remove("mt-icon-base");
      updateSaveEnabled();
      return;
    }
  });

  panel.addEventListener("change", (e) => {
    if (e.target && e.target.name === "mtfb-plan") updateSaveEnabled();
  });

  const noteField = $("#mtfb-note", panel);
  if (noteField) noteField.setAttribute("maxlength", String(NOTE_MAX));
  panel.addEventListener("input", (e) => {
    if (e.target && e.target.id === "mtfb-note") {
      const t = e.target;
      if (t.value.length > NOTE_MAX) t.value = t.value.slice(0, NOTE_MAX);
    }
  });

  $(".mtfb-save", panel)?.addEventListener("click", async () => {
    const url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";

    const rootNonce =
      document.querySelector("#mt-daily-journal")?.dataset?.nonce;
    const nonce = (window.mtAccounts && mtAccounts.nonce) || rootNonce || "";
    if (!nonce) {
      alert("Missing nonce. Reload the page.");
      return;
    }

    const moodBtn = $(".mtfb-mood [data-mood].is-active", panel);
    const planEl = $('input[name="mtfb-plan"]:checked', panel);

    const hasMood = !!moodBtn;
    const hasPlan = !!planEl;
    if (!hasMood || !hasPlan) {
      alert("Select a mood (1–5) AND choose Yes/No.");
      return;
    }

    const noteEl = $("#mtfb-note", panel);
    const rawVal = noteEl ? noteEl.value : "";
    const phVal = noteEl ? noteEl.getAttribute("placeholder") || "" : "";
    const normalize = (s) =>
      (s || "")
        .replace(/\u00A0/g, " ")
        .replace(/\s+/g, " ")
        .trim();
    const normRaw = normalize(rawVal);
    const normPh = normalize(noteEl?.dataset?.ph || phVal);
    const note = normRaw && normRaw !== normPh ? normRaw : "";

    const mood = parseInt(moodBtn.getAttribute("data-mood"), 10);
    const plan = planEl.value === "1" ? "1" : "0";

    const saveBtn = $(".mtfb-save", panel);
    if (saveBtn) saveBtn.disabled = true;
    document.querySelector(".preloader")?.classList.add("is-active");

    const body = new URLSearchParams();
    body.set("action", "mt_save_daily_feedback");
    body.set("nonce", nonce);
    body.set("account_id", String(current.accountId));
    body.set("trade_date", current.tradeDate);
    body.set("mood", String(mood));
    body.set("followed_plan", plan);
    body.set("note", note);

    try {
      const j = await window.MEGATRADER.fetchJSON(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
      });

      if (!j?.success) {
        const msg = j?.data?.msg || `Unexpected error`;
        throw new Error(msg);
      }

      const ic = document.querySelector(
        `${current.rowSel} .mt-dj-visibility .mt-icon`
      );
      if (ic) {
        ic.classList.remove("mt-icon_pencil");
        ic.classList.add("mt-icon_visibility");
      }

      const rowEl = document.querySelector(current.rowSel);
      if (rowEl) {
        rowEl.dataset.hasFb = "1";
        rowEl.dataset.mood = String(mood);
        rowEl.dataset.followed = plan;
        rowEl.dataset.note = note;
      }

      setReadOnly(true);

      if (typeof applyNotePlaceholderPolicy === "function") {
        applyNotePlaceholderPolicy({ note });
      } else if (noteEl) {
        if (note === "") noteEl.setAttribute("placeholder", "");
        else if (noteEl.dataset.ph)
          noteEl.setAttribute("placeholder", noteEl.dataset.ph);
      }

      positionTo(current.anchor, "bottom");
    } catch (err) {
      window.MEGATRADER?.showError?.(
        "Feedback Error",
        err?.message || "Could not save feedback."
      );
    } finally {
      document.querySelector(".preloader")?.classList.remove("is-active");
      if (saveBtn) saveBtn.disabled = false;
    }
  });
})();

// ===== Agreement Modal (centrado + backdrop con clases de Bootstrap) =====
(function () {
  function init() {
    var modal = document.getElementById("mt-agreement-modal");
    if (!modal) return;

    var closeBtn = modal.querySelector(".mt-modal__close");
    var actionBtn = modal.querySelector(".mt-agreement-button");
    var withBackdrop = null;

    function openModal() {
      modal.removeAttribute("hidden");
      modal.setAttribute("aria-hidden", "false");
      modal.classList.add("show");

      modal.style.position = "fixed";
      modal.style.inset = "0";
      modal.style.display = "flex";
      modal.style.alignItems = "center";
      modal.style.justifyContent = "center";
      modal.style.zIndex = "1055";

      withBackdrop = document.createElement("div");
      withBackdrop.className = "modal-backdrop fade show";
      withBackdrop.style.zIndex = "1050";
      document.body.appendChild(withBackdrop);

      document.body.classList.add("modal-open");
      try {
        modal.focus();
      } catch (e) {}
    }

    function closeModal() {
      modal.classList.remove("show");
      modal.setAttribute("aria-hidden", "true");
      modal.setAttribute("hidden", "");
      modal.style.display = "";
      modal.style.position = "";
      modal.style.inset = "";
      modal.style.alignItems = "";
      modal.style.justifyContent = "";
      modal.style.zIndex = "";

      if (withBackdrop && withBackdrop.parentNode) {
        withBackdrop.parentNode.removeChild(withBackdrop);
        withBackdrop = null;
      }
      if (!document.querySelector(".modal.show")) {
        document.body.classList.remove("modal-open");
      }
    }

    if (closeBtn) {
      closeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        closeModal();
      });
    }

    if (actionBtn) {
      actionBtn.addEventListener("click", function () {
        var href = actionBtn.getAttribute("href") || "";
        if (!href || href === "#") return;
        setTimeout(closeModal, 100);
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal.classList.contains("show")) {
        closeModal();
      }
    });

    if (modal.getAttribute("data-show") === "1") {
      if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", openModal);
      } else {
        openModal();
      }
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

// ===== Breach Alert Modal (centrado + backdrop) =====
(function () {
  function init() {
    var modal = document.getElementById("mt-breach-alert-modal");
    if (!modal) return;

    var closeBtn = modal.querySelector(".mt-modal__close");
    var actionBtn = modal.querySelector(".mt-breach-reset-button");
    var withBackdrop = null;

    // === NUEVO: refresca título y botón según data-* (resetId/mainId/accountType) ===
    function refreshBreachModalUI(modalEl) {
      if (!modalEl) return;

      var btn = modalEl.querySelector(".mt-breach-reset-button");
      var descEl =
        modalEl.querySelector("#mtbreach-desc") ||
        modalEl.querySelector(".modal-body .text-2xl.leading-7");

      var ds = modalEl.dataset || {};

      // helpers
      function normalizeId(v) {
        if (v == null) return "";
        var s = String(v).trim().toLowerCase();
        return s === "" || s === "0" || s === "null" || s === "undefined"
          ? ""
          : String(v).trim();
      }
      function normalizeType(t) {
        var raw =
          (t && String(t)) ||
          document
            .getElementById("mt-account-overview")
            ?.getAttribute("data-account-type") ||
          "";
        raw = String(raw).trim().toLowerCase();
        if (raw.startsWith("funded")) return "funded";
        if (raw.startsWith("evaluation") || raw.startsWith("eval"))
          return "evaluation";
        // fallback: intenta detectar palabra aislada
        if (raw.indexOf("funded") >= 0) return "funded";
        if (raw.indexOf("evaluation") >= 0 || raw.indexOf("eval") >= 0)
          return "evaluation";
        return "";
      }

      var resetId = normalizeId(ds.resetId);
      var accountId = normalizeId(ds.accountId);
      var mainId = normalizeId(ds.mainId);
      var accType = normalizeType(ds.accountType);

      var base = (ds.checkoutBase || "/checkout").replace(/(\?|#).*$/, "");
      var subsUrl =
        (ds.subscriptionsUrl || "/subscriptions/").replace(/\/+$/, "") + "/";

      // labels (inyectadas desde PHP)
      var fundedTitle = ds.fundedTitle || "";
      var evaluationTitle = ds.evaluationTitle || "";
      var btnDefault = ds.btnDefault || (btn ? btn.textContent : "");
      var btnNoReset = ds.btnNoReset || "";

      // 1) Descripción por tipo (si hay labels)
      if (descEl) {
        if (accType === "funded" && fundedTitle) {
          descEl.textContent = fundedTitle;
        } else if (accType === "evaluation" && evaluationTitle) {
          descEl.textContent = evaluationTitle;
        }
        // si no hay labels, queda el texto del server
      }

      // 2) Botón (href + label) — nunca ocultar
      if (!btn) return;
      btn.classList.remove("d-none", "disabled");
      btn.removeAttribute("aria-disabled");

      if (resetId) {
        // Con reset: checkout + label default
        btn.href =
          base +
          "?add-to-cart=" +
          encodeURIComponent(resetId) +
          "&account_id=" +
          encodeURIComponent(accountId);
        if (btnDefault) btn.textContent = btnDefault;
      } else if (mainId) {
        // Sin reset + con mainId: ir a /subscriptions/{mainId} + label no_reset
        btn.href = subsUrl + encodeURIComponent(mainId);
        if (btnNoReset) btn.textContent = btnNoReset;
      } else {
        // Sin reset ni mainId (o "0"): /subscriptions/ + label no_reset
        btn.href = subsUrl;
        if (btnNoReset) btn.textContent = btnNoReset;
      }
    }

    function openModal() {
      if (modal.classList.contains("show")) return;

      // NUEVO: refrescar UI antes de abrir
      refreshBreachModalUI(modal);

      modal.removeAttribute("inert");

      modal.removeAttribute("hidden");
      modal.setAttribute("aria-hidden", "false");
      modal.classList.add("show");

      modal.style.position = "fixed";
      modal.style.inset = "0";
      modal.style.display = "flex";
      modal.style.alignItems = "center";
      modal.style.justifyContent = "center";
      modal.style.zIndex = "1055";

      if (!withBackdrop && !document.querySelector(".modal-backdrop.show")) {
        withBackdrop = document.createElement("div");
        withBackdrop.className = "modal-backdrop fade show";
        withBackdrop.style.zIndex = "1050";
        document.body.appendChild(withBackdrop);
      }

      document.body.classList.add("modal-open");
      try {
        modal.focus();
      } catch (e) {}
    }

    function closeModal() {
      try {
        const active = document.activeElement;
        if (active && modal.contains(active)) {
          let fallback =
            modal.__opener ||
            document.querySelector(
              '[data-bs-target="#changeSubcriptionModal"]'
            ) ||
            document.querySelector(
              ".mega-navigation a, .mega-navigation select"
            ) ||
            document.getElementById("mt-account-overview") ||
            document.body;
          const needsTab = fallback && fallback.tabIndex < 0;
          if (needsTab) fallback.setAttribute("tabindex", "-1");
          fallback && fallback.focus({ preventScroll: true });
          if (needsTab) fallback.removeAttribute("tabindex");
        }
      } catch (_) {}

      modal.classList.remove("show");
      modal.setAttribute("aria-hidden", "true");
      modal.setAttribute("hidden", "");
      modal.setAttribute("inert", "");

      modal.style.display = "";
      modal.style.position = "";
      modal.style.inset = "";
      modal.style.alignItems = "";
      modal.style.justifyContent = "";
      modal.style.zIndex = "";

      if (withBackdrop && withBackdrop.parentNode) {
        withBackdrop.parentNode.removeChild(withBackdrop);
        withBackdrop = null;
      }
      if (!document.querySelector(".modal.show")) {
        document.body.classList.remove("modal-open");
      }
    }

    // Sanea estado inicial si el HTML vino en estado inconsistente
    (function sanitizeInitial() {
      var ds = modal.getAttribute("data-show");
      if (ds !== "1" && modal.classList.contains("show")) closeModal();
    })();

    if (closeBtn) {
      closeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        closeModal();
      });
    }

    if (actionBtn) {
      actionBtn.addEventListener("click", function () {
        var href = actionBtn.getAttribute("href") || "";
        if (!href || href === "#") return;
        setTimeout(closeModal, 100);
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal.classList.contains("show")) closeModal();
    });

    document.addEventListener("mt:hasSubscriptionChanged", function () {
      refreshBreachModalUI(modal);
    });

    modal.__open = openModal;
    modal.__close = closeModal;

    if (modal.getAttribute("data-show") === "1") {
      var openNow = function () {
        refreshBreachModalUI(modal); // refrescar en auto-open
        openModal();
      };
      if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", openNow, { once: true });
      } else {
        openNow();
      }
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else {
    init();
  }
})();

/* === fetchStatus (AJAX) — usado por breachGuard y otros === */
function fetchStatus(accountId) {
  var url =
    (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
  var nonce = (window.mtAccounts && mtAccounts.nonce) || "";

  var body = new URLSearchParams();
  body.set("action", "mt_accounts_status");
  if (nonce) body.set("nonce", nonce);
  body.set("accountId", String(accountId || ""));
  body.set("account_id", String(accountId || ""));

  return window.MEGATRADER.fetchJSON(url, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: body,
  }).catch(function () {
    return null;
  });
}

function fetchAccountOverview() {
  var url =
    (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
  var nonce = (window.mtAccounts && mtAccounts.nonce) || "";

  var body = new URLSearchParams();
  body.set("action", "mt_account_overview_data");
  if (nonce) body.set("nonce", nonce);

  MT_DATA.accounts.forEach(function (account) {
    body.append("accountId[]", account.id);
  });

  return window.MEGATRADER.fetchJSON(url, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: body,
  }).catch(function () {
    return null;
  });
}

/* === breachGuard: consulta estado y abre el modal si está BREACHED === */
var __breachGuard = { pending: false, lastId: null, lastAt: 0 };

function breachGuardCheck(accountId) {
  if (!accountId) return;

  var now = Date.now();
  if (__breachGuard.pending) return;
  if (__breachGuard.lastId === accountId && now - __breachGuard.lastAt < 800)
    return;

  __breachGuard.pending = true;
  __breachGuard.lastId = accountId;
  __breachGuard.lastAt = now;

  return fetchStatus(accountId)
    .then(function (j) {
      if (!j || !j.success) return;

      var raw = j.data && j.data.status != null ? String(j.data.status) : "";
      var status = raw.trim().toUpperCase();

      var target = (
        window.mtAccounts && mtAccounts.statusBreached
          ? String(mtAccounts.statusBreached)
          : "BREACHED"
      )
        .trim()
        .toUpperCase();

      if (status === target) {
        try {
          var grid = document.querySelector("#mt-accounts-grid");
          var card =
            grid &&
            (grid.querySelector(".subscription-card.active") ||
              grid.querySelector(
                '.subscription-card[data-account-id="' +
                  CSS.escape(String(accountId)) +
                  '"]'
              ));
          var resetId = card ? card.getAttribute("data-reset-id") || "" : "";
          var modal = document.getElementById("mt-breach-alert-modal");
          var btn = modal
            ? modal.querySelector(".mt-breach-reset-button")
            : null;
          var base =
            (modal && modal.getAttribute("data-checkout-base")) ||
            (window.MT_DATA && window.MT_DATA.checkoutBase) ||
            "/checkout";

          // expone el account_id por si lo necesitas en el checkout
          if (modal)
            modal.setAttribute("data-account-id", String(accountId || ""));

          if (btn) {
            if (resetId) {
              btn.href = base + "?add-to-cart=" + encodeURIComponent(resetId);
              btn.setAttribute("data-account-id", String(accountId || ""));
              btn.classList.remove("disabled");
              btn.removeAttribute("aria-disabled");
            } else {
              btn.href = "#";
              btn.classList.add("disabled");
              btn.setAttribute("aria-disabled", "true");
            }
          }
        } catch (_) {}

        var modalEl = document.getElementById("mt-breach-alert-modal");
        if (!modalEl || modalEl.classList.contains("show")) return;
        if (typeof modalEl.__open === "function") modalEl.__open();
        else {
          modalEl.setAttribute("data-show", "1");
          modalEl.removeAttribute("hidden");
          modalEl.setAttribute("aria-hidden", "false");
          modalEl.classList.add("show");
        }
      }
    })
    .finally(function () {
      __breachGuard.pending = false;
    });
}

/* === Registro en el bus de refresco y listeners === */
if (window.mtRefresh && typeof window.mtRefresh.register === "function") {
  window.mtRefresh.register("breachGuard", breachGuardCheck);
}

window.breachGuardCheck = breachGuardCheck;

// 1) primer chequeo al cargar, si hay selectedId
(function () {
  var firstId = (window.mtAccounts && mtAccounts.selectedId) || "";
  if (document.readyState === "loading") {
    document.addEventListener(
      "DOMContentLoaded",
      function () {
        if (firstId) breachGuardCheck(firstId);
      },
      { once: true }
    );
  } else {
    if (firstId) breachGuardCheck(firstId);
  }
})();

// == Main width var: --mt-main-width (+ --dj-viewport) ==
(function () {
  function clamp(n) {
    return Math.max(784, Math.round(n || 0));
  }

  function calcMainWidth() {
    var page = document.querySelector(".mt-page");
    var side = document.querySelector(".mt-page__sidebar");
    if (!page) return 0;
    var gap = parseFloat(getComputedStyle(page).gap || 0) || 0;
    var pageW = page.clientWidth;
    var sideW = side ? side.getBoundingClientRect().width : 0;
    return clamp(pageW - sideW - gap);
  }

  function applyWidth() {
    var w = calcMainWidth();
    if (!w) return;

    document.documentElement.style.setProperty("--mt-main-width", w + "px");
    document.body.style.setProperty("--mt-main-width", w + "px");

    var dj = document.getElementById("mt-daily-journal");
    if (dj) dj.style.setProperty("--dj-viewport", w + "px");
  }

  function boot() {
    applyWidth();

    var page = document.querySelector(".mt-page");
    var side = document.querySelector(".mt-page__sidebar");

    if (window.ResizeObserver && page) {
      var ro = new ResizeObserver(applyWidth);
      ro.observe(page);
      if (side) {
        var ro2 = new ResizeObserver(applyWidth);
        ro2.observe(side);
        side.addEventListener("transitionend", function (e) {
          if (["width", "flex-basis", "transform"].includes(e.propertyName)) {
            requestAnimationFrame(applyWidth);
          }
        });
      }
    } else {
      var t;
      window.addEventListener("resize", function () {
        clearTimeout(t);
        t = setTimeout(applyWidth, 100);
      });
    }
  }

  if (document.readyState === "loading")
    document.addEventListener("DOMContentLoaded", boot, { once: true });
  else boot();
})();

// === Bind nav -> POST /my-account/orders con orderId actual ===
function mtBindManageSubsNav() {
  var nav = document.querySelector(".mega-navigation");
  if (!nav) return;

  if (nav.__mtBoundManageSubs) return;
  nav.__mtBoundManageSubs = true;

  function readOrderId() {
    var root = document.getElementById("mt-account-overview");
    var raw =
      (root && (root.getAttribute("data-order-id") || root.dataset.orderId)) ||
      "";
    raw = String(raw).replace(/[^\d]/g, "");
    var id = parseInt(raw, 10) || 0;
    try {
      console.debug("[MT][Orders] orderId from data-order-id =", id);
    } catch (_) {}
    return id;
  }

  function ensurePostForm(actionUrl) {
    var form = document.getElementById("mt-manage-subs-form");
    if (!form) {
      form = document.createElement("form");
      form.id = "mt-manage-subs-form";
      form.method = "post";
      form.action =
        actionUrl ||
        (window.location.origin
          ? window.location.origin + "/my-account/orders/"
          : "/my-account/orders/");
      form.className = "d-none";

      var i1 = document.createElement("input");
      i1.type = "hidden";
      i1.name = "orderId";
      form.appendChild(i1);

      var i2 = document.createElement("input");
      i2.type = "hidden";
      i2.name = "optionalOrderId";
      form.appendChild(i2);

      document.body.appendChild(form);
    }
    return form;
  }

  function submitPost(toUrl) {
    var orderId = readOrderId();
    if (!orderId) {
      window.location.href = toUrl;
      return;
    }
    var form = ensurePostForm(toUrl);

    var action =
      toUrl && toUrl.indexOf("/my-account/orders") !== -1
        ? toUrl
        : window.location.origin
        ? window.location.origin + "/my-account/orders/"
        : "/my-account/orders/";
    form.action = action;

    var inp1 = form.querySelector('input[name="orderId"]');
    var inp2 = form.querySelector('input[name="optionalOrderId"]');
    if (inp1) inp1.value = String(orderId);
    if (inp2) inp2.value = String(orderId);

    try {
      console.debug("[MT][Orders] POST ->", form.action, { orderId });
    } catch (_) {}
    form.submit();
  }

  var link = nav.querySelector('a[href*="/my-account/orders"]');
  if (link) {
    link.addEventListener(
      "click",
      function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        submitPost(this.href);
      },
      { passive: false }
    );
  }

  var select = document.getElementById("mega-navigation-select");
  if (select) {
    try {
      select.onchange = null;
      select.removeAttribute("onchange");
    } catch (_) {}
    select.addEventListener(
      "change",
      function (e) {
        var url = this.value || "";
        if (!url) return;

        if (url.indexOf("/my-account/orders") !== -1) {
          e.preventDefault();
          e.stopImmediatePropagation();
          submitPost(url);
        } else {
          window.location.href = url;
        }
      },
      { passive: false }
    );
  }
}

// === Manage Subscription (show/hide + URL) según data-subscription-id del ROOT ===
(function () {
  function getRoot() {
    return document.getElementById("mt-account-overview");
  }

  function readSubscriptionId() {
    // 1) root (lo setea PHP y el picker)
    var root = getRoot();
    var sid =
      (root &&
        (root.getAttribute("data-subscription-id") ||
          root.dataset.subscriptionId)) ||
      "";

    // 2) fallback: card activa en el grid
    if (!sid) {
      var card = document.querySelector(
        "#mt-accounts-grid .subscription-card.active"
      );
      sid =
        (card &&
          (card.getAttribute("data-subscription-id") ||
            card.dataset.subscriptionId)) ||
        "";
    }
    return String(sid).trim();
  }

  function syncManageSubscription() {
    var li = document.getElementById("mt-nav-manage-subscription");
    if (!li) return;
    var a = li.querySelector("a");
    var sid = readSubscriptionId();

    if (sid) {
      var url =
        (window.location.origin || "") +
        "/my-account/view-subscription/" +
        encodeURIComponent(sid) +
        "/";
      if (a) a.href = url;

      // visible
      li.classList.remove("d-none");
      li.removeAttribute("aria-hidden");
      li.removeAttribute("inert");
      if (a) {
        a.removeAttribute("aria-disabled");
      }
    } else {
      // oculto + guard UI
      li.classList.add("d-none");
      li.setAttribute("aria-hidden", "true");
      li.setAttribute("inert", "");
      if (a) {
        a.setAttribute("href", "#");
        a.setAttribute("aria-disabled", "true");
      }
    }
  }

  // boot
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", syncManageSubscription, {
      once: true,
    });
  } else {
    syncManageSubscription();
  }

  // y cuando confirmas con el botón "Select" del picker (por si el root se actualiza ahí)
  document.addEventListener("click", function (e) {
    if (e.target && e.target.closest("#select-subscription-btn")) {
      setTimeout(syncManageSubscription, 0);
    }
  });
})();

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", mtBindManageSubsNav, {
    once: true,
  });
} else {
  mtBindManageSubsNav();
}

/* ===== Passed & Pending Activation Modal ===== */
(function () {
  function normalizeId(v) {
    if (v == null) return "";
    var s = String(v).trim();
    var sl = s.toLowerCase();
    return s === "" || s === "0" || sl === "null" ? "" : s;
  }

  function buildActivationHref(modal, activationId) {
    var base = (modal && modal.dataset.checkoutBase) || "";
    var id = normalizeId(activationId);
    return base && id ? base + "?add-to-cart=" + encodeURIComponent(id) : "#";
  }

  // --- NOTA: Solo se muestra en PASSED. En PENDING_ACTIVATION nunca se muestra nota.
  function setNote(status, activationId, modal) {
    var wrap = modal.querySelector("#mt-passed-note");
    var text = wrap ? wrap.querySelector("[data-note-text]") : null;
    if (!wrap || !text) return;

    var st = String(status || "")
      .trim()
      .toUpperCase();
    if (st === "ACTIVATION_PENDING") st = "PENDING_ACTIVATION";
    var hasId = normalizeId(activationId) !== "";

    var msgWithId = modal.dataset.notePassedWithId || "";
    var msgNoId = modal.dataset.notePassedNoId || "";

    var show = st === "PASSED";

    if (show) {
      text.textContent = hasId ? msgWithId : msgNoId;
      wrap.classList.remove("d-none");
      wrap.removeAttribute("hidden");
      wrap.setAttribute("aria-hidden", "false");
    } else {
      text.textContent = "";
      wrap.classList.add("d-none");
      wrap.setAttribute("hidden", "");
      wrap.setAttribute("aria-hidden", "true");
    }
  }

  function setButton(status, activationId, modal) {
    var btn = modal.querySelector("#mt-activation-btn");
    if (!btn) return;

    var st = String(status || "")
      .trim()
      .toUpperCase();
    if (st === "ACTIVATION_PENDING") st = "PENDING_ACTIVATION";
    var hasId = normalizeId(activationId) !== "";

    var accountId = modal.getAttribute("data-account-id") || "";

    btn.href = buildActivationHref(modal, activationId);

    btn.classList.remove("disabled", "d-none");
    btn.setAttribute("aria-disabled", "false");

    if (st === "PENDING_ACTIVATION" && hasId) {
    } else if (st === "PASSED" && hasId) {
      btn.classList.add("disabled");
      btn.setAttribute("aria-disabled", "true");
    } else if (st === "PASSED" && !hasId) {
      btn.classList.add("d-none");
      btn.href = "#";
    } else {
      btn.classList.add("d-none");
      btn.href = "#";
    }
  }

  // === Body text según status/activationId ===
  function setBodyText(status, activationId, modal) {
    var bodyEl = modal.querySelector("[data-body-text]");
    if (!bodyEl) return;

    var st = String(status || "")
      .trim()
      .toUpperCase();
    if (st === "ACTIVATION_PENDING") st = "PENDING_ACTIVATION";
    var hasId = normalizeId(activationId) !== "";

    var tDefault = modal.dataset.bodyDefault || "";
    var tWithId = modal.dataset.bodyWId || "";
    var tNoId = modal.dataset.bodyNoId || "";

    var next = tDefault;
    if (st === "PENDING_ACTIVATION" && hasId) {
      next = tDefault;
    } else if (st === "PASSED" && hasId) {
      next = tWithId || tDefault;
    } else if (st === "PASSED" && !hasId) {
      next = tNoId || tDefault;
    }
    bodyEl.textContent = next;
  }

  function syncUI(modal) {
    var status = modal.dataset.currentStatus || "";
    var actId = modal.dataset.activationId || "";
    setNote(status, actId, modal);
    setButton(status, actId, modal);
    setBodyText(status, actId, modal);
  }

  function initModal() {
    var modal = document.getElementById("mt-account-passed-modal");
    if (!modal) return;

    var closeBtn = modal.querySelector(".mt-modal__close");
    var actionBtn = modal.querySelector("#mt-activation-btn");
    var withBackdrop = null;

    if (actionBtn && !actionBtn.__mtBoundGuard) {
      actionBtn.addEventListener("click", function (e) {
        if (
          this.classList.contains("disabled") ||
          this.getAttribute("aria-disabled") === "true" ||
          !this.href ||
          this.href.endsWith("#")
        ) {
          e.preventDefault();
          e.stopPropagation();
        }
      });
      actionBtn.__mtBoundGuard = true;
    }

    function openModal() {
      if (modal.classList.contains("show")) return;

      syncUI(modal);

      modal.removeAttribute("hidden");
      modal.setAttribute("aria-hidden", "false");
      modal.classList.add("show");
      modal.setAttribute("data-show", "1");

      modal.style.position = "fixed";
      modal.style.inset = "0";
      modal.style.display = "flex";
      modal.style.alignItems = "center";
      modal.style.justifyContent = "center";
      modal.style.zIndex = "1055";

      modal.removeAttribute("inert");

      if (!withBackdrop && !document.querySelector(".modal-backdrop.show")) {
        withBackdrop = document.createElement("div");
        withBackdrop.className = "modal-backdrop fade show";
        withBackdrop.style.zIndex = "1050";
        document.body.appendChild(withBackdrop);
      }

      document.body.classList.add("modal-open");
      try {
        modal.focus();
      } catch (_) {}
    }

    function closeModal() {
      try {
        const active = document.activeElement;
        if (active && modal.contains(active)) {
          let fallback =
            modal.__opener ||
            document.querySelector(
              '[data-bs-target="#changeSubcriptionModal"]'
            ) ||
            document.querySelector(
              ".mega-navigation a, .mega-navigation select"
            ) ||
            document.getElementById("mt-account-overview") ||
            document.body;
          const needsTab = fallback && fallback.tabIndex < 0;
          if (needsTab) fallback.setAttribute("tabindex", "-1");
          fallback && fallback.focus({ preventScroll: true });
          if (needsTab) fallback.removeAttribute("tabindex");
        }
      } catch (_) {}

      modal.classList.remove("show");
      modal.setAttribute("aria-hidden", "true");
      modal.setAttribute("hidden", "");
      modal.setAttribute("inert", "");
      modal.setAttribute("data-show", "0");

      modal.style.display = "";
      modal.style.position = "";
      modal.style.inset = "";
      modal.style.alignItems = "";
      modal.style.justifyContent = "";
      modal.style.zIndex = "";

      if (withBackdrop && withBackdrop.parentNode) {
        withBackdrop.parentNode.removeChild(withBackdrop);
        withBackdrop = null;
      }
      if (!document.querySelector(".modal.show")) {
        document.body.classList.remove("modal-open");
      }
    }

    // Saneamos estado inicial si llega mal
    (function sanitizeInitial() {
      var ds = modal.getAttribute("data-show");
      if (ds !== "1" && modal.classList.contains("show")) {
        closeModal();
      }
    })();

    if (closeBtn) {
      closeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        closeModal();
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal.classList.contains("show")) closeModal();
    });

    modal.__open = openModal;
    modal.__close = closeModal;

    if (modal.getAttribute("data-show") === "1") {
      if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", openModal, {
          once: true,
        });
      } else {
        openModal();
      }
    }
  }

  function getActivationIdFromDOM(accountId) {
    var sel =
      '#mt-accounts-grid .subscription-card[data-account-id="' +
      String(accountId).replace(/"/g, "&quot;") +
      '"]';
    var card =
      document.querySelector(sel) ||
      document.querySelector(
        '.subscription-card[data-account-id="' +
          String(accountId).replace(/"/g, "&quot;") +
          '"]'
      );
    if (!card) return "";
    return card.getAttribute("data-activation-id") || "";
  }

  var __passedGuard = { pending: false, lastId: null, lastAt: 0 };

  function passedGuardCheck(accountId) {
    if (!accountId) return;
    var now = Date.now();
    if (__passedGuard.pending) return;
    if (__passedGuard.lastId === accountId && now - __passedGuard.lastAt < 800)
      return;

    __passedGuard.pending = true;
    __passedGuard.lastId = accountId;
    __passedGuard.lastAt = now;

    return fetchStatus(accountId)
      .then(function (j) {
        if (!j || !j.success) return;

        var statusRaw =
          j.data && j.data.status != null ? String(j.data.status) : "";
        var status = statusRaw.trim().toUpperCase();
        if (status === "ACTIVATION_PENDING") status = "PENDING_ACTIVATION";

        var actId =
          j.data && (j.data.activationProductId || j.data.activation_product_id)
            ? j.data.activationProductId || j.data.activation_product_id
            : getActivationIdFromDOM(accountId);

        if (status === "PASSED" || status === "PENDING_ACTIVATION") {
          var modal = document.getElementById("mt-account-passed-modal");
          if (!modal) return;

          // Guardamos también el accountId en el modal para construir el href
          modal.dataset.currentStatus = status;
          modal.dataset.activationId = actId == null ? "" : String(actId);
          modal.setAttribute("data-account-id", String(accountId || ""));

          if (typeof modal.__open === "function") {
            modal.__open();
          } else {
            modal.setAttribute("data-show", "1");
            syncUI(modal);
            modal.removeAttribute("hidden");
            modal.setAttribute("aria-hidden", "false");
            modal.classList.add("show");
          }
        }
      })
      .finally(function () {
        __passedGuard.pending = false;
      });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initModal, { once: true });
  } else {
    initModal();
  }

  if (window.mtRefresh && typeof window.mtRefresh.register === "function") {
    window.mtRefresh.register("passedGuard", passedGuardCheck);
  }
  window.passedGuardCheck = passedGuardCheck;

  (function () {
    var firstId = (window.mtAccounts && mtAccounts.selectedId) || "";
    if (document.readyState === "loading") {
      document.addEventListener(
        "DOMContentLoaded",
        function () {
          if (firstId) passedGuardCheck(firstId);
        },
        { once: true }
      );
    } else {
      if (firstId) passedGuardCheck(firstId);
    }
  })();

  window.__mtPassedSyncUI = function (modal) {
    modal = modal || document.getElementById("mt-account-passed-modal");
    if (modal) syncUI(modal);
  };
})();

// --- POST silencioso al checkout con account_id en el body (sin exponerlo en la URL)
(function attachMtModalPostCheckout() {
  if (window.__mtModalPostCheckoutBound) return;
  window.__mtModalPostCheckoutBound = true;

  function postTo(url, fields) {
    var form = document.createElement("form");
    form.method = "post";
    form.action = url.replace(/\?.*$/, "");
    form.className = "d-none";

    var m = (url.match(/[?&]add-to-cart=([^&#]+)/) || [])[1];
    if (m) {
      var inpATC = document.createElement("input");
      inpATC.type = "hidden";
      inpATC.name = "add-to-cart";
      inpATC.value = decodeURIComponent(m);
      form.appendChild(inpATC);
    }

    Object.keys(fields || {}).forEach(function (k) {
      var v = fields[k];
      if (v == null || v === "") return;
      var inp = document.createElement("input");
      inp.type = "hidden";
      inp.name = k;
      inp.value = String(v);
      form.appendChild(inp);
    });

    document.body.appendChild(form);
    form.submit();
  }

  // Intercepta clicks (breach, activation y el botón nuevo)
  document.addEventListener(
    "click",
    function (ev) {
      var el =
        ev.target && ev.target.closest
          ? ev.target.closest(
              ".mt-breach-reset-button, #mt-activation-btn, .account-reset-button"
            )
          : null;
      if (!el) return;

      var href = el.getAttribute("href") || "";
      if (!href || href === "#") return;

      var host = el.closest(
        "#mt-breach-alert-modal, #mt-account-passed-modal, .mt-picker-wrap, #mt-account-overview"
      );
      if (!host) return;

      var accountId =
        el.getAttribute("data-account-id") ||
        host.getAttribute("data-account-id") ||
        "";
      if (!accountId) return;

      ev.preventDefault();
      ev.stopPropagation();

      var fields = { account_id: accountId };
      var mainProductId = host.getAttribute("data-main-product-id") || "";
      if (mainProductId) fields.main_product_id = mainProductId;

      postTo(href, fields);
    },
    { capture: true }
  );
})();

/* === Notifications toggle === */
(function () {
  const CONTAINER_SEL = ".mt-my-profile__notifications";
  const PANEL_SEL = ".mt-account-notifications";
  const TOGGLE_ID = "#mt-notifications-toggle";
  const ANCHOR_SEL = ".mt-card.mt-card-dark.h-auto";

  // === util: cuántas notis quedan visibles ===
  function visibleCount(panel) {
    return panel.querySelectorAll("[data-mt-notif-item]").length;
  }

  // ==== Tooltip “You are up to date.” (dinámico) ====
  function ensureBrandedTooltip(container, text) {
    const toggle = container.querySelector(TOGGLE_ID);
    if (!toggle) return;

    let wrapper = toggle.closest(".mt-tooltip");
    if (!wrapper) {
      wrapper = document.createElement("span");
      wrapper.className = "mt-tooltip";
      wrapper.setAttribute("data-placement", "right");

      const parent = toggle.parentNode;
      parent.insertBefore(wrapper, toggle);
      wrapper.appendChild(toggle);

      const panel = document.createElement("span");
      panel.className = "mt-tooltip__panel";
      panel.setAttribute("role", "tooltip");

      const body = document.createElement("div");
      body.className = "mt-tooltip__body";
      body.textContent = text || "You are up to date.";
      panel.appendChild(body);
      wrapper.appendChild(panel);

      try {
        window.mtTooltips && window.mtTooltips.refresh(wrapper);
      } catch (_) {}
    } else {
      const body = wrapper.querySelector(".mt-tooltip__body");
      if (body) body.textContent = text || "You are up to date.";
      try {
        window.mtTooltips && window.mtTooltips.refresh(wrapper);
      } catch (_) {}
    }
  }

  function removeBrandedTooltip(container) {
    const toggle = container.querySelector(TOGGLE_ID);
    if (!toggle) return;
    const wrapper = toggle.closest(".mt-tooltip");
    if (wrapper) {
      try {
        window.mtTooltips && window.mtTooltips.closeAll();
      } catch (_) {}
      const parent = wrapper.parentNode;
      parent.insertBefore(toggle, wrapper);
      wrapper.remove();
      try {
        window.mtTooltips && window.mtTooltips.refresh(parent || document);
      } catch (_) {}
    }
  }

  // === enciende/apaga la campana según haya items ===
  function syncBell(container) {
    const toggle = container.querySelector(TOGGLE_ID);
    const panel = container.querySelector(PANEL_SEL);
    if (!toggle || !panel) return;

    const icon = toggle.querySelector(".mt-icon");
    const has = visibleCount(panel) > 0;

    if (icon) icon.classList.toggle("mt-icon-success", has);
    toggle.setAttribute("aria-disabled", has ? "false" : "true");

    if (!has) {
      ensureBrandedTooltip(container, "You are up to date.");
      toggle.setAttribute("aria-expanded", "false");
    } else {
      removeBrandedTooltip(container);
    }
  }

  // === AJAX: ocultar en BD ===
  function sendHide(panel, uid) {
    try {
      const url = panel?.dataset?.ajaxUrl;
      const nonce = panel?.dataset?.nonce;
      if (!url || !uid) return;

      const fd = new FormData();
      fd.append("action", "mt_user_notif_hide");
      fd.append("uid", uid);
      fd.append("nonce", nonce);

      fetch(url, {
        method: "POST",
        credentials: "same-origin",
        body: fd,
      }).catch(() => {});
    } catch (_) {}
  }

  function uidFromBtn(btn, article) {
    return (
      btn?.dataset?.uid ||
      btn?.dataset?.id ||
      (function () {
        const chip = article?.querySelector(".mt-chip");
        return chip ? chip.textContent.trim() : "";
      })()
    );
  }

  // ==== Layout / toggle ====
  function eachContainer(cb) {
    document.querySelectorAll(CONTAINER_SEL).forEach(cb);
  }
  function getAnchor(container) {
    return container.closest(ANCHOR_SEL) || container;
  }

  function layoutToAnchor(container) {
    const panel = container.querySelector(PANEL_SEL);
    if (!panel) return;
    const anchor = getAnchor(container);
    const w = anchor.clientWidth || anchor.getBoundingClientRect().width || 360;
    panel.style.width = w + "px";
    const acs = getComputedStyle(anchor);
    if (acs.position === "static") anchor.style.position = "relative";
    if (acs.overflow !== "visible") anchor.style.overflow = "visible";
    panel.style.position = "absolute";
    panel.style.right = "0";
    panel.style.top = "100%";
    panel.style.zIndex = "1060";
  }

  function openPanel(container) {
    const toggle = container.querySelector(TOGGLE_ID);
    const panel = container.querySelector(PANEL_SEL);
    if (!toggle || !panel) return;

    syncBell(container);
    if (visibleCount(panel) === 0) {
      toggle.setAttribute("aria-expanded", "false");
      return;
    }

    const rectToggle = toggle.getBoundingClientRect();
    const GAP = 8,
      panelWidth = 360;
    panel.style.position = "fixed";
    panel.style.width = panelWidth + "px";

    let left = Math.round(rectToggle.right + GAP);
    const vw = window.innerWidth || document.documentElement.clientWidth;
    if (left + panelWidth > vw - 8) {
      left = Math.round(rectToggle.left - panelWidth - GAP);
      if (left < 8) left = 8;
    }

    let top = Math.round(rectToggle.top);
    const vh = window.innerHeight || document.documentElement.clientHeight;
    if (top > vh - 8) top = vh - 8;

    panel.style.left = left + "px";
    panel.style.top = top + "px";
    panel.style.zIndex = "2000";
    panel.hidden = false;
    panel.classList.add("is-open");
    panel.style.setProperty("display", "block", "important");
    panel.style.setProperty("visibility", "visible", "important");
    panel.style.setProperty("opacity", "1", "important");

    toggle.setAttribute("aria-expanded", "true");
    try {
      panel.focus({ preventScroll: true });
    } catch (_) {}

    document.addEventListener("click", onDocClick, true);
    document.addEventListener("keydown", onKey, true);
  }

  function closePanel(container) {
    const toggle = container.querySelector(TOGGLE_ID);
    const panel = container.querySelector(PANEL_SEL);
    if (!toggle || !panel) return;

    panel.classList.remove("is-open");
    panel.hidden = true;
    [
      "position",
      "width",
      "left",
      "top",
      "zIndex",
      "display",
      "visibility",
      "opacity",
    ].forEach((p) => panel.style.removeProperty(p));

    toggle.setAttribute("aria-expanded", "false");
    document.removeEventListener("click", onDocClick, true);
    document.removeEventListener("keydown", onKey, true);
  }

  function onDocClick(ev) {
    const inScope =
      ev.target.closest(CONTAINER_SEL) &&
      (ev.target.closest(TOGGLE_ID) || ev.target.closest(PANEL_SEL));
    if (!inScope) eachContainer(closePanel);
  }
  function onKey(ev) {
    if (ev.key === "Escape") eachContainer(closePanel);
  }

  function bindHide(panel) {
    panel.addEventListener(
      "click",
      function (e) {
        const btn = e.target.closest("[data-mt-notif-hide]");
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();

        const article =
          btn.closest("[data-mt-notif-item]") ||
          btn.closest(".mt-notification-account") ||
          btn.closest("article");

        const uid = uidFromBtn(btn, article);
        if (!uid || !article) return;

        // quita de UI y sincroniza BD
        article.remove();
        sendHide(panel, uid);

        const container = panel.closest(CONTAINER_SEL) || document;
        syncBell(container);
        if (visibleCount(panel) === 0) {
          closePanel(container);
          try {
            window.mtTooltips && window.mtTooltips.refresh(container);
          } catch (_) {}
        }
      },
      true
    );

    panel.addEventListener(
      "keydown",
      function (e) {
        const btn = e.target.closest("[data-mt-notif-hide]");
        if (!btn) return;
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          btn.click();
        }
      },
      true
    );
  }

  function initContainer(container) {
    const toggle = container.querySelector(TOGGLE_ID);
    const panel = container.querySelector(PANEL_SEL);
    if (!toggle || !panel) return;

    container.style.position = "";
    container.style.overflow = "";

    const relayout = () => layoutToAnchor(container);
    window.addEventListener("resize", relayout);
    window.addEventListener("orientationchange", relayout);

    toggle.addEventListener(
      "click",
      function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        const isOpen = !panel.hidden;
        isOpen ? closePanel(container) : openPanel(container);
      },
      true
    );

    panel.querySelectorAll("[data-mt-notif-close]").forEach((btn) => {
      btn.addEventListener(
        "click",
        function (e) {
          e.preventDefault();
          e.stopPropagation();
          closePanel(container);
          try {
            toggle.focus({ preventScroll: true });
          } catch (_) {}
        },
        { capture: true }
      );
    });

    bindHide(panel);
    syncBell(container);
    try {
      window.mtTooltips && window.mtTooltips.refresh(container);
    } catch (_) {}
  }

  function init() {
    eachContainer(initContainer);
  }
  if (document.readyState !== "loading") init();
  else document.addEventListener("DOMContentLoaded", init);
})();

// load picker + modal account selection (hydrate desde MT_DATA)
(function () {
  const MODAL_ID = "changeSubcriptionModal";
  const PICKER_SRC =
    (window.MT_ASSETS && MT_ASSETS.picker_src) ||
    "/wp-content/themes/megatrader-addons/assets/js/mt-account-picker.js";
  const FALLBACK_LOGO =
    "/wp-content/themes/megatrader-addons/assets/svg/icon_megatrader.svg";
  const ric =
    window.requestIdleCallback ||
    function (cb) {
      return setTimeout(
        () => cb({ didTimeout: false, timeRemaining: () => 0 }),
        1
      );
    };

  // ---- Cookie helpers ----
  function getLastAccountKey() {
    try {
      var uid =
        (window.MT_DATA && (MT_DATA.userId || MT_DATA.user || MT_DATA.uid)) ||
        "";
      return "mt:lastAccountId" + (uid ? ":" + String(uid) : "");
    } catch (_) {
      return "mt:lastAccountId";
    }
  }
  function loadLastAccountId() {
    try {
      var key = getLastAccountKey().replace(/[-[\]/{}()*+?.\\^$|]/g, "\\$&");
      var m = document.cookie.match(
        new RegExp("(?:^|;)\\s*" + key + "=([^;]+)")
      );
      return m ? decodeURIComponent(m[1]) : "";
    } catch (_) {
      return "";
    }
  }

  // ---- Util ----
  function esc(s) {
    return String(s || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function normalizeStatus(s) {
    var st = String(s || "")
      .trim()
      .toUpperCase();
    return st === "ACTIVATION_PENDING" ? "PENDING_ACTIVATION" : st;
  }
  function statusToBucket(st) {
    st = normalizeStatus(st);
    if (st === "ACTIVE") return "ACTIVE";
    if (st === "PENDING_ACTIVATION") return "PENDING_ACTIVATION";
    if (st === "PASSED" || st === "UPGRADED") return "PASSED";
    if (st === "BREACHED") return "BREACHED";
    return "ACTIVE";
  }

  // ---- Render del grid ----
  function renderGridFromData(accounts, currentId) {
    console.info("renderGridFromData", accounts, currentId);
    const grid = document.getElementById("mt-accounts-grid");
    if (!grid) return;
    if (!accounts || !accounts.length) {
      grid.innerHTML =
        '<p class="text-a8a29e m-3"><em>No accounts found for this user.</em></p>';
      grid.removeAttribute("data-grid-empty");
      return;
    }
    let html = "";
    for (const a of accounts) {
      const aid = String(a.id || "");
      const platId = String(a.accountId || "");
      const isCur = aid === String(currentId || "");
      const cls =
        "subscription-card position-relative flex-column gap-2" +
        (isCur ? " active" : "");
      const statusRaw = String(a.status || "").trim();
      const statusKey = statusRaw.toLowerCase().replace(/[^a-z0-9]+/g, "-");
      const dotClass = "dot-status-" + statusKey;
      const logo = String(a.logo || "") || FALLBACK_LOGO;

      html += `
      <div class="${cls}" role="button"
        data-account-id="${esc(aid)}"
        data-status="${esc(statusRaw)}"
        data-platform-account-id="${esc(platId)}"
        data-size="${esc(a.size || "")}"
        data-reset-id="${esc(a.resetProductId || "")}"
        data-activation-id="${esc(a.activationProductId || "")}"
        data-name="${esc(a.name || "Account")}"
        data-account-type="${esc(a.programTypeText || "")}"
        data-logo="${esc(logo)}"
        data-main-id="${esc(a.mainProductId || "")}"
        data-order-id="${esc(a.order || 0)}"
        data-has-subscription="${
          a.subscriptionId || a.hasSubscription ? "1" : "0"
        }"
        data-subscription-id="${esc(a.subscriptionId || "")}">
        <div class="checkmark-icon position-absolute" style="top:10px;right:10px;${
          isCur ? "" : "display:none;"
        }">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" fill="#FFB34A" />
            <path d="M10.6 16.6L17.65 9.55L16.25 8.15L10.6 13.8L7.75 10.95L6.35 12.35L10.6 16.6Z" fill="black"/>
          </svg>
        </div>
        <div class="subscription-card__header text-center position-relative d-flex flex-column align-items-center">
          <div class="logo-container position-relative d-inline-block">
            <img src="${esc(
              logo
            )}" alt="platform logo" style="max-height:40px;" onerror="this.onerror=null;this.src='${esc(
        FALLBACK_LOGO
      )}'">
            <div class="dot-indicator ${esc(dotClass)}" title="${esc(
        statusRaw
      )}" style="position:absolute;right:-1px;bottom:-1px;">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                <circle cx="6" cy="6" r="6" fill="white" />
                <circle cx="6" cy="6" r="4" fill="currentColor" />
              </svg>
            </div>
          </div>
        </div>
        <div class="subscription-card__body text-center">
          <div class="subscription-card__name fw-medium text-base text-white">
            ${esc((a.size || "") + " " + (a.name || "Account"))}
          </div>
          <div class="subscription-card__id text-14px-line-20px text-a8a29e text-uppercase text-truncate">#${esc(
            platId || aid
          )}</div>
          ${
            a.programTypeText && a.programTypeClass
              ? `<div class="mt-2 badge-mega badge-mega-sm badge-mega-fit-content ${esc(
                  a.programTypeClass
                )}">${esc(a.programTypeText)}</div>`
              : ""
          }
        </div>
      </div>`;
    }
    grid.innerHTML = html;
    grid.removeAttribute("data-grid-empty");
    ric(() => {
      try {
        window.mtTooltips && window.mtTooltips.refresh(grid);
      } catch (_) {}
    });

    markActiveInGrid();
    forceFilterForActive();
    enableSelectIfAny();
  }

  // ---- Helpers de UI ----
  function cssEscapePoly(s) {
    return String(s).replace(/[^a-zA-Z0-9_\-]/g, "\\$&");
  }
  const cssEscape = window.CSS && CSS.escape ? CSS.escape : cssEscapePoly;

  function markActiveInGrid() {
    var grid = document.getElementById("mt-accounts-grid");
    if (!grid) return;

    var cookieId = loadLastAccountId();
    var currentId = cookieId || (window.MT_DATA && MT_DATA.currentId) || "";
    if (!currentId) {
      var opener = document.querySelector(
        '[data-bs-target="#' + MODAL_ID + '"][data-account-id]'
      );
      if (opener) currentId = opener.getAttribute("data-account-id") || "";
    }

    grid.querySelectorAll(".subscription-card.active").forEach(function (el) {
      el.classList.remove("active");
    });
    grid
      .querySelectorAll(".subscription-card .checkmark-icon")
      .forEach(function (el) {
        el.style.display = "none";
      });

    if (currentId) {
      var card = grid.querySelector(
        '.subscription-card[data-account-id="' + cssEscape(currentId) + '"]'
      );
      if (card) {
        card.classList.add("active");
        var ck = card.querySelector(".checkmark-icon");
        if (ck) ck.style.display = "block";
      }
    }
  }

  function forceFilterForActive() {
    var grid = document.getElementById("mt-accounts-grid");
    if (!grid) return;
    var active = grid.querySelector(".subscription-card.active");
    var wantBucket = "ACTIVE";
    if (active) {
      var st = active.getAttribute("data-status") || "";
      wantBucket = statusToBucket(st);
    } else {
      var sel = document.getElementById("mt-acc-filter");
      wantBucket = sel && sel.value ? sel.value : "ACTIVE";
    }

    var map = {
      ACTIVE: "Active",
      BREACHED: "Breached",
      PASSED: "Passed",
      PENDING_ACTIVATION: "Pending activation",
    };
    var lbl = document.getElementById("mt-acc-filter-label");
    if (lbl) lbl.textContent = map[wantBucket] || "Active";

    grid.querySelectorAll(".subscription-card").forEach(function (card) {
      var st = statusToBucket(card.getAttribute("data-status") || "");
      var match =
        (wantBucket === "ACTIVE" && st === "ACTIVE") ||
        (wantBucket === "BREACHED" && st === "BREACHED") ||
        (wantBucket === "PASSED" && st === "PASSED") ||
        (wantBucket === "PENDING_ACTIVATION" && st === "PENDING_ACTIVATION");
      if (match) {
        card.classList.add("d-flex");
        card.classList.remove("d-none");
        card.style.removeProperty("display");
      } else {
        card.classList.remove("d-flex");
        card.classList.add("d-none");
        card.style.setProperty("display", "none");
      }
    });
  }

  function enableSelectIfAny() {
    var grid = document.getElementById("mt-accounts-grid");
    var btn = document.getElementById("select-subscription-btn");
    if (!grid || !btn) return;
    var hasAny = !!grid.querySelector(".subscription-card:not(.d-none)");
    btn.disabled = !hasAny;
    btn.classList.toggle("disabled", !hasAny);
  }

  // ---- Hidratación al abrir ----
  function hydrateOnOpen() {
    const grid = document.getElementById("mt-accounts-grid");
    if (!grid || !grid.dataset.gridEmpty) return;
    const data = window.MT_DATA || {};
    const accounts = data.accounts || [];

    var cur = loadLastAccountId();
    if (!cur) {
      cur = data.currentId || "";
    } else {
      var exists = accounts.some((a) => String(a.id || "") === String(cur));
      if (!exists) cur = data.currentId || "";
    }

    performance.mark("mt-hydrate-start");
    ric(() => {
      renderGridFromData(accounts, cur);
      performance.mark("mt-hydrate-end");
      performance.measure("mt-hydrate", "mt-hydrate-start", "mt-hydrate-end");
      const m = performance.getEntriesByName("mt-hydrate").pop();
      if (m && m.duration && m.duration > 0) Math.round(m.duration);
    });
  }

  // ---- Carga on-demand del picker ----
  let loadingPicker = false;
  function loadPickerOnce(cb) {
    if (window.mtPicker) {
      cb && cb();
      return;
    }
    if (loadingPicker) return;
    loadingPicker = true;
    const s = document.createElement("script");
    s.id = "mt-picker-js";
    s.src = PICKER_SRC;
    s.async = true;
    s.onload = function () {
      loadingPicker = false;
      if (window.mtPreloader) window.mtPreloader.hide();
      cb && cb();
    };
    s.onerror = function () {
      loadingPicker = false;
      if (window.mtPreloader) window.mtPreloader.hide();
      console.error("[MT] failed to load picker:", PICKER_SRC);
    };
    if (window.mtPreloader) window.mtPreloader.show();
    document.head.appendChild(s);
  }

  // abrir por click del opener
  document.addEventListener("click", function (e) {
    const btn = e.target.closest('[data-bs-target="#' + MODAL_ID + '"]');
    if (!btn) return;
    hydrateOnOpen();
    loadPickerOnce(function () {
      markActiveInGrid();
      forceFilterForActive();
      enableSelectIfAny();
    });
  });

  // abrir por otros triggers
  const modal = document.getElementById(MODAL_ID);
  if (modal) {
    modal.addEventListener("show.bs.modal", function () {
      hydrateOnOpen();
      loadPickerOnce(function () {
        markActiveInGrid();
        forceFilterForActive();
        enableSelectIfAny();
      });
    });
  }

  // ======= Seed inicial breach + texto =========
  function seedBreachFromOpener() {
    var opener = document.querySelector(
      '[data-bs-target="#' + MODAL_ID + '"][data-account-id]'
    );
    var breach = document.getElementById("mt-breach-alert-modal");
    if (!opener || !breach) return;
    var accId = opener.getAttribute("data-account-id") || "";
    var mainId = opener.getAttribute("data-main-id") || "";
    var resetId = opener.getAttribute("data-reset-id") || "";
    var accType = opener.getAttribute("data-account-type") || "";
    if (accId) breach.setAttribute("data-account-id", accId);
    if (mainId) breach.setAttribute("data-main-id", mainId);
    if (resetId) breach.setAttribute("data-reset-id", resetId);
    if (accType) breach.setAttribute("data-account-type", accType);
    syncBreachText();
  }
  function syncBreachText() {
    var breach = document.getElementById("mt-breach-alert-modal");
    var span = document.getElementById("mtbreach-desc");
    if (!breach || !span) return;
    var tFunded = breach.getAttribute("data-funded-title") || "";
    var tEval = breach.getAttribute("data-evaluation-title") || "";
    var accType = (breach.getAttribute("data-account-type") || "").trim();
    if (!accType) return;
    span.textContent = accType === "FUNDED" ? tFunded : tEval;
  }

  document.addEventListener("mt:accountSelected", seedBreachFromOpener);
  document.addEventListener("mt:renderGridFromData", function (e) {
    e.detail && renderGridFromData(e.detail.accounts, e.detail.currentId);
    e.detail && e.detail.callback();
  });
  document.addEventListener("mt:hasSubscriptionChanged", syncBreachText);

  if (document.readyState === "loading") {
    document.addEventListener(
      "DOMContentLoaded",
      function () {
        seedBreachFromOpener();
        syncBreachText();
      },
      { once: true }
    );
  } else {
    seedBreachFromOpener();
    syncBreachText();
  }
})();

// ======= Overview & Journal Tab (Metric & Journal) =======
(function () {
  "use strict";

  const root = document.getElementById("mt-account-overview");
  const nav = document.querySelector(".mega-navigation");
  const elMet = document.querySelector("#mt-metrics");
  const elJour = document.querySelector("#mt-journal");
  const selMob = document.querySelector(
    'select[data-action="switch-view-select"]'
  );

  if (!root) return;

  // --- Preloader helpers ---
  let preloaderTimer = null;
  function preloaderShow() {
    try {
      if (
        window.jQuery &&
        window.jQuery.preloader &&
        typeof window.jQuery.preloader.show === "function"
      ) {
        window.jQuery.preloader.show();
        return;
      }
      if (
        window.jQuery &&
        window.$ &&
        $.preloader &&
        typeof $.preloader.show === "function"
      ) {
        $.preloader.show();
        return;
      }
    } catch (_) {}
    const el = document.querySelector(".preloader");
    if (el) el.style.display = "block";
  }
  function preloaderHide() {
    try {
      if (
        window.jQuery &&
        window.jQuery.preloader &&
        typeof window.jQuery.preloader.hide === "function"
      ) {
        window.jQuery.preloader.hide();
        return;
      }
      if (
        window.jQuery &&
        window.$ &&
        $.preloader &&
        typeof $.preloader.hide === "function"
      ) {
        $.preloader.hide();
        return;
      }
    } catch (_) {}
    const el = document.querySelector(".preloader");
    if (el) el.style.display = "none";
  }
  function blinkPreloader(ms = 500) {
    clearTimeout(preloaderTimer);
    preloaderShow();
    preloaderTimer = setTimeout(preloaderHide, ms);
  }

  function viewFromURL(href) {
    try {
      const u = new URL(href, location.origin);
      const v = (u.searchParams.get("view") || "").toLowerCase();
      if (v === "metrics" || v === "journal") return v;
      if (/trading-journal/i.test(u.pathname)) return "journal";
      if (/trade-area|overview/i.test(u.pathname)) return "metrics";
    } catch (_) {}
    return "";
  }

  let hydrated = false;
  let currentView = (
    new URLSearchParams(location.search).get("view") || "metrics"
  ).toLowerCase();
  applyView(currentView, false, false);
  hydrated = true;

  nav?.addEventListener(
    "click",
    function (e) {
      const a = e.target.closest("a");
      if (!a) return;
      if (
        a.target === "_blank" ||
        e.metaKey ||
        e.ctrlKey ||
        e.altKey ||
        e.shiftKey
      )
        return;

      const href = a.getAttribute("href") || "";
      const dataView = (a.getAttribute("data-view") || "").toLowerCase();
      const nextView = (dataView || viewFromURL(href)).toLowerCase();
      if (!nextView || (nextView !== "metrics" && nextView !== "journal"))
        return;

      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
      applyView(nextView, true, true);
    },
    { capture: true, passive: false }
  );

  // Select móvil
  selMob?.addEventListener("change", (ev) => {
    const opt = ev.target.options[ev.target.selectedIndex];
    const view = (opt?.getAttribute("data-view") || "").toLowerCase();
    if (!view) {
      location.href = ev.target.value;
      return;
    }
    applyView(view, true, true);
  });

  // Back/forward
  window.addEventListener("popstate", () => {
    const v = (
      new URLSearchParams(location.search).get("view") || "metrics"
    ).toLowerCase();
    applyView(v, false, false);
  });

  function applyView(view, push, doBlink) {
    view = (view || "metrics").toLowerCase();
    if (view !== "metrics" && view !== "journal") view = "metrics";
    if (view === currentView && hydrated) return;

    root.setAttribute("data-current-view", view);
    fastToggle(elMet, view === "metrics");
    fastToggle(elJour, view === "journal");
    updateActiveInMenu(view);
    syncMobileSelect(view);

    if (push) {
      const url = new URL(location.href);
      url.searchParams.set("view", view);
      history.pushState({ view }, "", url);
    }
    if (doBlink) blinkPreloader(500);
    currentView = view;
  }

  function fastToggle(el, show) {
    if (!el) return;
    el.hidden = !show;
    el.setAttribute("aria-hidden", show ? "false" : "true");
  }

  function updateActiveInMenu(view) {
    nav?.querySelectorAll("a").forEach((a) => {
      const li = a.closest("li");
      const v = (
        a.getAttribute("data-view") || viewFromURL(a.getAttribute("href") || "")
      ).toLowerCase();
      const on = v === view && li?.id !== "mt-nav-manage-subscription";
      a.setAttribute("aria-selected", on ? "true" : "false");
      if (li) li.classList.toggle("is-active", !!on);
    });
  }

  function syncMobileSelect(view) {
    if (!selMob) return;
    const idx = Array.from(selMob.options).findIndex(
      (o) => (o.getAttribute("data-view") || "").toLowerCase() === view
    );
    if (idx >= 0 && selMob.selectedIndex !== idx) selMob.selectedIndex = idx;
  }
})();

/* =========================
   TRADES FETCHER (AJAX + cache + eventos)
   ========================= */
(function () {
  const WRAP_ID = "mt-account-trades-history"; // contenedor externo
  const COMP_ID = "mt-trades"; // root del componente
  const wrap = document.getElementById(WRAP_ID);
  const comp = document.getElementById(COMP_ID);
  if (!wrap || !comp) return;

  const ajaxUrl =
    (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
  const nonce = (window.mtAccounts && mtAccounts.nonce) || "";

  let currentAccId =
    comp.getAttribute("data-account-id") ||
    wrap.getAttribute("data-account-id") ||
    "";
  const perPageAttr = parseInt(
    comp.getAttribute("data-per-page") ||
      wrap.getAttribute("data-per-page") ||
      "25",
    10
  );
  const perPage =
    Number.isFinite(perPageAttr) && perPageAttr > 0 ? perPageAttr : 25;

  // cache por (accountId:type:page:perPage)
  const cache = new Map();
  const key = (acc, t, p, pp) => `${acc}:${t}:${p}:${pp}`;

  function setLoading(on) {
    wrap.setAttribute("aria-busy", on ? "true" : "false");
    wrap.classList.toggle("mt-skeleton-pulse", !!on);
  }

  async function fetchTrades(type = "CLOSED", page = 1, force = false) {
    const t = type === "OPEN" ? "OPEN" : "CLOSED";
    const k = key(currentAccId, t, page, perPage);
    if (!force && cache.has(k)) return cache.get(k);

    const body = new URLSearchParams();
    body.set("action", "mt_trades_history");
    body.set("nonce", nonce);
    body.set("accountId", currentAccId);
    body.set("type", t);
    body.set("page", String(page));
    body.set("perPage", String(perPage));

    setLoading(true);
    try {
      const res = await fetch(ajaxUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
      });
      const json = await res.json();
      if (!json || !json.success)
        throw new Error(json?.data?.message || "Trades fetch failed");

      const data = json.data || {
        records: [],
        page,
        perPage,
        total: 0,
        pages: 1,
        type: t,
      };
      cache.set(k, data);

      // Notifica al renderer
      window.dispatchEvent(
        new CustomEvent("mt:trades:loaded", {
          detail: {
            accountId: currentAccId,
            type: t,
            page,
            perPage,
            payload: data,
          },
        })
      );
      return data;
    } catch (err) {
      window.dispatchEvent(
        new CustomEvent("mt:trades:error", {
          detail: {
            accountId: currentAccId,
            type: t,
            message: err?.message || String(err),
          },
        })
      );
      throw err;
    } finally {
      setLoading(false);
    }
  }

  // Cambio de cuenta (bus interno)
  if (window.mtRefresh && typeof window.mtRefresh.register === "function") {
    window.mtRefresh.register("tradesHistory", function (newAccountId) {
      if (!newAccountId || newAccountId === currentAccId) return;

      currentAccId = newAccountId;
      comp.setAttribute("data-account-id", newAccountId);
      // invalida todo el cache
      cache.clear();

      // vuelve a CLOSED page 1
      window.dispatchEvent(
        new CustomEvent("mt:trades:filter", {
          detail: { type: "CLOSED", page: 1 },
        })
      );
      fetchTrades("CLOSED", 1, /*force*/ true).catch(() => {});
    });
  }

  // API mínima expuesta
  window.mtTradesAPI = {
    refresh(type = "CLOSED", page = 1) {
      return fetchTrades(type, page, /*force*/ true);
    },
    getCached(type = "CLOSED", page = 1) {
      const t = type === "OPEN" ? "OPEN" : "CLOSED";
      const k = key(currentAccId, t, page, perPage);
      return cache.get(k) || null;
    },
  };

  // Primer fetch por defecto
  fetchTrades("CLOSED", 1).catch(() => {});
})();

/* =========================
   TRADES RENDERER (pinta la tabla y maneja UI)
   ========================= */
(function () {
  const COMP_ID = "mt-trades";
  const comp = document.getElementById(COMP_ID);
  if (!comp) return;

  // refs UI
  const viewport = comp.querySelector(".dj-viewport");
  const rowsWrap = comp.querySelector(".dj-rows");
  const pager = comp.querySelector(".dj-pager");
  const showing = pager?.querySelector(".js-showing");
  const totalEl = pager?.querySelector(".js-total");
  const btnPrev = pager?.querySelector(".js-prev");
  const btnNext = pager?.querySelector(".js-next");
  const segBtns = comp.querySelectorAll(".tr-seg__btn"); // botones con data-type="CLOSED|OPEN"

  // estado local UI
  let currentType = "CLOSED";
  let currentPage = 1;
  const perPageAttr = parseInt(comp.getAttribute("data-per-page") || "25", 10);
  const perPage =
    Number.isFinite(perPageAttr) && perPageAttr > 0 ? perPageAttr : 25;

  const setBusy = (on) =>
    viewport?.setAttribute("aria-busy", on ? "true" : "false");

  // ---------- helpers ----------
const symbolPostColon = (raw) => {
  if (raw == null) return "-";
  const s = String(raw);
  const slash = s.lastIndexOf("/");            // después del último "/"
  if (slash === -1) {
    // fallback: si no hay "/", toma lo que esté antes del primer ":" o todo
    const beforeColon = s.split(":")[0];
    return (beforeColon || "-").trim() || "-";
  }
  const colon = s.indexOf(":", slash + 1);     // hasta el primer ":" luego del "/"
  const end = colon === -1 ? s.length : colon;
  const sym = s.slice(slash + 1, end).trim();
  return sym || "-";
};

  // formatters
  const fmtMoney = (v) =>
    v == null || v === "" || isNaN(v)
      ? "-"
      : (v < 0 ? "-" : "") + "$" + Math.abs(+v).toFixed(2);
  const fmtPct = (v) =>
    v == null || v === "" || isNaN(v) ? "-" : Number(v).toFixed(2) + "%";
  const fmtDate = (s) => (s && /^\d{2}\/\d{2}\/\d{4}$/.test(s) ? s : s || "-"); // ya viene MM/DD/YYYY
  const fmtDur = (sec) => {
    if (sec == null || isNaN(sec)) return "-";
    sec = Math.floor(sec);
    const h = Math.floor(sec / 3600),
      m = Math.floor((sec % 3600) / 60),
      s = sec % 60;
    return h > 0
      ? `${h}h ${String(m).padStart(2, "0")}m ${String(s).padStart(2, "0")}s`
      : `${m}m ${String(s).padStart(2, "0")}s`;
  };
  const pill = (txt, kind) =>
    `<span class="mt-pill ${
      kind === "err" ? "mt-pill--err" : "mt-pill--sec"
    }">${txt}</span>`;

  // empty-state
  const renderEmpty = () => {
    rowsWrap.innerHTML = `
      <div class="dj-empty">
    <div>No trades to show</div>
    <div>When you place trades, they’ll appear here.</div>
  </div>
    `;
    if (pager) pager.hidden = true;
  };

  // fila (símbolo post-colon)
  function buildRow(r, isLast = false) {
    const netCls =
      typeof r.net === "number"
        ? r.net > 0
          ? "text-success"
          : r.net < 0
          ? "text-danger"
          : ""
        : "";
    const roiCls =
      typeof r.netRoi === "number"
        ? r.netRoi > 0
          ? "text-success"
          : r.netRoi < 0
          ? "text-danger"
          : ""
        : "";
    const status = String(r.status || "").toUpperCase();
    let statusHtml = "-";
    if (status === "WIN") statusHtml = pill("Win", "sec");
    if (status === "LOSS") statusHtml = pill("Loss", "err");
    if (status === "OPEN") statusHtml = pill("Open", "sec");

    const symRaw = r.symbol ?? r.instrument ?? r.ticker ?? "-";
    const sym = symbolPostColon(symRaw); // <-- aquí tomamos lo que viene después de los “:”

    return `
      <div class="dj-grid dj-row${isLast ? " is-last" : ""}" style="--cols:10;">
        <div class="dj-cell is-left">${r.side ?? "-"}</div>
        <div class="dj-cell is-left">${sym}</div>      
        <div class="dj-cell is-right">${fmtDate(r.closeDate)}</div>
        <div class="dj-cell is-right ${netCls}">${fmtMoney(r.net)}</div>
        <div class="dj-cell is-right ${roiCls}">${fmtPct(r.netRoi)}</div>
        <div class="dj-cell is-right">${fmtDur(r.durationSec)}</div>
        <div class="dj-cell is-right">${fmtMoney(r.avgEntry)}</div>
        <div class="dj-cell is-right">${fmtMoney(r.avgExit)}</div>
        <div class="dj-cell is-right">${fmtDate(r.openDate)}</div>
        <div class="dj-cell is-right">${statusHtml}</div>
      </div>`;
  }

  function render(payload) {
    const recs = Array.isArray(payload.records) ? payload.records : [];
    const page = Number(payload.page || 1);
    const total = Number(payload.total ?? recs.length ?? 0);
    const pages = Number(
      payload.pages || Math.ceil(total / Math.max(1, perPage))
    );

    // EMPTY STATE
    if (!recs.length || total === 0) {
      renderEmpty();
      setBusy(false);
      return;
    }

    const start = (page - 1) * perPage;
    const end = Math.min(start + perPage, recs.length);
    const slice = recs.slice(start, end);

    rowsWrap.innerHTML = slice
      .map((r, i) => buildRow(r, i === slice.length - 1))
      .join("");

    if (pager) {
      pager.hidden = total === 0;
      if (showing) showing.textContent = String(end);
      if (totalEl) totalEl.textContent = String(total);
      if (btnPrev) btnPrev.disabled = page <= 1;
      if (btnNext) btnNext.disabled = page >= pages;

      if (btnPrev)
        btnPrev.onclick = () => {
          if (page > 1) {
            currentPage = page - 1;
            setBusy(true);
            window.mtTradesAPI
              ?.refresh(currentType, currentPage)
              .catch(() => setBusy(false));
          }
        };
      if (btnNext)
        btnNext.onclick = () => {
          if (page < pages) {
            currentPage = page + 1;
            setBusy(true);
            window.mtTradesAPI
              ?.refresh(currentType, currentPage)
              .catch(() => setBusy(false));
          }
        };
    }
    setBusy(false);
  }

  // botones Close/Open (data-type="CLOSED|OPEN")
  segBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      const t = (btn.getAttribute("data-type") || "").toUpperCase();
      if (t !== "CLOSED" && t !== "OPEN") return;
      if (t === currentType) return;
      currentType = t;
      currentPage = 1;
      segBtns.forEach((b) => b.setAttribute("aria-pressed", String(b === btn)));
      setBusy(true);
      window.mtTradesAPI
        ?.refresh(currentType, currentPage)
        .catch(() => setBusy(false));
    });
  });

  // primer paint desde cache o fetch inicial
  (function init() {
    const cached = window.mtTradesAPI?.getCached?.("CLOSED", 1);
    setBusy(true);
    if (cached) render(cached);
    else window.mtTradesAPI?.refresh("CLOSED", 1).catch(() => setBusy(false));
  })();

  // eventos desde el fetcher
  window.addEventListener("mt:trades:loaded", (ev) => {
    const { type, page, payload } = ev.detail || {};
    if (!payload) return;
    currentType = type || currentType;
    currentPage = Number(page || currentPage);
    segBtns.forEach((b) => {
      const t = (b.getAttribute("data-type") || "").toUpperCase();
      b.setAttribute("aria-pressed", String(t === currentType));
    });
    render(payload);
  });

  window.addEventListener("mt:trades:error", () => {
    setBusy(false);
    // muestra empty-state también en error
    if (rowsWrap) {
      rowsWrap.innerHTML = `
        <div class="mt-empty">
          <div class="mt-empty__title">No trades to show</div>
          <div class="mt-empty__hint">Try again in a moment.</div>
        </div>
      `;
    }
    if (pager) pager.hidden = true;
  });
})();
