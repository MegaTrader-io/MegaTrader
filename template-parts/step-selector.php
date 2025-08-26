<?php 
    $step1 = get_query_var('step');
?>

<?php if( !$step1): ?>

    <div style="width: 100%; height: 100%; padding-bottom: 32px; justify-content: space-between; align-items: center; display: inline-flex">
        <div data-showlefttrack="false" data-showrighttrack="true" data-status="active" style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
            <div style="width: 32px; height: 32px; position: relative; background: var(--Primary-400, #FFB34A); overflow: hidden; border-radius: 64px; outline: 2px var(--Surface-Primary, #FFB34A) solid; outline-offset: -2px">
                <div style="left: 11px; top: 4px; position: absolute; text-align: center; color: var(--Surface-Body, #131210); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">1</div>
            </div>
            <div style="text-align: center; color: var(--Text-Headings, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Set up</div>
            <div style="flex: 1 1 0; height: 2px; background: var(--Colors-Gray-700, #404040)"></div>
        </div>
        <div data-showlefttrack="true" data-showrighttrack="false" data-status="default" style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
            <div style="flex: 1 1 0; height: 2px; background: var(--Colors-Gray-700, #404040)"></div>
            <div style="width: 32px; height: 32px; position: relative; background: var(--Surface-Body, #131210); overflow: hidden; border-radius: 64px; outline: 2px var(--Text-Body, #A8A29E) solid; outline-offset: -2px">
                <div style="left: 11px; top: 4px; position: absolute; text-align: center; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">2</div>
            </div>
            <div style="text-align: center; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Review and Pay</div>
        </div>
    </div>

<?php else: ?>

    <div style="width: 100%; height: 100%; justify-content: space-between; align-items: center; display: inline-flex">
        <div data-showlefttrack="false" data-showrighttrack="true" data-status="completed" style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
            <div style="width: 32px; height: 32px; position: relative; background: var(--Surface-Body, #131210); overflow: hidden; border-radius: 64px; outline: 4px var(--Surface-Primary, #FFB34A) solid; outline-offset: -4px">
                <div style="width: 32px; height: 32px; left: 0px; top: 0px; position: absolute">
                    <div style="width: 32px; height: 32px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                    <div style="width: 26.67px; height: 26.67px; left: 2.67px; top: 2.67px; position: absolute; background: var(--Surface-Primary, #FFB34A)"></div>
                </div>
            </div>
            <div style="text-align: center; color: var(--Text-Action-Primary-Text, #FFB34A); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Set up</div>
            <div style="flex: 1 1 0; height: 2px; background: var(--Colors-Gray-700, #404040)"></div>
        </div>
        <div data-showlefttrack="true" data-showrighttrack="false" data-status="active" style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
            <div style="flex: 1 1 0; height: 2px; background: var(--Colors-Gray-700, #404040)"></div>
            <div style="width: 32px; height: 32px; position: relative; background: var(--Primary-400, #FFB34A); overflow: hidden; border-radius: 64px; outline: 2px var(--Surface-Primary, #FFB34A) solid; outline-offset: -2px">
                <div style="left: 11px; top: 4px; position: absolute; text-align: center; color: var(--Surface-Body, #131210); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">2</div>
            </div>
            <div style="text-align: center; color: var(--Text-Headings, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Review and Pay</div>
        </div>
    </div>

<?php endif; ?>