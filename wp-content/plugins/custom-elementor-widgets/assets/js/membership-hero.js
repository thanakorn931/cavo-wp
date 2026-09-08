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
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.custom-membership-hero__arrow--prev' ) ) {
				show( current - 1 );
			} else if ( event.target.closest( '.custom-membership-hero__arrow--next' ) ) {
				show( current + 1 );
			}
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
