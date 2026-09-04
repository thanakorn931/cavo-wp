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
