/**
 * Nightlife, atmosphere.
 *
 * The row is moved by taking hold of it. Nothing is added on a touch screen or
 * a trackpad, which already move it; this is only for a mouse, which does not.
 *
 * The run is written three times and the row is put back a run whenever it
 * leaves the middle one. Where the row stands is a number rather than a
 * journey, so putting it back takes no time and is never seen.
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

		var track  = root.querySelector( '.custom-nightlife-carousel__track' );
		var slides = root.querySelectorAll( '.custom-nightlife-carousel__slide' );
		var many   = track ? parseInt( track.getAttribute( 'data-many' ), 10 ) || slides.length : slides.length;
		var runs   = many > 0 ? Math.round( slides.length / many ) : 1;
		var run    = 0;

		// What one run is worth: as many slides as the client gave, each of them
		// a slide and the space beside it.
		function measure() {
			if ( runs < 2 || slides.length === 0 || ! track ) {
				run = 0;

				return;
			}

			var wide = slides[ 0 ].offsetWidth;
			var gap  = parseFloat( window.getComputedStyle( track ).columnGap ) || 0;

			run = many * ( wide + gap );
		}

		// The copies are identical, so a row put back by exactly one run shows
		// the same pictures in the same places and the move cannot be seen.
		function keepToTheMiddle() {
			if ( run <= 0 ) {
				return;
			}

			var moved = 0;

			// A throw can cross more than one run between one frame and the
			// next, so it is put back as many times as it takes.
			while ( stage.scrollLeft + moved < run ) {
				moved += run;
			}

			while ( stage.scrollLeft + moved >= run * 2 ) {
				moved -= run;
			}

			if ( moved ) {
				stage.scrollLeft += moved;

				// A hold in progress measures from where it began, so its
				// origin travels with the row.
				startAt += moved;
			}
		}

		stage.addEventListener( 'scroll', keepToTheMiddle );

		window.addEventListener( 'resize', function () {
			measure();
			keepToTheMiddle();
		} );

		measure();

		// The row opens on the middle run, which is what puts a picture either
		// side of the one being read.
		stage.scrollLeft = run;

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
