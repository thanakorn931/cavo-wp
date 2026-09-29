/**
 * Every video a media slot prints.
 *
 * A video standing where a picture would plays itself, and holds still for a
 * reader whose system has asked for less movement. CSS cannot stop a loop, so
 * the asking is answered here — and answered again the moment they change
 * their mind.
 *
 * It arrives without its file. One in the first screen is handed it once the
 * page has finished loading; one further down, once the reader nears it —
 * the page is never kept waiting on a film.
 */
( function () {
	'use strict';

	var STILL = '( prefers-reduced-motion: reduce )';
	var asked = null;

	function settle( film ) {
		// Not handed its file yet: there is nothing to play or to hold.
		if ( ! asked || film.hasAttribute( 'data-src' ) ) {
			return;
		}

		if ( asked.matches ) {
			film.removeAttribute( 'autoplay' );
			film.pause();

			return;
		}

		film.setAttribute( 'autoplay', '' );

		// A browser that refuses the press has its own reasons; the video
		// simply stays where it is.
		var started = film.play();

		if ( started && started.catch ) {
			started.catch( function () {} );
		}
	}

	// The file is handed over, and the video plays or holds as asked.
	function arrive( film ) {
		if ( ! film.hasAttribute( 'data-src' ) ) {
			return;
		}

		film.preload = 'auto';
		film.src     = film.getAttribute( 'data-src' );
		film.removeAttribute( 'data-src' );

		settle( film );
	}

	// When: at once for one in the first screen, the page having loaded; for
	// one further down, as the reader comes within a screen of it.
	function when( film ) {
		if ( 'near' === film.getAttribute( 'data-custom-wait' ) && 'IntersectionObserver' in window ) {
			var near = new window.IntersectionObserver( function ( entries ) {
				for ( var i = 0; i < entries.length; i++ ) {
					if ( entries[ i ].isIntersecting ) {
						near.disconnect();
						arrive( film );

						return;
					}
				}
			}, { rootMargin: '100% 0px' } );

			near.observe( film );

			return;
		}

		arrive( film );
	}

	function watch( film ) {
		if ( ! film || film.dataset.wired ) {
			return;
		}

		film.dataset.wired = '1';

		if ( ! film.hasAttribute( 'data-src' ) ) {
			settle( film );

			return;
		}

		if ( 'complete' === document.readyState ) {
			when( film );
		} else {
			window.addEventListener( 'load', function () {
				when( film );
			} );
		}
	}

	function start( root ) {
		if ( ! window.matchMedia ) {
			return;
		}

		if ( ! asked ) {
			asked = window.matchMedia( STILL );

			function again() {
				var all = document.querySelectorAll( 'video[data-custom-plays]' );

				for ( var i = 0; i < all.length; i++ ) {
					settle( all[ i ] );
				}
			}

			if ( asked.addEventListener ) {
				asked.addEventListener( 'change', again );
			} else if ( asked.addListener ) {
				asked.addListener( again );
			}
		}

		var films = ( root || document ).querySelectorAll( 'video[data-custom-plays]' );

		for ( var i = 0; i < films.length; i++ ) {
			watch( films[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			start();
		} );
	} else {
		start();
	}

	// Elementor rebuilds a widget in the editor without reloading the page.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/global',
				function ( $scope ) {
					start( $scope[ 0 ] );
				}
			);
		}
	} );
}() );


/**
 * A list that comes on screen a few at a time.
 *
 * Every item is on the page from the start. What a press brings is not the
 * items but their pictures, which are not fetched until the item is shown, so
 * the page carries a long list without carrying its weight. While the button
 * waits on them it keeps its shape and loses its words, and nothing under it
 * moves.
 *
 * The section says how many stand before the button is pressed and how many
 * more each press brings, one count for each of the three screens.
 */
( function () {
	'use strict';

	// The button waits on the pictures of what it has just brought, but never
	// past this: one the browser has put off until the reader nears it may not
	// come at all, and a button that waits for it waits for ever.
	var PATIENCE = 3000;

	function whenLoaded( cards, done ) {
		var images = [];
		var over   = false;
		var i;
		var j;

		function finish() {
			if ( over ) {
				return;
			}

			over = true;
			done();
		}

		window.setTimeout( finish, PATIENCE );

		for ( i = 0; i < cards.length; i++ ) {
			var found = cards[ i ].querySelectorAll( 'img' );

			for ( j = 0; j < found.length; j++ ) {
				images.push( found[ j ] );
			}
		}

		var left = images.length;

		if ( ! left ) {
			finish();
			return;
		}

		function step() {
			left--;

			if ( left <= 0 ) {
				finish();
			}
		}

		for ( i = 0; i < images.length; i++ ) {
			if ( images[ i ].complete ) {
				step();
			} else {
				images[ i ].addEventListener( 'load', step );
				images[ i ].addEventListener( 'error', step );
			}
		}
	}

	// Which of the three screens this is. The counts are the section's, one
	// for each screen, and the script reads the one for the screen it is on.
	function tier() {
		if ( window.matchMedia( '(max-width: 767px)' ).matches ) {
			return 'mobile';
		}

		if ( window.matchMedia( '(max-width: 1024px)' ).matches ) {
			return 'tablet';
		}

		return 'desktop';
	}

	function count( root, name ) {
		var which = tier();
		var value = root.getAttribute( 'data-' + name + ( 'desktop' === which ? '' : '-' + which ) );

		if ( null === value ) {
			value = root.getAttribute( 'data-' + name );
		}

		return parseInt( value, 10 ) || 0;
	}

	function setUp( root ) {
		if ( ! root || root.dataset.feedWired ) {
			return;
		}

		root.dataset.feedWired = '1';

		var button = root.querySelector( '[data-feed-more]' );
		var cards  = root.querySelectorAll( '[data-feed-card]' );

		// A section may stand its first item apart on the wide screen — the
		// newest, across the band — and the count is of the rest beneath it. On
		// the narrow screens it stands among them and is counted with them.
		var lead     = root.querySelector( '[data-feed-lead]' ) ? 1 : 0;
		var revealed = 0;
		var pressed  = false;

		// How many stand on this screen before anything is pressed.
		function opening() {
			return Math.max( 0, count( root, 'shown' ) - ( 'desktop' === tier() ? 0 : lead ) );
		}

		function apply() {
			for ( var i = 0; i < cards.length; i++ ) {
				var shown = i < revealed;

				cards[ i ].hidden = ! shown;
				cards[ i ].classList.toggle( 'is-shown', shown );
				cards[ i ].classList.toggle( 'is-waiting', ! shown );
			}

			if ( button ) {
				button.hidden = revealed >= cards.length;
			}
		}

		revealed = Math.min( cards.length, opening() );
		apply();

		// A screen that changes width changes how many stand; what a press has
		// already brought on screen is not taken back.
		var was = tier();

		window.addEventListener( 'resize', function () {
			var now = tier();

			if ( now === was ) {
				return;
			}

			was      = now;
			revealed = Math.min( cards.length, pressed ? Math.max( revealed, opening() ) : opening() );
			apply();
		} );

		if ( ! button ) {
			return;
		}

		button.addEventListener( 'click', function () {
			// A step of nothing means everything that is left, at once.
			var step  = count( root, 'step' );
			var batch = Array.prototype.slice.call( cards, revealed, step > 0 ? revealed + step : cards.length );

			if ( ! batch.length ) {
				button.hidden = true;
				return;
			}

			pressed  = true;
			revealed = Math.min( cards.length, revealed + batch.length );

			button.classList.add( 'is-working' );
			button.disabled = true;

			apply();

			whenLoaded( batch, function () {
				button.classList.remove( 'is-working' );
				button.disabled = false;
			} );
		} );
	}

	function start( root ) {
		var roots = ( root || document ).querySelectorAll( '[data-feed]' );

		for ( var i = 0; i < roots.length; i++ ) {
			setUp( roots[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			start();
		} );
	} else {
		start();
	}

	// Elementor rebuilds a widget in the editor without reloading the page.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/global',
				function ( $scope ) {
					start( $scope[ 0 ] );
				}
			);
		}
	} );
}() );


/**
 * A row of pictures where one stands open.
 *
 * Every picture in the row can be the open one. Pressed, a picture opens and
 * the one that was open closes; left alone, the row turns itself, a picture
 * every five seconds, and round again. It waits while the reader is on it — a
 * hand over it, or a key in it — and it does not turn at all for a reader whose
 * system has asked for less movement, or while the page is not being looked at.
 *
 * On the tablet and the phone the same row is a carousel instead: one picture
 * across the whole row, the next beside it out of view. It does not turn of its
 * own accord and does not run round — the first has nothing before it and the
 * last nothing after — and it is moved by its two arrows, or pulled by hand
 * (the shared pull, further down), sliding from one picture to the next. Which
 * picture it stands on is the one open, so the wide tier opens that one when
 * the window grows back.
 */
( function () {
	'use strict';

	// How long a picture stands open before the row turns of its own accord.
	var TURN = 5000;

	// How far a pull goes before it turns the carousel, as an arrow would.
	var PULL_TURN = 40;

	// The tiers on which the row is a carousel.
	function carousel() {
		return !! ( window.matchMedia && window.matchMedia( '( max-width: 1024px )' ).matches );
	}

	function setUp( root ) {
		if ( ! root || root.dataset.panelsWired ) {
			return;
		}

		var panels = root.querySelectorAll( '[data-panel]' );

		if ( panels.length < 2 ) {
			return;
		}

		root.dataset.panelsWired = '1';

		var open  = 0;
		var timer = 0;
		var held  = false;

		function still() {
			return window.matchMedia && window.matchMedia( '( prefers-reduced-motion: reduce )' ).matches;
		}

		var prev = root.querySelector( '[data-panels-prev]' );
		var next = root.querySelector( '[data-panels-next]' );

		function show( index ) {
			// The carousel has ends; the row of the wide tier runs round.
			if ( carousel() ) {
				open = Math.max( 0, Math.min( panels.length - 1, index ) );
			} else {
				open = ( index + panels.length ) % panels.length;
			}

			root.style.setProperty( '--custom-panels-at', open );

			// An arrow with nowhere to go says so, and is not pressed.
			if ( prev ) {
				prev.disabled = 0 === open;
			}

			if ( next ) {
				next.disabled = panels.length - 1 === open;
			}

			for ( var i = 0; i < panels.length; i++ ) {
				var here = i === open;
				var pick = panels[ i ].querySelector( '[data-panel-pick]' );

				panels[ i ].classList.toggle( 'is-open', here );

				// The picture already open is not one to ask for.
				if ( pick ) {
					pick.disabled = here;
					pick.setAttribute( 'aria-pressed', here ? 'true' : 'false' );
				}
			}
		}

		function wait() {
			window.clearTimeout( timer );

			if ( held || still() || document.hidden || carousel() ) {
				return;
			}

			timer = window.setTimeout( function () {
				show( open + 1 );
				wait();
			}, TURN );
		}

		root.addEventListener( 'click', function ( event ) {
			if ( event.target.closest && event.target.closest( '[data-panels-prev]' ) ) {
				show( open - 1 );

				return;
			}

			if ( event.target.closest && event.target.closest( '[data-panels-next]' ) ) {
				show( open + 1 );

				return;
			}

			var pick = event.target.closest ? event.target.closest( '[data-panel-pick]' ) : null;

			if ( ! pick ) {
				return;
			}

			var panel = pick.closest( '[data-panel]' );

			for ( var i = 0; i < panels.length; i++ ) {
				if ( panels[ i ] === panel ) {
					show( i );
					break;
				}
			}

			// A picture asked for stands its full five seconds before the row
			// takes the turn back.
			wait();
		} );

		// The row holds still while the reader is on it, and takes up the turn
		// again once they have left.
		[ 'pointerenter', 'focusin' ].forEach( function ( name ) {
			root.addEventListener( name, function () {
				held = true;
				window.clearTimeout( timer );
			} );
		} );

		[ 'pointerleave', 'focusout' ].forEach( function ( name ) {
			root.addEventListener( name, function () {
				held = false;
				wait();
			} );
		} );

		document.addEventListener( 'visibilitychange', wait );

		// Pulled by hand, the carousel follows the hand, and a pull long enough
		// turns it one picture, as an arrow does; a shorter one lets it back.
		// Past either end it gives only a little, and comes back.
		function pullable() {
			var on = carousel() && ! still();

			root.classList.toggle( 'custom-pull', on );
			root.classList.toggle( 'is-pullable', on );
		}

		root.addEventListener( 'custom-pull', function ( event ) {
			var by = event.detail.by;

			if ( 'move' === event.detail.phase ) {
				var past = ( 0 === open && by > 0 ) || ( panels.length - 1 === open && by < 0 );

				root.style.setProperty( '--custom-panels-pull', ( past ? by / 3 : by ) + 'px' );

				return;
			}

			if ( 'end' === event.detail.phase ) {
				root.style.removeProperty( '--custom-panels-pull' );

				if ( Math.abs( by ) >= PULL_TURN ) {
					show( open + ( by < 0 ? 1 : -1 ) );
				}
			}
		} );

		// Crossing between the tiers, the row takes up the ways of the one it
		// is on: the wide tier turns again, the carousel keeps to its ends.
		if ( window.matchMedia ) {
			var tier = window.matchMedia( '( max-width: 1024px )' );
			var changed = function () {
				pullable();
				show( open );
				wait();
			};

			if ( tier.addEventListener ) {
				tier.addEventListener( 'change', changed );
			} else if ( tier.addListener ) {
				tier.addListener( changed );
			}
		}

		pullable();
		show( 0 );
		wait();
	}

	function start( root ) {
		var rows = ( root || document ).querySelectorAll( '[data-panels]' );

		for ( var i = 0; i < rows.length; i++ ) {
			setUp( rows[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			start();
		} );
	} else {
		start();
	}

	// Elementor rebuilds a widget in the editor without reloading the page.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/global',
				function ( $scope ) {
					start( $scope[ 0 ] );
				}
			);
		}
	} );
}() );


/**
 * A row that follows the hand.
 *
 * A section marks the part of itself that can be pulled with `custom-pull`, and
 * says it can be pulled right now with `is-pullable`. Pressed there and moved
 * sideways — by a mouse, a finger or a pen — it is told so through a
 * `custom-pull` event, whose detail says the phase (begin, move, end) and how
 * far the hand has gone. What the row does with that is the section's own: one
 * row follows the hand and settles on a card, another turns to the next slide.
 *
 * A press that barely moves stays a press, so the link or card under it still
 * answers; a pull that ends on a link does not open it. Up and down the page
 * stays the page's own, so a finger scrolling past the row is never caught.
 */
( function () {
	'use strict';

	// How far the hand goes before a press becomes a pull.
	var BEGIN = 6;

	var hold = null;

	function tell( area, phase, by ) {
		var said;

		try {
			said = new window.CustomEvent( 'custom-pull', { detail: { phase: phase, by: by } } );
		} catch ( error ) {
			said = document.createEvent( 'CustomEvent' );
			said.initCustomEvent( 'custom-pull', false, false, { phase: phase, by: by } );
		}

		area.dispatchEvent( said );
	}

	document.addEventListener( 'pointerdown', function ( event ) {
		var area = event.target.closest ? event.target.closest( '.custom-pull.is-pullable' ) : null;

		// A button inside the row — an arrow — is pressed, never pulled.
		if ( ! area || event.button || event.target.closest( 'button, input, select, textarea' ) ) {
			return;
		}

		hold = {
			area: area,
			id: event.pointerId,
			x: event.clientX,
			y: event.clientY,
			moved: false,
		};
	} );

	window.addEventListener( 'pointermove', function ( event ) {
		if ( ! hold || event.pointerId !== hold.id ) {
			return;
		}

		var by   = event.clientX - hold.x;
		var down = event.clientY - hold.y;

		if ( ! hold.moved ) {
			// Going up or down the page first: that was never a pull.
			if ( Math.abs( down ) > BEGIN && Math.abs( down ) > Math.abs( by ) ) {
				hold = null;

				return;
			}

			if ( Math.abs( by ) <= BEGIN ) {
				return;
			}

			hold.moved = true;
			hold.area.classList.add( 'is-holding' );
			tell( hold.area, 'begin', 0 );
		}

		event.preventDefault();
		tell( hold.area, 'move', by );
	}, { passive: false } );

	function letGo( event ) {
		if ( ! hold || event.pointerId !== hold.id ) {
			return;
		}

		var was = hold;

		hold = null;

		if ( ! was.moved ) {
			return;
		}

		was.area.classList.remove( 'is-holding' );

		// The press that ends a pull is not a press on whatever it ended over.
		was.area.dataset.customPulled = '1';
		window.setTimeout( function () {
			delete was.area.dataset.customPulled;
		}, 0 );

		// The browser taking the pointer back for itself leaves the row where
		// it was.
		tell( was.area, 'end', 'pointercancel' === event.type ? 0 : event.clientX - was.x );
	}

	window.addEventListener( 'pointerup', letGo );
	window.addEventListener( 'pointercancel', letGo );

	document.addEventListener( 'click', function ( event ) {
		var area = event.target.closest ? event.target.closest( '.custom-pull' ) : null;

		if ( area && area.dataset.customPulled ) {
			event.preventDefault();
			event.stopPropagation();
		}
	}, true );

	// A picture or a link in the row is part of the row, not something to carry
	// off it.
	document.addEventListener( 'dragstart', function ( event ) {
		if ( event.target.closest && event.target.closest( '.custom-pull' ) ) {
			event.preventDefault();
		}
	} );
}() );
