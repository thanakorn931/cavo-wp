/**
 * Home, the space.
 *
 * One area is open; the tabs say which, and the two arrows step through them in
 * the order they were written.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var tabs  = root.querySelectorAll( '.custom-home-space__tab' );
		var areas = root.querySelectorAll( '.custom-home-space__area' );

		if ( tabs.length === 0 || areas.length === 0 ) {
			return;
		}

		root.dataset.wired = '1';

		var current = 0;

		function show( index ) {
			// The tabs are a ring: past the last is the first again.
			current = ( index + areas.length ) % areas.length;

			for ( var i = 0; i < areas.length; i++ ) {
				areas[ i ].hidden = i !== current;
				areas[ i ].classList.toggle( 'is-here', i === current );
			}

			for ( var j = 0; j < tabs.length; j++ ) {
				tabs[ j ].classList.toggle( 'is-here', j === current );
				tabs[ j ].setAttribute( 'aria-selected', j === current ? 'true' : 'false' );
			}
		}

		root.addEventListener( 'click', function ( event ) {
			var tab = event.target.closest( '.custom-home-space__tab' );

			if ( tab ) {
				show( parseInt( tab.getAttribute( 'data-area' ), 10 ) || 0 );

				return;
			}

			if ( event.target.closest( '.custom-home-space__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-home-space__arrow--next' ) ) {
				show( current + 1 );
			}
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-home-space' );

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
				'frontend/element_ready/home-the-space.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-home-space' ) );
				}
			);
		}
	} );
}() );
