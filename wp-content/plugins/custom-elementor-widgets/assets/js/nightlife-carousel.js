/**
 * Nightlife, atmosphere.
 *
 * The row is moved by taking hold of it. Nothing is added on a touch screen or
 * a trackpad, which already move it; this is only for a mouse, which does not.
 */
( function () {
	'use strict';

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		var stage = root.querySelector( '.custom-nightlife-carousel__stage' );

		if ( ! stage ) {
			return;
		}

		root.dataset.wired = '1';

		var holding = false;
		var startX  = 0;
		var startAt = 0;

		stage.addEventListener( 'pointerdown', function ( event ) {
			if ( event.pointerType === 'touch' ) {
				return;
			}

			holding = true;
			startX  = event.clientX;
			startAt = stage.scrollLeft;

			stage.classList.add( 'is-holding' );
			stage.setPointerCapture( event.pointerId );
		} );

		stage.addEventListener( 'pointermove', function ( event ) {
			if ( ! holding ) {
				return;
			}

			event.preventDefault();
			stage.scrollLeft = startAt - ( event.clientX - startX );
		} );

		[ 'pointerup', 'pointercancel', 'pointerleave' ].forEach( function ( name ) {
			stage.addEventListener( name, function () {
				holding = false;
				stage.classList.remove( 'is-holding' );
			} );
		} );
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-nightlife-carousel' );

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
				'frontend/element_ready/nightlife-carousel.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-nightlife-carousel' ) );
				}
			);
		}
	} );
}() );
