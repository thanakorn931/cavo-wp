/**
 * Nightlife, atmosphere.
 *
 * The row is moved by taking hold of it, with a finger or a mouse, and let go:
 * pulled a little either way it moves one slide that way and comes to rest
 * on it, and pulled hardly at all it settles back. It never moves more than
 * one slide for one hold, however far the hold travelled, and while it is
 * held it follows the hand no further than that one slide.
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
		var pitch  = 0;

		// While the row is being put back a run, the putting back is not
		// itself something to put back.
		var shifting = false;

		// How far a hold must travel before it counts as a pull.
		var PULL = 24;

		// The move under way, so a new hold can cut it short.
		var flight = { frame: 0, clock: 0 };

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

		// A slide and the space beside it, whether or not there is more than
		// one run to keep to.
		function measurePitch() {
			if ( slides.length === 0 || ! track ) {
				pitch = 0;

				return;
			}

			pitch = slides[ 0 ].offsetWidth + ( parseFloat( window.getComputedStyle( track ).columnGap ) || 0 );
		}

		// The row rests with a slide in the middle, and the slide in the middle
		// is a count: the first stands there when the row has not moved, each
		// after it one pitch further on.
		function restOn( index ) {
			var target = index < 0 ? 0 : index * pitch;

			// The snap is held off before anything else, so the row is not
			// taken from where the hand left it before the move begins — the
			// browser draws that taking as a move of its own, back and then
			// on again.
			stop();
			stage.classList.add( 'is-moving' );

			// A step back out of the middle run is taken from the same place a
			// run further on, where the copies are identical, so the step stays
			// inside the run and is never put back while it is still moving.
			if ( run > 0 && target < run ) {
				shifting = true;
				stage.scrollLeft += run;
				shifting = false;
				target += run;
			}

			// The move is drawn frame by frame, easing out, with the snap held
			// off until it has arrived: left to the browser it is cut short by
			// the snap, or not drawn at all where nothing pulls it. Where no
			// frames are drawn the clock ends the move instead, so it arrives
			// either way.
			var from  = stage.scrollLeft;
			var dist  = target - from;
			var began = Date.now();
			var SPAN  = 450;

			function arrive() {
				stop();
				stage.scrollLeft = target;
				stage.classList.remove( 'is-moving' );
			}

			function step() {
				var t = Math.min( 1, ( Date.now() - began ) / SPAN );
				var e = 1 - Math.pow( 1 - t, 3 );

				if ( t >= 1 ) {
					arrive();

					return;
				}

				stage.scrollLeft = from + dist * e;
				flight.frame = window.requestAnimationFrame( step );
			}

			flight.clock = window.setTimeout( arrive, SPAN + 100 );
			step();
		}

		// Whatever move is under way is left where it is.
		function stop() {
			window.cancelAnimationFrame( flight.frame );
			window.clearTimeout( flight.clock );
		}

		// The copies are identical, so a row put back by exactly one run shows
		// the same pictures in the same places and the move cannot be seen.
		function keepToTheMiddle() {
			if ( run <= 0 || shifting ) {
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
			measurePitch();
			keepToTheMiddle();
		} );

		measure();
		measurePitch();

		// The row opens on the middle run, which is what puts a picture either
		// side of the one being read.
		stage.scrollLeft = run;

		stage.addEventListener( 'pointerdown', function ( event ) {
			stop();
			stage.classList.remove( 'is-moving' );

			holding = true;
			startX  = event.clientX;
			startAt = stage.scrollLeft;

			stage.classList.add( 'is-holding' );

			try {
				stage.setPointerCapture( event.pointerId );
			} catch ( error ) {
				// A pointer the page did not see begin cannot be held; the hold
				// goes on without it.
			}
		} );

		// Held, the row follows the hand, but no further than one slide either
		// way: a long pull shows the next slide arriving and no more, so that
		// letting go never has far to come back from.
		stage.addEventListener( 'pointermove', function ( event ) {
			if ( ! holding ) {
				return;
			}

			event.preventDefault();

			var pulled = event.clientX - startX;

			if ( pitch > 0 ) {
				pulled = Math.max( -pitch, Math.min( pitch, pulled ) );
			}

			stage.scrollLeft = startAt - pulled;
		} );

		// Let go, the row goes one slide the way it was pulled, or settles back
		// where it was if it was hardly pulled at all.
		[ 'pointerup', 'pointercancel', 'pointerleave' ].forEach( function ( name ) {
			stage.addEventListener( name, function ( event ) {
				if ( ! holding ) {
					return;
				}

				holding = false;

				// The move to rest begins before the hold is given up: between
				// the two the snap would take the row.
				if ( pitch > 0 ) {
					var pulled = name === 'pointercancel' ? 0 : event.clientX - startX;
					var from   = Math.round( startAt / pitch );
					var step   = Math.abs( pulled ) >= PULL ? ( pulled < 0 ? 1 : -1 ) : 0;

					restOn( from + step );
				}

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
