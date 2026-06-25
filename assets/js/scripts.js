/**
 * LSC Group — Main Theme Scripts
 * @package lsc-group
 */

( function ( $ ) {
	'use strict';

	$( function () {

		// ─────────────────────────────────────────────────────────────
		// HEADER OFFSET
		// Sets --header-offset so sticky-aware sections can position
		// themselves. Re-calculated on resize and after fonts load.
		// ─────────────────────────────────────────────────────────────

		const $header = $( '.site-header' );

		function updateHeaderOffset() {
			if ( ! $header.length ) return;
			document.documentElement.style.setProperty(
				'--header-offset',
				$header.outerHeight() + 'px'
			);
		}

		updateHeaderOffset();

		let resizeTimer;
		$( window ).on( 'resize', function () {
			clearTimeout( resizeTimer );
			resizeTimer = setTimeout( updateHeaderOffset, 100 );
		} );

		$( window ).on( 'load', function () {
			setTimeout( updateHeaderOffset, 200 );
		} );


		// ─────────────────────────────────────────────────────────────
		// HEADER SCROLL STATE
		// Adds .is-scrolled after 30px — CSS uses this for compact mode.
		// ─────────────────────────────────────────────────────────────

		if ( $header.length ) {
			const SCROLL_THRESHOLD = 30;

			function handleScroll() {
				$header.toggleClass( 'is-scrolled', $( window ).scrollTop() > SCROLL_THRESHOLD );
			}

			handleScroll();
			$( window ).on( 'scroll', handleScroll );
		}


		// ─────────────────────────────────────────────────────────────
		// MOBILE MENU
		// ─────────────────────────────────────────────────────────────

		const $toggle  = $( '.mobile-menu-toggle' );
		const $mobileNav = $( '.mobile-navigation' );
		const $overlay = $( '.mobile-menu-overlay' );
		const $close   = $( '.mobile-menu-close' );
		const $body    = $( 'body' );

		const FOCUSABLE = 'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])';

		function openMenu() {
			$mobileNav.addClass( 'is-active' );
			$overlay.addClass( 'is-active' );
			$toggle.addClass( 'is-open' ).attr( 'aria-expanded', 'true' );
			$mobileNav.attr( 'aria-hidden', 'false' );
			$body.addClass( 'no-scroll' );
			// Move focus to first focusable element inside panel
			$mobileNav.find( FOCUSABLE ).first().trigger( 'focus' );
		}

		function closeMenu() {
			$mobileNav.removeClass( 'is-active' );
			$overlay.removeClass( 'is-active' );
			$toggle.removeClass( 'is-open' ).attr( 'aria-expanded', 'false' );
			$mobileNav.attr( 'aria-hidden', 'true' );
			$body.removeClass( 'no-scroll' );
			$toggle.trigger( 'focus' );
		}

		function isMenuOpen() {
			return $mobileNav.hasClass( 'is-active' );
		}

		$toggle.on( 'click', function () {
			isMenuOpen() ? closeMenu() : openMenu();
		} );

		$close.on( 'click', closeMenu );
		$overlay.on( 'click', closeMenu );

		// Close when resizing back to desktop
		$( window ).on( 'resize', function () {
			if ( isMenuOpen() && $( window ).width() > 1199 ) {
				closeMenu();
			}
		} );

		// Keyboard: Escape closes; Tab traps focus inside panel
		$( document ).on( 'keydown', function ( e ) {
			if ( ! isMenuOpen() ) return;

			if ( e.key === 'Escape' ) {
				closeMenu();
				return;
			}

			if ( e.key === 'Tab' ) {
				const $focusable = $mobileNav.find( FOCUSABLE ).filter( ':visible' );
				const $first = $focusable.first();
				const $last  = $focusable.last();

				if ( e.shiftKey && $( document.activeElement ).is( $first ) ) {
					e.preventDefault();
					$last.trigger( 'focus' );
				} else if ( ! e.shiftKey && $( document.activeElement ).is( $last ) ) {
					e.preventDefault();
					$first.trigger( 'focus' );
				}
			}
		} );


		// ─────────────────────────────────────────────────────────────
		// MOBILE SUBMENU TOGGLES
		// Injects a chevron button next to each parent link and
		// handles expand / collapse with aria state.
		// ─────────────────────────────────────────────────────────────

		const chevronSVG = '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

		$( '.mobile-menu li.menu-item-has-children' ).each( function () {
			const $li      = $( this );
			const $submenu = $li.children( '.sub-menu' ).hide();
			const $btn     = $( '<button>', {
				class:           'submenu-toggle',
				'aria-expanded': 'false',
				'aria-label':    'Expand submenu',
				html:            chevronSVG,
			} );

			$li.children( 'a' ).after( $btn );

			$btn.on( 'click', function () {
				const isExpanded = $btn.attr( 'aria-expanded' ) === 'true';

				// Close all other open submenus
				$( '.mobile-menu li.menu-item-has-children' ).not( $li ).each( function () {
					$( this ).children( '.sub-menu' ).slideUp( 300 );
					$( this ).find( '.submenu-toggle' ).attr( 'aria-expanded', 'false' );
				} );

				$btn.attr( 'aria-expanded', String( ! isExpanded ) );
				$submenu.slideToggle( 300 );
			} );
		} );


		// ─────────────────────────────────────────────────────────────
		// DESKTOP NAV — KEYBOARD ACCESSIBILITY
		// Arrow keys navigate items; Escape closes open dropdowns.
		// ─────────────────────────────────────────────────────────────

		$( '.main-menu > li.menu-item-has-children > a' ).on( 'keydown', function ( e ) {
			if ( e.key !== 'ArrowDown' && e.key !== 'Enter' ) return;
			if ( e.key === 'Enter' && $( this ).attr( 'href' ) !== '#' ) return;

			e.preventDefault();
			const $submenu = $( this ).siblings( '.sub-menu' );
			$submenu.find( 'a' ).first().trigger( 'focus' );
		} );

		$( '.main-menu .sub-menu a' ).on( 'keydown', function ( e ) {
			const $links  = $( this ).closest( '.sub-menu' ).find( 'a' );
			const index   = $links.index( this );

			if ( e.key === 'ArrowDown' ) {
				e.preventDefault();
				$links.eq( index + 1 ).trigger( 'focus' );
			} else if ( e.key === 'ArrowUp' ) {
				e.preventDefault();
				if ( index === 0 ) {
					$( this ).closest( '.sub-menu' ).siblings( 'a' ).trigger( 'focus' );
				} else {
					$links.eq( index - 1 ).trigger( 'focus' );
				}
			} else if ( e.key === 'Escape' ) {
				$( this ).closest( '.sub-menu' ).siblings( 'a' ).trigger( 'focus' );
			}
		} );


		// ─────────────────────────────────────────────────────────────
		// BACK TO TOP
		// Shows after 400px scroll; hides when back at top.
		// ─────────────────────────────────────────────────────────────

		const $backToTop = $( '.back-to-top' );

		if ( $backToTop.length ) {
			$( window ).on( 'scroll.backToTop', function () {
				const visible = $( this ).scrollTop() > 400;
				$backToTop
					.toggleClass( 'is-visible', visible )
					.attr( 'aria-hidden', String( ! visible ) );
			} );

			$backToTop.on( 'click', function () {
				$( 'html, body' ).animate( { scrollTop: 0 }, 500, 'swing' );
				$( this ).trigger( 'blur' );
			} );
		}


		// ─────────────────────────────────────────────────────────────
		// SMOOTH SCROLL TO ANCHOR
		// Offset accounts for sticky header height.
		// ─────────────────────────────────────────────────────────────

		$( 'a[href^="#"]' ).on( 'click', function ( event ) {
			// Ignore programmatic triggers (e.g. WooCommerce activating its
			// description/reviews tabs via $(...).trigger('click') on load) —
			// only react to genuine user clicks on in-page nav links.
			if ( event.isTrigger ) return;

			const href = $( this ).attr( 'href' );
			if ( ! href || href === '#' || href === '#!' ) return;

			const $target = $( href );
			if ( ! $target.length ) return;

			event.preventDefault();

			const offset = $header.outerHeight() + 20 || 20;
			$( 'html, body' ).animate(
				{ scrollTop: $target.offset().top - offset },
				600,
				'swing'
			);
		} );

		// ─────────────────────────────────────────────────────────────
		// STAGE-PADDING CAROUSEL — TOGGLE TRIGGER CLASSES (MOBILE)
		// Elements opting in via .js-stage-padding get .stagePaddingRight
		// + .itemMargin (the slick spacing helpers in
		// lsc-group-slick-custom.css) below 768px, removed above —
		// these are what the carousel init below activates against.
		// ─────────────────────────────────────────────────────────────

		function toggleStagePaddingClasses() {
			const $elements = $( '.js-stage-padding' );
			if ( $( window ).width() <= 767 ) {
				$elements.addClass( 'stagePaddingRight itemMargin' );
			} else {
				$elements.removeClass( 'stagePaddingRight itemMargin' );
			}
		}

		toggleStagePaddingClasses();

		let stagePaddingTimer;
		$( window ).on( 'resize', function () {
			clearTimeout( stagePaddingTimer );
			stagePaddingTimer = setTimeout( toggleStagePaddingClasses, 100 );
		} );


		// ─────────────────────────────────────────────────────────────
		// STAGE-PADDING CAROUSEL — INIT (MOBILE ONLY)
		// Turns any .js-stage-padding grid into a single-slide Slick
		// carousel below 768px and un-slicks it back to a static grid
		// above that breakpoint. Cards inside (.card / .icon-card /
		// .product-card) are equal-heighted while the carousel runs.
		// Add grid-specific exclusions to the .not() list for any grid
		// that ships with its own carousel.
		// ─────────────────────────────────────────────────────────────

		function setEqualHeight() {
			if ( window.innerWidth < 768 ) {
				$( '.js-stage-padding' ).each( function () {
					const $carousel = $( this );
					const $cards    = $carousel.find( '.card, .icon-card, .product-card' );
					let maxHeight   = 0;

					$cards.css( 'height', '' );
					$cards.each( function () {
						maxHeight = Math.max( maxHeight, $( this ).outerHeight() );
					} );
					$cards.css( 'height', maxHeight + 'px' );
				} );
			} else {
				$( '.js-stage-padding .card, .js-stage-padding .icon-card, .js-stage-padding .product-card' ).css( 'height', '' );
			}
		}

		function initStagePaddingCarousel() {
			const $carousel = $( '.js-stage-padding' ).not( '.latest-news-grid, .related-products-grid, .logo-showcase-grid, .card-grid-carousel, .js-testimonials-carousel' );

			if ( ! $carousel.length ) return;

			if ( window.innerWidth < 768 ) {
				if ( ! $carousel.hasClass( 'slick-initialized' ) ) {
					$carousel.slick( {
						dots:           false,
						arrows:         false,
						infinite:       true,
						speed:          300,
						slidesToShow:   1,
						slidesToScroll: 1,
						adaptiveHeight: false,
						onSetPosition:  setEqualHeight,
					} );

					setTimeout( setEqualHeight, 100 );
				}
			} else if ( $carousel.hasClass( 'slick-initialized' ) ) {
				$carousel.slick( 'unslick' );
				$( '.js-stage-padding .card, .js-stage-padding .icon-card, .js-stage-padding .product-card' ).css( 'height', '' );
			}
		}

		setTimeout( initStagePaddingCarousel, 100 );

		let carouselResizeTimer;
		$( window ).on( 'resize', function () {
			clearTimeout( carouselResizeTimer );
			carouselResizeTimer = setTimeout( initStagePaddingCarousel, 250 );
		} );

		// ─────────────────────────────────────────────────────────────
		// TESTIMONIALS CAROUSEL
		// ─────────────────────────────────────────────────────────────

		$( '.js-testimonials-carousel' ).each( function () {
			const $carousel = $( this );

			if ( $carousel.hasClass( 'slick-initialized' ) ) return;

			const $section = $carousel.closest( '.testimonials-section__slider-wrap' );
			const updateFeaturedCard = function ( slick ) {
				const $slides       = $( slick.$slider ).find( '.slick-slide' );
				const $activeSlides = $slides.filter( '.slick-active' );

				$slides.find( '.testimonial-card' ).removeClass( 'is-featured' );

				if ( $activeSlides.length < 3 ) return;

				$activeSlides
					.eq( Math.floor( $activeSlides.length / 2 ) )
					.find( '.testimonial-card' )
					.addClass( 'is-featured' );
			};

			$carousel.on( 'init afterChange breakpoint setPosition', function ( event, slick ) {
				updateFeaturedCard( slick );
			} );

			$carousel.slick( {
				dots:           false,
				arrows:         true,
				infinite:       true,
				speed:          300,
				slidesToShow:   3,
				slidesToScroll: 1,
				prevArrow:      $section.find( '.testimonials-section__arrow--prev' ),
				nextArrow:      $section.find( '.testimonials-section__arrow--next' ),
				responsive: [
					{
						breakpoint: 992,
						settings: {
							slidesToShow: 2,
						},
					},
					{
						breakpoint: 768,
						settings: {
							slidesToShow: 1,
						},
					},
				],
			} );
		} );

		// ─────────────────────────────────────────────────────────────
		// PRODUCT HERO — FACTS OVERLAP
		// Pulls the .inner-hero__facts-wrap up over the hero by half the
		// rendered height of its .inner-hero__facts card, so the card
		// straddles the hero edge regardless of content/columns.
		// Recalculated on load and resize. Desktop only (>991px); on
		// mobile the inline margin is cleared so the CSS fallback wins.
		// ─────────────────────────────────────────────────────────────

		const $heroFactsWraps = $( '.inner-hero__facts-wrap' );

		function updateHeroFactsOverlap() {
			if ( ! $heroFactsWraps.length ) return;

			$heroFactsWraps.each( function () {
				const $wrap = $( this );
				const $card = $wrap.find( '.inner-hero__facts' );
				if ( ! $card.length ) return;

				if ( window.innerWidth <= 991 ) {
					$wrap.css( 'margin-top', '' );
					return;
				}

				$wrap.css( 'margin-top', ( -$card.outerHeight() / 2 ) + 'px' );
			} );
		}

		updateHeroFactsOverlap();

		let heroFactsResizeTimer;
		$( window ).on( 'resize', function () {
			clearTimeout( heroFactsResizeTimer );
			heroFactsResizeTimer = setTimeout( updateHeroFactsOverlap, 100 );
		} );

		$( window ).on( 'load', updateHeroFactsOverlap );

	} ); // end document.ready

} )( jQuery );


// ─────────────────────────────────────────────────────────────────
// FAQ ACCORDION
// jQuery slide-toggle; each item opens/closes independently.
// Isolated in its own ready handler so unrelated scripts can't block it.
// Icon swap (plus/minus) is driven by .is-open / aria-expanded (CSS),
// with a JS display fallback so it still works before that CSS lands.
// ─────────────────────────────────────────────────────────────────

( function ( $ ) {
	'use strict';

	$( function () {

		const $items = $( '.faqs__item' );
		if ( ! $items.length ) return;

		// Start fully closed regardless of any markup/CSS state.
		$items.each( function () {
			const $item   = $( this );
			const $button = $item.find( '.faqs__question' );
			const $panel  = $item.find( '.faqs__answer' );

			$item.removeClass( 'is-open' );
			$button.attr( 'aria-expanded', 'false' );
			$panel.removeAttr( 'hidden' ).hide();
			$item.find( '.faqs__icon-minus' ).hide();
		} );

		// Delegated so it survives any DOM re-render.
		$( document ).on( 'click', '.faqs__question', function ( e ) {
			e.preventDefault();

			const $button = $( this );
			const $item   = $button.closest( '.faqs__item' );
			const $panel  = $item.find( '.faqs__answer' );
			const isOpen  = $button.attr( 'aria-expanded' ) === 'true';

			$button.attr( 'aria-expanded', isOpen ? 'false' : 'true' );
			$item.toggleClass( 'is-open', ! isOpen );

			// Icon fallback (CSS may also handle this).
			$item.find( '.faqs__icon-plus' ).toggle( isOpen );
			$item.find( '.faqs__icon-minus' ).toggle( ! isOpen );

			$panel.stop( true, true ).slideToggle( 300 );
		} );

	} );

} )( jQuery );


// ─────────────────────────────────────────────────────────────────
// VIDEO AUTOPLAY ON SCROLL
// Plays/pauses .autoplay-video containers on intersection.
// Kept outside jQuery wrapper — no jQuery dependency needed.
// ─────────────────────────────────────────────────────────────────

document.addEventListener( 'DOMContentLoaded', function () {
	const containers = document.querySelectorAll( '.autoplay-video' );
	if ( ! containers.length ) return;

	const observer = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			const video = entry.target.querySelector( 'video' );
			if ( ! video ) return;
			if ( entry.isIntersecting ) {
				video.currentTime = 0;
				video.play().catch( function () {} );
			} else {
				video.pause();
			}
		} );
	}, { threshold: 0.5 } );

	containers.forEach( function ( el ) {
		observer.observe( el );
	} );
} );
