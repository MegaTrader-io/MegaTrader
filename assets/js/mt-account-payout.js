(function () {
  "use strict";

  // admin-ajax
  var AJAX_URL = (window.ajaxurl || "/wp-admin/admin-ajax.php");

  // DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else { init(); }

  function $(sel, ctx){ return (ctx||document).querySelector(sel); }
  function $all(sel, ctx){ return Array.prototype.slice.call((ctx||document).querySelectorAll(sel)); }

  function buildItemRow(item){
    // fila para el menú desplegable
    var li = document.createElement("li");
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "dropdown-item py-2 d-flex align-items-center justify-content-between gap-2 mt-payout-option";
    btn.setAttribute("data-value", item.id);
    btn.setAttribute("data-account-id", item.id);
    btn.setAttribute("data-platform-account-id", item.accountId || "");
    btn.innerHTML =
      '<span class="d-flex align-items-center gap-2">'
      + '  <span class="mt-icon mt-icon-diamond" aria-hidden="true"></span>'
      + (item.logo ? ('  <img src="'+escapeHtml(item.logo)+'" alt="" style="width:24px;height:24px;border-radius:50%;">') : '')
      + '  <span class="fw-bold text-white">'+escapeHtml(item.accountId || "Account")+'</span>'
      + '</span>'
      + '<span class="'+escapeHtml(item.badge.class)+'">'+escapeHtml(item.badge.text)+'</span>';
    li.appendChild(btn);
    return li;
  }

  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }

  function init(){
    var modal = document.getElementById("mt-request-payout-modal");
    if (!modal) return;

    modal.addEventListener("show.bs.modal", hydrateOnce, { once:true });
  }

  function hydrateOnce(){
    var modal = document.getElementById("mt-request-payout-modal");
    var mount = $("#mt-payout-accounts", modal);
    if (!mount) return;

    var email = modal.getAttribute("data-user-email") || "";
    mount.innerHTML = '<div class="text-muted">Loading accounts…</div>';

    // fetch payload
    var fd = new FormData();
    fd.append("action", "mt_payouts_prepare_ui");
    fd.append("email", email);

    fetch(AJAX_URL, { method: "POST", body: fd, credentials: "same-origin" })
      .then(r => r.json())
      .then(res => {
        if (!res || !res.success || !res.data || !Array.isArray(res.data.items)) {
          throw new Error((res && res.data && res.data.message) || "No data");
        }
        renderDropdown(mount, res.data);
      })
      .catch(err => {
        mount.innerHTML = '<div class="text-danger">Error loading accounts</div>';
        console.error("[PAYOUT]", err);
      });
  }

  function renderDropdown(mount, data){
    if (!data.items.length) {
      mount.innerHTML = '<div class="text-muted">No funded active accounts found.</div>';
      return;
    }

    var selected = data.selectedId || data.items[0].id;
    var selItem = data.items.find(it => it.id === selected) || data.items[0];

    // dropdown shell (mismas clases/estructura que tu picker)
    // ver mt-account-picker.js para consistencia de UI y semántica. :contentReference[oaicite:0]{index=0}
    var wrap = document.createElement("div");
    wrap.className = "dropdown w-100";

    var btn = document.createElement("button");
    btn.id = "mt-payout-acc-btn";
    btn.type = "button";
    btn.className = "btn btn-dark w-100 d-flex justify-content-between align-items-center rounded-12";
    btn.setAttribute("data-bs-toggle", "dropdown");
    btn.setAttribute("aria-expanded", "false");
    btn.setAttribute("data-account-id", selItem.id);
    btn.setAttribute("data-platform-account-id", selItem.accountId || "");
    btn.innerHTML =
      '<span class="d-flex align-items-center gap-2">'
      + '  <span class="mt-icon mt-icon-diamond" aria-hidden="true"></span>'
      + (selItem.logo ? ('  <img src="'+escapeHtml(selItem.logo)+'" alt="" style="width:24px;height:24px;border-radius:50%;">') : '')
      + '  <span id="mt-payout-acc-label" class="fw-bold text-white">'+escapeHtml(selItem.accountId || "Account")+'</span>'
      + '</span>'
      + '<span class="'+escapeHtml(selItem.badge.class)+'">'+escapeHtml(selItem.badge.text)+'</span>'
      + '<span class="mt-icon mt-icon_caret-down mt-icon-white"></span>';

    var ul = document.createElement("ul");
    ul.id = "mt-payout-acc-menu";
    ul.className = "dropdown-menu w-100 p-0 overflow-hidden rounded-12 mt-1";

    data.items.forEach(function (it) { ul.appendChild(buildItemRow(it)); });

    // hidden select (por accesibilidad si lo necesitas)
    var select = document.createElement("select");
    select.id = "mt-payout-acc-select";
    select.className = "d-none";
    data.items.forEach(function (it) {
      var opt = document.createElement("option");
      opt.value = it.id;
      opt.textContent = it.accountId || "Account";
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
      var item = data.items.find(it => it.id === id);
      if (!item) return;

      select.value = id;
      btn.setAttribute("data-account-id", id);
      btn.setAttribute("data-platform-account-id", item.accountId || "");
      $("#mt-payout-acc-label", btn).textContent = item.accountId || "Account";

      // actualizar badge + logo
      var img = btn.querySelector("img");
      if (img && item.logo) img.src = item.logo;
      var oldBadge = btn.querySelector(".badge-mega");
      if (oldBadge) oldBadge.replaceWith(badgeNode(item.badge.text, item.badge.class));

      updateLimitsAndCTA(item, maxEl, cont);
    });

    function badgeNode(text, cls){
      var span = document.createElement("span");
      span.className = cls;
      span.textContent = text;
      return span;
    }

    function updateLimitsAndCTA(item, maxEl, contBtn){
      var max = (item.meta && typeof item.meta.maxWithdrawal === "number") ? item.meta.maxWithdrawal : null;
      if (maxEl) maxEl.textContent = "Max withdrawal: " + (max !== null ? ("$" + max) : "—");
      // habilita continuar solo si es elegible
      var can = !!item.eligible;
      if (contBtn){
        contBtn.disabled = !can;
        contBtn.classList.toggle("disabled", !can);
      }
      // habilita input de monto si hace falta
      var amt = document.getElementById("mt-payout-amount");
      if (amt){
        amt.disabled = !can;
        if (max !== null && can) { amt.setAttribute("max", String(max)); }
        else { amt.removeAttribute("max"); }
      }
    }
  }
})();
