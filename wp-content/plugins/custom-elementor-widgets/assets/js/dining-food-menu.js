/**
 * Dining, food menu — the pictures and the dots beneath them.
 */
( function () {
	'use strict';

	function setUp( root ) {
		var slides = root.querySelectorAll( '.custom-dining-menu__slide' );
		var dots   = root.querySelectorAll( '.custom-dining-menu__dot' );

		if ( slides.length === 0 ) {
			return;
		}

		function show( index ) {
			for ( var i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.toggle( 'is-current', i === index );

				if ( dots[ i ] ) {
					dots[ i ].classList.toggle( 'is-current', i === index );
					dots[ i ].setAttribute( 'aria-current', i === index ? 'true' : 'false' );
				}
			}
		}

		root.addEventListener( 'click', function ( event ) {
			var dot = event.target.closest( '.custom-dining-menu__dot' );

			if ( ! dot ) {
				return;
			}

			show( Number( dot.getAttribute( 'data-index' ) ) );
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-dining-menu' );

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
				'frontend/element_ready/dining-food-menu.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-dining-menu' ) || $scope[ 0 ] );
				}
			);
		}
	} );
}() );
