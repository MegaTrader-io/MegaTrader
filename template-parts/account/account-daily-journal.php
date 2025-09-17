<?php
/** Account Daily Journal (GRID, sin scripts, sin encabezado externo) */
if (!defined('ABSPATH')) exit;

$root_id  = 'mt-daily-journal';
$pager_id = 'mt-daily-journal-pager';

function _money_fmt($n){ $sign=$n<0?'-':''; return $sign.'$'.number_format(abs($n),2); }
?>
<div id="<?php echo esc_attr($root_id); ?>" class="mt-card" data-per-page="7" data-total-pages="2">
  <style>
    /* ===== Scope del componente ===== */
    #<?php echo $root_id; ?>{
      --dj-bg: var(--Surface-Page, #1E1E1E);
      --cols: 13;        /* total columnas */
      --col-w: 90px;    /* ancho fijo por columna (ajústalo si quieres) */
      min-width: 0;      /* clave si el padre es flex */
      overflow: hidden;  /* CLIP: evita que el contenido se salga del card */
      box-sizing: border-box;
      max-width: 784px;
    }

    /* Clip interno + scroll-x controlado */
    #<?php echo $root_id; ?> .dj-clip{
      position: relative;
      overflow: hidden;     /* clip vertical y lateral al borde del card */
      border-radius: inherit;
      background: var(--dj-bg);
      isolation: isolate;   /* aísla pintura para evitar “bleed” */
    }
    #<?php echo $root_id; ?> .dj-scroll{
      display: block;
      width: 100%;
      max-width: 100%;
      overflow-x: auto;      /* scroll horizontal aquí */
      overflow-y: hidden;
      background: var(--dj-bg);
      border-radius: inherit;
      will-change: scroll-position;
    }

    /* Grid: no se encoge; ocupa la suma de columnas → obliga scroll-x */
    #<?php echo $root_id; ?> .dj-grid{
      display: grid;
      grid-template-columns: repeat(var(--cols), var(--col-w));
      inline-size: max-content;    /* width:max-content robusto */
      min-inline-size: max-content;
      background: var(--dj-bg);
    }

    /* Celdas / header */
    #<?php echo $root_id; ?> .dj-head,
    #<?php echo $root_id; ?> .dj-cell{
      background: var(--dj-bg);
      color: #fff;
      padding: .75rem .9rem;
      min-width: 0;
      word-break: break-word;
      white-space: normal;
      line-height: 1.25;
      font-size: .875rem;
    }

    /* Bordes solo por fila */
    #<?php echo $root_id; ?> .dj-headrow{ border-bottom: 1px solid rgba(255,255,255,.12); }
    #<?php echo $root_id; ?> .dj-row{     border-bottom: 1px solid rgba(255,255,255,.08); }

    /* Header visual */
    #<?php echo $root_id; ?> .dj-head{
      color: #a1a1aa;
      text-transform: uppercase;
      font-weight: 700;
    }

    /* Alineaciones */
    #<?php echo $root_id; ?> .is-left{ text-align: left; }
    #<?php echo $root_id; ?> .is-right{ text-align: right; }

    /* Icono de visibilidad */
    #<?php echo $root_id; ?> .mt-dj-visibility{
      width: 28px; height: 28px; border-radius: 6px;
      display: inline-flex; align-items: center; justify-content: center;
      cursor: pointer;
    }
    #<?php echo $root_id; ?> .mt-dj-visibility:hover{ outline: 1px solid rgba(255,255,255,.15); }

    /* Pos/Neg */
    #<?php echo $root_id; ?> .text-success{ color: #45d19f !important; }
    #<?php echo $root_id; ?> .text-danger{  color: #ff6b6b !important; }

    /* Paginado */
    #<?php echo $root_id; ?> .dj-pager{
      display: flex; gap: .5rem; justify-content: flex-end; padding: .5rem 0 0;
    }
    #<?php echo $root_id; ?> .dj-btn{
      background: #0f0f10; color: #fff;
      border: 1px solid rgba(255,255,255,.15);
      border-radius: .5rem; padding: .25rem .5rem; font-size: .8rem;
    }
    #<?php echo $root_id; ?> .dj-btn:disabled{ opacity: .4; cursor: not-allowed; }
    #<?php echo $root_id; ?> .dj-btn.active{ background: #1f1f20; }
  </style>

  <div class="dj-clip">
    <div class="dj-scroll">
      <!-- Header (13 columnas; el texto puede wrappear con <br>) -->
      <div class="dj-grid dj-headrow">
        <div class="dj-head is-left">Daily<br>Journal</div>
        <div class="dj-head is-right">Date</div>
        <div class="dj-head is-right">Net P&L</div>
        <div class="dj-head is-right">P&L High</div>
        <div class="dj-head is-right">P&L Low</div>
        <div class="dj-head is-right">Total<br>Contracts</div>
        <div class="dj-head is-right">Total Fees<br>+Comm</div>
        <div class="dj-head is-right">Total Trades</div>
        <div class="dj-head is-right">Avg. Winning<br>Trades</div>
        <div class="dj-head is-right">Avg. Losing<br>Trades</div>
        <div class="dj-head is-right">Winning<br>Trade %</div>
        <div class="dj-head is-right">Max. Consec.<br>W/L Trades</div>
        <div class="dj-head is-right">Avg. W/L<br>Duration</div>
      </div>

      <?php
        // Mock: 10 filas (7 visibles pág.1, 3 ocultas pág.2)
        $rows = [
          ['date'=>'Sep 30','net'=> -73.40,'hi'=> -1.00,'lo'=> -73.40,'ct'=>70,'fees'=> 25.90,'trades'=>4,'awin'=>  2.60,'aloss'=>39.90,'win'=> 50.00,'max'=>'2/1','dur'=>'00:05:05 00:01:17'],
          ['date'=>'Sep 29','net'=>  45.10,'hi'=> 88.20,'lo'=> -5.25 ,'ct'=>22,'fees'=> 12.20,'trades'=>9,'awin'=> 18.00,'aloss'=> 9.50,'win'=> 62.50,'max'=>'3/1','dur'=>'00:08:10 00:03:25'],
          ['date'=>'Sep 28','net'=>-234.28,'hi'=>  6.90,'lo'=>-240.78,'ct'=>158,'fees'=>139.28,'trades'=>22,'awin'=> 28.07,'aloss'=>22.04,'win'=> 22.73,'max'=>'2/12','dur'=>'00:06:09 00:04:42'],
          ['date'=>'Sep 27','net'=> 120.50,'hi'=>160.10,'lo'=> -8.40,'ct'=>11,'fees'=>  6.80,'trades'=>5,'awin'=> 42.30,'aloss'=>15.60,'win'=> 66.67,'max'=>'4/1','dur'=>'00:04:20 00:02:10'],
          ['date'=>'Sep 26','net'=> -15.75,'hi'=> 20.00,'lo'=>-30.40,'ct'=>33,'fees'=>  9.99,'trades'=>7,'awin'=> 10.15,'aloss'=>11.30,'win'=> 42.86,'max'=>'2/2','dur'=>'00:03:50 00:02:40'],
          ['date'=>'Sep 25','net'=>  90.00,'hi'=>120.00,'lo'=> -2.10,'ct'=>40,'fees'=> 19.10,'trades'=>8,'awin'=> 25.50,'aloss'=>10.40,'win'=> 62.50,'max'=>'3/1','dur'=>'00:07:33 00:03:01'],
          ['date'=>'Sep 24','net'=> -12.30,'hi'=>  1.50,'lo'=>-25.00,'ct'=>18,'fees'=>  8.70,'trades'=>6,'awin'=>  6.10,'aloss'=> 9.80,'win'=> 33.33,'max'=>'1/3','dur'=>'00:02:15 00:01:45'],
          ['date'=>'Sep 23','net'=> 300.00,'hi'=>350.00,'lo'=> 10.00,'ct'=>45,'fees'=> 29.00,'trades'=>10,'awin'=> 40.00,'aloss'=>14.00,'win'=> 70.00,'max'=>'5/1','dur'=>'00:09:40 00:03:30'],
          ['date'=>'Sep 22','net'=> -88.88,'hi'=>  0.00,'lo'=>-120.0,'ct'=>27,'fees'=> 16.75,'trades'=>9,'awin'=> 12.00,'aloss'=>21.00,'win'=> 22.22,'max'=>'1/4','dur'=>'00:04:55 00:03:15'],
          ['date'=>'Sep 21','net'=>  15.00,'hi'=> 30.00,'lo'=> -5.00,'ct'=>12,'fees'=>  6.40,'trades'=>3,'awin'=>  9.00,'aloss'=> 6.50,'win'=> 66.67,'max'=>'2/0','dur'=>'00:01:40 00:01:10'],
        ];
        foreach ($rows as $i=>$r):
          $page = (int)floor($i/7)+1;
          $hidden = $page===1 ? '' : 'style="display:none"';
          $net_class = $r['net']>0?'text-success':($r['net']<0?'text-danger':'');
      ?>
      <div class="dj-grid dj-row" data-page="<?php echo esc_attr($page); ?>" <?php echo $hidden; ?>>
        <div class="dj-cell is-left">
          <span class="mt-dj-visibility" role="button" tabindex="0">
            <i class="mt-icon mt-icon-white mt-icon_visibility" aria-hidden="true"></i>
          </span>
        </div>
        <div class="dj-cell is-right"><?php echo esc_html($r['date']); ?></div>
        <div class="dj-cell is-right <?php echo esc_attr($net_class); ?>"><?php echo esc_html(_money_fmt($r['net'])); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html(_money_fmt($r['hi'])); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html(_money_fmt($r['lo'])); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html(number_format($r['ct'])); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html(_money_fmt($r['fees'])); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html(number_format($r['trades'])); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html(_money_fmt($r['awin'])); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html(_money_fmt($r['aloss'])); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html(number_format($r['win'],2)); ?>%</div>
        <div class="dj-cell is-right"><?php echo esc_html($r['max']); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($r['dur']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Paginado -->
  <nav id="<?php echo esc_attr($pager_id); ?>" class="dj-pager" aria-label="Daily Journal pagination">
    <button class="dj-btn mt-dj-prev" type="button" disabled>&laquo;</button>
    <button class="dj-btn active" type="button">1</button>
    <button class="dj-btn" type="button">2</button>
    <button class="dj-btn mt-dj-next" type="button">&raquo;</button>
  </nav>
</div>
