/**
 * Every video a media slot prints.
 *
 * A video standing where a picture would plays itself, and holds still for a
 * reader whose system has asked for less movement. CSS cannot stop a loop, so
 * the asking is answered here — and answered again the moment they change
 * their mind.
 *
 * It arrives without its file. One in the first screen is handed it once the
 * page has finished loading; one further down, once the reader nears it —
 * the page is never kept waiting on a film.
 */
( function () {
	'use strict';

	var STILL = '( prefers-reduced-motion: reduce )';
	var asked = null;

	function settle( film ) {
		// Not handed its file yet: there is nothing to play or to hold.
		if ( ! asked || film.hasAttribute( 'data-src' ) ) {
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

	// The file is handed over, and the video plays or holds as asked.
	function arrive( film ) {
		if ( ! film.hasAttribute( 'data-src' ) ) {
			return;
		}

		film.preload = 'auto';
		film.src     = film.getAttribute( 'data-src' );
		film.removeAttribute( 'data-src' );

		settle( film );
	}

	// When: at once for one in the first screen, the page having loaded; for
	// one further down, as the reader comes within a screen of it.
	function when( film ) {
		if ( 'near' === film.getAttribute( 'data-custom-wait' ) && 'IntersectionObserver' in window ) {
			var near = new window.IntersectionObserver( function ( entries ) {
				for ( var i = 0; i < entries.length; i++ ) {
					if ( entries[ i ].isIntersecting ) {
						near.disconnect();
						arrive( film );

						return;
					}
				}
			}, { rootMargin: '100% 0px' } );

			near.observe( film );

			return;
		}

		arrive( film );
	}

	function watch( film ) {
		if ( ! film || film.dataset.wired ) {
			return;
		}

		film.dataset.wired = '1';

		if ( ! film.hasAttribute( 'data-src' ) ) {
			settle( film );

			return;
		}

		if ( 'complete' === document.readyState ) {
			when( film );
		} else {
			window.addEventListener( 'load', function () {
				when( film );
			} );
		}
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
