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


/**
 * A list that comes on screen a few at a time.
 *
 * Every item is on the page from the start. What a press brings is not the
 * items but their pictures, which are not fetched until the item is shown, so
 * the page carries a long list without carrying its weight. While the button
 * waits on them it keeps its shape and loses its words, and nothing under it
 * moves.
 *
 * The section says how many stand before the button is pressed and how many
 * more each press brings, one count for each of the three screens.
 */
( function () {
	'use strict';

	// The button waits on the pictures of what it has just brought, but never
	// past this: one the browser has put off until the reader nears it may not
	// come at all, and a button that waits for it waits for ever.
	var PATIENCE = 3000;

	function whenLoaded( cards, done ) {
		var images = [];
		var over   = false;
		var i;
		var j;

		function finish() {
			if ( over ) {
				return;
			}

			over = true;
			done();
		}

		window.setTimeout( finish, PATIENCE );

		for ( i = 0; i < cards.length; i++ ) {
			var found = cards[ i ].querySelectorAll( 'img' );

			for ( j = 0; j < found.length; j++ ) {
				images.push( found[ j ] );
			}
		}

		var left = images.length;

		if ( ! left ) {
			finish();
			return;
		}

		function step() {
			left--;

			if ( left <= 0 ) {
				finish();
			}
		}

		for ( i = 0; i < images.length; i++ ) {
			if ( images[ i ].complete ) {
				step();
			} else {
				images[ i ].addEventListener( 'load', step );
				images[ i ].addEventListener( 'error', step );
			}
		}
	}

	// Which of the three screens this is. The counts are the section's, one
	// for each screen, and the script reads the one for the screen it is on.
	function tier() {
		if ( window.matchMedia( '(max-width: 767px)' ).matches ) {
			return 'mobile';
		}

		if ( window.matchMedia( '(max-width: 1024px)' ).matches ) {
			return 'tablet';
		}

		return 'desktop';
	}

	function count( root, name ) {
		var which = tier();
		var value = root.getAttribute( 'data-' + name + ( 'desktop' === which ? '' : '-' + which ) );

		if ( null === value ) {
			value = root.getAttribute( 'data-' + name );
		}

		return parseInt( value, 10 ) || 0;
	}

	function setUp( root ) {
		if ( ! root || root.dataset.feedWired ) {
			return;
		}

		root.dataset.feedWired = '1';

		var button = root.querySelector( '[data-feed-more]' );
		var cards  = root.querySelectorAll( '[data-feed-card]' );

		// A section may stand its first item apart on the wide screen — the
		// newest, across the band — and the count is of the rest beneath it. On
		// the narrow screens it stands among them and is counted with them.
		var lead     = root.querySelector( '[data-feed-lead]' ) ? 1 : 0;
		var revealed = 0;
		var pressed  = false;

		// How many stand on this screen before anything is pressed.
		function opening() {
			return Math.max( 0, count( root, 'shown' ) - ( 'desktop' === tier() ? 0 : lead ) );
		}

		function apply() {
			for ( var i = 0; i < cards.length; i++ ) {
				var shown = i < revealed;

				cards[ i ].hidden = ! shown;
				cards[ i ].classList.toggle( 'is-shown', shown );
				cards[ i ].classList.toggle( 'is-waiting', ! shown );
			}

			if ( button ) {
				button.hidden = revealed >= cards.length;
			}
		}

		revealed = Math.min( cards.length, opening() );
		apply();

		// A screen that changes width changes how many stand; what a press has
		// already brought on screen is not taken back.
		var was = tier();

		window.addEventListener( 'resize', function () {
			var now = tier();

			if ( now === was ) {
				return;
			}

			was      = now;
			revealed = Math.min( cards.length, pressed ? Math.max( revealed, opening() ) : opening() );
			apply();
		} );

		if ( ! button ) {
			return;
		}

		button.addEventListener( 'click', function () {
			// A step of nothing means everything that is left, at once.
			var step  = count( root, 'step' );
			var batch = Array.prototype.slice.call( cards, revealed, step > 0 ? revealed + step : cards.length );

			if ( ! batch.length ) {
				button.hidden = true;
				return;
			}

			pressed  = true;
			revealed = Math.min( cards.length, revealed + batch.length );

			button.classList.add( 'is-working' );
			button.disabled = true;

			apply();

			whenLoaded( batch, function () {
				button.classList.remove( 'is-working' );
				button.disabled = false;
			} );
		} );
	}

	function start( root ) {
		var roots = ( root || document ).querySelectorAll( '[data-feed]' );

		for ( var i = 0; i < roots.length; i++ ) {
			setUp( roots[ i ] );
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
