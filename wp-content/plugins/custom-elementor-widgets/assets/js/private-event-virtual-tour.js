/**
 * Private events, virtual tour.
 *
 * The mark in the middle is a control only where there is a film behind it.
 * Over a picture it is what the design draws and nothing more.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var film = root.querySelector( '.custom-private-tour__media video' );
		var play = root.querySelector( '.custom-private-tour__play' );

		if ( ! film || ! play ) {
			return;
		}

		root.dataset.wired = '1';

		play.addEventListener( 'click', function () {
			film.play();
		} );

		film.addEventListener( 'play', function () {
			play.hidden = true;
			film.setAttribute( 'controls', 'controls' );
		} );

		film.addEventListener( 'pause', function () {
			play.hidden = false;
		} );

		film.addEventListener( 'ended', function () {
			play.hidden = false;
		} );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-private-tour' );

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
				'frontend/element_ready/private-event-virtual-tour.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-private-tour' ) );
				}
			);
		}
	} );
}() );
