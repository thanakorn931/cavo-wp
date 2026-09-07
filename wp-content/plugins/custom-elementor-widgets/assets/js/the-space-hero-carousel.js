/**
 * The Space, hero carousel.
 *
 * One slide is current; the title and the two links above it, and the words in
 * the band below it, belong to that slide and change with it. The track is moved so the current slide sits in the middle of
 * the band, which is what puts its neighbours half off either edge.
 */
( function () {
	'use strict';

	function setUp( root ) {
		var track  = root.querySelector( '.custom-space-hero__track' );
		var slides = root.querySelectorAll( '.custom-space-hero__slide' );
		var stage  = root.querySelector( '.custom-space-hero__stage' );
		var title  = root.querySelector( '.custom-space-hero__title' );
		var words  = root.querySelector( '.custom-space-hero__words' );
		var tour   = root.querySelector( '.custom-space-hero__tour-link' );
		var host   = root.querySelector( '.custom-space-hero__button' );

		if ( ! track || slides.length === 0 || ! stage ) {
			return;
		}

		var current = 0;

		// One address and the two toggles that travel with it, the same three
		// the widget prints for the slide it starts on.
		function follow( link, slide, name ) {
			if ( ! link ) {
				return;
			}

			var address  = slide.getAttribute( 'data-' + name );
			var blank    = slide.getAttribute( 'data-' + name + '-blank' ) === 'yes';
			var nofollow = slide.getAttribute( 'data-' + name + '-nofollow' ) === 'yes';
			var rel      = [];

			if ( address ) {
				link.setAttribute( 'href', address );
			} else {
				link.removeAttribute( 'href' );
			}

			if ( blank ) {
				link.setAttribute( 'target', '_blank' );
				rel.push( 'noopener' );
				rel.push( 'noreferrer' );
			} else {
				link.removeAttribute( 'target' );
			}

			if ( nofollow ) {
				rel.push( 'nofollow' );
			}

			if ( rel.length ) {
				link.setAttribute( 'rel', rel.join( ' ' ) );
			} else {
				link.removeAttribute( 'rel' );
			}
		}

		function show( index ) {
			current = ( index + slides.length ) % slides.length;

			for ( var i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.toggle( 'is-current', i === current );
				slides[ i ].setAttribute( 'aria-hidden', i === current ? 'false' : 'true' );
			}

			// The widths change with the class, so the offset is read after.
			window.requestAnimationFrame( function () {
				var slide = slides[ current ];
				var shift = slide.offsetLeft + ( slide.offsetWidth / 2 ) - ( stage.offsetWidth / 2 );

				track.style.transform = 'translateX(' + ( -shift ) + 'px)';
			} );

			var slideTitle = slides[ current ].getAttribute( 'data-title' );

			if ( title && slideTitle ) {
				title.textContent = slideTitle;
			}

			if ( words ) {
				words.textContent = slides[ current ].getAttribute( 'data-words' ) || '';
			}

			// Both addresses belong to the slide, so both travel with it.
			follow( tour, slides[ current ], 'link' );
			follow( host, slides[ current ], 'host' );
		}

		root.addEventListener( 'click', function ( event ) {
			var previous = event.target.closest( '.custom-space-hero__arrow--prev' );
			var next     = event.target.closest( '.custom-space-hero__arrow--next' );

			if ( previous ) {
				show( current - 1 );
			} else if ( next ) {
				show( current + 1 );
			}
		} );

		window.addEventListener( 'resize', function () {
			show( current );
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-space-hero' );

		for ( var i = 0; i < roots.length; i++ ) {
			setUp( roots[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}

	// Elementor rebuilds a widget in the editor without reloading the page.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/the-space-hero-carousel.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-space-hero' ) || $scope[ 0 ] );
				}
			);
		}
	} );
}() );
