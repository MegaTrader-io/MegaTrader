(function () {
  "use strict";

  var I18N = (typeof window !== "undefined" && window.MT_PAYOUT_I18N) ? window.MT_PAYOUT_I18N : {};
  var CACHE_TTL_MS = 15000;
  var MT_PAYOUT_CACHE = null;
  var AJAX_URL = window.ajaxurl || "/wp-admin/admin-ajax.php";

  function prefetchPayoutData(force) {
    var modal = document.getElementById("mt-request-payout-modal");
    if (!modal) return Promise.resolve(null);
    var email = modal.getAttribute("data-user-email") || "";
    if (!email) return Promise.resolve(null);

    var now = Date.now();
    var fresh = MT_PAYOUT_CACHE && now - MT_PAYOUT_CACHE.ts < CACHE_TTL_MS;
    if (!force && fresh) return Promise.resolve(MT_PAYOUT_CACHE.data);

    var fd = new FormData();
    fd.append("action", "mt_payouts_prepare_ui");
    fd.append("email", email);

    return fetch(AJAX_URL, { method: "POST", body: fd, credentials: "same-origin" })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res && res.success && res.data && Array.isArray(res.data.items)) {
          MT_PAYOUT_CACHE = { ts: Date.now(), data: res.data };
          return MT_PAYOUT_CACHE.data;
        }
        throw new Error("No data");
      })
      .catch(function () { return MT_PAYOUT_CACHE ? MT_PAYOUT_CACHE.data : null; });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      prefetchPayoutData(false);
      wirePrefetchTriggers();
    }, { once: true });
  } else {
    prefetchPayoutData(false);
    wirePrefetchTriggers();
  }

  function wirePrefetchTriggers() {
    var triggers = document.querySelectorAll('[data-bs-target="#mt-request-payout-modal"]');
    triggers.forEach(function (btn) {
      btn.addEventListener("mouseenter", function () { prefetchPayoutData(false); }, { passive: true });
      btn.addEventListener("click", function () { prefetchPayoutData(false); }, { passive: true });
    });
  }

  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function escapeHtml(s) {
    return String(s || "").replace(/[&<>"']/g, function (m) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m];
    });
  }
  function fmtMoney(n) { if (!isFinite(n)) return "—"; return "$" + (Math.round(Number(n) * 100) / 100).toLocaleString(); }

  // === Steps ===
function switchToStep(step) {
  var s1 = document.getElementById("mt-payout-step1");
  var s2 = document.getElementById("mt-payout-step2");
  var backBtn = document.getElementById("mt-payout-back");
  var cancelBtn = document.getElementById("mt-payout-cancel");
  var contBtn = document.getElementById("mt-payout-continue");
  if (!s1 || !s2 || !backBtn || !cancelBtn || !contBtn) return;

  // Asegurar mismo estilo (link) para ambos
  backBtn.className = "flex-1-1-0 mt-btn mt-btn--link text-white";
  cancelBtn.className = "flex-1-1-0 mt-btn mt-btn--link text-white";

  if (step === 2) {
    // Mostrar Step 2
    s1.classList.add("d-none");
    s2.classList.remove("d-none");

    // Back visible, Cancel oculto
    backBtn.classList.remove("d-none");
    cancelBtn.classList.add("d-none");

    // Texto del botón principal
    contBtn.textContent = (I18N?.confirmButton) || "Confirm Request";
  } else {
    // Mostrar Step 1
    s2.classList.add("d-none");
    s1.classList.remove("d-none");

    // Cancel visible, Back oculto
    cancelBtn.classList.remove("d-none");
    backBtn.classList.add("d-none");

    // Texto del botón principal
    contBtn.textContent = (I18N?.submitButton) || "Continue";
  }
}


  function buildItemRow(item) {
    var li = document.createElement("li");
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "dropdown-item py-2 d-flex align-items-center justify-content-between gap-2 mt-payout-option";
    btn.setAttribute("data-value", item.id);
    btn.setAttribute("data-account-id", item.id);
    btn.setAttribute("data-platform-account-id", item.platformAccountId || "");
    btn.innerHTML =
      '<span class="d-flex align-items-center gap-2">' +
      '  <span class="mt-icon mt-icon-primary mt-icon_diamond mt-icon-md" aria-hidden="true"></span>' +
      (item.logo ? '  <img src="' + escapeHtml(item.logo) + '" alt="" style="width:30px;height:30px;border-radius:50%;">' : "") +
      '  <span class="fw-bold text-white">' + escapeHtml(item.accountName || "—") + "</span>" +
      "</span>" +
      '<span class="d-flex align-items-center gap-3">' +
      '  <span class="' + escapeHtml(item.badge.class) + '">' + escapeHtml(item.badge.text) + "</span>" +
      '  <span class="mt-icon mt-icon_caret-right mt-icon-white"></span>' +
      "</span>";
    li.appendChild(btn);
    return li;
  }

  function init() {
    var modal = document.getElementById("mt-request-payout-modal");
    if (!modal) return;
    modal.addEventListener("show.bs.modal", function () {
      switchToStep(1);
      hydrateOnce(true);
    });
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else {
    init();
  }

  function hydrateOnce(force) {
    var modal = document.getElementById("mt-request-payout-modal");
    var mount = $("#mt-payout-accounts", modal);
    if (!mount) return;

    if (MT_PAYOUT_CACHE && Array.isArray(MT_PAYOUT_CACHE.data?.items)) {
      renderDropdown(mount, MT_PAYOUT_CACHE.data);
    } else {
      mount.innerHTML = '<div class="text-muted">Loading accounts…</div>';
    }

    prefetchPayoutData(!!force).then(function (data) {
      if (data && Array.isArray(data.items)) {
        renderDropdown(mount, data);
      } else if (!MT_PAYOUT_CACHE) {
        mount.innerHTML = '<div class="text-danger">Error loading accounts</div>';
      }
    });
  }

  function renderDropdown(mount, data) {
    if (!data.items.length) {
      mount.innerHTML = '<div class="text-muted">No funded active accounts found.</div>';
      return;
    }

    var selected = data.selected || data.selectedId || data.items[0].id;
    var selItem = data.items.find(function (it) { return it.id === selected; }) || data.items[0];

    var wrap = document.createElement("div");
    wrap.className = "dropdown w-100";

    var btn = document.createElement("button");
    btn.id = "mt-payout-acc-btn";
    btn.type = "button";
    btn.className = "btn btn border-gray bg-131210 w-100 d-flex justify-content-between align-items-center rounded-2xl p-3";
    btn.setAttribute("data-bs-toggle", "dropdown");
    btn.setAttribute("aria-expanded", "false");
    btn.setAttribute("data-account-id", selItem.id);
    btn.setAttribute("data-platform-account-id", selItem.platformAccountId || "");

    btn.innerHTML =
      '<span class="d-flex align-items-center gap-2">' +
      '  <span class="mt-icon mt-icon-primary mt-icon_diamond mt-icon-md" aria-hidden="true"></span>' +
      (selItem.logo ? '  <img src="' + escapeHtml(selItem.logo) + '" alt="" style="width:30px;height:30px;border-radius:50%;">' : "") +
      '  <span id="mt-payout-acc-label" class="fw-bold text-white text-base">' + (selItem.accountName || "—") + "</span>" +
      "</span>" +
      '<span class="d-flex align-items-center gap-3 right-group">' +
      '  <span class="' + selItem.badge.class + '">' + selItem.badge.text + "</span>" +
      '  <span class="mt-icon mt-icon_caret-down mt-icon-white"></span>' +
      "</span>";

    var ul = document.createElement("ul");
    ul.id = "mt-payout-acc-menu";
    ul.className = "dropdown-menu w-100 p-0 overflow-hidden rounded-12 mt-1 bg-1e1e1e border-gray";
    data.items.forEach(function (it) { ul.appendChild(buildItemRow(it)); });

    var select = document.createElement("select");
    select.id = "mt-payout-acc-select";
    select.className = "d-none";
    data.items.forEach(function (it) {
      var opt = document.createElement("option");
      opt.value = it.id;
      opt.textContent = it.accountName || "—";
      if (it.id === selItem.id) opt.selected = true;
      select.appendChild(opt);
    });

    mount.innerHTML = "";
    wrap.appendChild(btn);
    wrap.appendChild(ul);
    wrap.appendChild(select);
    mount.appendChild(wrap);

    var maxEl = document.getElementById("mt-payout-max");
    var amtIn = document.getElementById("mt-payout-amount");
    var errEl = document.getElementById("mt-payout-error");
    var contBtn = document.getElementById("mt-payout-continue");
    applyLimitsAndValidation(selItem, maxEl, amtIn, errEl, contBtn);

    ul.addEventListener("click", function (e) {
      var opt = e.target.closest(".mt-payout-option");
      if (!opt) return;

      var id = opt.getAttribute("data-value");
      var item = data.items.find(function (it) { return it.id === id; });
      if (!item) return;

      select.value = id;
      btn.setAttribute("data-account-id", id);
      btn.setAttribute("data-platform-account-id", item.platformAccountId || "");
      $("#mt-payout-acc-label", btn).textContent = item.accountName || "—";

      var img = btn.querySelector("img");
      if (img && item.logo) img.src = item.logo;
      else if (!img && item.logo) {
        var labelNode = $("#mt-payout-acc-label", btn);
        if (labelNode) {
          var holder = labelNode.parentNode;
          var newImg = document.createElement("img");
          newImg.src = item.logo;
          newImg.alt = "";
          newImg.style.width = "30px";
          newImg.style.height = "30px";
          newImg.style.borderRadius = "50%";
          holder.insertBefore(newImg, labelNode);
        }
      }
      var badge = btn.querySelector(".badge-mega");
      if (badge) {
        badge.className = item.badge.class;
        badge.textContent = item.badge.text;
      }

      applyLimitsAndValidation(item, maxEl, amtIn, errEl, contBtn);
    });

    var backBtn = document.getElementById("mt-payout-back");
    if (backBtn) {
      backBtn.addEventListener("click", function () { switchToStep(1); }, { passive: true });
    }
  }

  function applyLimitsAndValidation(item, maxEl, inputEl, errEl, contBtn) {
    var eligible = !!item.eligible && !!item.eligibleForPayout;
    var maxUI = item.meta && typeof item.meta.maxWithdrawalUI === "number" ? item.meta.maxWithdrawalUI : null;

    if (maxEl) {
      var txt = maxUI !== null && maxUI > 0 ? fmtMoney(maxUI) : "—";
      maxEl.textContent = "Max withdrawal: " + txt;
      maxEl.dataset.max = String(maxUI || 0);
    }

    if (inputEl) {
      inputEl.value = "";
      inputEl.classList.remove("is-invalid");
      inputEl.disabled = !eligible || !(maxUI > 0);
      if (maxUI > 0) inputEl.setAttribute("max", String(Math.floor(maxUI)));
      else inputEl.removeAttribute("max");
    }
    if (errEl) { errEl.style.display = "none"; errEl.textContent = ""; }
    if (contBtn) { contBtn.disabled = true; contBtn.classList.toggle("disabled", true); }

    if (!eligible || !(maxUI > 0) || !inputEl) return;

    var max = function () {
      var v = parseFloat(maxEl?.dataset?.max || "0");
      return Number.isFinite(v) ? v : 0;
    };

    function setError(msg) {
      if (!errEl) return;
      if (!msg) { errEl.style.display = "none"; errEl.textContent = ""; inputEl.classList.remove("is-invalid"); }
      else { errEl.style.display = ""; errEl.textContent = msg; inputEl.classList.add("is-invalid"); }
    }

    function validate() {
      var m = max();
      var raw = inputEl.value.trim();
      var val = parseFloat(raw);

      if (contBtn) { contBtn.disabled = true; contBtn.classList.add("disabled"); }

      if (!raw) { setError(""); return; }
      if (!Number.isFinite(val) || val <= 0) { setError(I18N.withdrawalAmountMinorZero || "Enter a valid amount greater than 0."); return; }
      if (m <= 0) { setError(I18N.withdrawalAmountNotEligible || "This account is not eligible for payout at the moment."); return; }
      if (val > m) { setError(I18N.withdrawalAmountError || "Amount exceeds the maximum allowed for this payout."); return; }

      setError("");
      if (contBtn) { contBtn.disabled = false; contBtn.classList.remove("disabled"); }
    }

    inputEl.addEventListener("input", validate, { passive: true });

    if (contBtn) {
      contBtn.addEventListener("click", function () {
        var step2Visible = !document.getElementById("mt-payout-step2")?.classList.contains("d-none");
        if (step2Visible) {
          console.log("SUBMIT PAYOUT", window.MT_PAYOUT_SUBMIT);
          return;
        }

        validate();
        if (contBtn.disabled) return;

        var modal = document.getElementById("mt-request-payout-modal");
        var email = modal?.getAttribute("data-user-email") || document.getElementById("mt-payout-email")?.value || "";
        var accBtn = document.getElementById("mt-payout-acc-btn");
        var internalId = accBtn?.getAttribute("data-account-id") || item.id;
        var platformAccountId = accBtn?.getAttribute("data-platform-account-id") || item.platformAccountId || "";

        var raw = inputEl.value.trim();
        var amount = parseFloat(raw) || 0;

        var fee = Math.round(amount * 0.10 * 100) / 100;
        var receive = Math.max(0, Math.round((amount - fee) * 100) / 100);

        var setTxt = function (id, val) { var el = document.getElementById(id); if (el) el.textContent = val; };
        setTxt("mt-review-email", email || "—");
        setTxt("mt-review-account", platformAccountId || "—");
        setTxt("mt-review-amount", fmtMoney(amount));
        setTxt("mt-review-fee", fmtMoney(fee));
        setTxt("mt-review-receive", fmtMoney(receive));

        window.MT_PAYOUT_SUBMIT = { accountId: internalId, platformAccountId: platformAccountId, amount: amount, fee: fee, receive: receive };

        switchToStep(2);
      }, { passive: true });
    }
  }

  window.MT_PAYOUT_DEBUG = {
    forceRefresh: function () { MT_PAYOUT_CACHE = null; return prefetchPayoutData(true).then(console.log); },
    cache: function () { return MT_PAYOUT_CACHE; },
  };
})();
