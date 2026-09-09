/**
 * The button both event sections use to bring more of the list on screen.
 *
 * The items are already on the page, waiting; what the button waits for is
 * their pictures, which are not fetched until the item is shown. While it
 * waits it keeps its shape and loses its words, so nothing under it moves.
 */
( function () {
	'use strict';

	function whenLoaded( cards, done ) {
		var images = [];
		var i;
		var j;

		for ( i = 0; i < cards.length; i++ ) {
			var found = cards[ i ].querySelectorAll( 'img' );

			for ( j = 0; j < found.length; j++ ) {
				images.push( found[ j ] );
			}
		}

		var left = images.length;

		if ( ! left ) {
			done();
			return;
		}

		function step() {
			left--;

			if ( left <= 0 ) {
				done();
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
		if ( ! root || root.dataset.wired ) {
			return;
		}

		root.dataset.wired = '1';

		var button = root.querySelector( '.custom-event-more' );
		var cards  = root.querySelectorAll( '.custom-event-card' );

		// On the wide screen the newest item stands across the top on its own
		// and the count is of the grid beneath it; on the narrow screens it
		// stands in the grid like the rest and is counted with them.
		var lead     = root.querySelector( '.custom-event-list__lead' ) ? 1 : 0;
		var revealed = 0;
		var pressed  = false;

		// How many cards stand on this screen before anything is pressed.
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

	function start() {
		var roots = document.querySelectorAll( '[data-event-feed]' );

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
			[ 'event-events', 'event-past-events' ].forEach( function ( name ) {
				window.elementorFrontend.hooks.addAction(
					'frontend/element_ready/' + name + '.default',
					function ( $scope ) {
						setUp( $scope[ 0 ].querySelector( '[data-event-feed]' ) );
					}
				);
			} );
		}
	} );
}() );
