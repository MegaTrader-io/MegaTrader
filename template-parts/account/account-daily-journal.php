<?php
/**
 * Template Part: Account Daily Journal (AJAX-only, sin scripts inline)
 * Ruta: template-parts/account/account-daily-journal.php
 *
 * Notas:
 * - No usa $_GET. Todo se llena vía AJAX desde mt-account-overview.js.
 * - Paginación client-side (7 por página) controlada por JS.
 * - El <tbody> se inyecta por AJAX (rowsHtml) desde el endpoint mt_account_daily_journal.
 */

if (!defined('ABSPATH'))
    exit;

$root_id = 'mt-daily-journal';
$table_id = 'mt-daily-journal-table';
$pager_id = 'mt-daily-journal-pager';
?>
<div id="<?php echo esc_attr($root_id); ?>" class="mt-daily-journal my-4" data-per-page="7" data-total-pages="1">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <h3 class="h5 m-0">Daily Journal</h3>
            <span class="badge bg-secondary">
                Acct: <strong class="ms-1" id="mt-daily-journal-account">—</strong>
            </span>
        </div>
        <div class="text-muted small">
            <span id="mt-dj-count">Showing <strong>0–0</strong> of <strong>0</strong></span>
        </div>
    </div>

    <div class="table-responsive">
        <table id="<?php echo esc_attr($table_id); ?>" class="table table-sm align-middle mb-2">
            <thead class="table-light">
                <tr>
                    <th style="min-width:120px">Daily Journal</th>
                    <th style="min-width:120px">Date</th>
                    <th style="min-width:120px">Net P&amp;L</th>
                    <th style="min-width:120px">P&amp;L High</th>
                    <th style="min-width:120px">P&amp;L Low</th>
                    <th style="min-width:140px">Total Contracts</th>
                    <th style="min-width:160px">Total Fees + Comm</th>
                    <th style="min-width:120px">Total Trades</th>
                    <th style="min-width:170px">Avg. Winning Trades</th>
                    <th style="min-width:170px">Avg. Losing Trades</th>
                    <th style="min-width:150px">Winning Trade %</th>
                    <th style="min-width:190px">Max. Consec. W/L Trades</th>
                    <th style="min-width:160px">Avg. W/L Duration</th>
                </tr>
            </thead>
            <tbody><!-- AJAX inyecta filas aquí (rowsHtml) --></tbody>
        </table>
    </div>

    <nav id="<?php echo esc_attr($pager_id); ?>" aria-label="Daily Journal pagination"
        class="d-flex justify-content-between align-items-center mt-2">
        <button class="btn btn-sm btn-outline-secondary mt-dj-prev" type="button" disabled>Previous</button>
        <div class="small">
            Page <span class="mt-dj-current">1</span> / <span class="mt-dj-total">1</span>
        </div>
        <button class="btn btn-sm btn-outline-secondary mt-dj-next" type="button" disabled>Next</button>
    </nav>
</div>