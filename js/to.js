
function to_get() {
	// get the data from the hrefjson if the server not localhost
    if ( window.location.hostname === 'localhost' ) {
		// log to console
		console.log('not localhost');
		return;
	}
	var ele = $("[hrefjson]");
	ele.each(function(){
		var item = $(this);
		var hrefjson = item.attr("hrefjson");
		// get the data from the hrefjson then add it to the value
        //---------------
		// console.log(hrefjson);
		
		// console.log(item.text());
		//---------------
        jQuery.ajax({
            url: hrefjson,
            // data: params,
            type: 'GET',
            success: function(data) {
                //---------------
				var view = 0;
                var items = data.items;
				// get view count from items array
				items.forEach(function(aa) {
					view += aa['views'];
					// console.log(view);
                });
				//---
				item.text(view);
				var pa = item.parent();
				pa.attr('data-sort', view);
				//---
				// var txt2 = $("<span></span>").text(view).hide();     // Create with jQuery
				// item.before(txt2);
				//---
				var p = $('#hrefjsontoadd').text();
				// add the view to hrefjsontoadd value
				var nu = parseFloat(p) + view;
				$('#hrefjsontoadd').text(nu);
				//---
            },
            error: function(data) {
            }
        });
	});
	//---
	return true;
};