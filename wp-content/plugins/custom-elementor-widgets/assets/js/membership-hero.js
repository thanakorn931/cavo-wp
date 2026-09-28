/**
 * Membership, the plans.
 *
 * The row moves one plan at a time and stops at either end, so the last plan
 * comes fully onto the band rather than running past it.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var track = root.querySelector( '.custom-membership-hero__track' );
		var cards = root.querySelectorAll( '.custom-membership-hero__card' );
		var prev  = root.querySelector( '.custom-membership-hero__arrow--prev' );
		var next  = root.querySelector( '.custom-membership-hero__arrow--next' );

		if ( ! track || cards.length === 0 ) {
			return;
		}

		root.dataset.wired = '1';

		var pull = root.querySelector( '.custom-membership-hero__stage' ) || root;

		pull.classList.add( 'custom-pull' );

		var current = 0;

		function last() {
			var stage = root.querySelector( '.custom-membership-hero__stage' );
			var room  = stage ? stage.clientWidth : 0;
			var gap   = parseFloat( window.getComputedStyle( track ).columnGap ) || 0;
			var width = cards[ 0 ].offsetWidth + gap;
			var seen  = width > 0 ? Math.max( 1, Math.floor( room / width ) ) : 1;

			return Math.max( 0, cards.length - seen );
		}

		function show( index ) {
			current = Math.min( Math.max( index, 0 ), last() );

			var shift = cards[ current ].offsetLeft - cards[ 0 ].offsetLeft;

			track.style.transform = 'translateX(' + ( -shift ) + 'px)';

			if ( prev ) {
				prev.disabled = current === 0;
			}

			if ( next ) {
				next.disabled = current === last();
			}

			pull.classList.toggle( 'is-pullable', last() > 0 );
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.custom-membership-hero__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-membership-hero__arrow--next' ) ) {
				show( current + 1 );
			}
		} );

		// Pulled sideways, the row follows the hand and settles on the plan the
		// pull reached, as an arrow leaves it on a plan.
		var TURN = 40;
		var from = 0;

		function offset( index ) {
			return cards[ index ].offsetLeft - cards[ 0 ].offsetLeft;
		}

		// Past either end the row gives half of what the hand asks, so the end
		// of the row is felt rather than struck.
		function give( to ) {
			var far = -offset( last() );

			if ( to > 0 ) {
				return to / 2;
			}

			return to < far ? far + ( to - far ) / 2 : to;
		}

		// The plan nearest where the hand left the row; a pull too short to
		// reach the next one still counts as reaching for it.
		function land( at, by ) {
			var best = current;

			for ( var i = 0; i <= last(); i++ ) {
				if ( Math.abs( -offset( i ) - at ) < Math.abs( -offset( best ) - at ) ) {
					best = i;
				}
			}

			if ( best === current && Math.abs( by ) >= TURN ) {
				best += by < 0 ? 1 : -1;
			}

			return best;
		}

		pull.addEventListener( 'custom-pull', function ( event ) {
			var by = event.detail.by;

			if ( 'begin' === event.detail.phase ) {
				from = -offset( current );
				track.style.transition = 'none';

				return;
			}

			if ( 'move' === event.detail.phase ) {
				track.style.transform = 'translateX(' + give( from + by ) + 'px)';

				return;
			}

			track.style.transition = '';
			show( land( from + by, by ) );
		} );

		window.addEventListener( 'resize', function () {
			show( current );
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-membership-hero' );

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
				'frontend/element_ready/membership-hero.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-membership-hero' ) );
				}
			);
		}
	} );
}() );
