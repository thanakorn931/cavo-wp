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

	function setUp( root ) {
		if ( ! root ) {
			return;
		}

		var button = root.querySelector( '.custom-event-more' );

		if ( ! button || button.dataset.wired ) {
			return;
		}

		button.dataset.wired = '1';

		// A step of nothing means everything that is left, at once.
		var step = parseInt( button.getAttribute( 'data-step' ), 10 ) || 0;

		button.addEventListener( 'click', function () {
			var waiting = root.querySelectorAll( '.custom-event-card.is-waiting' );

			if ( ! waiting.length ) {
				button.hidden = true;
				return;
			}

			var batch = Array.prototype.slice.call( waiting, 0, step > 0 ? step : waiting.length );

			button.classList.add( 'is-working' );
			button.disabled = true;

			for ( var i = 0; i < batch.length; i++ ) {
				batch[ i ].classList.remove( 'is-waiting' );
				batch[ i ].hidden = false;
			}

			whenLoaded( batch, function () {
				button.classList.remove( 'is-working' );
				button.disabled = false;

				if ( ! root.querySelector( '.custom-event-card.is-waiting' ) ) {
					button.hidden = true;
				}
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
