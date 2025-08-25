const $ = jQuery; //TODO: remove, temp for dev mode

(function($){
	"use strict";
	jQuery(document).on('ready', function () {

        $(window).on("load", function () {
            $(".preloader").fadeOut();
        });

        /*---------- 04. Sticky fix ----------*/
        // $(window).scroll(function () {
        //     var topPos = $(this).scrollTop();
        //     if (topPos > 300) {
        //         $('.sticky-wrapper').addClass('sticky');
        //     } else {
        //         $('.sticky-wrapper').removeClass('sticky')
        //     }
        // })

        function checkHeight() {
            if ($('body').height() < $(window).height()) {
              $('.footer-sitcky').addClass('sticky-footer');
              $('body').css('min-height', '100vh');
            } else {
              $('.footer-sitcky').removeClass('sticky-footer');
            }
        }
    
        $(window).on('load resize', function () {
            checkHeight();
        });

        
        if (window.matchMedia("(min-width: 992px)").matches) {
            if ($('.login-height').length > 0) {
                function updateBillingMargin() {
                    var loginHeight = $('.login-height').outerHeight();
                    var messageHeight = $('.woocommerce-error, .woocommerce-message').length > 0 
                                        ? $('.woocommerce-error, .woocommerce-message').outerHeight() + 40 
                                        : 0;
            
                    var marginTopValue = (loginHeight + messageHeight) * -1;
            
                    console.log(messageHeight);
            
                    $('.adjust-margin').css('margin-top', marginTopValue + 'px');
                }
            
                updateBillingMargin();
            
                $(window).resize(function() {
                    updateBillingMargin();
                });
            
                $( 'body' ).on( 'updated_checkout', function() {
                    updateBillingMargin();
                });
            
                $('body').on('applied_coupon', function() {
                    setTimeout(function() {
                        updateBillingMargin();
                    }, 300); 
                });
            
                $('form.checkout').on('checkout_place_order', function() {
                    setTimeout(function() {
                        updateBillingMargin();
                    }, 300); 
                });
            
                if (typeof MutationObserver !== 'undefined') {
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                                updateBillingMargin(); // Update margin when sticky state changes
                            }
                        });
                    });
                    observer.observe(document.querySelector('.login-height'), { attributes: true });
                }
            }
            
        }

        $('#account_username').on('input', function() {
            var username = $(this).val();
            var availabilityMessage = $('#username-availability-message');
            
            if (username.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: theme_ajax.ajax_url, // Make sure this is available or replace with your site's ajax URL
                    data: {
                        action: 'check_username_availability',
                        username: username
                    },
                    success: function(response) {
                        if (response.available) {
                            availabilityMessage.text(response.message).css('color', 'green');
                        } else {
                            availabilityMessage.text(response.message).css('color', 'red');
                        }
                    }
                });
            } else {
                availabilityMessage.text('');
            }
        });

        if ($('body').is('.logged-in')) {
            $('#customer_details #contact_details h3').each(function() {
                $(this).hide();
            });
        } else {
            $('#customer_details #contact_details h3').each(function() {
                $(this).text('Or, create a new account');
            });
        }
        
        $('.woocommerce-orders-table__cell-order-status').each(function() {
            var statusText = $(this).text().trim().toLowerCase().replace(/\s+/g, '-'); // Replaces spaces with hyphens
            $(this).addClass('status-' + statusText);
        });
        $('.woocommerce-orders-table__header-subscription-actions, .account-payment-methods-table th.payment-method-actions').each(function() {
            $('.woocommerce-orders-table__header-subscription-actions, .account-payment-methods-table th.payment-method-actions').text('Action');
        });
        
        $('.woocommerce-MyAccount-content address br').each(function() {
            $(this).after('<hr>');
        });

        $('.shop_table.subscription_details tbody tr:last-child td:last-child').each(function() {
            $(this).wrapInner('<div class="btn-wrapper"></div>');
        });
        
        $('p.order-again a').text('Back to home');
            
        $('p.order-again a').attr('href','https://trader.megatrader.com/');

        //TODO: improve to support future Addons
        //TODO: migrate to checkout/billing scripts
        ["drawdown-buffer", "anytime-payouts"].forEach(function(className) {
            $("." + className).click(function() {
                $(this).toggleClass('active');
                $('input[value="' + className + '"]').click();
            });
        });

        $('#wc_checkout_add_ons input[type="checkbox"]:checked').each(function() {
            var value = $(this).val();
            $("." + value).addClass('active');
        });

        let data;
        
        function fetchProductsAttributeData(){
            fetch('/wp-json/custom/v1/products-with-attributes')
                .then(response => response.json())
                .then(_data => {
                    data = _data;

                    // console.log('PRODUCTS-WITH-ATTR', data)

                    if($('.pricing-buttons').length > 0) {
                        updatePlanWidget();
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });

            // fetch('/wp-json/custom/v1/products-with-categories')
            //     .then(response => response.json())
            //     .then(data => {
            //         console.log('PRODUCTS-WITH-CATEGORIES', data)
            //     })
        }

        function objectIsEmpty(obj){
            Object.keys(obj).length === 0
        }
        function updatePlatformName(){
            const platformName = $("#platform .button.active .title").text();
            $(".platformName").text(platformName);
        }
        function updatePlatformIcon(){
            $(".platformIcon img").attr("src", $("#account-type .button.active img").attr("src"));
        }
        function accountTypeTitle(){
            var accountTypeTitle = $("#account-type .button.active .title").text();        
            $(".accountType").text(accountTypeTitle);
        }
        function updateWidgetAccountSize(activeCapital){
            $(".capitalSize").text(activeCapital);
        } 
        function updateCheckoutLink(productInfo){
            const productId = productInfo.id; 
            const addToCartUrl = "/checkout/?add-to-cart=" + productId;
            $(".start-trading-btn, .continue-to-pay-link").attr("href", addToCartUrl);
        }

        function getProductInfo(productInfoArray){
            return Array.isArray(productInfoArray)
                ? Object.assign({}, ...productInfoArray)
                : {};
        }
        function getPrice(productInfo){
            const onSaleSplitText = 'price is: ';
            const priceMontly = productInfo['price-monthly'] ?? '';
            const onSale = priceMontly.includes(onSaleSplitText);
            return {
                value: onSale ? priceMontly.split(onSaleSplitText).at(-1).slice(0, -1): priceMontly,
                previousValue: onSale ? priceMontly.split(' ')[0]: '',
                onSale
            }
                
        }

        function updateSizePrices(activeProductSizes, activeType, activePlatform){
            Object.entries(activeProductSizes).forEach( ([size, content]) => {
                const pInfo = getProductInfo(content[activeType][activePlatform]);
                const priceObj = getPrice(pInfo);

                $("#trading-capital [data-value='" + size + "'] .a-price").text(priceObj.value);

                // Show/Hide OnSale Badge
                const button = $("#trading-capital [data-value='" + size + "']");
                const badge = button.find(".mbadge");
                const onSaleClass = 'on-sale';
                const hideClass = 'd-none';

                if(priceObj.previousValue){
                    button.addClass(onSaleClass)
                    badge.removeClass(hideClass);
                } else {
                    button.removeClass(onSaleClass);
                    badge.addClass(hideClass);
                }
            })
        }
        function updateWidgetAccountSizeChecklist(productInfo){
            const metaInfo = productInfo['meta-info'];
            if (metaInfo) {
                console.log({metaInfo})
                $(".metaInfo li").hide();
                Object.keys(metaInfo).forEach(key => {
                    const selector = `.metaInfo li.${key}`;
                    const value = metaInfo[key];
                    if (value && value !== "" && value !== null) {
                        $(selector).show();
                        $(selector).find('span').text(value);
                    }
                });
            }
        }
        function updateWidgetPlatformChecklist(activePlatform){
                // Find the attribute for the active platform
            const attribute = data.attributes.find(cat => 
                cat.slug.toLowerCase() === activePlatform.toLowerCase()
            );

            // Update attribute meta list
            if (attribute && attribute.attribute_meta) {
                const $platformMetaList = $('.cat-meta-list');
                $platformMetaList.empty(); // Clear existing items

                attribute.attribute_meta.forEach(metaItem => {
                    $platformMetaList.append(`<li>${metaItem}</li>`);
                });
            }
        }

        function updatePlanWidget() {
            const activeType = $("#account-type .button.active").attr('data-value');
            const activeCapital = $("#trading-capital .button.active").attr('data-value');
            const activePlatform = $("#platform .button.active").attr('data-value');

            const availableProductsMap = data.attributes.reduce((map, currentAttribute) =>
                currentAttribute.taxonomy === 'pa_account-types'
                    ? { ...map, [currentAttribute.slug]: currentAttribute }
                    : map
                , {});

            const products = data.products.reduce((map, product) =>
                product.slug in availableProductsMap
                    ? {...map, [product.slug]: product}
                    : map
                , {});

            const activeProductSizes = products?.[activeType]?.[activeType];
            const productInfoArray = activeProductSizes?.[activeCapital]?.[activeType]?.[activePlatform] ?? [];
            const productInfo = getProductInfo(productInfoArray);

            if(!objectIsEmpty(productInfo)){
                updateCheckoutLink(productInfo);
                updateWidgetAccountSizeChecklist(productInfo)
            }
            updateWidgetPlatformChecklist(activePlatform);
            updateWidgetAccountSize(activeCapital)
            accountTypeTitle();
            updatePlatformIcon();
            updatePlatformName();
            updateSizePrices(activeProductSizes, activeType, activePlatform);
        }

        $('.pricing-buttons .button:not(.coming-soon)').on('click', function() {
            $(this).siblings().removeClass('active');
            $(this).addClass('active');

            updatePlanWidget();
        });

        fetchProductsAttributeData();
                
        // $('form.checkout').on('click', 'button:not(#place_order)', function(e) {
        //     e.preventDefault();      
        // });
    });

}(jQuery));

// scrollCue
// scrollCue.init();

MT_Tabs.init();
