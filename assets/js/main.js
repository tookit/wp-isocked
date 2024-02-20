(function () {

    const screenWidth = window.innerWidth;
    const hideGetQuoteForMobile = (screenWidth) => {
        const btn = document.querySelector('.elementor-element-67547b3');
        if(btn) {
            if(screenWidth < 767) {
                console.log(screenWidth)
                jQuery(btn).addClass('hidden');
            }else {
                jQuery(btn).removeClass('hidden');
            }
        }
    }

    hideGetQuoteForMobile(screenWidth);

    jQuery(window).on("resize", () => {
        const screenWidth = window.innerWidth;
		hideGetQuoteForMobile(screenWidth);

	});

    jQuery(window).on("submit_success", function(event) {
        const data = jQuery(event.target).serializeArray().reduce(function (json, { name, value }) {
            json[name] = value;
            return json;
          }, {});;
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'generate_lead',
            'userInputs': data,
            'lead_email': data['form_fields[email]'],
            'lead_name': data['form_fields[name]'],
            'lead_company': data['form_fields[company]'],
         });
   });

})();
