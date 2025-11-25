function changeIdentityToVerified() {
    const elements = document.querySelectorAll('.mt-verified');
    const btnVerify = document.getElementById('get-verified-btn');

    if (elements.length > 0) {
        document.querySelectorAll('.mt-verified').forEach(element => element.dataset.verified = 1);
    }

    if (btnVerify) {
        btnVerify.disabled = true;
    }
}

function cancelVerification() {
    const btnVerify = document.getElementById('get-verified-btn');
    btnVerify.disabled = false;
    $.preloader.hide();
}

function showMessage(text, type = 'info') {
    const root = document.getElementById('veriff-container');
    if (!root) return;

    root.innerHTML = ''; // limpia el iframe si estaba visible

    const msg = document.createElement('div');
    msg.className = `alert alert-${type} mt-3`;
    msg.textContent = text;
    root.appendChild(msg);
}

// 🔹 Función que llama a tu endpoint mt_get_veriff_status
async function checkVerificationStatus() {
    try {
        // showMessage('Checking verification status...', 'info');
        $.preloader.show();

        const response = await fetch(MT_AP.ajaxUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({action: 'mt_get_veriff_status', security: MT_AP.nonce}),
        });

        const result = await response.json();
        if (!result.success) throw new Error('Backend error');

        const {verified, status} = result.data;

        $.preloader.hide();

        if (verified) {
            // showMessage('✅ Your identity has been verified successfully!', 'success');
            changeIdentityToVerified();
        } else {
            console.info(`Verification status: ${status}`);
            // showMessage(`Verification status: ${status}`, 'warning');
        }
    } catch (err) {
        console.error('checkVerificationStatus failed:', err);
        // showMessage('⚠️ Unable to confirm verification status.', 'danger');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('get-verified-btn');
    if (!btn || typeof Veriff === 'undefined') return;

    btn.addEventListener('click', async () => {
        btn.disabled = true;

        $.preloader.show();

        // Llamar tu endpoint AJAX que crea la sesión en Veriff
        const response = await fetch(MT_AP.ajaxUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'mt_start_veriff_verification',
                security: MT_AP.nonce,
            }),
        });

        const result = await response.json();
        if (!result.success) {
            console.info(result.data?.message || 'Error starting verification.');
            $.preloader.hide();
            btn.disabled = false;
            return;
        }

        const {createVeriffFrame, MESSAGES} = window.veriffSDK;
        const {token: sessionToken, url: VERIFF_SESSION_URL} = result.data;

        // 🔹 Iniciar Veriff embebido
        const veriff = Veriff({
            host: 'https://stationapi.veriff.com',
            apiKey: MT_AP.veriffKey,
            parentId: 'veriff-container',
            onSession: function (err, response) {
                if (err) {
                    console.error(err);
                    return;
                }

                veriff.mount({
                    sessionToken,
                    onFinish: (res) => console.log('Verification finished:', res),
                });
            },
        });

        createVeriffFrame({
            url: VERIFF_SESSION_URL,
            onEvent: async function (msg) {
                console.log('🔹 Veriff Event:', msg);

                switch (msg) {
                    case MESSAGES.STARTED:
                        $.preloader.hide();
                        console.info('Verification started');
                        break;

                    case MESSAGES.SUBMITTED:
                        console.info('Verification submitted');
                        // Mostrar mensaje de progreso
                        // showMessage('Verification submitted, waiting for confirmation...', 'info');
                        console.info('Verification submitted, waiting for confirmation...');
                        break;

                    case MESSAGES.FINISHED:
                        document.getElementById('veriff-container').innerHTML = '';
                        console.info('Verification finished');
                        await checkVerificationStatus();
                        break;

                    case MESSAGES.CANCELED:
                        console.info('Verification closed by user');
                        console.info('Verification was canceled.');

                        cancelVerification();
                        break;

                    case MESSAGES.RELOAD_REQUEST:
                        console.info('Verification reload requested');
                        // Si quieres recrear el frame:
                        // createVeriffFrame({ url: VERIFF_SESSION_URL, onEvent });
                        break;
                }
            }
        });

        $.preloader.hide();
    });

});