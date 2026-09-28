/**
 * Nightlife, hall of beats.
 *
 * The row moves one card at a time. Only the first few cards carry a picture
 * to begin with; each step forward gives one more its own, so a long row costs
 * no more to open than a short one.
 */
( function () {
	'use strict';

	function load( card ) {
		if ( ! card ) {
			return;
		}

		var images = card.querySelectorAll( 'img[data-src]' );

		for ( var i = 0; i < images.length; i++ ) {
			images[ i ].setAttribute( 'src', images[ i ].getAttribute( 'data-src' ) );
			images[ i ].removeAttribute( 'data-src' );
		}
	}

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var track = root.querySelector( '.custom-nightlife-beat__track' );
		var stage = root.querySelector( '.custom-nightlife-beat__stage' );
		var cards = root.querySelectorAll( '.custom-nightlife-beat__card' );
		var prev  = root.querySelector( '.custom-nightlife-beat__arrow--prev' );
		var next  = root.querySelector( '.custom-nightlife-beat__arrow--next' );

		if ( ! track || ! stage || cards.length === 0 ) {
			return;
		}

		root.dataset.wired = '1';

		var pull = stage;

		pull.classList.add( 'custom-pull' );

		var current = 0;

		function last() {
			// The furthest the row may move: one card short of running dry.
			var perView = Math.max( 1, Math.floor( stage.offsetWidth / ( cards[ 0 ].offsetWidth + 18 ) ) );

			return Math.max( 0, cards.length - perView );
		}

		function show( index ) {
			current = Math.min( Math.max( index, 0 ), last() );

			var card  = cards[ current ];
			var shift = card.offsetLeft - cards[ 0 ].offsetLeft;

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
			if ( event.target.closest( '.custom-nightlife-beat__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-nightlife-beat__arrow--next' ) ) {
				// One more card gets its picture with every step forward.
				var waiting = root.querySelector( '.custom-nightlife-beat__card img[data-src]' );

				if ( waiting ) {
					load( waiting.closest( '.custom-nightlife-beat__card' ) );
				}

				show( current + 1 );
			}
		} );

		// Pulled sideways, the row follows the hand and settles on the card the
		// pull reached, as an arrow leaves it on a card.
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

		// The card nearest where the hand left the row; a pull too short to
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

			var was = current;

			show( land( from + by, by ) );

			// One more card gets its picture for every step the pull took forward.
			for ( var steps = current - was; steps > 0; steps-- ) {
				var waiting = root.querySelector( '.custom-nightlife-beat__card img[data-src]' );

				if ( waiting ) {
					load( waiting.closest( '.custom-nightlife-beat__card' ) );
				}
			}
		} );

		window.addEventListener( 'resize', function () {
			show( current );
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-nightlife-beat' );

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
				'frontend/element_ready/nightlife-beat.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-nightlife-beat' ) );
				}
			);
		}
	} );
}() );
