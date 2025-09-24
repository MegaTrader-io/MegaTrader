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

    fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    })
      .then((r) => r.json())
      .then((j) => {
        if (j && j.success && j.data && j.data.html)
          container.innerHTML = j.data.html;
      })
      .finally(() =>
        document.querySelector(".preloader")?.classList.remove("is-active")
      );
  };
})();

/* ===== Floating tooltips (portal to <body>) ===== */
(function () {
  if (window.__mtTooltipsBound) return;
  window.__mtTooltipsBound = true;

  // Portal y burbuja reutilizable
  const portal = document.createElement("div");
  portal.id = "mt-tooltips-portal";
  Object.assign(portal.style, {
    position: "fixed",
    inset: "0",
    pointerEvents: "none",
    zIndex: "9999",
  });
  document.body.appendChild(portal);
  document.documentElement.classList.add("has-portal-tooltips");

  const bubble = document.createElement("div");
  bubble.className = "mt-tooltip__panel is-portal";
  portal.appendChild(bubble);

  let anchor = null,
    hideTimer = 0;

  const clampNum = (n, min, max) => Math.max(min, Math.min(max, n));

  function positionBubble(el) {
    anchor = el;
    bubble.style.visibility = "hidden";
    bubble.style.display = "block";

    const r = el.getBoundingClientRect();
    const bw = bubble.offsetWidth;
    const bh = bubble.offsetHeight;
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    const pad = 8;

    // Arriba por defecto, flip abajo si no cabe
    let top = r.top - bh - pad;
    let placement = "top";
    if (top < pad) {
      top = r.bottom + pad;
      placement = "bottom";
    }

    let left = r.left + r.width / 2 - bw / 2;
    left = clampNum(left, pad, vw - bw - pad);

    bubble.style.left = Math.round(left) + "px";
    bubble.style.top = Math.round(top) + "px";
    bubble.setAttribute("data-placement", placement);

    const arrowLeft = clampNum(r.left + r.width / 2 - left, 10, bw - 10);
    bubble.style.setProperty("--arrow-left", arrowLeft + "px");

    bubble.style.visibility = "visible";
  }

  function showFor(target) {
    const tip = target.closest(".mt-tooltip");
    if (!tip) return;
    const panel = tip.querySelector(".mt-tooltip__panel");
    if (!panel) return;

    clearTimeout(hideTimer);
    if (anchor === tip && bubble.style.display === "block") return;

    bubble.innerHTML = panel.innerHTML; // reutiliza tu HTML
    positionBubble(tip);
  }

  function hideSoon() {
    clearTimeout(hideTimer);
    hideTimer = setTimeout(() => {
      bubble.style.display = "none";
      anchor = null;
    }, 120);
  }

  // Mantener visible si pasas el mouse a la burbuja
  bubble.addEventListener("mouseenter", () => clearTimeout(hideTimer));
  bubble.addEventListener("mouseleave", hideSoon);

  // Delegación: mouseover/mouseout
  document.addEventListener(
    "mouseover",
    (e) => {
      const tip = e.target.closest(".mt-tooltip");
      if (!tip) return;
      showFor(e.target);
    },
    true
  );

  document.addEventListener(
    "mouseout",
    (e) => {
      const from = e.target.closest(".mt-tooltip");
      if (!from) return;
      const to = e.relatedTarget;
      if (
        to &&
        (to.closest?.(".mt-tooltip") || to === bubble || bubble.contains(to))
      )
        return;
      hideSoon();
    },
    true
  );

  // Teclado: focus/blur (icons con tabindex/role)
  document.addEventListener("focusin", (e) => showFor(e.target));
  document.addEventListener("focusout", hideSoon);
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") hideSoon();
  });

  // Reposicionar en scroll/resize
  window.addEventListener(
    "scroll",
    () => {
      if (anchor) positionBubble(anchor);
    },
    true
  );
  window.addEventListener("resize", () => {
    if (anchor) positionBubble(anchor);
  });
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
    holder.innerHTML = ""; // limpia números

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

  function goTo(root, st, page) {
    st.currentPage = Math.max(1, Math.min(st.totalPages, page));
    applyPageVisibility(root, st);
    updatePagerUI(root, st);
  }

  function recalcAndRender(root, st) {
    const rows = findRows(root);
    st.totalRows = rows.length;
    st.totalPages = Math.max(1, Math.ceil(st.totalRows / st.perPage));
    st.currentPage = Math.min(st.currentPage, st.totalPages) || 1;
    rebuildNumericPager(root, st);
    applyPageVisibility(root, st);
    updatePagerUI(root, st);
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

    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    })
      .then((r) => r.json())
      .then((j) => {
        if (!j?.success || !j?.data) return;
        const { rowsHtml, per_page } = j.data;

        if (scroll && rowsHtml != null) {
          // limpiar filas actuales (mantener header .dj-headrow)
          qsa(".dj-row", scroll).forEach((n) => n.remove());
          const tmp = document.createElement("div");
          tmp.innerHTML = rowsHtml;
          qsa(".dj-row", tmp).forEach((n) => scroll.appendChild(n));
        }
        if (per_page) root.setAttribute("data-per-page", String(per_page));

        const st = root.__djState || stateFrom(root);
        recalcAndRender(root, st);
        root.__djState = st;
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

    // Donuts (data-donut-value -> 0..100)
    root.querySelectorAll(".mt-donut").forEach(function (d) {
      var v = clamp(d.dataset.donutValue, 0, 100);
      d.style.setProperty("--mt-donut-value", v);
      d.classList.toggle("is-empty", v === 0);
      var t = d.querySelector(".mt-donut__percent, .mt-donut__value");
      if (t) t.textContent = v ? v + "%" : "0%";
    });

    // Barras (intenta data-bar-value; si no, lee --w del style inline)
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

    // Dualbar (data-reward/data-risk -> widths %)
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

    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body,
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (j) {
        if (!j || !j.success || j.data == null || j.data.html == null) return;
        wrap.innerHTML = j.data.html; // reemplaza TODO el componente
        initFeatureContent(wrap); // re-inicializa donuts + barras + dualbar
      })
      .catch(function (err) {
        console.error("[MT] feature AJAX error:", err);
      });
  });
})();
// ===== Performance Chart (AJAX refresh) =====
(function () {
  if (!window.mtRefresh || typeof window.mtRefresh.register !== "function")
    return;

  // Ejecuta <script> inline dentro del contenedor, envueltos en IIFE para evitar colisiones globales
  function runInlineScripts(container) {
    container.querySelectorAll("script:not([src])").forEach(function (old) {
      var s = document.createElement("script");
      if (old.type) s.type = old.type;
      s.text = "(function(){\n" + (old.textContent || "") + "\n})();";
      document.body.appendChild(s);
      document.body.removeChild(s);
    });
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
    tag.onerror = cb; // en caso de estar ya cargado por otro lado
    document.head.appendChild(tag);
  }

  window.mtRefresh.register("performanceChart", function (accountId) {
    var wrap = document.querySelector(".mt-account-performance-chart-content");
    if (!wrap) return;

    var url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
    var nonce = (window.mtAccounts && mtAccounts.nonce) || "";
    var body = new URLSearchParams();
    body.set("action", "mt_account_performance_chart");
    body.set("nonce", nonce);
    body.set("accountId", String(accountId || ""));

    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body,
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (j) {
        if (!j || !j.success || !j.data || j.data.html == null) return;
        wrap.innerHTML = j.data.html; // reemplaza TODO el componente
        ensureApexThen(wrap, function () {
          // espera ApexCharts si hace falta
          runInlineScripts(wrap); // ejecuta el script inline del chart
        });
      })
      .catch(function (err) {
        console.error("[MT] chart AJAX error:", err);
      });
  });
})();

// ===== Account Data (AJAX refresh) =====
(function () {
  if (!window.mtRefresh || typeof window.mtRefresh.register !== "function")
    return;

  window.mtRefresh.register("accountData", function (accountId) {
    var wrap = document.querySelector(".mt-account-data");
    if (!wrap) return;

    var url =
      (window.mtAccounts && mtAccounts.ajaxUrl) || "/wp-admin/admin-ajax.php";
    var nonce = (window.mtAccounts && mtAccounts.nonce) || "";
    var body = new URLSearchParams();
    body.set("action", "mt_account_data");
    body.set("nonce", nonce);
    body.set("accountId", String(accountId || ""));

    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body,
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (j) {
        if (!j || !j.success || !j.data || j.data.html == null) return;
        wrap.innerHTML = j.data.html;
      })
      .catch(function (err) {
        console.error("[MT] account-data AJAX error:", err);
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

  // --- Límite de nota ---
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

  // Helper: manejar placeholder visible/oculto según nota
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

  // Abrir desde el ícono (pencil/eye)
  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".mt-dj-visibility");
    if (!btn) return;

    const row = btn.closest(".dj-row");
    const tradeDate = row?.dataset.tradeDate || "";
    const root = document.querySelector("#mt-daily-journal");
    const accountId = parseInt(root?.dataset.accountId || "0", 10);
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

  // Cerrar por click fuera
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

  // Editar (habilitar campos) → restaurar placeholder original para escribir
  btnEdit?.addEventListener("click", () => {
    const noteEl = $("#mtfb-note", panel);
    if (noteEl) noteEl.setAttribute("placeholder", noteEl.dataset.ph || "");
    setReadOnly(false);
  });

  // Reposicionar en scroll/resize
  ["scroll", "resize"].forEach((ev) => {
    window.addEventListener(
      ev,
      () => {
        if (isOpen && current.anchor) positionTo(current.anchor);
      },
      { passive: true }
    );
  });

  // Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && isOpen) closePopover();
  });

  // Caritas: activar (respeta disabled) y revalidar Save
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

  // Radios: revalidar Save al cambiar Sí/No
  panel.addEventListener("change", (e) => {
    if (e.target && e.target.name === "mtfb-plan") updateSaveEnabled();
  });

  // --- Enforce maxlength en textarea (incluye pegar) ---
  const noteField = $("#mtfb-note", panel);
  if (noteField) noteField.setAttribute("maxlength", String(NOTE_MAX));
  panel.addEventListener("input", (e) => {
    if (e.target && e.target.id === "mtfb-note") {
      const t = e.target;
      if (t.value.length > NOTE_MAX) t.value = t.value.slice(0, NOTE_MAX);
    }
  });

  // Guardar AJAX (botón Save) — sin cerrar el popover
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

    // NO guardar placeholder
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
      const res = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
      });
      const j = await res.json().catch(() => null);
      if (!res.ok || !j?.success) {
        const msg = j?.data?.msg || `HTTP ${res.status}`;
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

      // si la nota quedó vacía, no mostrar placeholder mientras queda abierto
      if (typeof applyNotePlaceholderPolicy === "function") {
        applyNotePlaceholderPolicy({ note });
      } else if (noteEl) {
        if (note === "") noteEl.setAttribute("placeholder", "");
        else if (noteEl.dataset.ph)
          noteEl.setAttribute("placeholder", noteEl.dataset.ph);
      }

      positionTo(current.anchor, "bottom");
    } catch (err) {
      alert(err.message || "Could not save feedback.");
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
    var actionBtn = modal.querySelector(".mt-agreement-button"); // <— botón que abre el agreement
    var withBackdrop = null;

    function openModal() {
      // quitar oculto duro
      modal.removeAttribute("hidden");
      modal.setAttribute("aria-hidden", "false");
      modal.classList.add("show");

      // forzar layout si falta CSS de Bootstrap
      modal.style.position = "fixed";
      modal.style.inset = "0";
      modal.style.display = "flex";
      modal.style.alignItems = "center";
      modal.style.justifyContent = "center";
      modal.style.zIndex = "1055"; // por encima del backdrop

      // crear backdrop
      withBackdrop = document.createElement("div");
      withBackdrop.className = "modal-backdrop fade show";
      withBackdrop.style.zIndex = "1050";
      document.body.appendChild(withBackdrop);

      document.body.classList.add("modal-open");
      try { modal.focus(); } catch (e) {}
    }

    function closeModal() {
      modal.classList.remove("show");
      modal.setAttribute("aria-hidden", "true");
      modal.setAttribute("hidden", ""); // vuelve a ocultar
      modal.style.display = ""; // limpia estilos inyectados
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

    // cerrar con botón X
    if (closeBtn) {
      closeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        closeModal();
      });
    }

    // cerrar cuando el usuario hace clic en “Open Agreement” (abre nueva pestaña)
    if (actionBtn) {
      actionBtn.addEventListener("click", function () {
        // si no hay URL válida, no cerrar
        var href = actionBtn.getAttribute("href") || "";
        if (!href || href === "#") return;
        // da un tick al navegador para abrir el tab y luego cierra
        setTimeout(closeModal, 100);
      });
    }

    // cerrar con Escape
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal.classList.contains("show")) {
        closeModal();
      }
    });

    // auto-open si el servidor indicó que falta firma
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

