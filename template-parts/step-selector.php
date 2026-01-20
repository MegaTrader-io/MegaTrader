<?php
$step = get_query_var('step');
?>

<style>
.step-selector {
  	width: 100%;
  	position: relative;
  	display: flex;
  	flex-direction: row;
  	align-items: center;
  	justify-content: space-between;
  	padding: 0px 0px 32px;
  	box-sizing: border-box;
  	gap: 8px;
  	max-width: 600px;
  	text-align: center;
  	font-size: 16px;
  	color: #131210;
  	font-family: Roboto;
}
.step-selector__step {
  	display: flex;
  	align-items: center;
  	gap: 8px;
}
.steps {
  	width: 32px;
  	position: relative;
  	border-radius: 64px;
  	background-color: #ffb34a;
  	border: 2px solid #ffb34a;
  	box-sizing: border-box;
  	height: 32px;
  	overflow: hidden;
  	flex-shrink: 0;
}
.step-selector__step__indicator {
  	position: absolute;
  	top: calc(50% - 12px);
  	left: calc(50% - 5px);
  	line-height: 24px;
}
.steppersteps-title {
  	position: relative;
  	line-height: 24px;
  	font-weight: 500;
  	color: #fff;
}
.step-selector__separator {
  	flex: 1;
  	position: relative;
  	background-color: #404040;
  	height: 2px;
}
.step-selector__step.step-future {
  	color: #a8a29e;
}


.steps2 {
  	width: 32px;
  	position: relative;
  	border-radius: 64px;
  	background-color: #131210;
  	border: 2px solid #a8a29e;
  	box-sizing: border-box;
  	height: 32px;
  	overflow: hidden;
  	flex-shrink: 0;
}
.step-selector__step step-future.step-selector__step__title {
  	position: relative;
  	line-height: 24px;
  	font-weight: 500;
}

</style>

<?php if ($step === 1): ?>

    <div class="step-selector">
        <div class="step-selector__step">
            <div class="steps">
                <b class="step-selector__step__indicator">1</b>
            </div>
            <div class="steppersteps-title d-none d-md-block">Select Plan</div>
        </div>
		<div class="step-selector__separator"></div>
        <div class="step-selector__step step-future">
            <div class="steps2">
                <b class="step-selector__step__indicator">2</b>
            </div>
            <div class="step-selector__step__title d-none d-md-block">Checkout</div>
        </div>
		<div class="step-selector__separator"></div>
        <div class="step-selector__step step-future">
            <div class="steps2">
                <b class="step-selector__step__indicator">3</b>
            </div>
            <div class="step-selector__step__title d-none d-md-block">Confirmation</div>
        </div>
    </div>



<?php elseif($step === 2): ?>

    <div class="w-100 d-flex justify-content-between align-items-center pb-32 m-auto w-max-600px gap-2">
        <div class="d-flex gap-2 align-items-center">
            <a href="<?php echo esc_url(home_url('/subscriptions')); ?>" class="actived">
                <svg width="32" height="32" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z"
                        fill="#FFB34A" />
                </svg>
            </a>
            <div class="text-base text-primary fw-medium d-none d-md-block">Set up</div>
        </div>
		<div class="bg-404040 flex-fill h-2px"></div>
        <div class="d-flex gap-2 align-items-center">
            <div class="bg-mgt-primary border-mgt-primary h-32px overflow-hidden rounded-64px w-32px position-relative">
                <div style="left: 11px;top: 4px;" class="fw-bold position-absolute text-131210 text-base text-center">2
                </div>
            </div>
            <div class="text-white text-base fw-medium d-none d-md-block">Review and Pay</div>
        </div>
		<div class="step-selector__separator"></div>
        <div class="step-selector__step step-future">
            <div class="steps2">
                <b class="step-selector__step__indicator">3</b>
            </div>
            <div class="step-selector__step__title d-none d-md-block">Finished</div>
        </div>
    </div>

<?php elseif($step === 3): ?>

    <div class="w-100 d-flex justify-content-between align-items-center pb-32 m-auto w-max-600px gap-2">
        <div class="d-flex gap-2 align-items-center">
			<svg width="32" height="32" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z"
					fill="#FFB34A" />
			</svg>
            <div class="text-base text-primary fw-medium d-none d-md-block">Set up</div>
        </div>
		<div class="bg-404040 flex-fill h-2px"></div>
        <div class="d-flex gap-2 align-items-center">
            <svg width="32" height="32" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z"
					fill="#FFB34A" />
			</svg>
			<div class="text-base text-primary fw-medium d-none d-md-block">Review and Pay</div>
        </div>
		<div class="bg-404040 flex-fill h-2px"></div>
		<div class="d-flex gap-2 align-items-center">
            <div class="bg-mgt-primary border-mgt-primary h-32px overflow-hidden rounded-64px w-32px position-relative">
                <div style="left: 11px;top: 4px;" class="fw-bold position-absolute text-131210 text-base text-center">3
                </div>
            </div>
            <div class="text-white text-base fw-medium d-none d-md-block">Finished</div>
        </div>
    </div>

<?php else: ?>
<!-- No Render for Step-Selector. Step = <?= $step ?> --->
<?php endif; ?>