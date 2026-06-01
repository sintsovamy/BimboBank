document.addEventListener("moonshine:init", () => {
    MoonShine.onCallback('updateSelect', function(response, element, events, component) {
        try {
            console.log(response.data)
            if (response.data && response.data.htmlData && response.data.htmlData.length > 0) {
                const receiveAccountSelect = document.querySelector('#receive-account-field');
                if (receiveAccountSelect) {
                    const htmlString = response.data.htmlData[0].html;
                    receiveAccountSelect.innerHTML = htmlString;
                }
            }
        } catch (error) {
            console.error('Error in updateSelect callback:', error);
        }
    });
});
