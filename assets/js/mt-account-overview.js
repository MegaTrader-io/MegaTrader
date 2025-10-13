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

  const show = () =>
    document.querySelector(".preloader")?.classList.add("is-active");
  const hide = () =>
    document.querySelector(".preloader")?.classList.remove("is-active");

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
  }

  return {
    register(name, fn) {
      handlers[name] = fn;
    },
    refreshAll(id) {
      Object.values(handlers).forEach((fn) => fn && track(fn(id)));
    },
  };
})();

// Dispara refresh de TODOS ante selección de cuenta
document.addEventListener("mt:accountSelected", (e) => {
  const id = e?.detail?.accountId;
  if (!id) return;
  window.mtRefresh.refreshAll(id);
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

    // 🔒 Cerrar tooltips antes del replace
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

        wrap.innerHTML = j.data.html; // ⬅️ reemplaza el bloque
        initFeatureContent(wrap); // ♻️ re-init visual (donuts/barras)

        // ✅ Re-inicializa tooltips en el NUEVO contenido
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

  // Ejecuta <script> inline dentro del contenedor (evita colisiones globales)
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

  // Asegura ApexCharts antes de ejecutar los inline
  function ensureApexThen(container, cb) {
    if (window.ApexCharts) return cb();
    var url =
      container
        .querySelector('script[src*="apexcharts"]')
        ?.getAttribute("src") || "https://cdn.jsdelivr.net/npm/apexcharts";
    var tag = document.createElement("script");
    tag.src = url;
    tag.onload = cb;
    tag.onerror = cb;
    document.head.appendChild(tag);
  }

  // Inyecta CSS una sola vez para que el follower tenga control total
  function ensureTipCSS() {
    if (window.__mtTipFollowerCSS) return;
    const css = `
      .apexcharts-tooltip{
        position:absolute !important;
        left:0 !important; top:0 !important;            /* neutraliza el posicionamiento de Apex */
        transition:none !important;
        pointer-events:none !important;
        background:transparent !important;
        border:0 !important; box-shadow:none !important; padding:0 !important;
        z-index:10;
      }
      .apexcharts-tooltip.mt-tip-hidden{
        transform: translate3d(-9999px, -9999px, 0) !important;
        opacity:0 !important; visibility:hidden !important;
      }
    `;
    const style = document.createElement("style");
    style.textContent = css;
    document.head.appendChild(style);
    window.__mtTipFollowerCSS = true;
  }

  // === REEMPLAZO: lógica de datos del chart con regla de mínimos ===
  function chartDataInfo(wrap) {
    const carrier =
      wrap.querySelector(
        "[data-has-series],[data-has-data],[data-points],[data-min-points]"
      ) || wrap;
    const rawHas =
      carrier.getAttribute("data-has-series") ??
      carrier.getAttribute("data-has-data");
    const rawPts = carrier.getAttribute("data-points");
    const rawMin = carrier.getAttribute("data-min-points");

    let hasSeries = null;
    if (rawHas != null) {
      const v = String(rawHas).trim().toLowerCase();
      if (v === "true" || v === "1") hasSeries = true;
      else if (v === "false" || v === "0") hasSeries = false;
    }

    let points = Number.isFinite(parseInt(rawPts, 10))
      ? parseInt(rawPts, 10)
      : null;

    if (
      points == null &&
      window.__mtChartInstance &&
      window.__mtChartInstance.w &&
      window.__mtChartInstance.w.globals
    ) {
      const g = window.__mtChartInstance.w.globals;
      try {
        const series0 =
          (g.seriesXvalues && g.seriesXvalues[0]) ||
          (g.series && g.series[0]) ||
          [];
        points = Array.isArray(series0)
          ? series0.length
          : Number.isFinite(series0)
          ? series0
          : 0;
        if (hasSeries == null) hasSeries = points > 0;
      } catch (_) {}
    }

    if (hasSeries == null) {
      hasSeries = !!wrap.querySelector(
        ".apexcharts-series path, .apexcharts-series rect, .apexcharts-series circle"
      );
    }

    let minPoints = Number.isFinite(parseInt(rawMin, 10))
      ? parseInt(rawMin, 10)
      : 7;

    return {
      hasSeries: Boolean(hasSeries),
      points: points == null ? null : Math.max(0, points),
      minPoints,
    };
  }

  function applyChartOverlay(wrap) {
    const overlay = wrap.querySelector(".account-performance-chart__overlay");
    if (overlay && overlay.getAttribute("data-autotoggle") === "off") return;

    const info = chartDataInfo(wrap);

    const enoughPoints =
      info.points == null ? true : info.points >= info.minPoints;
    const shouldShow = !(info.hasSeries && enoughPoints);

    if (window.mtOverlay) window.mtOverlay.toggle(wrap, shouldShow);
  }

  // ---------- TIP FOLLOWER ----------
  function makeTipFollower(root) {
    try {
      root.__tipFollowerCleanup && root.__tipFollowerCleanup();
    } catch (_) {}
    try {
      root.__tipFollowerObserver && root.__tipFollowerObserver.disconnect();
    } catch (_) {}

    ensureTipCSS();

    function attach() {
      const canvas = root.querySelector(".apexcharts-canvas");
      const svg = root.querySelector(".apexcharts-svg");
      if (!canvas || !svg) return;

      const base = root.querySelector(".apexcharts-inner") || canvas;
      const tipEl = () => root.querySelector(".apexcharts-tooltip");

      let rafId = 0,
        wantX = -9999,
        wantY = -9999;

      function render() {
        rafId = 0;
        const tip = tipEl();
        if (!tip) return;

        const r = base.getBoundingClientRect
          ? base.getBoundingClientRect()
          : { left: 0, top: 0, width: 0, height: 0 };
        const tw = tip.offsetWidth || 220;
        const th = tip.offsetHeight || 60;

        let x = Math.max(6, Math.min(wantX, r.width - tw - 6));
        let y = Math.max(6, Math.min(wantY, r.height - th - 6));

        tip.classList.remove("mt-tip-hidden");
        tip.style.transform = `translate3d(${Math.round(x)}px, ${Math.round(
          y
        )}px, 0)`;
        tip.style.opacity = "1";
        tip.style.visibility = "visible";
        tip.style.left = "0px";
        tip.style.top = "0px";
      }

      function queue(x, y) {
        wantX = x;
        wantY = y;
        if (!rafId) rafId = requestAnimationFrame(render);
      }

      function posFrom(ev) {
        const tip = tipEl();
        const r = base.getBoundingClientRect
          ? base.getBoundingClientRect()
          : { left: 0, top: 0 };
        const mx = ev.clientX - r.left;
        const my = ev.clientY - r.top;
        const tw = tip ? tip.offsetWidth || 220 : 220;
        const th = tip ? tip.offsetHeight || 60 : 60;

        let x = mx + 12;
        let y = my - th - 12;

        return { x, y };
      }

      function onMove(ev) {
        const p = posFrom(ev);
        queue(p.x, p.y);
      }
      function onEnter(ev) {
        const p = posFrom(ev);
        queue(p.x, p.y);
      }
      function onLeave() {
        const tip = tipEl();
        if (!tip) return;
        tip.classList.add("mt-tip-hidden");
        tip.style.opacity = "0";
        tip.style.visibility = "hidden";
        tip.style.transform = "translate3d(-9999px, -9999px, 0)";
      }

      const target = svg || canvas;
      target.addEventListener("pointermove", onMove, { passive: true });
      target.addEventListener("mousemove", onMove, { passive: true });
      target.addEventListener("pointerenter", onEnter, { passive: true });
      target.addEventListener("mouseenter", onEnter, { passive: true });
      target.addEventListener("pointerleave", onLeave, { passive: true });
      target.addEventListener("mouseleave", onLeave, { passive: true });

      onLeave();

      requestAnimationFrame(() => {
        const r = base.getBoundingClientRect
          ? base.getBoundingClientRect()
          : null;
        if (!r) return;
        onEnter({ clientX: r.left + 24, clientY: r.top + 24 });
      });

      root.__tipFollowerCleanup = function () {
        target.removeEventListener("pointermove", onMove);
        target.removeEventListener("mousemove", onMove);
        target.removeEventListener("pointerenter", onEnter);
        target.removeEventListener("mouseenter", onEnter);
        target.removeEventListener("pointerleave", onLeave);
        target.removeEventListener("mouseleave", onLeave);
        if (rafId) cancelAnimationFrame(rafId);
        rafId = 0;
      };
    }

    const obs = new MutationObserver(() => {
      if (
        root.querySelector(".apexcharts-canvas") &&
        root.querySelector(".apexcharts-svg")
      ) {
        attach();
        obs.disconnect();
      }
    });
    obs.observe(root, { childList: true, subtree: true });
    root.__tipFollowerObserver = obs;
  }

  function hookFollower(root) {
    if (!root) return;
    makeTipFollower(root);
    requestAnimationFrame(() => makeTipFollower(root));
    setTimeout(() => makeTipFollower(root), 150);
  }

  (function bootInitialFollower() {
    const wrap =
      document.querySelector(".mt-account-performance-chart-content") ||
      document;
    const root =
      wrap.querySelector("#account-performance-chart")?.parentElement || wrap;
    hookFollower(root);
  })();

  let ctrl = null;
  let reqToken = 0;

  window.mtRefresh.register("performanceChart", function (accountId) {
    var wrap = document.querySelector(".mt-account-performance-chart-content");
    if (!wrap) return;

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
        if (myToken !== reqToken) return; // respuesta vieja

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

        try {
          if (
            window.__mtChartInstance &&
            typeof window.__mtChartInstance.destroy === "function"
          ) {
            window.__mtChartInstance.destroy();
            window.__mtChartInstance = null;
          }
        } catch (e) {
          console.warn("[MT][Chart] destroy prev error", e);
        }

        document
          .querySelectorAll(
            ".apexcharts-tooltip, .apexcharts-xcrosshairs, .apexcharts-ycrosshairs"
          )
          .forEach((n) => {
            try {
              n.remove();
            } catch (_) {}
          });

        wrap
          .querySelectorAll(
            ".apexcharts-tooltip, .apexcharts-xcrosshairs, .apexcharts-ycrosshairs, .apexcharts-canvas"
          )
          .forEach((n) => {
            try {
              n.remove();
            } catch (_) {}
          });

        wrap.innerHTML = j.data.html;
        applyChartOverlay(wrap);

        ensureApexThen(wrap, function () {
          runInlineScripts(wrap);
          applyChartOverlay(wrap);
          setTimeout(function () {
            applyChartOverlay(wrap);
          }, 150);
          const root =
            wrap.querySelector("#account-performance-chart")?.parentElement ||
            wrap;
          hookFollower(root);
        });
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

    function openModal() {
      if (modal.classList.contains("show")) return;

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

// 2) cuando el usuario cambia de cuenta
document.addEventListener("mt:accountSelected", function (e) {
  var id = (e && e.detail && (e.detail.accountId || e.detail.id)) || "";
  if (id) breachGuardCheck(id);
});
// ===== ENd Breach Modal =====

document.addEventListener("mt:accountSelected", function (e) {
  var id = (e && e.detail && (e.detail.accountId || e.detail.id)) || "";
  if (id) breachGuardCheck(id);
});

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

// === Sync de "Manage Subscription" con la cuenta activa (sin inline PHP) ===
(function () {
  var ORDERS_BASE = (window.location.origin || "") + "/my-account/orders/";

  function int(v) {
    v = parseInt(v, 10);
    return isNaN(v) ? 0 : v;
  }

  function getRootOrderId() {
    var root = document.getElementById("mt-account-overview");
    var raw =
      (root && (root.getAttribute("data-order-id") || root.dataset.orderId)) ||
      "";
    return int(String(raw).replace(/[^\d]/g, ""));
  }

  function readOrderFromActiveCard() {
    var active = document.querySelector(
      "#mt-accounts-grid .subscription-card.active"
    );
    if (!active) return 0;
    return int(active.getAttribute("data-order") || active.dataset.order);
  }

  function buildOrdersMap() {
    var map = Object.create(null);
    document
      .querySelectorAll("#mt-accounts-grid .subscription-card")
      .forEach(function (card) {
        var id = card.getAttribute("data-account-id") || "";
        var ord = int(card.getAttribute("data-order") || card.dataset.order);
        if (id) map[id] = ord;
      });
    return map;
  }

  function rewriteManageSubsURLs(orderId) {
    var url =
      ORDERS_BASE + (orderId ? "?orderId=" + encodeURIComponent(orderId) : "");
    var nav = document.querySelector(".mega-navigation");

    var a = nav && nav.querySelector('a[href*="/my-account/orders"]');
    if (a) a.href = url;

    var sel = document.getElementById("mega-navigation-select");
    if (sel) {
      Array.prototype.forEach.call(sel.options || [], function (opt) {
        if ((opt.value || "").indexOf("/my-account/orders") !== -1) {
          opt.value = url;
        }
      });
    }

    var form = document.getElementById("mt-manage-subs-form");
    if (form) {
      var i1 = form.querySelector('input[name="orderId"]');
      var i2 = form.querySelector('input[name="optionalOrderId"]');
      if (i1) i1.value = orderId ? String(orderId) : "";
      if (i2) i2.value = orderId ? String(orderId) : "";
      form.action = ORDERS_BASE;
    }
  }

  function setActiveOrder(orderId) {
    var root = document.getElementById("mt-account-overview");
    if (root)
      root.setAttribute("data-order-id", orderId ? String(orderId) : "");

    window.mtOrders = window.mtOrders || {};
    window.mtOrders.activeOrderId = orderId || 0;

    rewriteManageSubsURLs(orderId);
  }

  (function init() {
    var initial = getRootOrderId();
    if (initial) rewriteManageSubsURLs(initial);
  })();

  document.addEventListener("mt:accountSelected", function (ev) {
    var d = (ev && ev.detail) || {};
    var id = d.accountId || d.id || "";
    var ord = int(d.orderId || d.order || 0);
    if (!ord && id) {
      var map = buildOrdersMap();
      ord = map[id] || 0;
    }
    if (!ord) ord = readOrderFromActiveCard();
    setActiveOrder(ord);
  });

  document.addEventListener("click", function (e) {
    var btn = e.target.closest("#select-subscription-btn");
    if (!btn) return;
    setTimeout(function () {
      var ord = readOrderFromActiveCard();
      if (ord) setActiveOrder(ord);
    }, 0);
  });

  (function softenBinder() {
    try {
      var nav = document.querySelector(".mega-navigation");
      if (!nav) return;
      var link = nav.querySelector('a[href*="/my-account/orders"]');
      if (!link) return;
      link.addEventListener(
        "click",
        function (e) {
          if (/\borderId=\d+/.test(this.href)) return;
          var orderId = getRootOrderId() || readOrderFromActiveCard();
          if (!orderId) return;
          e.preventDefault();
          var form = document.getElementById("mt-manage-subs-form");
          if (!form) return (window.location.href = this.href);
          var i1 = form.querySelector('input[name="orderId"]');
          var i2 = form.querySelector('input[name="optionalOrderId"]');
          if (i1) i1.value = String(orderId);
          if (i2) i2.value = String(orderId);
          form.action = this.href.replace(/\?.*$/, "");
          form.submit();
        },
        { passive: false }
      );
    } catch (_) {}
  })();
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
        document.querySelector('[data-bs-target="#changeSubcriptionModal"]') ||
        document.querySelector(".mega-navigation a, .mega-navigation select") ||
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
    form.action = url.replace(/\?.*$/, ""); // ej: "/checkout"
    form.className = "d-none";

    // Si el href traía ?add-to-cart=, reenviarlo por POST (Woo lo entiende)
    var m = (url.match(/[?&]add-to-cart=([^&#]+)/) || [])[1];
    if (m) {
      var inpATC = document.createElement("input");
      inpATC.type = "hidden";
      inpATC.name = "add-to-cart";
      inpATC.value = decodeURIComponent(m);
      form.appendChild(inpATC);
    }

    // Campos extra
    Object.keys(fields || {}).forEach(function (k) {
      var v = fields[k];
      if (v == null || v === "") return; // sólo si hay valor
      var inp = document.createElement("input");
      inp.type = "hidden";
      inp.name = k;
      inp.value = String(v);
      form.appendChild(inp);
    });

    document.body.appendChild(form);
    form.submit();
  }

  // Intercepta clicks en botones de los modales (breach + activation)
  document.addEventListener(
    "click",
    function (ev) {
      var el =
        ev.target && ev.target.closest
          ? ev.target.closest(".mt-breach-reset-button, #mt-activation-btn")
          : null;
      if (!el) return;

      var href = el.getAttribute("href") || "";
      if (!href || href === "#") return;

      var host = el.closest("#mt-breach-alert-modal, #mt-account-passed-modal");
      if (!host) return;

      var accountId = host.getAttribute("data-account-id") || "";
      var mainProductId = host.getAttribute("data-main-product-id") || "";

      if (!accountId) return;

      ev.preventDefault();
      ev.stopPropagation();

      var fields = { account_id: accountId };
      if (mainProductId) fields.main_product_id = mainProductId;

      postTo(href, fields);
    },
    { capture: true }
  );
})();
