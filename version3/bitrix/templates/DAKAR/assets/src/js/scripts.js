/**
 * author: BlueWolves
 * author url: http://bluewolv.es
 */
!(function() {

var BetaDesign = function(opt)
{
	beta = this;
	beta.init();
}

BetaDesign.prototype = {
	init : function() {
		beta.initInstragram();
		beta.initFilter();
		beta.initCalendar();
		beta.initRevolutionSlider();
		beta.initRoundabout();
		beta.initSlider();
		beta.initHistorySlider();
		beta.initCircliful();
		beta.initMedia();
		beta.initTooltip();
		beta.initProgressBar();
		beta.initColorbox();
		beta.onCart();
		beta.setSelect();
		beta.animateBanners();
		beta.absFullwidth();
		beta.filterItems();
		beta.accordionHandler();
	}, 

	initInstragram : function() {
		dug({
	      endpoint: 'https://api.instagram.com/v1/users/1140781/media/recent/?client_id=01a35e4dd4bf4d2681e48c88d006636e&count=6',
	      target : jQuery('#beta-instagram-feed')[0],
	      cacheExpire : 3600,
	      template: '\
            {{#data}}\
                <a href="{{link}}" target="_blank">\
                  <img src="{{images.low_resolution.url}}">\
                </a>\
            {{/data}}'
	    });
	},

	initFilter : function() {
		if(!jQuery.fn.slider) return;

		jQuery( "#price-filter-range" ).slider({
			range: true,
			min: 0,
			max: 1000,
			values: [ 0, 300 ],
			slide: function( event, ui ) {
				jQuery( "#price-filter-amount" ).text( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
			}
	    });
	    jQuery( "#price-filter-amount" ).text( "$" + jQuery( "#price-filter-range" ).slider( "values", 0 ) +
	      " - $" + jQuery( "#price-filter-range" ).slider( "values", 1 ) );
	},

	initCalendar : function() {
		if(!jQuery.fn.datepicker) return;

		jQuery(".beta-calendar").datepicker();
	},

	initRevolutionSlider : function() {
		if(!jQuery.fn.revolution) return;

		var opt = {
			delay:9000,
			startwidth:1140,
			startheight:480,
			navigationType : "none"
		};

		if(jQuery('.tp-banner').hasClass("fullwidth"))
		{
			opt.fullWidth = "on";
			opt.forceFullWidth = "on";
			opt.startheight = 530;
		}
		
		jQuery('.tp-banner').revolution(opt);
	},

	initRoundabout : function() {
		if(!jQuery.fn.roundabout) return;

		jQuery(".beta-roundabout").roundabout({
			responsive : true
		});
	},

	initCircliful : function() {
		if(!jQuery.fn.circliful) return;

		var mq = window.matchMedia( "(max-width: 1200px)" );
		var small_devices = window.matchMedia( "(max-width: 993px)" );
		var mobiles = window.matchMedia( "(max-width: 768px)" );

		if(mobiles.matches)
		{
			jQuery(".beta-rotator").circliful({
				dimension : "370px"
			});
		}
		else if(small_devices.matches)
		{
			jQuery(".beta-rotator").circliful({
				dimension : "158px"
			});
		}
		else if(mq.matches)
		{
			jQuery(".beta-rotator").circliful({
				dimension : "213px"
			});
		}
		else
		{
			jQuery(".beta-rotator").circliful({
				dimension : "260px"
			});
		}
	},

	initMedia : function() {
		if(!jQuery.fn.mediaelementplayer) return;

		var audioWidth = jQuery(".beta-audio").parent().width();

		jQuery('audio,video').mediaelementplayer({
			audioWidth : audioWidth
		});
	},

	initTooltip : function() {
		jQuery("[data-toggle='tooltip']").tooltip();
	},

	initProgressBar : function() {
		jQuery(".progress-bar").each(function(index, el) {
			var amount = jQuery(el).attr("aria-valuenow");
			var time = (parseInt(amount) / 100) * 5000;

			jQuery(el).animate({ width : amount + "%" }, time);
		});
	},

	initSlider : function() {

		jQuery(".beta-slider").each(function(index, el) {
			var container = jQuery(el);
			var slider = container.find(".beta-slider-items");
			var next = container.find(".beta-arrow-right");
			var prev = container.find(".beta-arrow-left");

			// if custom pager exists
			var pager = container.find(".beta-pager");
			var pager_gallery = container.find(".beta-pager-gallery");

			// slider options
			var slider_options = {
				auto : true,
				autoHover : true,
				controls : false,
				pager : false
			};

			if(pager.length > 0)
			{
				slider_options.pager = true;
				slider_options.pagerCustom = pager;
			}

			if(pager_gallery.length > 0)
			{
				slider_options.pager = true;
				slider_options.pagerCustom = pager_gallery;
			}

			var currentSlider = slider.bxSlider(slider_options);

			next.on("click", function() {
				currentSlider.goToNextSlide();
				return false;
			});

			prev.on("click", function() {
				currentSlider.goToPrevSlide();
				return false;
			});
		});
	},

	initHistorySlider : function() {
		jQuery(".history-slider").each(function(index, el) {
			var container = jQuery(el);
			var slider = container.find(".history-slides");
			var pagerNavigation = container.find(".history-navigation");

			slider.bxSlider({
				auto : true,
				autoHover : true,
				controls : false,
				pager : true,
				pagerCustom : pagerNavigation
			});
		});
	},

	initColorbox : function() {
		jQuery(".colorbox").colorbox({
			maxWidth : "95%",
			maxHeight : "95%",
			rel:'gal'
		});
	},

	setSelect : function() {
		// languages
		jQuery("select[name='languages']").betaSelect({
			"text" : "Language:",
			"value" : "English"
		});

		// currency
		jQuery("select[name='currency']").betaSelect({
			"text" : "Currency:",
			"value" : "USD"
		});

		// for all selects on the website
		jQuery(".beta-select-primary").betaSelect({
			"value" : "Select",
			"class" : "beta-select-primary"
		});
	},

	onCart : function() {
		jQuery(".cart .beta-select").on("click", function() {
			jQuery(".cart-body").slideToggle();
			return false;
		});

		var container = jQuery(".cart");
		jQuery(document).mouseup(function (e)
		{
		    if (!container.is(e.target) // if the target of the click isn't the container...
		        && container.has(e.target).length === 0) // ... nor a descendant of the container
		    {
		        jQuery(".cart-body").slideUp();
		    }
		});
	},

	animateBanners : function() {
		jQuery(".beta-banner").each(function(index, el) {
			var layers = jQuery(el).find("[data-animo]");
			var animations = [];

			for(var i = 0; i < layers.length; i++)
			{
				var data = JSON.parse(jQuery(layers[i]).attr("data-animo"));
				data.el = jQuery(layers[i]);
				animations.push( data );
			}
			
			new Animo(animations);
		});
	},

	absFullwidth : function() {
		jQuery(".abs-fullwidth").each(function(index, el) {
			var height = jQuery(el).height();
			var images = jQuery(el).find("img");

			jQuery(el).after("<div class='abs-fullwidth-height' style='height:"+height+"px'></div>");
			if(images.length > 0)
			{
				images.on("load", function() {
					height = jQuery(el).height();
					jQuery(el).next(".abs-fullwidth-height").height( height + "px" );
				});
			}
		});
	},

	filterItems : function() {
		jQuery(".beta-filter-container").each(function(index, el) {
			var container = jQuery(el);
			var filters   = container.find(".beta-filter a");
			var items     = container.find(".beta-filter-body li");

			filters.on("click", function() {
				var $this = jQuery(this);
				var category = $this.data("filter");
				
				// filter menu toggle active class
				$this.addClass("is-active").siblings().removeClass("is-active"); 

				// toggle items based on category
				for(var i = 0; i < items.length; i++)
				{
					if(!jQuery(items[i]).hasClass(category))
					{
						jQuery(items[i]).addClass("is-item-hidden");
					}
					else
					{
						jQuery(items[i]).removeClass("is-item-hidden");
					}
				}

				return false;
			});
		});
	},

	accordionHandler : function() {
		jQuery(".panel-group").on("hidden.bs.collapse", function(e) {
			var headingBar = jQuery(e.target).prev();

			headingBar.removeClass("beta-active-tab");
			headingBar.find(".accordion-circle .fa").removeClass("fa-minus").addClass("fa-plus");
		});

		jQuery(".panel-group").on("show.bs.collapse", function(e) {
			var headingBar = jQuery(e.target).prev();

			headingBar.addClass("beta-active-tab");
			headingBar.find(".accordion-circle .fa").removeClass("fa-plus").addClass("fa-minus");
		});
	}
};

////////////////////////// WooCommerce Functionality
var BetaWC = {
	init : function() {
		this.initTabs();
	},

	initTabs : function() {
		// Tabs
		jQuery( '.woocommerce-tabs .panel' ).hide();

		jQuery( '.woocommerce-tabs ul.tabs li a' ).click( function() {

			var $tab = jQuery( this ),
				$tabs_wrapper = $tab.closest( '.woocommerce-tabs' );

			jQuery( 'ul.tabs li', $tabs_wrapper ).removeClass( 'active' );
			jQuery( 'div.panel', $tabs_wrapper ).hide();
			jQuery( 'div' + $tab.attr( 'href' ), $tabs_wrapper).show();
			$tab.parent().addClass( 'active' );

			return false;
		});

		jQuery( '.woocommerce-tabs' ).each( function() {
			var hash	= window.location.hash,
				url		= window.location.href,
				tabs	= jQuery( this );

			if ( hash.toLowerCase().indexOf( "comment-" ) >= 0 ) {
				jQuery('ul.tabs li.reviews_tab a', tabs ).click();

			} else if ( url.indexOf( "comment-page-" ) > 0 || url.indexOf( "cpage=" ) > 0 ) {
				jQuery( 'ul.tabs li.reviews_tab a', jQuery( this ) ).click();

			} else {
				jQuery( 'ul.tabs li:first a', tabs ).click();
			}
		});

		jQuery( 'a.woocommerce-review-link' ).click( function() {
			jQuery( '.reviews_tab a' ).click();
			return true;
		});
	}
}

// on dom start
jQuery(function() {
	var beta = new BetaDesign();

	if(typeof BetaUtils != "undefined")
	{
		// use utils class ***********************/
		// set google map
		BetaUtils.setMap(jQuery("#beta-map")[0], {
			latitude : 40.722803,
			longitude : -74.00882,
			zoom : 10,
			content : "BetaDesign",
			title : "Beta Design"
		});
	}

	// init woocommerce
	BetaWC.init();

	// wc logic
	jQuery(".shipping-calculator-button").on("click", function() {
		jQuery(".shipping-calculator-form ").slideToggle();

		return false;
	});

	// payments method
	jQuery("input[name='payment_method']").on("click", function() {
		var activate = jQuery(this).attr("id");

		jQuery(".payment_box").each(function(index, el) {
			if(jQuery(el).hasClass(activate))
			{
				jQuery(el).show();
			}
			else
			{
				jQuery(el).hide();
			}
		});
	});


	/******************************************************************/
	// Menu Responsive Toggle
	jQuery(".beta-menu-toggle").on("click", function() {
		jQuery(".main-menu > ul").slideToggle();
		return false;
	});

	// banners fallback
	var mq = window.matchMedia( "(max-width: 1200px)" );

	if(mq.matches)
	{
		jQuery(".beta-banner .beta-fallback").each(function(index, el) {
			var newSrc = jQuery(el).data("fallback");
			 jQuery(el).siblings(".beta-banner-layer").remove().end().attr("src", newSrc);
		});
	}
});
// end of dom start

})();