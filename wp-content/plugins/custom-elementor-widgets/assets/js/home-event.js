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
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.custom-home-event__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-home-event__arrow--next' ) ) {
				show( current + 1 );
			}
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
