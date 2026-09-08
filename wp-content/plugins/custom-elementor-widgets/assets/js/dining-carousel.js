/**
 * Dining, carousel.
 *
 * One slide is current and stands in the middle of the band, which is what puts
 * its neighbours half off either edge. The run is written three times, so there
 * is always a picture on both sides and the reader never reaches an end.
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

		if ( ! track || slides.length === 0 || ! stage || root.dataset.wired ) {
			return;
		}

		root.dataset.wired = '1';

		var many = parseInt( track.getAttribute( 'data-many' ), 10 ) || slides.length;
		var runs = Math.round( slides.length / many );

		var current = runs > 1 ? many : 0;
		var moving  = false;

		var gap    = 0;
		var narrow = 0;
		var wide   = 0;
		var spread = 0;

		// What a slide is worth is read with travel switched off. Asked while a
		// width is easing, the page answers with the width being passed through,
		// and the run is then centred on a number that was never true.
		function measure() {
			silently( function () {
				gap    = parseFloat( window.getComputedStyle( track ).columnGap ) || 0;
				wide   = slides[ current ].offsetWidth;
				narrow = slides.length > 1 ? slides[ current === 0 ? 1 : 0 ].offsetWidth : wide;
				spread = wide + ( ( slides.length - 1 ) * ( narrow + gap ) );
			} );
		}

		function mark() {
			for ( var i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.toggle( 'is-current', i === current );
			}
		}

		// Whatever is done inside is done at once and without travel: a width
		// that eases after the move has ended, or a run put back in view, is
		// what the eye reads as a stumble.
		var silence = 0;

		function silently( change ) {
			silence++;
			root.classList.add( 'is-settling' );

			change();

			silence--;

			if ( silence === 0 ) {
				// Read something back so everything done inside has taken hold
				// before travel is switched on again.
				void root.offsetWidth;
				root.classList.remove( 'is-settling' );
			}
		}

		function place() {
			var room  = stage.offsetWidth;
			var left  = ( ( room - spread ) / 2 ) + ( current * ( narrow + gap ) );
			var shift = left + ( wide / 2 ) - ( room / 2 );

			track.style.transform = 'translateX(' + ( -shift ) + 'px)';
		}

		// The run is brought back to the middle only once the move has finished.
		// Done on the way in, a jump and a slide share one breath and the two
		// together are what the eye reads as a stumble. The copies are identical,
		// so the return is never seen.
		function settle() {
			if ( ! moving ) {
				return;
			}

			window.clearTimeout( settle.timer );
			moving = false;

			if ( runs > 1 && ( current < many || current >= many * 2 ) ) {
				current += current < many ? many : -many;

				silently( function () {
					mark();
					place();
				} );
			}
		}

		track.addEventListener( 'transitionend', function ( event ) {
			if ( event.target === track && event.propertyName === 'transform' ) {
				settle();
			}
		} );

		function step( way ) {
			if ( moving ) {
				return;
			}

			if ( runs > 1 ) {
				current += way;
			} else {
				current = ( current + way + slides.length ) % slides.length;
			}

			mark();
			place();

			moving = true;

			// A move that is never drawn never reports itself finished, so the
			// press is handed back after the longest a move can take.
			window.clearTimeout( settle.timer );
			settle.timer = window.setTimeout( settle, 800 );
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.custom-dining-carousel__arrow--prev' ) ) {
				step( -1 );
			} else if ( event.target.closest( '.custom-dining-carousel__arrow--next' ) ) {
				step( 1 );
			}
		} );

		window.addEventListener( 'resize', function () {
			measure();
			silently( place );
		} );

		silently( function () {
			mark();
			measure();
			place();
		} );
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
