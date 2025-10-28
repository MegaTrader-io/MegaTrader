(function () {
  "use strict";

  // =========================
  // Prefetch / Caché en memoria
  // =========================
  var MT_PAYOUT_CACHE = null;
  var AJAX_URL = (window.ajaxurl || "/wp-admin/admin-ajax.php");

  function prefetchPayoutData() {
    var modal = document.getElementById("mt-request-payout-modal");
    if (!modal) return Promise.resolve(null);

    var email = modal.getAttribute("data-user-email") || "";
    if (!email) return Promise.resolve(null);

    if (MT_PAYOUT_CACHE) return Promise.resolve(MT_PAYOUT_CACHE);

    var fd = new FormData();
    fd.append("action", "mt_payouts_prepare_ui");
    fd.append("email", email);

    return fetch(AJAX_URL, { method: "POST", body: fd, credentials: "same-origin" })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res && res.success && res.data && Array.isArray(res.data.items)) {
          MT_PAYOUT_CACHE = res.data; // cache
          return MT_PAYOUT_CACHE;
        }
        throw new Error("No data");
      })
      .catch(function () { return null; });
  }

  // Prefetch global temprano + en hover/click del trigger
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function(){
      prefetchPayoutData();
      wirePrefetchTriggers();
    }, { once: true });
  } else {
    prefetchPayoutData();
    wirePrefetchTriggers();
  }

  function wirePrefetchTriggers(){
    var triggers = document.querySelectorAll('[data-bs-target="#mt-request-payout-modal"]');
    triggers.forEach(function(btn){
      btn.addEventListener("mouseenter", prefetchPayoutData, {passive:true});
      btn.addEventListener("click", prefetchPayoutData, {passive:true});
    });
  }

  // =========================
  // Utilidades DOM
  // =========================
  function $(sel, ctx){ return (ctx||document).querySelector(sel); }
  function $all(sel, ctx){ return Array.prototype.slice.call((ctx||document).querySelectorAll(sel)); }

  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]); }); }

  // =========================
  // Fila de opción en dropdown
  // =========================
  function buildItemRow(item){
    var li = document.createElement("li");
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "dropdown-item py-2 d-flex align-items-center justify-content-between gap-2 mt-payout-option";
    btn.setAttribute("data-value", item.id);
    btn.setAttribute("data-account-id", item.id);
    btn.setAttribute("data-platform-account-id", item.platformAccountId || "");
    btn.innerHTML =
      '<span class="d-flex align-items-center gap-2">'
      + '  <span class="mt-icon mt-icon-primary mt-icon_diamond mt-icon-md" aria-hidden="true"></span>'
      + (item.logo ? ('  <img src="'+escapeHtml(item.logo)+'" alt="" style="width:24px;height:24px;border-radius:50%;">') : '')
      + '  <span class="fw-bold text-white">'+escapeHtml(item.accountName || "—")+'</span>'
      + '</span>'
      + '<span class="d-flex align-items-center gap-3">'
      + '  <span class="'+escapeHtml(item.badge.class)+'">'+escapeHtml(item.badge.text)+'</span>'
      + '  <span class="mt-icon mt-icon_caret-right mt-icon-white"></span>'
      + '</span>';
    li.appendChild(btn);
    return li;
  }

  // =========================
  // Init / Hydrate
  // =========================
  function init(){
    var modal = document.getElementById("mt-request-payout-modal");
    if (!modal) return;

    // hidratar solo una vez al abrir
    modal.addEventListener("show.bs.modal", hydrateOnce, { once:true });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else { init(); }

  function hydrateOnce(){
    var modal = document.getElementById("mt-request-payout-modal");
    var mount = $("#mt-payout-accounts", modal);
    if (!mount) return;

    // 1) Si tenemos caché, pinta al instante
    if (MT_PAYOUT_CACHE && Array.isArray(MT_PAYOUT_CACHE.items)) {
      renderDropdown(mount, MT_PAYOUT_CACHE);
    } else {
      mount.innerHTML = '<div class="text-muted">Loading accounts…</div>';
    }

    // 2) Refresca en segundo plano (y re-render si cambió)
    prefetchPayoutData().then(function(data){
      if (data && Array.isArray(data.items)) {
        renderDropdown(mount, data);
      } else if (!MT_PAYOUT_CACHE) {
        mount.innerHTML = '<div class="text-danger">Error loading accounts</div>';
      }
    });
  }

  // =========================
  // Render principal
  // =========================
  function renderDropdown(mount, data){
    if (!data.items.length) {
      mount.innerHTML = '<div class="text-muted">No funded active accounts found.</div>';
      return;
    }

    var selected = data.selected || data.selectedId || data.items[0].id;
    var selItem = data.items.find(function(it){ return it.id === selected; }) || data.items[0];

    var wrap = document.createElement("div");
    wrap.className = "dropdown w-100";

    var btn = document.createElement("button");
    btn.id = "mt-payout-acc-btn";
    btn.type = "button";
    btn.className = "btn btn border-gray bg-131210 w-100 d-flex justify-content-between align-items-center rounded-12";
    btn.setAttribute("data-bs-toggle", "dropdown");
    btn.setAttribute("aria-expanded", "false");
    btn.setAttribute("data-account-id", selItem.id);
    btn.setAttribute("data-platform-account-id", selItem.platformAccountId || "");

    btn.innerHTML =
      '<span class="d-flex align-items-center gap-2">'
      + '  <span class="mt-icon mt-icon-primary mt-icon_diamond mt-icon-md" aria-hidden="true"></span>'
      + (selItem.logo ? ('  <img src="'+escapeHtml(selItem.logo)+'" alt="" style="width:24px;height:24px;border-radius:50%;">') : '')
      + '  <span id="mt-payout-acc-label" class="fw-bold text-white">'+escapeHtml(selItem.accountName || "—")+'</span>'
      + '</span>'
      + '<span class="d-flex align-items-center gap-3">'
      + '  <span class="'+escapeHtml(selItem.badge.class)+'">'+escapeHtml(selItem.badge.text)+'</span>'
      + '  <span class="mt-icon mt-icon_caret-down mt-icon-white"></span>'
      + '</span>';

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

    wrap.appendChild(btn);
    wrap.appendChild(ul);
    wrap.appendChild(select);

    mount.innerHTML = "";
    mount.appendChild(wrap);

    // Max withdrawal + estado del botón Continue (si es eligible)
    var maxEl = document.getElementById("mt-payout-max");
    var cont  = document.getElementById("mt-payout-continue");
    updateLimitsAndCTA(selItem, maxEl, cont);

    ul.addEventListener("click", function (e) {
      var opt = e.target.closest(".mt-payout-option");
      if (!opt) return;

      var id = opt.getAttribute("data-value");
      var item = data.items.find(function(it){ return it.id === id; });
      if (!item) return;

      select.value = id;
      btn.setAttribute("data-account-id", id);
      btn.setAttribute("data-platform-account-id", item.platformAccountId || "");
      $("#mt-payout-acc-label", btn).textContent = item.accountName || "—";

      // actualizar badge + logo
      var img = btn.querySelector("img");
      if (img && item.logo) img.src = item.logo;
      else if (!img && item.logo) {
        var labelNode = $("#mt-payout-acc-label", btn);
        if (labelNode){
          var holder = labelNode.parentNode;
          var newImg = document.createElement("img");
          newImg.src = item.logo;
          newImg.alt = "";
          newImg.style.width = "24px";
          newImg.style.height = "24px";
          newImg.style.borderRadius = "50%";
          holder.insertBefore(newImg, labelNode);
        }
      }
      var rightGroup = btn.querySelector(".d-flex.align-items-center.gap-3 .badge-mega");
      if (rightGroup) {
        rightGroup.className = item.badge.class;
        rightGroup.textContent = item.badge.text;
      }

      updateLimitsAndCTA(item, maxEl, cont);
    });

    function updateLimitsAndCTA(item, maxEl, contBtn){
      var max = (item.meta && typeof item.meta.maxWithdrawal === "number") ? item.meta.maxWithdrawal : null;
      if (maxEl) maxEl.textContent = "Max withdrawal: " + (max !== null ? ("$" + max) : "—");
      var can = !!item.eligible;
      if (contBtn){
        contBtn.disabled = !can;
        contBtn.classList.toggle("disabled", !can);
      }
      var amt = document.getElementById("mt-payout-amount");
      if (amt){
        amt.disabled = !can;
        if (max !== null && can) { amt.setAttribute("max", String(max)); }
        else { amt.removeAttribute("max"); }
      }
    }
  }
})();
