/**
 * Dining, food menu.
 *
 * With more than one picture, one shows at a time and the next takes its place
 * every five seconds, running round. A dot for each says which is showing, and
 * pressed, goes to its picture and starts the five seconds again. The pictures
 * can be pulled sideways too, by mouse, finger or pen, on every tier: a pull
 * to the left brings the next, to the right the one before, and the five
 * seconds start again.
 */
( function () {
	'use strict';

	var EVERY = 5000;

	// A reader whose system has asked for less movement is not moved on; the
	// dots still take them to any picture they choose.
	function still() {
		return !! ( window.matchMedia && window.matchMedia( '( prefers-reduced-motion: reduce )' ).matches );
	}

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var slides = root.querySelectorAll( '.custom-dining-menu__slide' );
		var dots   = root.querySelectorAll( '.custom-dining-menu__dot' );

		if ( slides.length < 2 ) {
			return;
		}

		root.dataset.wired = '1';

		var current = 0;
		var timer   = null;

		function show( index ) {
			current = ( index + slides.length ) % slides.length;

			for ( var i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.toggle( 'is-current', i === current );

				if ( i === current ) {
					slides[ i ].removeAttribute( 'aria-hidden' );
				} else {
					slides[ i ].setAttribute( 'aria-hidden', 'true' );
				}
			}

			for ( var j = 0; j < dots.length; j++ ) {
				dots[ j ].classList.toggle( 'is-current', j === current );

				if ( j === current ) {
					dots[ j ].setAttribute( 'aria-current', 'true' );
				} else {
					dots[ j ].removeAttribute( 'aria-current' );
				}
			}
		}

		function run() {
			window.clearInterval( timer );

			if ( still() ) {
				return;
			}

			timer = window.setInterval( function () {
				show( current + 1 );
			}, EVERY );
		}

		// Pulled far enough, the gallery turns one picture; a shorter pull is
		// let go. The shared pull (base-widget.js) tells it how far.
		var TURN    = 40;
		var gallery = root.querySelector( '.custom-dining-menu__gallery' );

		if ( gallery ) {
			gallery.classList.add( 'custom-pull', 'is-pullable' );

			gallery.addEventListener( 'custom-pull', function ( event ) {
				if ( 'end' !== event.detail.phase || Math.abs( event.detail.by ) < TURN ) {
					return;
				}

				show( current + ( event.detail.by < 0 ? 1 : -1 ) );
				run();
			} );
		}

		root.addEventListener( 'click', function ( event ) {
			var dot = event.target.closest( '.custom-dining-menu__dot' );

			if ( ! dot ) {
				return;
			}

			show( parseInt( dot.getAttribute( 'data-index' ), 10 ) || 0 );
			run();
		} );

		show( 0 );
		run();
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-dining-menu' );

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
				'frontend/element_ready/dining-food-menu.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-dining-menu' ) );
				}
			);
		}
	} );
}() );
