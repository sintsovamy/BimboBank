document.addEventListener("moonshine:init", () => {
    MoonShine.onCallback('enableAmountField', function(data, messageType, component) {
        console.log('myAfterResponse', data, messageType, component)
    })
})
