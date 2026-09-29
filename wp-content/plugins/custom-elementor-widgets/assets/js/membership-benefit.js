/**
 * Membership, benefits.
 *
 * One benefit is on show at a time — its picture, its words and the picture
 * beside them turn together. The list runs round. The small picture is the
 * next benefit's, and pressed, goes to it; only the large picture is pulled.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var slides = root.querySelectorAll( '.custom-membership-benefit__slide' );

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
			if ( event.target.closest( '.custom-membership-benefit__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-membership-benefit__arrow--next' ) || ( slides.length > 1 && event.target.closest( '.custom-membership-benefit__thumb' ) ) ) {
				show( current + 1 );
			}
		} );

		// How far a pull goes before it turns the row, as an arrow would.
		var TURN = 40;

		// Only the large picture is pulled: the words and the small picture
		// beside them are pressed, never pulled. Pulled sideways, the benefits
		// turn as the arrows turn them.
		var pulls = root.querySelectorAll( '.custom-membership-benefit__stage' );

		function pulled( event ) {
			var by = event.detail.by;

			if ( 'end' === event.detail.phase && Math.abs( by ) >= TURN ) {
				show( current + ( by < 0 ? 1 : -1 ) );
			}
		}

		for ( var p = 0; p < pulls.length; p++ ) {
			pulls[ p ].classList.add( 'custom-pull' );
			pulls[ p ].classList.toggle( 'is-pullable', slides.length > 1 );
			pulls[ p ].addEventListener( 'custom-pull', pulled );
		}

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-membership-benefit' );

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
				'frontend/element_ready/membership-benefit.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-membership-benefit' ) );
				}
			);
		}
	} );
}() );
