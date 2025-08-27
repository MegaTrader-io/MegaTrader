<?php 
    $step1 = get_query_var('step');
?>

<?php if( !$step1): ?>

    <div class="w-100 d-flex justify-content-between align-items-center pb-32 m-auto w-max-320px">
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


   

      <div class="w-100 d-flex justify-content-between align-items-center pb-32 m-auto w-max-320px">
                            <div class="flex-grow-1 flex-shrink-1 d-flex gap-2 align-items-center">
                                <a href="<?php echo esc_url(home_url('/')); ?>" class="actived">
                                <svg width="32" height="32" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z"
                                        fill="#FFB34A" />
                                </svg>
                            </a>
                                <div class="text-base text-primary fw-medium">Set up</div>
                                <div class="bg-404040 flex-fill h-2px"></div>
                            </div>
                            <div class="flex-grow-1 flex-shrink-1 d-flex gap-2 align-items-center">
                                <div class="bg-404040 flex-fill h-2px"></div>
                                <div class="bg-mgt-primary border-mgt-primary h-32px overflow-hidden rounded-64px w-32px position-relative">
                                    <div style="left: 11px;top: 4px;" class="fw-bold position-absolute text-131210 text-base text-center">2</div>
                                </div>
                                <div class="text-white text-base fw-medium">Review and Pay</div>
                            </div>
                        </div>





<?php endif; ?>