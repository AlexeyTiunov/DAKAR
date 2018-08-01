!(function( jQuery ) {

	jQuery.fn.betaSelect = function( options ) {

		var defaults = {
			text  : "", // text to append before value
			value : "", // value
			class : "" // extra class to add to container
		};

		var opt = jQuery.extend( {}, defaults, options );

		return this.each(function() {
			var el = jQuery(this);
			var wrapper = el.wrap("<div class='beta-select "+opt.class+"'></div>").closest(".beta-select");
			wrapper.append("<i class='fa fa-chevron-down'></i>");
			wrapper.prepend("<span class='beta-select-title'>"+opt.text+"</span><span class='beta-select-value'>"+opt.value+"</span>");

			el.change(function(elem) {
				var text = jQuery(this).find("option:selected").text();
				wrapper.find(".beta-select-value").text(text);
			});
		});
	};

})( jQuery );