(function( $ ) {
	$(document).on('click', '#hamburger', function(e){
		e.preventDefault();
		$('body').toggleClass('showNav');
	});

	window.scrollInterval = window.setInterval(function(){
		if(window.scrollY > window.innerHeight){
			$('body:not(.small-height-header)').addClass('small-height-header');
		}else if(window.scrollY < window.innerHeight - $('#masthead').height()){
			$('body.small-height-header').removeClass('small-height-header');
		}
	}, 100);
	if($('.productHeaderBlock, .headerblock, .header-slider-block').length){
		window.headerBlockScrollInterval = window.setInterval(function(){
			if(window.scrollY <  window.innerHeight){
				$('header#masthead:not(.over-headerblock)').addClass('over-headerblock');
			}
			if(window.scrollY >= window.innerHeight){
				$('header#masthead.over-headerblock').removeClass('over-headerblock');
			}
		}, 100);
	}

	$(document).on('click', '.expander', function(e){
		e.preventDefault();
		$(this).parent().toggleClass('expanded');
	});

	$(document).on('click', '.menu-toggle', function(e){
		e.preventDefault();
		$('#site-navigation').toggleClass('expanded');
	});

	$(document).on('click', '.projects-block .slider-nav button, .products-slider-block .slider-nav button', function(e){
		e.preventDefault();
		let s = $(this).parents('.slider').scrollLeft();
		if($(this).hasClass('left')){
			s -= $(window).width() / 3;
		}else{
			s += $(window).width() / 3;
		}
		$(this).parents('.slider').scrollLeft(s);
		if(s > 0){
			$(this).parents('.slider').addClass('scrolled');
		}else{
			$(this).parents('.slider').removeClass('scrolled')
		}
	});




	$(document).on('click', '.productHeaderBlock .image-col', function(e){
		e.preventDefault();
		if(!$('.image-popup').length){
			createImagePopup();
		}
		$('body').addClass('showOverlay');

		let imageIndex = $(e.target).parent().index();
		let imageHeight = $('.image-popup .image-col figure:first-child').outerHeight();
		let scrollPos = imageIndex * imageHeight;

		$('.image-popup .image-col').scrollTop(scrollPos);
	});

	$(document).on('keyup', function(e){
		if($('body').hasClass('showOverlay') && e.key === 'Escape'){
			$('body').removeClass('showOverlay');
		}
	});

	function createImagePopup(){
		let popup = $('<div class="image-popup">')
			.appendTo($('body'));
		$('.productHeaderBlock h1').clone().appendTo(popup);
		$('.productHeaderBlock .image-col').clone().appendTo(popup);
		
		let closeBtn = $('<button class="close-button">Close</button>').appendTo(popup);
		$(document).on('click', '.close-button', function(e){
			e.preventDefault();
			$('body').removeClass('showOverlay');
		});

		let scrollBtn = $('<button class="scroll-button">Click to scroll</button>').appendTo(popup);
		$(document).on('click', '.scroll-button', function(e){
			e.preventDefault();
			let currScroll = popup.find('.image-col').scrollTop();
			let imageHeight = popup.find('.image-col figure:first-child').outerHeight();
			popup.find('.image-col').scrollTop(currScroll + imageHeight);
		});

		$(document).on('click', '.image-popup .image-col', function(e){
			$(this).toggleClass('cover');
		});
	}

	if($('form.cart').length){
		/**
		 * Add-to-cart customization
		 */
		var variationPrice = 0;
		if(!variationPrice && typeof norhage_product_info !== 'undefined'){
			variationPrice = norhage_product_info.price;
		}
		
		add_addons_to_wc_variation_price(); // initial price correction

		$( '.single_variation' ).on( 'show_variation', function( event, variation ) {
			variationPrice = variation.display_price;
			add_addons_to_wc_variation_price();
		});

		$(document).on('change', 'form.cart .quantity input', function(e){
			e.preventDefault();
			add_addons_to_wc_variation_price();
		});

		function add_addons_to_wc_variation_price(){
			let newPrice = parseFloat(variationPrice);

			// first we need to calculate the unit price
			if($('.sizes_input').length){
				let width = parseFloat($('.sizes_input input[name="cutting_variables[width]"]').val()) / 1000;
				let height = parseFloat($('.sizes_input input[name="cutting_variables[height]"]').val()) / 1000;
				let cutting_fee = parseFloat($('.sizes_input input[name="cutting_variables[cutting_fee]"]').val());
				let currency = 'NOK';

				if(isNaN(width)){
					width = 1;
				}
				if(isNaN(height)){
					height = 1;
				}
				newPrice = cutting_fee + (width * height * variationPrice);

				if(typeof window.wcSettings.currency != 'undefined'){
					currency = window.wcSettings.currency.code;
				}
				if(currency == 'NOK' || currency == 'SEK'){
					// round prices in NOK and SEK
					newPrice = Math.round(newPrice);
				}
			}

			// then we add the addons
			$('.addon input.qty').each(function(){
				let quantity = parseInt($(this).val());
				let price = parseFloat($(this).data('price'));
				newPrice += (price * quantity);
			});

			let currencySymbol;
			let priceNode;
			if(typeof norhage_product_info !== 'undefined' && norhage_product_info.productType == 'simple'){
				currencySymbol = $('.simple_product_wrap > .price > .woocommerce-Price-amount > bdi .woocommerce-Price-currencySymbol, .simple_product_wrap > .price > ins > .woocommerce-Price-amount > bdi .woocommerce-Price-currencySymbol').first().clone();
				priceNode = $('.simple_product_wrap > .price > .woocommerce-Price-amount > bdi, .simple_product_wrap > .price > ins > .woocommerce-Price-amount > bdi');
			}else{
				currencySymbol = $('.woocommerce-variation-price .price .woocommerce-Price-amount > bdi .woocommerce-Price-currencySymbol, .woocommerce-variation-price .price .woocommerce-Price-amount > ins bdi .woocommerce-Price-currencySymbol').first().clone();
				priceNode = $('.woocommerce-variation-price .price .woocommerce-Price-amount bdi');
				if($('.woocommerce-variation-price .price del').length){
					priceNode = $('.woocommerce-variation-price .price ins .woocommerce-Price-amount bdi');
				}
			}
			//formattedPrice = newPrice.toFixed(2).replace('.', ',');
			formattedPrice = Number(newPrice).toLocaleString(
					wcSettings.locale.siteLocale.replace('_', '-'), 
					{style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2}
				)
				.replaceAll('\xa0', wcSettings.currency.thousandSeparator);
			priceNode.html(' ' + formattedPrice + ' ');
			
			
			if(wcSettings.currency.symbolPosition == 'left' || wcSettings.currency.symbolPosition == 'left_space'){
				priceNode.prepend(currencySymbol);
			}else if(wcSettings.currency.symbolPosition == 'right' || wcSettings.currency.symbolPosition == 'right_space'){
				priceNode.append(currencySymbol);
			}
		}
	}


	/**
	 * quantity stepper input
	 */
	$('.quantity:has(input)').each(function(){
		let plusBtn = $('<button class="qtyBtn plus">+</button>').appendTo($(this));
		let minBtn = $('<button class="qtyBtn min">-</button>').prependTo($(this));
	});

	$(document).on('click', '.quantity button', function(e) {
		e.preventDefault();
		if($(this).hasClass('plus')){
			$(this).buttonPlusMin('plus');
		}else{
			$(this).buttonPlusMin('min');
		}
	});

	$(document).on('change', '.quantity input[type="number"]', function(e){
		let numVal = parseInt($(this).val());
		let min = parseInt($(this).attr('min'));
		let max = parseInt($(this).attr('max'));
		console.log('numval = ' + numVal);
		if(min !== 'NaN' && numVal < min){
			numVal = min;
		}
		if(max !== 'NaN' && max > 0 && numVal > max){
			console.log(max);
			numVal = max;
		}
		$(this).val(numVal);

	});

	/**
	 * filters open and close btns
	 * */
	$(document).on('click', 'button.open-filters', function (e) {
		$('#filters-sidebar').toggleClass('open');
	});
	$(document).on('click', '#filters-sidebar .close-btn', function (e) {
		$('#filters-sidebar').removeClass('open');
	});

	if($('.header-slider-block').length){
		$('.header-slider-block').slick({
			autoplay: true,
			autoplaySpeed: 3000,
			arrows: false,
			pauseOnHover: false,
			vertical: true,
			responsive: [
				{
					breakpoint: 768,
					settings: {
						vertical: false
					}
				}
			]
		});
	}

	if($('.reviews-block .reviews').length){
		$('.reviews-block .reviews').slick({
			autoplay: true,
			autoplaySpeed: 2000,
			arrows: true,
			pauseOnHover: true,
			adaptiveHeight: false,
			responsive: [
				{
					breakpoint: 768,
					settings: {
						arrows: false
					}
				}
			]
		});
	}

	window.dataLayer = window.dataLayer || [];
	$(document).on('click', 'a[href^="mailto:"]', function(e){
		dataLayer.push({'event': 'click_email_link'});
	});
	$(document).on('click', 'a[href^="tel:"]', function(e){
		dataLayer.push({'event': 'click_phone_link'});
	});
	$(document).on('wpcf7mailsent', function(e){
		dataLayer.push({'event': 'contact_form_submit'});
	});

	$.fn.buttonPlusMin = function(action){
		let inputSibling = $(this).siblings('input');
		let currentValue = parseFloat(inputSibling.val());
		let stepSize = 1;

		if(inputSibling[0].hasAttribute('step')){
			stepSize = parseFloat( inputSibling.attr('step'));
		}

		if(action == 'plus'){
			currentValue += stepSize;
			if(inputSibling[0].hasAttribute('max') && inputSibling.attr('max') !== '' && currentValue > inputSibling.attr('max')){
				currentValue = parseFloat(inputSibling.attr('max'));
			}
		}else{
			currentValue -= stepSize;
			if(inputSibling[0].hasAttribute('min') && currentValue < inputSibling.attr('min')){
				currentValue = parseFloat(inputSibling.attr('min'));
			}
		}
		
		// to fix rounding errors with floating points
		if(stepSize < 1){
			currentValue = currentValue.toFixed(3); 
		}
		inputSibling.val(currentValue);
		inputSibling.trigger('change');
	}

})(jQuery);