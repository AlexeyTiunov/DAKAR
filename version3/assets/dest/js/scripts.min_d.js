!
function (a) {
    a.fn.betaSelect = function (b) {
        var c = {
            text: "",
            value: "",
            "class": ""
        },
            d = a.extend({}, c, b);
        return this.each(function () {
            var b = a(this),
                c = b.wrap("<div class='beta-select " + d.class + "'></div>").closest(".beta-select");
            c.append("<i class='fa fa-chevron-down'></i>"),
            c.prepend("<span class='beta-select-title'>" + d.text + "</span><span class='beta-select-value'>" + d.value + "</span>"),
            b.change(function () {
                var b = a(this).find("option:selected").text();
                c.find(".beta-select-value").text(b)
            })
        })
    }
}(jQuery),
!
function () {
    var a = function () {
        beta = this,
        beta.init()
    };
    a.prototype = {
        init: function () {
            beta.initInstragram(),
            beta.initFilter(),
            beta.initCalendar(),
            beta.initRevolutionSlider(),
            beta.initRoundabout(),
            beta.initSlider(),
            beta.initHistorySlider(),
            beta.initCircliful(),
            beta.initMedia(),
            beta.initTooltip(),
            beta.initProgressBar(),
            beta.initColorbox(),
            beta.onCart(),
            beta.setSelect(),
            beta.animateBanners(),
            beta.absFullwidth(),
            beta.filterItems(),
            beta.accordionHandler()
        },
        initInstragram: function () {
            
        },
        initFilter: function () {
            jQuery.fn.slider && (jQuery("#price-filter-range").slider({
                range: !0,
                min: 0,
                max: 1e3,
                values: [0, 300],
                slide: function (a, b) {
                    jQuery("#price-filter-amount").text("$" + b.values[0] + " - $" + b.values[1])
                }
            }), jQuery("#price-filter-amount").text("$" + jQuery("#price-filter-range").slider("values", 0) + " - $" + jQuery("#price-filter-range").slider("values", 1)))
        },
        initCalendar: function () {
            jQuery.fn.datepicker && jQuery(".beta-calendar").datepicker()
        },
        initRevolutionSlider: function () {
            if (jQuery.fn.revolution) {
                var a = {
                    delay: 9e3,
                    startwidth: 1140,
                    startheight: 480,
                    navigationType: "none"
                };
                jQuery(".tp-banner").hasClass("fullwidth") && (a.fullWidth = "on", a.forceFullWidth = "on", a.startheight = 530),
                jQuery(".tp-banner").revolution(a)
            }
        },
        initRoundabout: function () {
            jQuery.fn.roundabout && jQuery(".beta-roundabout").roundabout({
                responsive: !0
            })
        },
        initCircliful: function () {
            if (jQuery.fn.circliful) {
                var a = window.matchMedia("(max-width: 1200px)"),
                    b = window.matchMedia("(max-width: 993px)"),
                    c = window.matchMedia("(max-width: 768px)");
                jQuery(".beta-rotator").circliful(c.matches ? {
                    dimension: "370px"
                } : b.matches ? {
                    dimension: "158px"
                } : a.matches ? {
                    dimension: "213px"
                } : {
                    dimension: "260px"
                })
            }
        },
        initMedia: function () {
            if (jQuery.fn.mediaelementplayer) {
                var a = jQuery(".beta-audio").parent().width();
                jQuery("audio,video").mediaelementplayer({
                    audioWidth: a
                })
            }
        },
        initTooltip: function () {
            jQuery("[data-toggle='tooltip']").tooltip()
        },
        initProgressBar: function () {
            jQuery(".progress-bar").each(function (a, b) {
                var c = jQuery(b).attr("aria-valuenow"),
                    d = parseInt(c) / 100 * 5e3;
                jQuery(b).animate({
                    width: c + "%"
                }, d)
            })
        },
        initSlider: function () {
            jQuery(".beta-slider").each(function (a, b) {
                var c = jQuery(b),
                    d = c.find(".beta-slider-items"),
                    e = c.find(".beta-arrow-right"),
                    f = c.find(".beta-arrow-left"),
                    g = c.find(".beta-pager"),
                    h = c.find(".beta-pager-gallery"),
                    i = {
                        auto: !0,
                        autoHover: !0,
                        controls: !1,
                        pager: !1
                    };
                g.length > 0 && (i.pager = !0, i.pagerCustom = g),
                h.length > 0 && (i.pager = !0, i.pagerCustom = h);
                var j = d.bxSlider(i);
                e.on("click", function () {
                    return j.goToNextSlide(),
                    !1
                }),
                f.on("click", function () {
                    return j.goToPrevSlide(),
                    !1
                })
            })
        },
        initHistorySlider: function () {
            jQuery(".history-slider").each(function (a, b) {
                var c = jQuery(b),
                    d = c.find(".history-slides"),
                    e = c.find(".history-navigation");
                d.bxSlider({
                    auto: !0,
                    autoHover: !0,
                    controls: !1,
                    pager: !0,
                    pagerCustom: e
                })
            })
        },
        initColorbox: function () {
            jQuery(".colorbox").colorbox({
                maxWidth: "95%",
                maxHeight: "95%",
                rel: "gal"
            })
        },
        setSelect: function () {
            jQuery("select[name='languages']").betaSelect({
                text: "Language:",
                value: "English"
            }),
            jQuery("select[name='currency']").betaSelect({
                text: "Валюта:",
                value: "USD"
            }),
            jQuery(".beta-select-primary").betaSelect({
                value: "Select",
                "class": "beta-select-primary"
            })
        },
        onCart: function () {
            jQuery(".cart .beta-select").on("click", function () {
                return jQuery(".cart-body").slideToggle(),
                !1
            });
            var a = jQuery(".cart");
            jQuery(document).mouseup(function (b) {
                a.is(b.target) || 0 !== a.has(b.target).length || jQuery(".cart-body").slideUp()
            })
        },
        animateBanners: function () {
            jQuery(".beta-banner").each(function (a, b) {
                for (var c = jQuery(b).find("[data-animo]"), d = [], e = 0; e < c.length; e++) {
                    var f = JSON.parse(jQuery(c[e]).attr("data-animo"));
                    f.el = jQuery(c[e]),
                    d.push(f)
                }
                new Animo(d)
            })
        },
        absFullwidth: function () {
            jQuery(".abs-fullwidth").each(function (a, b) {
                var c = jQuery(b).height(),
                    d = jQuery(b).find("img");
                jQuery(b).after("<div class='abs-fullwidth-height' style='height:" + c + "px'></div>"),
                d.length > 0 && d.on("load", function () {
                    c = jQuery(b).height(),
                    jQuery(b).next(".abs-fullwidth-height").height(c + "px")
                })
            })
        },
        filterItems: function () {
            jQuery(".beta-filter-container").each(function (a, b) {
                var c = jQuery(b),
                    d = c.find(".beta-filter a"),
                    e = c.find(".beta-filter-body li");
                d.on("click", function () {
                    var a = jQuery(this),
                        b = a.data("filter");
                    a.addClass("is-active").siblings().removeClass("is-active");
                    for (var c = 0; c < e.length; c++) jQuery(e[c]).hasClass(b) ? jQuery(e[c]).removeClass("is-item-hidden") : jQuery(e[c]).addClass("is-item-hidden");
                    return !1
                })
            })
        },
        accordionHandler: function () {
            jQuery(".panel-group").on("hidden.bs.collapse", function (a) {
                var b = jQuery(a.target).prev();
                b.removeClass("beta-active-tab"),
                b.find(".accordion-circle .fa").removeClass("fa-minus").addClass("fa-plus")
            }),
            jQuery(".panel-group").on("show.bs.collapse", function (a) {
                var b = jQuery(a.target).prev();
                b.addClass("beta-active-tab"),
                b.find(".accordion-circle .fa").removeClass("fa-plus").addClass("fa-minus")
            })
        }
    };
    var b = {
        init: function () {
            this.initTabs()
        },
        initTabs: function () {
            jQuery(".woocommerce-tabs .panel").hide(),
            jQuery(".woocommerce-tabs ul.tabs li a").click(function () {
                var a = jQuery(this),
                    b = a.closest(".woocommerce-tabs");
                return jQuery("ul.tabs li", b).removeClass("active"),
                jQuery("div.panel", b).hide(),
                jQuery("div" + a.attr("href"), b).show(),
                a.parent().addClass("active"),
                !1
            }),
            jQuery(".woocommerce-tabs").each(function () {
                var a = window.location.hash,
                    b = window.location.href,
                    c = jQuery(this);
                a.toLowerCase().indexOf("comment-") >= 0 ? jQuery("ul.tabs li.reviews_tab a", c).click() : b.indexOf("comment-page-") > 0 || b.indexOf("cpage=") > 0 ? jQuery("ul.tabs li.reviews_tab a", jQuery(this)).click() : jQuery("ul.tabs li:first a", c).click()
            }),
            jQuery("a.woocommerce-review-link").click(function () {
                return jQuery(".reviews_tab a").click(),
                !0
            })
        }
    };
    jQuery(function () {
        new a;
        "undefined" != typeof BetaUtils && BetaUtils.setMap(jQuery("#beta-map")[0], {
            latitude: 40.722803,
            longitude: -74.00882,
            zoom: 10,
            content: "BetaDesign",
            title: "Beta Design"
        }),
        b.init(),
        jQuery(".shipping-calculator-button").on("click", function () {
            return jQuery(".shipping-calculator-form ").slideToggle(),
            !1
        }),
        jQuery("input[name='payment_method']").on("click", function () {
            var a = jQuery(this).attr("id");
            jQuery(".payment_box").each(function (b, c) {
                jQuery(c).hasClass(a) ? jQuery(c).show() : jQuery(c).hide()
            })
        }),
        jQuery(".beta-menu-toggle").on("click", function () {
            return jQuery(".main-menu > ul").slideToggle(),
            !1
        });
        var c = window.matchMedia("(max-width: 1200px)");
        c.matches && jQuery(".beta-banner .beta-fallback").each(function (a, b) {
            var c = jQuery(b).data("fallback");
            jQuery(b).siblings(".beta-banner-layer").remove().end().attr("src", c)
        })
    })
}();