// mt-tooltips.js
(function () {
  var OPEN = null; // {wrap, trigger, panel, bubble, hideTimer}
  var PORTAL = null;
  var MARGIN = 8; // separación trigger/burbuja
  var HIDE_DELAY = 120; // ms para evitar parpadeo al salir/entrar

  function onReady(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn, { once: true });
  }

  function ensurePortal() {
    if (!PORTAL) {
      PORTAL = document.createElement("div");
      PORTAL.id = "mt-tooltips-portal";
      document.body.appendChild(PORTAL);
      document.documentElement.classList.add("has-portal-tooltips");
    }
    return PORTAL;
  }

  function getTriggerAndPanel(wrap) {
    if (!wrap) return null;
    var panel = wrap.querySelector(".mt-tooltip__panel");
    var trigger =
      wrap.querySelector("[aria-describedby]") || wrap.firstElementChild;
    if (!panel && trigger) {
      var id = trigger.getAttribute("aria-describedby");
      if (id) panel = document.getElementById(id);
    }
    if (!panel) panel = wrap.querySelector(".mt-tooltip__panel"); // fallback
    return panel && trigger ? { trigger: trigger, panel: panel } : null;
  }

  function getForcedPlacement(wrap, panel) {
    var p1 = panel && panel.dataset ? panel.dataset.placement : "";
    var p2 = wrap && wrap.dataset ? wrap.dataset.placement : "";
    var forced = (p1 || p2 || "").toLowerCase().trim();
    if (
      forced === "top" ||
      forced === "bottom" ||
      forced === "left" ||
      forced === "right"
    ) {
      return forced;
    }
    return null; // auto
  }

  function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
  }

  function measureBubble(tmp) {
    tmp.style.visibility = "hidden";
    tmp.style.display = "block";
    tmp.style.left = "0px";
    tmp.style.top = "0px";
    tmp.style.position = "fixed";
    document.body.appendChild(tmp);
    var w = tmp.offsetWidth;
    var h = tmp.offsetHeight;
    return { w: w, h: h };
  }

  function pickPlacement(triggerRect, bubbleSize, forced) {
    var vw = window.innerWidth;
    var vh = window.innerHeight;

    // si está forzado, respétalo
    if (forced) return forced;

    var fits = {
      top: triggerRect.top >= bubbleSize.h + MARGIN,
      bottom: vh - triggerRect.bottom >= bubbleSize.h + MARGIN,
      right: vw - triggerRect.right >= bubbleSize.w + MARGIN,
      left: triggerRect.left >= bubbleSize.w + MARGIN,
    };

    // preferencia: top, bottom, right, left
    if (fits.top) return "top";
    if (fits.bottom) return "bottom";
    if (fits.right) return "right";
    if (fits.left) return "left";

    // si nada "cabe", elige donde haya más espacio
    var space = {
      top: triggerRect.top,
      bottom: vh - triggerRect.bottom,
      right: vw - triggerRect.right,
      left: triggerRect.left,
    };
    var best = "bottom",
      maxVal = space.bottom;
    ["top", "right", "left"].forEach(function (k) {
      if (space[k] > maxVal) {
        maxVal = space[k];
        best = k;
      }
    });
    return best;
  }

  function positionBubble(bubble, triggerRect, bubbleSize, placement) {
    var vw = window.innerWidth;
    var vh = window.innerHeight;

    var left = 0,
      top = 0;
    bubble.setAttribute("data-placement", placement);

    if (placement === "top" || placement === "bottom") {
      var idealLeft =
        triggerRect.left + triggerRect.width / 2 - bubbleSize.w / 2;
      left = clamp(idealLeft, MARGIN, vw - bubbleSize.w - MARGIN);
      top =
        placement === "top"
          ? triggerRect.top - bubbleSize.h - MARGIN
          : triggerRect.bottom + MARGIN;

      var arrowPx = triggerRect.left + triggerRect.width / 2 - left;
      var arrowPct = clamp((arrowPx / bubbleSize.w) * 100, 8, 92);
      bubble.style.setProperty("--arrow-left", arrowPct + "%");
    } else {
      // left / right
      var idealTop =
        triggerRect.top + triggerRect.height / 2 - bubbleSize.h / 2;
      top = clamp(idealTop, MARGIN, vh - bubbleSize.h - MARGIN);
      left =
        placement === "right"
          ? triggerRect.right + MARGIN
          : triggerRect.left - bubbleSize.w - MARGIN;

      var arrowPy = triggerRect.top + triggerRect.height / 2 - top;
      var arrowPctY = clamp((arrowPy / bubbleSize.h) * 100, 8, 92);
      bubble.style.setProperty("--arrow-top", arrowPctY + "%");
    }

    bubble.style.left = Math.round(left) + "px";
    bubble.style.top = Math.round(top) + "px";
  }

  function createBubble(panel) {
    var bubble = panel.cloneNode(true);
    bubble.classList.add("is-portal");
    bubble.removeAttribute("id");
    bubble.style.display = "block";
    // limpia atributos ARIA que puedan duplicar IDs
    bubble.removeAttribute("aria-describedby");
    bubble.removeAttribute("aria-labelledby");
    return bubble;
  }

  function showTooltip(wrap) {
    var pair = getTriggerAndPanel(wrap);
    if (!pair) return;
    var trigger = pair.trigger;
    var panel = pair.panel;

    hideTooltip(true);

    ensurePortal();
    var bubble = createBubble(panel);
    PORTAL.appendChild(bubble);

    // medir (usar un clon visible pero oculto)
    var ghost = bubble.cloneNode(true);
    var size = measureBubble(ghost);
    ghost.remove();

    var rect = trigger.getBoundingClientRect();
    var forced = getForcedPlacement(wrap, panel);
    var placement = pickPlacement(rect, size, forced);
    positionBubble(bubble, rect, size, placement);

    OPEN = {
      wrap: wrap,
      trigger: trigger,
      panel: panel,
      bubble: bubble,
      hideTimer: null,
    };

    // mantener abierto si el puntero entra al bubble
    bubble.addEventListener("mouseenter", cancelHide);
    bubble.addEventListener("mouseleave", scheduleHide);
  }

  function cancelHide() {
    if (OPEN && OPEN.hideTimer) {
      clearTimeout(OPEN.hideTimer);
      OPEN.hideTimer = null;
    }
  }

  function scheduleHide() {
    if (!OPEN) return;
    cancelHide();
    OPEN.hideTimer = setTimeout(hideTooltip, HIDE_DELAY);
  }

  function hideTooltip(skipTimer) {
    if (!OPEN) return;
    if (!skipTimer && OPEN.hideTimer) {
      clearTimeout(OPEN.hideTimer);
    }
    if (OPEN.bubble && OPEN.bubble.parentNode)
      OPEN.bubble.parentNode.removeChild(OPEN.bubble);
    OPEN = null;
  }

  function reposition() {
    if (!OPEN || !OPEN.bubble) return;
    var bubble = OPEN.bubble;
    var trigger = OPEN.trigger;

    // medir de nuevo (por si cambia tamaño por wrap)
    var ghost = bubble.cloneNode(true);
    var size = measureBubble(ghost);
    ghost.remove();

    var rect = trigger.getBoundingClientRect();
    var forced = getForcedPlacement(OPEN.wrap, OPEN.panel);
    var placement = pickPlacement(rect, size, forced);
    positionBubble(bubble, rect, size, placement);
  }

  function addHandlers(wrap) {
    // abrir al hover/focus
    wrap.addEventListener("mouseenter", function () {
      cancelHide();
      showTooltip(wrap);
    });
    wrap.addEventListener("focusin", function () {
      cancelHide();
      showTooltip(wrap);
    });

    // cerrar al salir/blur con leve delay
    wrap.addEventListener("mouseleave", scheduleHide);
    wrap.addEventListener("focusout", scheduleHide);

    // para touch/click: toggle rápido
    wrap.addEventListener("click", function (e) {
      // evita que click en el trigger cierre inmediatamente por blur
      e.stopPropagation();
      if (OPEN && OPEN.wrap === wrap) hideTooltip();
      else showTooltip(wrap);
    });
  }
  function addHandlers(wrap) {
    if (wrap.__mtTipBound) return;
    wrap.__mtTipBound = true;

    wrap.addEventListener("mouseenter", function () {
      cancelHide();
      showTooltip(wrap);
    });
    wrap.addEventListener("focusin", function () {
      cancelHide();
      showTooltip(wrap);
    });
    wrap.addEventListener("mouseleave", scheduleHide);
    wrap.addEventListener("focusout", scheduleHide);
    wrap.addEventListener("click", function (e) {
      e.stopPropagation();
      if (OPEN && OPEN.wrap === wrap) hideTooltip();
      else showTooltip(wrap);
    });
  }

  function globalHandlers() {
    // cerrar al hacer click fuera
    document.addEventListener(
      "click",
      function (e) {
        if (!OPEN) return;
        var insideWrap = OPEN.wrap.contains(e.target);
        var insideBubble = OPEN.bubble && OPEN.bubble.contains(e.target);
        if (!insideWrap && !insideBubble) hideTooltip();
      },
      true
    );

    // reposicionar al hacer scroll/resize
    window.addEventListener("scroll", reposition, true); // captura (incluye scroll de contenedores)
    window.addEventListener("resize", reposition);
  }

  onReady(function () {
    ensurePortal();
    globalHandlers();

    // inicializa todos los tooltips presentes
    document.querySelectorAll(".mt-tooltip").forEach(addHandlers);

    // si luego inyectas HTML con tooltips, puedes llamar:
    // window.mtTooltips && window.mtTooltips.refresh();
  });

  // API pública mínima
  window.mtTooltips = {
    refresh: function (root) {
      var scope = root || document;
      scope.querySelectorAll(".mt-tooltip").forEach(addHandlers);
    },
    closeAll: function () {
      hideTooltip(true);
    },
  };
})();
