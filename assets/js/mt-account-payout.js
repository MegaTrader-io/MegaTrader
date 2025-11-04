(function () {
  "use strict";

  var I18N =
    typeof window !== "undefined" && window.MT_PAYOUT_I18N
      ? window.MT_PAYOUT_I18N
      : {};
  var CACHE_TTL_MS = 15000;
  var MT_PAYOUT_CACHE = null;
  var AJAX_URL =
    (window.MT_PAYOUT_VARS && window.MT_PAYOUT_VARS.ajaxurl) ||
    window.ajaxurl ||
    "/wp-admin/admin-ajax.php";
  var AJAX_NONCE = window.MT_PAYOUT_VARS && window.MT_PAYOUT_VARS.nonce;

  // ---------- Utils ----------
  function $(sel, ctx) {
    return (ctx || document).querySelector(sel);
  }
  function $all(sel, ctx) {
    return Array.prototype.slice.call((ctx || document).querySelectorAll(sel));
  }
  function escapeHtml(s) {
    return String(s || "").replace(/[&<>"']/g, function (m) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;",
      }[m];
    });
  }
  function fmtMoney(n) {
    if (!isFinite(n)) return "—";
    return "$" + (Math.round(Number(n) * 100) / 100).toLocaleString();
  }
  function showPreloader() {
    try {
      if (window.jQuery && window.jQuery(".preloader").length)
        window.jQuery(".preloader").fadeIn();
    } catch (e) {}
  }
  function hidePreloader() {
    try {
      if (window.jQuery && window.jQuery(".preloader").length)
        window.jQuery(".preloader").fadeOut();
    } catch (e) {}
  }

  // ---------- Modal error ----------
  function showGlobalError(headline, message, opts) {
    try {
      if (
        window.MEGATRADER &&
        typeof window.MEGATRADER.showError === "function"
      ) {
        window.MEGATRADER.showError(
          headline || "Request failed",
          message || "Unexpected error.",
          opts || {}
        );
        setTimeout(function () {
          var lastBackdrop = document.querySelector(
            ".modal-backdrop:last-of-type"
          );
          if (lastBackdrop) lastBackdrop.classList.add("mt-error");
        }, 0);
        return;
      }
      var m = document.getElementById("mt-error-modal");
      if (m) {
        var t = document.getElementById("mt-error-title");
        var msg = document.getElementById("mt-error-message");
        if (t) t.textContent = headline || "Request failed";
        if (msg) msg.textContent = message || "Unexpected error.";
        var inst =
          window.bootstrap && window.bootstrap.Modal
            ? window.bootstrap.Modal.getOrCreateInstance(m, { backdrop: true })
            : null;
        if (inst) {
          inst.show();
          setTimeout(function () {
            var lastBackdrop = document.querySelector(
              ".modal-backdrop:last-of-type"
            );
            if (lastBackdrop) lastBackdrop.classList.add("mt-error");
          }, 0);
        } else {
          alert(
            (headline ? headline + "\n\n" : "") +
              (message || "Unexpected error.")
          );
        }
      } else {
        alert(
          (headline ? headline + "\n\n" : "") + (message || "Unexpected error.")
        );
      }
    } catch (e) {
      console.error("showError failed:", e);
      alert(
        (headline ? headline + "\n\n" : "") + (message || "Unexpected error.")
      );
    }
  }

  // ---------- Prefetch cuentas ----------
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
    if (AJAX_NONCE) fd.append("nonce", AJAX_NONCE); // esta ruta usa 'nonce'

    return fetch(AJAX_URL, {
      method: "POST",
      body: fd,
      credentials: "same-origin",
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        if (res && res.success && res.data && Array.isArray(res.data.items)) {
          MT_PAYOUT_CACHE = { ts: Date.now(), data: res.data };
          return MT_PAYOUT_CACHE.data;
        }
        throw new Error("No data");
      })
      .catch(function () {
        return MT_PAYOUT_CACHE ? MT_PAYOUT_CACHE.data : null;
      });
  }

  function wirePrefetchTriggers() {
    var triggers = document.querySelectorAll(
      '[data-bs-target="#mt-request-payout-modal"]'
    );
    triggers.forEach(function (btn) {
      btn.addEventListener(
        "mouseenter",
        function () {
          prefetchPayoutData(false);
        },
        { passive: true }
      );
      btn.addEventListener(
        "click",
        function () {
          prefetchPayoutData(false);
        },
        { passive: true }
      );
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener(
      "DOMContentLoaded",
      function () {
        prefetchPayoutData(false);
        wirePrefetchTriggers();
      },
      { once: true }
    );
  } else {
    prefetchPayoutData(false);
    wirePrefetchTriggers();
  }

  // ---------- Steps ----------
  function ensureConfirmButton() {
    var footer = document.querySelector(
      "#mt-request-payout-modal .modal-footer"
    );
    if (!footer) return null;
    var confirmBtn = document.getElementById("mt-payout-confirm");
    if (!confirmBtn) {
      confirmBtn = document.createElement("button");
      confirmBtn.id = "mt-payout-confirm";
      confirmBtn.type = "button";
      confirmBtn.className =
        "flex-1-1-0 mt-btn mt-btn--md mt-btn--primary d-none";
      confirmBtn.textContent =
        (I18N && I18N.confirmButton) || "Confirm Request";
      footer.appendChild(confirmBtn);
    }
    return confirmBtn;
  }

  function switchToStep(step) {
    var s1 = document.getElementById("mt-payout-step1");
    var s2 = document.getElementById("mt-payout-step2");
    var backBtn = document.getElementById("mt-payout-back");
    var cancelBtn = document.getElementById("mt-payout-cancel");
    var contBtn = document.getElementById("mt-payout-continue");
    var confirmBtn = ensureConfirmButton();
    if (!s1 || !s2 || !backBtn || !cancelBtn || !contBtn || !confirmBtn) return;

    backBtn.className = "flex-1-1-0 mt-btn mt-btn--link text-white";
    cancelBtn.className = "flex-1-1-0 mt-btn mt-btn--link text-white";

    if (step === 2) {
      s1.classList.add("d-none");
      s2.classList.remove("d-none");
      backBtn.classList.remove("d-none");
      cancelBtn.classList.add("d-none");
      contBtn.classList.add("d-none");
      confirmBtn.classList.remove("d-none");
      confirmBtn.disabled = false;
    } else {
      s2.classList.add("d-none");
      s1.classList.remove("d-none");
      cancelBtn.classList.remove("d-none");
      backBtn.classList.add("d-none");
      contBtn.classList.remove("d-none");
      confirmBtn.classList.add("d-none");
    }
  }

  // ---------- Render cuentas ----------
  function buildItemRow(item) {
    var li = document.createElement("li");
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className =
      "dropdown-item py-2 d-flex align-items-center justify-content-between gap-2 mt-payout-option";
    btn.setAttribute("data-value", item.id);
    btn.setAttribute("data-account-id", item.id);
    btn.setAttribute("data-platform-account-id", item.platformAccountId || "");
    btn.innerHTML =
      '<span class="d-flex align-items-center gap-2">' +
      '  <span class="mt-icon mt-icon_caret-right mt-icon-white"></span>' +
      '  <span class="mt-icon mt-icon-primary mt-icon_diamond mt-icon-md" aria-hidden="true"></span>' +
      (item.logo
        ? '  <img src="' +
          escapeHtml(item.logo) +
          '" alt="" style="width:30px;height:30px;border-radius:50%;">'
        : "") +
      '  <span class="fw-bold text-white text-base text-uppercase">' +
      escapeHtml(item.accountSize || "—") +
      "</span>" +
      '  <span class="mt-badge mt-badge-light mt-badge-rounded-sm">' +
      escapeHtml(item.accountName || "—") +
      "</span>" +
      "</span>" +
      '<span class="d-flex align-items-center gap-3 right-group d-none">' +
      '  <span class="' +
      escapeHtml(item.badge.class) +
      '">' +
      escapeHtml(item.badge.text) +
      "</span>" +
      "</span>";
    li.appendChild(btn);
    return li;
  }

  function hydrateOnce(force) {
    var modal = document.getElementById("mt-request-payout-modal");
    var mount = $("#mt-payout-accounts", modal);
    if (!mount) return;

    if (
      MT_PAYOUT_CACHE &&
      Array.isArray(MT_PAYOUT_CACHE.data && MT_PAYOUT_CACHE.data.items)
    ) {
      renderDropdown(mount, MT_PAYOUT_CACHE.data);
    } else {
      mount.innerHTML = '<div class="text-muted">Loading accounts…</div>';
    }

    prefetchPayoutData(!!force).then(function (data) {
      if (data && Array.isArray(data.items)) {
        renderDropdown(mount, data);
      } else if (!MT_PAYOUT_CACHE) {
        mount.innerHTML =
          '<div class="text-danger">Error loading accounts</div>';
      }
    });
  }

  function renderDropdown(mount, data) {
    if (!data.items.length) {
      mount.innerHTML =
        '<div class="text-muted">No funded active accounts found.</div>';
      return;
    }

    var selected = data.selected || data.selectedId || data.items[0].id;
    var selItem =
      data.items.find(function (it) {
        return it.id === selected;
      }) || data.items[0];

    var wrap = document.createElement("div");
    wrap.className = "dropdown w-100";

    var btn = document.createElement("button");
    btn.id = "mt-payout-acc-btn";
    btn.type = "button";
    btn.className =
      "btn btn border-gray bg-131210 w-100 d-flex justify-content-between align-items-center rounded-2xl p-3";
    btn.setAttribute("data-bs-toggle", "dropdown");
    btn.setAttribute("aria-expanded", "false");
    btn.setAttribute("data-account-id", selItem.id);
    btn.setAttribute(
      "data-platform-account-id",
      selItem.platformAccountId || ""
    );
    btn.innerHTML =
      '<span class="d-flex align-items-center gap-2">' +
      '  <span class="mt-icon mt-icon_caret-down mt-icon-white"></span>' +
      '  <span class="mt-icon mt-icon-primary mt-icon_diamond mt-icon-md" aria-hidden="true"></span>' +
      (selItem.logo
        ? '  <img src="' +
          escapeHtml(selItem.logo) +
          '" alt="" style="width:30px;height:30px;border-radius:50%;">'
        : "") +
      '  <span id="mt-payout-acc-size" class="fw-bold text-white text-base text-uppercase">' +
      (selItem.accountSize || "—") +
      "</span>" +
      '  <span id="mt-payout-acc-name-badge" class="mt-badge mt-badge-light mt-badge-rounded-sm">' +
      (selItem.accountName || "—") +
      "</span>" +
      "</span>" +
      '<span class="d-flex align-items-center gap-3 right-group d-none">' +
      '  <span class="' +
      selItem.badge.class +
      '">' +
      selItem.badge.text +
      "</span>" +
      "</span>";

    var ul = document.createElement("ul");
    ul.id = "mt-payout-acc-menu";
    ul.className =
      "dropdown-menu w-100 p-0 rounded-12 mt-1 bg-1e1e1e border-gray mt-acc-menu";
    data.items.forEach(function (it) {
      ul.appendChild(buildItemRow(it));
    });

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

    var modal = document.getElementById("mt-request-payout-modal");
    if (modal)
      modal.setAttribute(
        "data-account-id",
        selItem.id || modal.getAttribute("data-account-id") || ""
      );

    var maxEl = document.getElementById("mt-payout-max");
    var amtIn = document.getElementById("mt-payout-amount");
    var errEl = document.getElementById("mt-payout-error");
    var contBtn = document.getElementById("mt-payout-continue");
    applyLimitsAndValidation(selItem, maxEl, amtIn, errEl, contBtn);

    ul.addEventListener("click", function (e) {
      var opt = e.target.closest(".mt-payout-option");
      if (!opt) return;
      var id = opt.getAttribute("data-value");
      var item = data.items.find(function (it) {
        return it.id === id;
      });
      if (!item) return;

      select.value = id;
      btn.setAttribute("data-account-id", id);
      btn.setAttribute(
        "data-platform-account-id",
        item.platformAccountId || ""
      );

      var labSize = $("#mt-payout-acc-size", btn);
      if (labSize) labSize.textContent = item.accountSize || "—";

      var nameBadge = $("#mt-payout-acc-name-badge", btn);
      if (nameBadge) {
        nameBadge.textContent = item.accountName || "—";
        nameBadge.className = "mt-badge mt-badge-light mt-badge-rounded-sm";
      }

      var img = btn.querySelector("img");
      if (img && item.logo) img.src = item.logo;
      else if (!img && item.logo) {
        var sizeNode = $("#mt-payout-acc-size", btn);
        if (sizeNode) {
          var holder = sizeNode.parentNode;
          var newImg = document.createElement("img");
          newImg.src = item.logo;
          newImg.alt = "";
          newImg.style.width = "30px";
          newImg.style.height = "30px";
          newImg.style.borderRadius = "50%";
          holder.insertBefore(newImg, sizeNode);
        }
      }

      var elig = btn.querySelector(".right-group .badge-mega");
      if (elig) {
        elig.className = item.badge.class;
        elig.textContent = item.badge.text;
      }

      var modal = document.getElementById("mt-request-payout-modal");
      if (modal) modal.setAttribute("data-account-id", id);
      applyLimitsAndValidation(item, maxEl, amtIn, errEl, contBtn);
    });

    var backBtn = document.getElementById("mt-payout-back");
    if (backBtn)
      backBtn.addEventListener(
        "click",
        function () {
          switchToStep(1);
        },
        { passive: true }
      );

    wireMethodSelectors();
  }

  // ---------- Method selectors ----------
  function wireMethodSelectors() {
    var modal = document.getElementById("mt-request-payout-modal");
    if (!modal) return;

    var btns = $all(
      '#mt-payout-step1 .mt-btn[data-role="mt-method-btn"][data-value]',
      modal
    );

    btns.forEach(function (b) {
      b.addEventListener(
        "click",
        function () {
          btns.forEach(function (x) {
            x.classList.remove("mt-btn--primary");
            x.classList.add("mt-btn--secondary");
          });
          b.classList.remove("mt-btn--secondary");
          b.classList.add("mt-btn--primary");

          // IMPORTANTE: sin toLowerCase — debe quedar "Rise"
          var val = b.getAttribute("data-value") || "";
          modal.setAttribute("data-method", val);

          var hf = document.getElementById("mt-payout-methodfield-primary");
          if (hf) hf.setAttribute("name", "email"); // el campo principal siempre se llama email
          var mHidden = document.getElementById("mt-payout-method");
          if (mHidden) mHidden.value = val;
        },
        { passive: true }
      );
    });

    // Default a "Rise" si existe, sino el primero
    var def =
      btns.find(function (x) {
        return (x.getAttribute("data-value") || "") === "Rise";
      }) || btns[0];
    if (def) def.click();
  }

  // ---------- Validación + Step 1/2 ----------
  function applyLimitsAndValidation(item, maxEl, inputEl, errEl, contBtn) {
    var eligible = !!item.eligible && !!item.eligibleForPayout;
    var maxUI =
      item.meta && typeof item.meta.maxWithdrawalUI === "number"
        ? item.meta.maxWithdrawalUI
        : null;
    var minUI =
      item.meta && typeof item.meta.minWithdrawal === "number"
        ? item.meta.minWithdrawal
        : 0;

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
    if (errEl) {
      errEl.style.display = "none";
      errEl.textContent = "";
    }
    if (contBtn) {
      contBtn.disabled = true;
      contBtn.classList.toggle("disabled", true);
    }

    if (!eligible || !(maxUI > 0) || !inputEl) return;

    var max = function () {
      var v = parseFloat((maxEl && maxEl.dataset && maxEl.dataset.max) || "0");
      return Number.isFinite(v) ? v : 0;
    };

    function setError(msg) {
      if (!errEl) return;
      if (!msg) {
        errEl.style.display = "none";
        errEl.textContent = "";
        inputEl.classList.remove("is-invalid");
      } else {
        errEl.style.display = "";
        errEl.textContent = msg;
        inputEl.classList.add("is-invalid");
      }
    }

    function validate() {
      var m = max();
      var raw = inputEl.value.trim();
      var val = parseFloat(raw);
      if (contBtn) {
        contBtn.disabled = true;
        contBtn.classList.add("disabled");
      }

      if (!raw) {
        setError("");
        return;
      }
      if (!Number.isFinite(val) || val <= 0) {
        setError(
          (I18N && I18N.withdrawalAmountMinorZero) ||
            "Enter a valid amount greater than 0."
        );
        return;
      }
      if (m <= 0) {
        setError(
          (I18N && I18N.withdrawalAmountNotEligible) ||
            "This account is not eligible for payout at the moment."
        );
        return;
      }
      if (val > m) {
        setError(
          (I18N && I18N.withdrawalAmountError) ||
            "Amount exceeds the maximum allowed for this payout."
        );
        return;
      }
      if (val < minUI) {
        setError(
          ((I18N && I18N.withdrawalAmountBelowMin) ||
            "Amount is below the minimum withdrawal for this account.") +
            (minUI > 0 ? " (" + fmtMoney(minUI) + " min)" : "")
        );
        return;
      }
      setError("");
      if (contBtn) {
        contBtn.disabled = false;
        contBtn.classList.remove("disabled");
      }
    }

    inputEl.addEventListener("input", validate, { passive: true });

    if (contBtn && !contBtn._wired) {
      contBtn._wired = true;
      contBtn.addEventListener(
        "click",
        function () {
          var s2 = document.getElementById("mt-payout-step2");
          var step2Visible = s2 && !s2.classList.contains("d-none");
          if (step2Visible) return;

          validate();
          if (contBtn.disabled) return;

          var modal = document.getElementById("mt-request-payout-modal");
          var email =
            (modal && modal.getAttribute("data-user-email")) ||
            ($("#mt-payout-email") && $("#mt-payout-email").value) ||
            "";

          var method = (modal && modal.getAttribute("data-method")) || "";
          if (!method) {
            setError("Select a payout method.");
            return;
          }

          var accBtn = document.getElementById("mt-payout-acc-btn");
          var internalId =
            (accBtn && accBtn.getAttribute("data-account-id")) ||
            (modal && modal.getAttribute("data-account-id")) ||
            item.id;

          var raw = inputEl.value.trim();
          var amount = parseFloat(raw) || 0;
          var fee = Math.round(amount * 0.1 * 100) / 100;
          var receive = Math.max(0, Math.round((amount - fee) * 100) / 100);

          function setTxt(id, val) {
            var el = document.getElementById(id);
            if (el) el.textContent = val;
          }
          setTxt("mt-review-email", email || "—");
          setTxt(
            "mt-review-account",
            (accBtn && accBtn.getAttribute("data-platform-account-id")) ||
              item.platformAccountId ||
              "—"
          );
          setTxt("mt-review-amount", fmtMoney(amount));
          setTxt("mt-review-fee", fmtMoney(fee));
          setTxt("mt-review-receive", fmtMoney(receive));

          window.MT_PAYOUT_SUBMIT = {
            accountId: internalId,
            platformAccountId:
              (accBtn && accBtn.getAttribute("data-platform-account-id")) ||
              item.platformAccountId ||
              "",
            amount: amount,
            fee: fee,
            receive: receive,
            method: method,
            email: email,
          };

          switchToStep(2);
        },
        { passive: true }
      );
    }

    // Confirm (Step 2) → POST JSON
    var confirmBtn = ensureConfirmButton();
    if (confirmBtn && !confirmBtn._wired) {
      confirmBtn._wired = true;
      confirmBtn.addEventListener("click", function () {
        var s2 = document.getElementById("mt-payout-step2");
        if (!s2 || s2.classList.contains("d-none")) return;

        var st = window.MT_PAYOUT_SUBMIT || {};
        var account = st.accountId || "";
        var amount = st.amount || 0;
        var method = st.method || ""; // NO lower-case
        var email = st.email || "";

        if (!account || !amount || !method || !email) {
          var err = document.getElementById("mt-payout-error");
          if (err) {
            err.style.display = "";
            err.textContent = "Account, amount, method and email are required.";
          }
          return;
        }

        var url = (function () {
          var base =
            (window.MT_PAYOUT_VARS && window.MT_PAYOUT_VARS.ajaxurl) ||
            window.ajaxurl ||
            "/wp-admin/admin-ajax.php";
          var q = new URLSearchParams();
          q.set("action", "mt_payouts_create");
          if (window.MT_PAYOUT_VARS && MT_PAYOUT_VARS.nonce)
            q.set("_wpnonce", MT_PAYOUT_VARS.nonce);
          return base + "?" + q.toString();
        })();

        var payload = {
          account: String(account),
          amount: Number(amount),
          method: String(method), // “Rise”, “crypto-bitcoin”, etc. tal cual
          currency: "USD",
          reason: "Customer request from js",
          methodFields: [{ name: "email", value: String(email) }],
          ip: "127.0.0.1",
        };

        console.log("[PAYOUT] POST(JSON) →", url, payload);

        confirmBtn.disabled = true;
        showPreloader();

        fetch(url, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
          body: JSON.stringify(payload),
        })
          .then(function (r) {
            var status = r.status;
            return r.text().then(function (t) {
              var json = null;
              try {
                json = JSON.parse(t);
              } catch (e) {}
              return { status: status, raw: t, json: json };
            });
          })
          .then(function (resp) {
            console.log("[PAYOUT] ←", resp);
            var status = resp.status,
              res = resp.json;

            if (status === 409) {
              showGlobalError(
                "Payout unavailable",
                (res && (res.data?.message || res.message)) ||
                  "You already have a payout request in progress.",
                { code: "409" }
              );
              confirmBtn.disabled = false;
              return;
            }

            if (status < 200 || status >= 300 || !res) {
              var msg =
                (res && (res.data?.message || res.data?.error)) ||
                (res && res.message) ||
                "Request failed.";
              showGlobalError("Request failed", msg, { code: String(status) });
              confirmBtn.disabled = false;
              return;
            }

            if (res && res.success) {
              renderCongratsStep();
            } else {
              var msg2 =
                (res && (res.data?.message || res.data?.error)) ||
                "Unknown error.";
              showGlobalError("Request failed", msg2, {});
              confirmBtn.disabled = false;
            }
          })
          .catch(function (e) {
            console.error("[PAYOUT] ERR ←", e);
            showGlobalError(
              "Network error",
              "Network/server error, please try again later.",
              {}
            );
            confirmBtn.disabled = false;
          })
          .finally(function () {
            hidePreloader();
          });
      });
    }
  }

  // ---------- Step 3 Congrats ----------
  function renderCongratsStep() {
    var modal = document.getElementById("mt-request-payout-modal");
    if (!modal) return;
    var body = modal.querySelector(".modal-body");
    var footer = modal.querySelector(".modal-footer");
    var headerTitle = modal.querySelector("#mtpayout-title");
    if (headerTitle) headerTitle.textContent = "Payout Request";

    var congrats = (I18N && I18N.congrats) || "Congrats!";
    var msg =
      (I18N && I18N.congratsMessage) ||
      "Your request has been successfully submitted";
    var subtitle =
      (I18N && I18N.congratsSubtitle) ||
      "You’ll be notified once your request is approved.";

    if (body) {
      body.innerHTML =
        '<div class="text-center w-100 mb-n4">' +
        '  <img fetchpriority="high" decoding="async" class="d-none d-sm-inline-block" src="/wp-content/themes/megatrader-addons/assets/img/thank-you.png" alt="thank you" width="690" height="132">' +
        '  <img decoding="async" class="d-inline-block d-sm-none" src="/wp-content/themes/megatrader-addons/assets/img/thank-you2.png" alt="thank you" width="327" height="132">' +
        "</div>" +
        '<div class="d-flex flex-column align-items-center gap-2 pb-4">' +
        '  <div class="fw-medium leading-60px text-5xl text-uppercase text-white">' +
        congrats +
        "</div>" +
        '  <div class="text-white fw-medium text-uppercase text-2xl leading-7 text-center">' +
        msg +
        "</div>" +
        '  <div class="fw-medium text-a8a29e text-base text-center">' +
        subtitle +
        "</div>" +
        "</div>";
    }

    if (footer) {
      footer.innerHTML = "";
      var closeBtn = document.createElement("button");
      closeBtn.type = "button";
      closeBtn.className = "mt-btn mt-btn--md mt-btn--primary m-auto";
      closeBtn.setAttribute("data-bs-dismiss", "modal");
      closeBtn.textContent = "Close";
      footer.appendChild(closeBtn);
    }
  }

  // === Reset modal al cerrar y al abrir (evita reabrir en Step 3) ===
  (function () {
    var _mtp_initSnapshot = null;

    function snapshotModal() {
      var modal = document.getElementById("mt-request-payout-modal");
      if (!modal || _mtp_initSnapshot) return;
      var body = modal.querySelector(".modal-body");
      var footer = modal.querySelector(".modal-footer");
      var title = modal.querySelector("#mtpayout-title");
      _mtp_initSnapshot = {
        body: body ? body.innerHTML : "",
        footer: footer ? footer.innerHTML : "",
        title: title ? title.textContent : "",
      };
    }

    function restoreModal() {
      var modal = document.getElementById("mt-request-payout-modal");
      if (!modal || !_mtp_initSnapshot) return;

      var body = modal.querySelector(".modal-body");
      var footer = modal.querySelector(".modal-footer");
      var title = modal.querySelector("#mtpayout-title");
      if (body) body.innerHTML = _mtp_initSnapshot.body;
      if (footer) footer.innerHTML = _mtp_initSnapshot.footer;
      if (title) title.textContent = _mtp_initSnapshot.title;

      // limpiar estados
      modal.removeAttribute("data-method");
      modal.removeAttribute("data-account-id");
      window.MT_PAYOUT_SUBMIT = null;

      var errEl = document.getElementById("mt-payout-error");
      if (errEl) {
        errEl.style.display = "none";
        errEl.textContent = "";
      }
      var amt = document.getElementById("mt-payout-amount");
      if (amt) {
        amt.value = "";
        amt.classList.remove("is-invalid");
      }

      var confirmBtn = document.getElementById("mt-payout-confirm");
      if (confirmBtn) {
        confirmBtn.classList.add("d-none");
        confirmBtn.disabled = false;
      }

      try {
        switchToStep(1);
      } catch (e) {}
    }

    // tomar snapshot cuando cargue
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", snapshotModal, {
        once: true,
      });
    } else {
      snapshotModal();
    }

    // al abrir: forzar Step 1 y refrescar cuentas
    document.addEventListener("show.bs.modal", function (ev) {
      if (ev.target && ev.target.id === "mt-request-payout-modal") {
        try {
          switchToStep(1);
        } catch (e) {}
        try {
          hydrateOnce(true);
        } catch (e) {}
      }
    });

    // al cerrar: restaurar HTML original (borra el Step 3)
    document.addEventListener("hidden.bs.modal", function (ev) {
      if (ev.target && ev.target.id === "mt-request-payout-modal") {
        restoreModal();
        try {
          MT_PAYOUT_CACHE = null;
        } catch (e) {} // opcional: fuerza cuentas frescas
      }
    });
  })();

  // ---------- Init ----------
  function init() {
    var modal = document.getElementById("mt-request-payout-modal");
    if (!modal) return;
    var backBtn = document.getElementById("mt-payout-back");
    if (backBtn)
      backBtn.addEventListener(
        "click",
        function () {
          switchToStep(1);
        },
        { passive: true }
      );
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else {
    init();
  }

  // Debug helpers
  window.MT_PAYOUT_DEBUG = Object.assign(window.MT_PAYOUT_DEBUG || {}, {
    forceRefresh: function () {
      MT_PAYOUT_CACHE = null;
      return prefetchPayoutData(true).then(console.log);
    },
    cache: function () {
      return MT_PAYOUT_CACHE;
    },
    congrats: renderCongratsStep,
  });
})();
