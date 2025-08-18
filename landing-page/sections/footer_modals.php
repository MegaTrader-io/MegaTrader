<div data-state="open"
     class="overlay-footer-top fixed inset-0 data-[state=open]:animate-overlayShow z-[60] bg-[#131210]/90"
     style="pointer-events: auto;display: none;" data-aria-hidden="true" aria-hidden="true">
</div>

<?php
foreach (mgt_footer_links() as $dialog):
    ob_start();
    require $dialog['template'];
    $template = ob_get_clean();
    echo mgt_render_dialog(modalId: $dialog['id'], title: $dialog['title'], template: $template);
endforeach;
?>

<span class="overlay-footer-bottom" data-radix-focus-guard="" tabindex="0"
      style="outline: none; opacity: 0; position: fixed; pointer-events: none;"
      data-aria-hidden="true" aria-hidden="true">
</span>
