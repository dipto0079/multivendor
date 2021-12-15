/*------------------------------------------------------------------
[Table of contents]

- Project:	Seven Store - Ecommerce HTML/CSS Template
- Version:	1.1
- Author:  Andrey Sokoltsov
- Profile:	http://themeforest.net/user/andreysokoltsov
--*/

(function() {

	"use strict";

	var Core = {

		initialized: false,

		initialize: function() {

			if (this.initialized) return;
			this.initialized = true;

			this.build();

		},

		build: function() {

			//Placeholder for IE
			$('input, textarea').placeholder();

			// Dropdown menu
			this.dropdownhover();

			// Toggle header cart
			this.carttoggle();

			// Owl Carousel
			this.initOwlCarousel();

			//Isotope Filter
			this.isotopeFilter();

			//Isotope Filter
			this.initBxSlider();

			//Setup WOW.js
			this.initWowSlider();

			//Go to top animation
			this.goToTop();

			//Fixed Header
			this.fixedHeader();

			//jQuery UI Slider - Range slider
			this.rangeSlider();

			//Product Counter
			this.productCounter();

			//Mobile Menu Animation
			this.mainMenuSwitch();
		},

		dropdownhover: function(options) {
			/** Extra script for smoother navigation effect **/
			if ($(window).width() > 992) {
				$('.navbar-main-slide').on('mouseenter', '.navbar-nav > .dropdown', function() {
					"use strict";
					$(this).addClass('open');
				}).on('mouseleave', '.navbar-nav > .dropdown', function() {
					"use strict";
					$(this).removeClass('open');
				});
			}
		},
		carttoggle: function(options) {
			/** Extra script for toggle header catr **/
			$('.header-cart').on('mouseenter', function() {
				"use strict";
				$('.header-cart_product').addClass('open');
			});
			$('.header-cart').on('mouseleave', function() {
				"use strict";
				$('.header-cart_product').removeClass('open');
			});
		},
		initOwlCarousel: function(options) {

			$(".enable-owl-carousel").each(function(i) {
				var $owl = $(this);

				var navigationData = $owl.data('navigation');
				var paginationData = $owl.data('pagination');
				var singleItemData = $owl.data('single-item');
				var autoPlayData = $owl.data('auto-play');
				var transitionStyleData = $owl.data('transition-style');
				var mainSliderData = $owl.data('main-text-animation');
				var afterInitDelay = $owl.data('after-init-delay');
				var stopOnHoverData = $owl.data('stop-on-hover');
				var min600 = $owl.data('min600');
				var min800 = $owl.data('min800');
				var min1200 = $owl.data('min1200');

				$owl.owlCarousel({
					navigation : navigationData,
					pagination: paginationData,
					singleItem : singleItemData,
					autoPlay : autoPlayData,
					transitionStyle : transitionStyleData,
					stopOnHover: stopOnHoverData,
					navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
					itemsCustom:[
						[0, 1],
						[600, min600],
						[800, min800],
						[1200, min1200]
					],
					afterInit: function(elem){
						if(mainSliderData){
							setTimeout(function(){
								$('.main-slider_zoomIn').css('visibility','visible').removeClass('zoomIn').addClass('zoomIn');
								$('.main-slider_fadeInLeft').css('visibility','visible').removeClass('fadeInLeft').addClass('fadeInLeft');
								$('.main-slider_fadeInLeftBig').css('visibility','visible').removeClass('fadeInLeftBig').addClass('fadeInLeftBig');
								$('.main-slider_fadeInRightBig').css('visibility','visible').removeClass('fadeInRightBig').addClass('fadeInRightBig');
							}, afterInitDelay);
						}
					},
					beforeMove: function(elem){
						if(mainSliderData){
							$('.main-slider_zoomIn').css('visibility','hidden').removeClass('zoomIn');
							$('.main-slider_slideInUp').css('visibility','hidden').removeClass('slideInUp');
							$('.main-slider_fadeInLeft').css('visibility','hidden').removeClass('fadeInLeft');
							$('.main-slider_fadeInRight').css('visibility','hidden').removeClass('fadeInRight');
							$('.main-slider_fadeInLeftBig').css('visibility','hidden').removeClass('fadeInLeftBig');
							$('.main-slider_fadeInRightBig').css('visibility','hidden').removeClass('fadeInRightBig');
						}
					},
					afterMove: sliderContentAnimate,
					afterUpdate: sliderContentAnimate,
				});
			});
			function sliderContentAnimate(elem){
				var $elem = elem;
				var afterMoveDelay = $elem.data('after-move-delay');
				var mainSliderData = $elem.data('main-text-animation');
				if(mainSliderData){
					setTimeout(function(){
						$('.main-slider_zoomIn').css('visibility','visible').addClass('zoomIn');
						$('.main-slider_slideInUp').css('visibility','visible').addClass('slideInUp');
						$('.main-slider_fadeInLeft').css('visibility','visible').addClass('fadeInLeft');
						$('.main-slider_fadeInRight').css('visibility','visible').addClass('fadeInRight');
						$('.main-slider_fadeInLeftBig').css('visibility','visible').addClass('fadeInLeftBig');
						$('.main-slider_fadeInRightBig').css('visibility','visible').addClass('fadeInRightBig');
					}, afterMoveDelay);
				}
			}
		},
		isotopeFilter: function(options) {
			var $container = $('.isotope-filter');

			$container.imagesLoaded(function() {
				$container.isotope({
					// options
					filter: '.bestsellers',
					itemSelector: '.isotope-item'
				});
			});

			// filter items when filter link is clicked
			$('#filter').on('click', 'a', function() {
				$('#filter  a').removeClass('current');
				$(this).addClass('current');
				var selector = $(this).attr('data-filter');
				$container.isotope({
					filter: selector
				});
				return false;
			});
		},
		initBxSlider: function(options){
			$(".bxslider").each(function(i){
				var sliderMode = $(this).data("mode");
				var slideMargin = $(this).data("slide-margin");
				var minSlides = $(this).data("min-slides");
				var moveSlides = $(this).data("move-slides");
				var sliderPager = $(this).data("pager");
				var sliderPagerCustom = $(this).data("pager-custom");
				var sliderControls = $(this).data("controls");

				$(this).bxSlider({
					mode: sliderMode,
					slideMargin: slideMargin,
					minSlides: minSlides,
					moveSlides: moveSlides,
					pager: sliderPager,
					pagerCustom: sliderPagerCustom,
					controls: sliderControls,
					prevText:'<i class="fa fa-angle-left"></i>',
					nextText:'<i class="fa fa-angle-right"></i>'
				});
			});
		},
		initWowSlider: function(options){
			var scrollingAnimations = $('body').data("scrolling-animations");
			if(scrollingAnimations){
				new WOW().init();
			}
		},
		goToTop: function(options){
			$("#footer").on('click', '.goToTop', function(e){
				e.preventDefault();
				$('html,body').animate({
					scrollTop: 0,
				},'slow');
			});
			// Show/Hide Button on Window Scroll event.
			$(window).on('scroll', function(){
				var fromTop = $(this).scrollTop();
				var display = 'none';
				if(fromTop > 650){
					display = 'block';
				}
				$('#scrollTop').css({'display': display});
			});
		},
		fixedHeader: function(options){
			// Fixed Header
			var topOffset = $(window).scrollTop();
			if(topOffset > 0){
				$('body').addClass('fixed-header');
			}
			$(window).on('scroll', function(){
				var fromTop = $(this).scrollTop();
				if(fromTop > 0){
					$('body').addClass('fixed-header');
				}
				else{
					$('body').removeClass('fixed-header');
				}

			});
		},
		rangeSlider: function(options){
			// jQuery UI Slider - Range slider
			// $(".slider-range").each(function(i){
			// 	var minAmount = $(this).data("min");
			// 	var minDefaultAmount = $(this).data("default-min");
			// 	var maxAmount = $(this).data("max");
			// 	var maxDefaultAmount = $(this).data("default-max");
			// 	var rangeData = $(this).data("range");
			// 	var valueContainerId = $(this).data("value-container-id");
			//
			// 	$(this).slider({
			// 		range: rangeData,
			// 		min: minAmount,
			// 		max: maxAmount,
			// 		values: [ minDefaultAmount, maxDefaultAmount ],
			// 		slide: function(event, ui){
			// 			$("#"+valueContainerId).val("$" + ui.values[0] + " - $" + ui.values[1]);
			// 		}
			// 	});
			// 	$("#"+valueContainerId).val("$" + $(this).slider("values", 0) + " - $" + $(this).slider("values", 1));
			// });
		},
		productCounter: function(options){
			$(".product-counter").on('click', '.productCounter', function(e){
				e.preventDefault();
				var counterStep = parseInt($(this).data("counter-step"), 10);
				var counterType = $(this).data("counter-type");
				var counterField = $(this).data("counter-field");
				var counterAmount = parseInt($(counterField).val(), 10);
				if(!isNaN(counterAmount)){
					if(counterType == 'add'){
						counterAmount = counterAmount + counterStep;
						if(counterAmount >= 10){
							counterAmount = 10;
						}
					}
					else if(counterType == 'minus'){
						counterAmount = counterAmount - counterStep;
					}
					if(counterAmount < 1){
						counterAmount = 1;
					}
					$(counterField).val(counterAmount);
				}
			});
		},
		mainMenuSwitch: function(options){
			$('#header').on('click', '.mobileMenuSwitcher', function(e){
				$('body').toggleClass('openMenu');
			});
		},
	};

	Core.initialize();

})();
