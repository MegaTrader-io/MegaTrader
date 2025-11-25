document.addEventListener('DOMContentLoaded', function () {
    const url = new URL(window.location.href);

    if (url.searchParams.has("logged_out")) {
        // Aquí ya WooCommerce debió agregar el notice desde PHP,
        // pero si quieres forzar un mensaje en el front:
        // alert("You have successfully logged out");

        // Eliminar el parámetro de la URL sin recargar la página
        url.searchParams.delete("logged_out");

        history.pushState(null, "", "/auth/login/");
        window.onpopstate = () => {
            history.pushState(null, "", "/auth/login/");
        };

        // window.history.replaceState({}, document.title, url.toString());
    }

    async function handlerRestCookieInvalidNonce(form) {
        if ($.refreshNonce) {
            MG_GLOBAL.nonce = await $.refreshNonce();
            console.info('Nonce refreshed, retrying submit...');
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'woocommerce-notices-wrapper';
        const msg = document.createElement('div');
        msg.className = 'woocommerce-error';
        msg.setAttribute('role', 'alert');
        msg.setAttribute('tabindex', '-1');
        msg.textContent = 'Security check failed. Please refresh the page and try again.';
        wrapper.appendChild(msg);
        form.parentNode.insertBefore(wrapper, form);
    }

    // Initialize Slider
    function initializeSwiper() {
        (new Swiper('.swiper', {
            direction: 'horizontal',
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
            },
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            scrollbar: {
                el: '.swiper-scrollbar',
            },
        }))
    }

    initializeSwiper();

    const wrapper = document.querySelector('.woocommerce-notices-wrapper');
    if (wrapper && wrapper.innerHTML.trim() === '') {
        wrapper.remove();
    }

    const passwordWrapper = document.querySelectorAll('.password-wrapper');

    passwordWrapper.forEach(wrapper => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.classList.add('password-wrapper__btn_eye');
        wrapper.appendChild(btn);

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const inputPassword = e.currentTarget.parentElement.querySelector('input');
            if (!inputPassword) {
                return;
            }

            inputPassword.type = inputPassword.type.toLowerCase() === 'password' ? 'text' : 'password';
        })
    });

    const emailInput = document.querySelector('[name=username]') || document.querySelector('[name=user_login]') || document.querySelector('[name=email]');
    if (emailInput) {
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function removeEmailError(input) {
            const errorUsername = input.parentElement.querySelector('[id=error-username]');
            input.classList.remove('auth-form--error-message');

            if (errorUsername) {
                errorUsername.remove();
            }
        }

        emailInput.addEventListener('blur', function (ev) {
            const errorUsername = emailInput.parentElement.querySelector('[id=error-username]');
            const value = ev.currentTarget.value.trim();
            if (!value) {
                ev.currentTarget.value = '';
                removeEmailError(emailInput);
                return;
            }

            const isValidEmail = validateEmail(value);

            if (isValidEmail) {
                removeEmailError(emailInput);
                return;
            }

            if (errorUsername) {
                return;
            }

            emailInput.classList.add('auth-form--error-message');
            const errorMessage = document.createElement('span');

            errorMessage.id = 'error-username';
            errorMessage.classList.add('auth-form__error_message');
            errorMessage.innerHTML = 'Enter a valid email address.';

            emailInput.parentElement.appendChild(errorMessage);
        });
    }

    const formLogin = document.getElementById('form-login');

    let probar = null;
    formLogin?.addEventListener('submit', async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const form = e.currentTarget;
        const usernameEl = form.querySelector('#username');
        const passwordEl = form.querySelector('#password');
        const rememberEl = form.querySelector('#rememberme');
        const submitBtn = form.querySelector('button[type="submit"]');
        const inputs = form.querySelectorAll('input, button, select, textarea');

        // ✅ detectar redirect_to en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const redirectTo = urlParams.get('redirect_to') || '';

        // ✅ limpiar mensajes previos
        form.querySelectorAll('.auth-form__error_message').forEach(el => el.remove());
        usernameEl.classList.remove('auth-form--error-message');
        passwordEl.classList.remove('auth-form--error-message');

        // ✅ eliminar avisos globales previos
        const globalNotice = document.querySelector('.woocommerce-notices-wrapper');
        if (globalNotice) globalNotice.remove();

        // ✅ bloquear inputs y submit
        inputs.forEach(el => el.readOnly = true);
        submitBtn.classList.add('btn--loading');

        try {
            $.preloader.show();
            const response = await fetch(MG_GLOBAL.loginAjaxApi, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-WP-Nonce': MG_GLOBAL.nonce,
                },
                body: new URLSearchParams({
                    username: usernameEl.value.trim(),
                    password: passwordEl.value,
                    rememberme: rememberEl.checked ? '1' : '',
                    redirect_to: redirectTo, // <--- enviado al backend
                }),
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect || '/';
                return;
            }

            // --- handle field errors dynamically ---
            const createOrUpdateError = (inputEl, id, message) => {
                inputEl.classList.add('auth-form--error-message');
                let span = form.querySelector(`#${id}`);
                if (!span) {
                    span = document.createElement('span');
                    span.id = id;
                    span.className = 'auth-form__error_message';
                    inputEl.parentNode.insertBefore(span, inputEl.nextSibling);
                }
                span.textContent = message;
            };

            let firstErrorField = null;

            if (data.errors) {
                if (data.errors.username) {
                    createOrUpdateError(usernameEl, 'error-username', data.errors.username);
                    firstErrorField = usernameEl;
                }
                if (data.errors.password) {
                    createOrUpdateError(passwordEl, 'error-password', data.errors.password);
                    if (!firstErrorField) firstErrorField = passwordEl;
                }

                // --- show general errors like WooCommerce ---
                if (data.errors.general) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'woocommerce-notices-wrapper';
                    const msg = document.createElement('div');
                    msg.className = 'woocommerce-message';
                    msg.setAttribute('role', 'alert');
                    msg.setAttribute('tabindex', '-1');
                    msg.textContent = data.errors.general;
                    wrapper.appendChild(msg);
                    form.parentNode.insertBefore(wrapper, form);
                }

                // ✅ focus on first error field
                if (firstErrorField) {
                    firstErrorField.focus();
                    firstErrorField.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            }

            if (data.code && data.code === 'rest_cookie_invalid_nonce') {
                void handlerRestCookieInvalidNonce(form);
            }

            // ✅ desbloquear inputs y botón
            $.preloader.hide();
            inputs.forEach(el => el.readOnly = false);
            submitBtn.classList.remove('btn--loading');
        } catch (err) {
            console.error('Login request failed:', err);
            const wrapper = document.createElement('div');
            wrapper.className = 'woocommerce-notices-wrapper';
            const msg = document.createElement('div');
            msg.className = 'woocommerce-message';
            msg.textContent = 'An unexpected error occurred. Please try again later.';
            wrapper.appendChild(msg);
            form.parentNode.insertBefore(wrapper, form);

            // ✅ desbloquear inputs y botón
            $.preloader.hide();
            inputs.forEach(el => el.readOnly = false);
            submitBtn.classList.remove('btn--loading');
        }
    });

    const usernameEl = formLogin?.querySelector('#username');
    if (usernameEl) {
        setTimeout(() => {
            usernameEl.focus();
        }, 0);
    }

    const registerForm = document.getElementById('register-form');

    registerForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const form = e.currentTarget;
        const inputs = form.querySelectorAll('input, select, button');
        const submitBtn = form.querySelector('button[type="submit"]');

        // limpiar errores previos
        form.querySelectorAll('.auth-form__error_message').forEach(el => el.remove());
        form.querySelectorAll('.auth-form--error-message').forEach(el => el.classList.remove('auth-form--error-message'));

        // ✅ eliminar avisos globales previos
        const globalNotice = document.querySelector('.woocommerce-notices-wrapper');
        if (globalNotice) globalNotice.remove();

        const formData = new FormData(form);
        const payload = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            payload.append(key, value.trim?.() ?? value);
        }

        inputs.forEach(el => el.readOnly = true);
        submitBtn.classList.add('btn--loading');
        $.preloader.show();

        try {
            const response = await fetch(MG_GLOBAL.registerAjaxApi, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-WP-Nonce': MG_GLOBAL.nonce,
                },
                body: payload,
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect || '/my-account/overview/';
                return;
            }

            const createOrUpdateError = (inputEl, id, message) => {
                inputEl.classList.add('auth-form--error-message');
                let span = form.querySelector(`#${id}`);
                if (!span) {
                    span = document.createElement('span');
                    span.id = id;
                    span.className = 'auth-form__error_message';
                    if (id === 'error-privacy_policy') {
                        inputEl.parentNode.insertAdjacentElement('beforeend', span)
                    } else {
                        inputEl.parentNode.insertBefore(span, inputEl.nextSibling);
                    }
                }
                span.textContent = message;
            };

            let firstErrorField = null;

            if (data.errors) {
                Object.entries(data.errors).forEach(([field, message]) => {
                    const inputEl = form.querySelector(`[name="${field}"]`);
                    if (inputEl) {
                        createOrUpdateError(inputEl, `error-${field}`, message);
                        if (!firstErrorField) firstErrorField = inputEl;
                    } else {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'woocommerce-notices-wrapper';
                        const msg = document.createElement('div');
                        msg.className = 'woocommerce-error';
                        msg.setAttribute('role', 'alert');
                        msg.textContent = message || 'An unexpected error occurred.';
                        wrapper.appendChild(msg);
                        form.parentNode.insertBefore(wrapper, form);
                    }
                });
            }

            if (data.code && data.code === 'rest_cookie_invalid_nonce') {
                void handlerRestCookieInvalidNonce(form);
            }

            $.preloader.hide();
            inputs.forEach(el => el.readOnly = false);
            submitBtn.classList.remove('btn--loading');

            if (firstErrorField) {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({behavior: 'smooth', block: 'center'});
            }

        } catch (err) {
            console.error('Register request failed:', err);
            const wrapper = document.createElement('div');
            wrapper.className = 'woocommerce-notices-wrapper';
            const msg = document.createElement('div');
            msg.className = 'woocommerce-message';
            msg.textContent = 'An unexpected error occurred. Please try again later.';
            wrapper.appendChild(msg);
            form.parentNode.insertBefore(wrapper, form);

            $.preloader.hide();
            inputs.forEach(el => el.readOnly = false);
            submitBtn.classList.remove('btn--loading');
        }
    });
});

window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        console.info('reload auth', new Date());
        window.location.reload();
    }
});
