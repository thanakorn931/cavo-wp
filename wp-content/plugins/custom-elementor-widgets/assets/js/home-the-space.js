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

		// On the wide tier the words have a room of their own above the second
		// picture. Where they run past it they are cut at the last line that
		// fits whole, so no line stands half shown and the buttons stay put.
		function cut() {
			var bodies = root.querySelectorAll( '.custom-home-space__body' );

			for ( var i = 0; i < bodies.length; i++ ) {
				var body = bodies[ i ];

				body.classList.remove( 'is-cut' );
				body.style.webkitLineClamp = '';

				if ( window.innerWidth <= 1024 || body.closest( '[hidden]' ) ) {
					continue;
				}

				if ( body.scrollHeight <= body.clientHeight + 1 ) {
					continue;
				}

				// The lines are read off the words themselves, each where it
				// stands: a line of one script is not the height of a line of
				// another, so no single measure counts them.
				var room  = body.clientHeight;
				var top   = body.getBoundingClientRect().top;
				var range = document.createRange();
				var foot  = {};

				range.selectNodeContents( body );

				var boxes = range.getClientRects();

				for ( var k = 0; k < boxes.length; k++ ) {
					if ( boxes[ k ].height <= 0 ) {
						continue;
					}

					var at = Math.round( boxes[ k ].top - top );

					foot[ at ] = Math.max( foot[ at ] || 0, boxes[ k ].bottom - top );
				}

				var whole = 0;

				Object.keys( foot ).forEach( function ( at ) {
					if ( foot[ at ] <= room + 0.5 ) {
						whole++;
					}
				} );

				body.classList.add( 'is-cut' );
				body.style.webkitLineClamp = String( Math.max( 1, whole ) );
			}
		}

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

			cut();
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

		window.addEventListener( 'resize', cut );
		window.addEventListener( 'load', cut );

		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( cut );
		}
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
