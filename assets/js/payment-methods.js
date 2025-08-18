const Selector = {
  PaymentId: '#payment',
  SavedPaymentMethodRadioSelector:  '.woocommerce-SavedPaymentMethods [type="radio"]',
  NewPaymentMethodRadioSelector: '.woocommerce-SavedPaymentMethods-new [type="radio"]',
}

const errorsBlackList = [
    // CC Numer
    'Your card number is incomplete.',
    'Your card number is invalid.',
    'Your card number is invalid.',
    // Exp Date
    'Your card’s expiration year is in the past.',
    'Your card’s expiration year is invalid.',
    //CVV
    'Your card’s security code is incomplete.',
    //Zip Code
    'Your ZIP is invalid.',
]

addPaymentMethodBoxToggleListeners();
mutationObserver([
    {
        matches: Selector.PaymentId,
        callbacks: [
            addPaymentMethodBoxToggleListeners,
        ],
    },
    {
        matches: '.woocommerce-error',
        callbacks: [filterErrors(errorsBlackList)]
    },
    // {   // Debug Added Nodes
        // matches: '*', callbacks: [ (node)=>{ console.info('Node Added:', node) } ]
    // }
]);


function mutationObserver(configMap = []){
    const observer = new MutationObserver((mutations, obs) => {
        mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
            configMap.forEach( config => {
            if (
                node.nodeType === 1 &&
                node.matches(config.matches)
            ) {
                if(!config.completed){
                    config.callbacks.forEach(callback => {
                        callback(node);
                    })

                    if(config.once){
                        config.completed = true;
                    }
                }
            }
            })
        });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

function filterErrors(stringsToRemove){
    return (node) => {
        if(!node || !node.children) return;

        if (stringsToRemove.includes(node.textContent.trim())) {
            node.remove();
        }
    }
}

// ========== JQUERY SLIDE ANIMATION ==========
function slideCollapse(targetElement, isExpanded, useCustom){
    jQuery(function ($) {
        const duration = 300;
        $targetEl = $(targetElement);
        if(isExpanded){
        if(useCustom){
            targetElement.style.display='';
            targetElement.style.overflow='hidden';
            targetElement.style.height = `${targetElement.scrollHeight}px`;
            targetElement.style['padding-bottom'] = '';
            setTimeout(() => {
            targetElement.style.height = '';
            targetElement.style.overflow = '';
            }, duration)
        } else {
            $targetEl.slideDown(duration);
        }
        } else {
        if(useCustom){
            requestAnimationFrame(()=>{
            targetElement.style.overflow='hidden';
            targetElement.style.height = '0';
            targetElement.style['padding-bottom'] = '0';
            })
        } else {
            $targetEl.slideUp(duration);
        }
        }
    })
}

// ========== TOGGLE CC BOX COLLAPSE ==========
function addPaymentMethodBoxToggleListeners(){
    const newMethodRadio = document.querySelector(Selector.NewPaymentMethodRadioSelector);

    const savedMethods = document.querySelector('.woocommerce-SavedPaymentMethods');
    if(!savedMethods || !savedMethods.getAttribute('data-count') || !parseInt(savedMethods.getAttribute('data-count')) > 0){
        newMethodRadio.checked = true;
        return;
    }
    const paymentForm = document.querySelector('.wc-payment-form');
    const saveNewCardCheckbox = document.querySelector('.woocommerce-SavedPaymentMethods-saveNew');
    let useCustom;

    // Cannot Use Animations Ending on "display: none" like jQuery toggle
    // or NMI input fields iframes won't render properly
    if(paymentForm.id.includes('nmi')){
        useCustom = true;
        paymentForm.classList.add('collapsable');
        saveNewCardCheckbox.classList.add('collapsable');
    }

    const togglePaymentFormCollapse = () => {
        slideCollapse(paymentForm, newMethodRadio.checked, useCustom);
        slideCollapse(saveNewCardCheckbox, newMethodRadio.checked);
    }

    document.querySelectorAll(Selector.SavedPaymentMethodRadioSelector).forEach(radio => 
        radio.addEventListener("click", togglePaymentFormCollapse, true)
    )

    togglePaymentFormCollapse()
}
