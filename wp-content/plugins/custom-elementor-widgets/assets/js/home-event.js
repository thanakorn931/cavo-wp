/**
 * Home, events.
 *
 * The cards run off the right edge of the band. An arrow moves them one card,
 * and stops where there is nothing further that way.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var track = root.querySelector( '.custom-home-event__track' );
		var rail  = root.querySelector( '.custom-home-event__rail' );
		var items = root.querySelectorAll( '.custom-home-event__item' );
		var prev  = root.querySelector( '.custom-home-event__arrow--prev' );
		var next  = root.querySelector( '.custom-home-event__arrow--next' );

		if ( ! track || ! rail || items.length === 0 ) {
			return;
		}

		root.dataset.wired = '1';

		var current = 0;

		function step() {
			// One card and the gap beside it, read rather than written down.
			return items.length > 1
				? items[ 1 ].offsetLeft - items[ 0 ].offsetLeft
				: items[ 0 ].offsetWidth;
		}

		function last() {
			var over = track.scrollWidth - rail.offsetWidth;

			return over > 0 ? Math.ceil( over / step() ) : 0;
		}

		function show( index ) {
			current = Math.max( 0, Math.min( last(), index ) );

			track.style.transform = 'translateX(' + ( -current * step() ) + 'px)';

			if ( prev ) {
				prev.disabled = current === 0;
			}

			if ( next ) {
				next.disabled = current === last();
			}

			rail.classList.toggle( 'is-pullable', last() > 0 );
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.custom-home-event__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-home-event__arrow--next' ) ) {
				show( current + 1 );
			}
		} );

		// The row goes where a hand takes it as well as where an arrow sends it:
		// pressed and pulled, it follows, and settles on the card the pull
		// reached.
		var PULL    = 24;
		var holding = false;
		var moved   = false;
		var startX  = 0;
		var startAt = 0;

		// Past either end the row gives half of what the hand asks, so the end
		// of the row is felt rather than struck.
		function give( to ) {
			var far = -last() * step();

			if ( to > 0 ) {
				return to / 2;
			}

			return to < far ? far + ( to - far ) / 2 : to;
		}

		// The hold is not taken from the card under the hand: a pointer held by
		// the row would have the press that ends on a card answered by the row
		// instead, and the card would never open. The window says where the
		// hand goes and when it lets go, wherever it is by then.
		rail.addEventListener( 'pointerdown', function ( event ) {
			if ( last() === 0 || event.button ) {
				return;
			}

			holding = true;
			moved   = false;
			startX  = event.clientX;
			startAt = -current * step();
		} );

		// The row follows the hand only once the hand has gone somewhere: a
		// press that barely moves is a press, and the card under it answers.
		window.addEventListener( 'pointermove', function ( event ) {
			if ( ! holding ) {
				return;
			}

			var pulled = event.clientX - startX;

			if ( ! moved && Math.abs( pulled ) > 4 ) {
				moved = true;
				track.style.transition = 'none';
				rail.classList.add( 'is-holding' );
			}

			if ( ! moved ) {
				return;
			}

			event.preventDefault();
			track.style.transform = 'translateX(' + give( startAt + pulled ) + 'px)';
		} );

		[ 'pointerup', 'pointercancel' ].forEach( function ( name ) {
			window.addEventListener( name, function ( event ) {
				if ( ! holding ) {
					return;
				}

				holding = false;
				track.style.transition = '';
				rail.classList.remove( 'is-holding' );

				var pulled = 'pointercancel' === name ? 0 : event.clientX - startX;
				var lands  = Math.round( -( startAt + pulled ) / step() );

				// A pull too small to carry a whole card still counts as the
				// card it was reaching for.
				if ( lands === current && Math.abs( pulled ) >= PULL ) {
					lands += pulled < 0 ? 1 : -1;
				}

				show( lands );
			} );
		} );

		// The hand let go over a card, but what it did was move the row.
		rail.addEventListener( 'click', function ( event ) {
			if ( moved ) {
				moved = false;
				event.preventDefault();
				event.stopPropagation();
			}
		}, true );

		// A picture is part of the row here, not something to carry off it.
		rail.addEventListener( 'dragstart', function ( event ) {
			event.preventDefault();
		} );

		window.addEventListener( 'resize', function () {
			show( current );
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-home-event' );

		for ( var i = 0; i < roots.length; i++ ) {
			setUp( roots[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}

	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/home-event.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-home-event' ) );
				}
			);
		}
	} );
}() );
