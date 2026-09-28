/**
 * Nightlife, weekly program.
 *
 * One ticket is on show at a time. The arrows turn to the next and to the one
 * before, and the list runs round.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var slides = root.querySelectorAll( '.custom-nightlife-program__slide' );

		if ( slides.length === 0 ) {
			return;
		}

		root.dataset.wired = '1';

		var current = 0;

		function show( index ) {
			current = ( index + slides.length ) % slides.length;

			for ( var i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.toggle( 'is-current', i === current );
				slides[ i ].setAttribute( 'aria-hidden', i === current ? 'false' : 'true' );
			}
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.custom-nightlife-program__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-nightlife-program__arrow--next' ) ) {
				show( current + 1 );
			}
		} );

		// How far a pull goes before it turns the row, as an arrow would.
		var TURN = 40;

		// Pulled sideways, the tickets turn as the arrows turn them.
		var pull = root.querySelector( '.custom-nightlife-program__card' ) || root;

		pull.classList.add( 'custom-pull' );
		pull.classList.toggle( 'is-pullable', slides.length > 1 );

		pull.addEventListener( 'custom-pull', function ( event ) {
			var by = event.detail.by;

			if ( 'end' === event.detail.phase && Math.abs( by ) >= TURN ) {
				show( current + ( by < 0 ? 1 : -1 ) );
			}
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-nightlife-program' );

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
				'frontend/element_ready/nightlife-program.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-nightlife-program' ) );
				}
			);
		}
	} );
}() );
