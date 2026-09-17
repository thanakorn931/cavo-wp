/**
 * Nightlife, atmosphere.
 *
 * The row is moved by taking hold of it, with a finger or a mouse, and let go:
 * held, it follows the hand as far as the hand goes, and let go it comes to
 * rest on the nearest picture, carrying on the way it was thrown for as long
 * as the throw was worth.
 *
 * The row never ends. The run is written three times, and where a band is
 * wider than one run the row is written out further until a run is wider than
 * the band — otherwise where the run ends would come into view. The row is put
 * back a run whenever it leaves the middle one; where it stands is a number
 * rather than a journey, so putting it back takes no time and is never seen.
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

		// How long a throw goes on being worth something after the hand has
		// left the row, in milliseconds of the speed it let go at.
		var THROW = 220;

		// The hand's last moment: where it was, when, and how fast.
		var lastX  = 0;
		var lastAt = 0;
		var speed  = 0;

		// The move under way. A hold that begins before it has arrived is not
		// taken: the row finishes coming to rest first, and is taken hold of
		// from rest.
		var flight = { frame: 0, clock: 0 };
		var moving = false;

		// The pictures as the client gave them, before any were written out
		// again. Every copy is made from these.
		var given = [];

		for ( var g = 0; g < many && g < slides.length; g++ ) {
			given.push( slides[ g ] );
		}

		// A run must be wider than the band, or where it ends comes into view.
		// Where the client gave too few pictures for that, the row is written
		// out again until one run covers the band, and three of those runs
		// stand in the row as three did before.
		function fill() {
			if ( ! track || many <= 0 || pitch <= 0 || given.length < many ) {
				return;
			}

			var block = Math.max( 1, Math.ceil( ( stage.clientWidth + pitch ) / ( many * pitch ) ) );
			var want  = block * many * 3;
			var have  = track.children.length;

			for ( var i = have; i < want; i++ ) {
				var copy = given[ i % many ].cloneNode( true );

				copy.setAttribute( 'aria-hidden', 'true' );

				// A copy is made after the page has handed out its films, so
				// it is handed its own here rather than waiting for a hand
				// that has already been given out.
				var film = copy.querySelector( 'video[data-src]' );

				if ( film ) {
					film.src = film.getAttribute( 'data-src' );
					film.removeAttribute( 'data-src' );
				}

				track.appendChild( copy );
			}

			if ( want > have ) {
				slides = root.querySelectorAll( '.custom-nightlife-carousel__slide' );
			}

			runs = 3;
			run  = block * many * pitch;
		}

		// What one run is worth, once the row is long enough to hold three.
		function measure() {
			if ( slides.length === 0 || ! track || many <= 0 ) {
				run = 0;

				return;
			}

			fill();
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
			moving = true;
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
				moving = false;
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
			// A run narrower than the band was measured before the row had its
			// shape; it is not a run, and nothing is put back by it.
			if ( run <= 0 || run < stage.clientWidth || shifting ) {
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

		stage.addEventListener( 'dragstart', function ( event ) {
			event.preventDefault();
		} );

		// What a slide is worth is read again whenever it may have changed:
		// when the page has finished arriving, when the window changes, and
		// as a hold begins. Read once, before the stylesheet had reached the
		// row, it is the width of nothing, and every move after is measured
		// against that.
		function measureAll() {
			// What a slide is worth is read first: a run is a count of them.
			measurePitch();
			measure();
		}

		window.addEventListener( 'resize', function () {
			measureAll();
			keepToTheMiddle();
		} );

		// The row opens on the middle run, which is what puts a picture either
		// side of the one being read. It is put there once the row has its
		// shape, and again on load in case it had not.
		function open() {
			measureAll();

			if ( run > 0 && stage.scrollLeft < run ) {
				stage.scrollLeft = run;
			}
		}

		open();
		window.addEventListener( 'load', open );

		stage.addEventListener( 'pointerdown', function ( event ) {
			if ( moving ) {
				return;
			}

			// A mouse pressed on the row is a hold on the row and nothing else:
			// not the start of a selection, which the browser would go on
			// dragging the row after once the hand has left it, and not a
			// picture being picked up.
			event.preventDefault();
			measureAll();

			holding = true;
			startX  = event.clientX;
			startAt = stage.scrollLeft;
			lastX   = event.clientX;
			lastAt  = Date.now();
			speed   = 0;

			stage.classList.add( 'is-holding' );

			try {
				stage.setPointerCapture( event.pointerId );
			} catch ( error ) {
				// A pointer the page did not see begin cannot be held; the hold
				// goes on without it.
			}
		} );

		// Held, the row follows the hand as far as the hand goes: a long pull
		// carries the row past as many pictures as it travelled.
		stage.addEventListener( 'pointermove', function ( event ) {
			if ( ! holding ) {
				return;
			}

			event.preventDefault();

			var now = Date.now();

			// How fast the hand is going, read from the last moment of it
			// alone: what it did earlier is not where it is throwing the row.
			if ( now > lastAt ) {
				speed  = ( event.clientX - lastX ) / ( now - lastAt );
				lastX  = event.clientX;
				lastAt = now;
			}

			stage.scrollLeft = startAt - ( event.clientX - startX );
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

					// Where the row stands now, and where the throw carries it
					// on to: a hand that was still when it let go leaves the
					// row on the nearest picture.
					var thrown = name === 'pointercancel' ? 0 : -speed * THROW;
					var lands  = ( stage.scrollLeft + thrown ) / pitch;
					var rest   = Math.round( lands );

					// A pull too small to count for a picture still counts as
					// the one it was reaching for.
					if ( rest === Math.round( startAt / pitch ) && Math.abs( pulled ) >= PULL ) {
						rest += pulled < 0 ? 1 : -1;
					}

					restOn( rest );
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
