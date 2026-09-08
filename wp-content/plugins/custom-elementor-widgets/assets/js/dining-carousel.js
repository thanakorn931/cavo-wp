/**
 * Dining, carousel.
 *
 * One slide is current. The track is moved so that slide sits in the middle of
 * the band, which is what puts its neighbours half off either edge.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root ) {
			return;
		}

		var track  = root.querySelector( '.custom-dining-carousel__track' );
		var slides = root.querySelectorAll( '.custom-dining-carousel__slide' );
		var stage  = root.querySelector( '.custom-dining-carousel__stage' );

		if ( ! track || slides.length === 0 || ! stage ) {
			return;
		}

		// Three copies of the run where there is more than one slide, so the
		// reader is always a step from the edge and never reaches it.
		var many    = parseInt( track.getAttribute( 'data-many' ), 10 ) || slides.length;
		var runs    = slides.length / many;
		var current = runs > 1 ? many : 0;

		// A press landing while the last one is still travelling replaces the move
		// in flight, and the easing begins again from wherever the track has
		// reached. The press is let go instead.
		var moving = false;

		function settle() {
			if ( ! moving ) {
				return;
			}

			window.clearTimeout( settle.timer );
			moving = false;

			if ( runs > 1 && ( current < many || current >= many * 2 ) ) {
				current += current < many ? many : -many;

				mark();
				replace( true );
			}
		}

		track.addEventListener( 'transitionend', function ( event ) {
			if ( event.target === track && event.propertyName === 'transform' ) {
				settle();
			}
		} );

		function replace( quiet ) {
			var slide = slides[ current ];
			var shift = slide.offsetLeft + ( slide.offsetWidth / 2 ) - ( stage.offsetWidth / 2 );

			if ( quiet ) {
				root.classList.add( 'is-settling' );
			}

			track.style.transform = 'translateX(' + ( -shift ) + 'px)';

			if ( quiet ) {
				void root.offsetWidth;
				root.classList.remove( 'is-settling' );
			}
		}

		function mark() {
			for ( var i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.toggle( 'is-current', i === current );
			}
		}

		function show( index ) {
			current = runs > 1 ? index : ( index + slides.length ) % slides.length;

			mark();

			// The widths change with the class, so the offset is read after.
			window.requestAnimationFrame( function () {
				replace( false );
			} );
		}

		root.addEventListener( 'click', function ( event ) {
			var previous = event.target.closest( '.custom-dining-carousel__arrow--prev' );
			var next     = event.target.closest( '.custom-dining-carousel__arrow--next' );

			if ( ! previous && ! next ) {
				return;
			}

			if ( moving ) {
				return;
			}

			moving = true;

			// A move that is never drawn never reports itself finished.
			window.clearTimeout( settle.timer );
			settle.timer = window.setTimeout( settle, 800 );

			show( previous ? current - 1 : current + 1 );
		} );

		window.addEventListener( 'resize', function () {
			settle();
			show( current );
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-dining-carousel' );

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
				'frontend/element_ready/dining-carousel.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-dining-carousel' ) );
				}
			);
		}
	} );
}() );
