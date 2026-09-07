/**
 * Private events, virtual tour.
 *
 * The film plays itself. A reader who has asked their system for less movement
 * is given the first frame and nothing else, and is given it back the moment
 * they change their mind.
 */
( function () {
	'use strict';

	var STILL = '( prefers-reduced-motion: reduce )';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var film = root.querySelector( '.custom-private-tour__media video' );

		if ( ! film || ! window.matchMedia ) {
			return;
		}

		root.dataset.wired = '1';

		var asked = window.matchMedia( STILL );

		function settle() {
			if ( asked.matches ) {
				film.removeAttribute( 'autoplay' );
				film.pause();

				return;
			}

			film.setAttribute( 'autoplay', '' );

			// A browser that refuses the press has its own reasons; the film
			// simply stays where it is.
			var started = film.play();

			if ( started && started.catch ) {
				started.catch( function () {} );
			}
		}

		if ( asked.addEventListener ) {
			asked.addEventListener( 'change', settle );
		} else if ( asked.addListener ) {
			asked.addListener( settle );
		}

		settle();
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
