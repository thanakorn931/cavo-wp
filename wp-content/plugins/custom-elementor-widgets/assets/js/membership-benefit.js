/**
 * Membership, benefits.
 *
 * One benefit is on show at a time — its picture, its words and the picture
 * beside them turn together. The list runs round.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var slides = root.querySelectorAll( '.custom-membership-benefit__slide' );

		if ( slides.length === 0 ) {
			return;
		}

		root.dataset.wired = '1';

		var current = 0;

		function show( index ) {
			current = ( index + slides.length ) % slides.length;

			for ( var i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.toggle( 'is-current', i === current );
				slides[ i ].setAttribute( 'aria-hidden', i === current ? 'false' : 'true' );
			}
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.custom-membership-benefit__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-membership-benefit__arrow--next' ) ) {
				show( current + 1 );
			}
		} );

		show( 0 );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-membership-benefit' );

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
				'frontend/element_ready/membership-benefit.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-membership-benefit' ) );
				}
			);
		}
	} );
}() );
