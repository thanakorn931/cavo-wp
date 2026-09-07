/**
 * Every video a media slot prints.
 *
 * A video standing where a picture would plays itself, and holds still for a
 * reader whose system has asked for less movement. CSS cannot stop a loop, so
 * the asking is answered here — and answered again the moment they change
 * their mind.
 */
( function () {
	'use strict';

	var STILL = '( prefers-reduced-motion: reduce )';
	var asked = null;

	function settle( film ) {
		if ( ! asked ) {
			return;
		}

		if ( asked.matches ) {
			film.removeAttribute( 'autoplay' );
			film.pause();

			return;
		}

		film.setAttribute( 'autoplay', '' );

		// A browser that refuses the press has its own reasons; the video
		// simply stays where it is.
		var started = film.play();

		if ( started && started.catch ) {
			started.catch( function () {} );
		}
	}

	function watch( film ) {
		if ( ! film || film.dataset.wired ) {
			return;
		}

		film.dataset.wired = '1';

		settle( film );
	}

	function start( root ) {
		if ( ! window.matchMedia ) {
			return;
		}

		if ( ! asked ) {
			asked = window.matchMedia( STILL );

			function again() {
				var all = document.querySelectorAll( 'video[data-custom-plays]' );

				for ( var i = 0; i < all.length; i++ ) {
					settle( all[ i ] );
				}
			}

			if ( asked.addEventListener ) {
				asked.addEventListener( 'change', again );
			} else if ( asked.addListener ) {
				asked.addListener( again );
			}
		}

		var films = ( root || document ).querySelectorAll( 'video[data-custom-plays]' );

		for ( var i = 0; i < films.length; i++ ) {
			watch( films[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			start();
		} );
	} else {
		start();
	}

	// Elementor rebuilds a widget in the editor without reloading the page.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/global',
				function ( $scope ) {
					start( $scope[ 0 ] );
				}
			);
		}
	} );
}() );
