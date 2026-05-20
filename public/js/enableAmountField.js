document.addEventListener("moonshine:init", () => {
    MoonShine.onCallback('enableAmountField', function(response, element, events, component) {
        try {
            if (response.data && response.data.htmlData && response.data.htmlData.length > 0) {
                const previewContainer = document.querySelector('#receive-user-name');
                if (previewContainer) {
                    const htmlString = response.data.htmlData[0].html;
                    previewContainer.innerHTML = htmlString;
                }
            }

            const phoneField = document.querySelector('#phone-field');
            const cardField = document.querySelector('#card-number-field');
            const amountField = document.querySelector('#amount-field');

            if (response.data && response.data.foundBy) {
                if (amountField) {
                    amountField.disabled = false;
                    amountField.classList.remove('opacity-50', 'bg-gray-100');
                }
                if (response.data.foundBy === 'phone') {
                    if (cardField) {
                        cardField.disabled = true;
                        cardField.classList.add('opacity-50', 'bg-gray-100');
                    }
                }
                else if (response.data.foundBy === 'card') {
                    if (phoneField) {
                        phoneField.disabled = true;
                        phoneField.classList.add('opacity-50', 'bg-gray-100');
                    }
                }
            }
        } catch (error) {
            console.error('Error in enableAmountField callback:', error);
        }
    });
});
