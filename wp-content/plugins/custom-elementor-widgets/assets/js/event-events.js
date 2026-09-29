/**
 * Event, the list of what is coming.
 *
 * The items picked out stand one at a time across the band. With more than one
 * they turn every five seconds, running round, and slide from one to the next;
 * the two arrows step through them, and the row can be pulled by hand on every
 * screen (the shared pull, base-widget.js). The turning waits while the reader
 * is on the row — a hand over it, or a key in it — and does not happen at all
 * for a reader whose system has asked for less movement, or while the page is
 * not being looked at.
 */
( function () {
	'use strict';

	// How long an item stands before the row turns of its own accord.
	var TURN = 5000;

	// How far a pull goes before it turns the row, as an arrow would.
	var PULL = 40;

	function still() {
		return !! ( window.matchMedia && window.matchMedia( '( prefers-reduced-motion: reduce )' ).matches );
	}

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var leads = root.querySelectorAll( '.custom-event-list__lead' );

		if ( leads.length < 2 ) {
			return;
		}

		root.dataset.wired = '1';

		var at    = 0;
		var timer = 0;
		var held  = false;

		function show( index ) {
			// The row runs round: past the last is the first again.
			at = ( index + leads.length ) % leads.length;

			root.style.setProperty( '--custom-highlights-at', at );

			for ( var i = 0; i < leads.length; i++ ) {
				if ( i === at ) {
					leads[ i ].removeAttribute( 'aria-hidden' );
				} else {
					leads[ i ].setAttribute( 'aria-hidden', 'true' );
				}
			}
		}

		function wait() {
			window.clearTimeout( timer );

			if ( held || still() || document.hidden ) {
				return;
			}

			timer = window.setTimeout( function () {
				show( at + 1 );
				wait();
			}, TURN );
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '[data-highlights-prev]' ) ) {
				show( at - 1 );
				wait();
			} else if ( event.target.closest( '[data-highlights-next]' ) ) {
				show( at + 1 );
				wait();
			}
		} );

		// Pulled by hand, the row follows the hand, and a pull long enough turns
		// it one item, as an arrow does; a shorter one lets it back.
		root.classList.add( 'custom-pull', 'is-pullable' );

		root.addEventListener( 'custom-pull', function ( event ) {
			var by = event.detail.by;

			if ( 'move' === event.detail.phase ) {
				root.style.setProperty( '--custom-highlights-pull', by + 'px' );

				return;
			}

			if ( 'end' === event.detail.phase ) {
				root.style.removeProperty( '--custom-highlights-pull' );

				if ( Math.abs( by ) >= PULL ) {
					show( at + ( by < 0 ? 1 : -1 ) );
				}

				wait();
			}
		} );

		// The row holds still while the reader is on it, and takes up the turn
		// again once they have left.
		[ 'pointerenter', 'focusin' ].forEach( function ( name ) {
			root.addEventListener( name, function () {
				held = true;
				window.clearTimeout( timer );
			} );
		} );

		[ 'pointerleave', 'focusout' ].forEach( function ( name ) {
			root.addEventListener( name, function () {
				held = false;
				wait();
			} );
		} );

		document.addEventListener( 'visibilitychange', wait );

		show( 0 );
		wait();
	}

	function start( scope ) {
		var rows = ( scope || document ).querySelectorAll( '[data-highlights]' );

		for ( var i = 0; i < rows.length; i++ ) {
			setUp( rows[ i ] );
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
				'frontend/element_ready/event-events.default',
				function ( $scope ) {
					start( $scope[ 0 ] );
				}
			);
		}
	} );
}() );
