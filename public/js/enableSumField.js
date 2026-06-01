document.addEventListener("moonshine:init", () => {
    MoonShine.onCallback('enableSumField', function(response, element, events, component) {
        try {
            if (response.data && response.data.htmlData && response.data.htmlData.length > 0) {
                const sumField = document.querySelector('#sum-field');
                if (sumField) {
                    const htmlString = response.data.htmlData[0].html;
                    sumField.innerHTML = htmlString;
                }
            }
        } catch (error) {
            console.error('Error in enableSumField callback:', error);
        }
    });
});
