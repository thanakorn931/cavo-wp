/**
 * The header.
 *
 * Two things the file cannot settle on its own: the bar that starts clear
 * becomes the design as soon as the page moves, and the band under a bar that
 * is out of the flow is told what the bar actually came to rather than a
 * number written down for one width.
 */
( function () {
	'use strict';

	function measure( root ) {
		var bar    = root.querySelector( '.custom-header__bar' );
		var spacer = root.querySelector( '.custom-header__spacer' );

		// Open, the bar is the whole screen, which is not what the band
		// under it should come to.
		if ( ! bar || ! spacer || root.classList.contains( 'is-open' ) ) {
			return;
		}

		spacer.style.height = bar.offsetHeight + 'px';
	}

	// A menu with more in it than its room is pulled across by hand. A pull
	// that moved it is not a press on the page it ended over.
	function pull( root ) {
		var nav = root.querySelector( '.custom-header__menu' );

		if ( ! nav ) {
			return;
		}

		var startX = 0;
		var startLeft = 0;
		var moved = false;
		var held = false;

		function room() {
			nav.classList.toggle( 'is-overflowing', ! root.classList.contains( 'is-open' ) && nav.scrollWidth > nav.clientWidth + 1 );
		}

		nav.addEventListener( 'pointerdown', function ( event ) {
			if ( root.classList.contains( 'is-open' ) || nav.scrollWidth <= nav.clientWidth + 1 ) {
				return;
			}

			held      = true;
			moved     = false;
			startX    = event.clientX;
			startLeft = nav.scrollLeft;
		} );

		window.addEventListener( 'pointermove', function ( event ) {
			if ( ! held ) {
				return;
			}

			var dx = event.clientX - startX;

			if ( ! moved && Math.abs( dx ) > 4 ) {
				moved = true;
				nav.classList.add( 'is-dragging' );
			}

			if ( moved ) {
				nav.scrollLeft = startLeft - dx;
				event.preventDefault();
			}
		} );

		function release() {
			if ( ! held ) {
				return;
			}

			held = false;
			nav.classList.remove( 'is-dragging' );
		}

		window.addEventListener( 'pointerup', release );
		window.addEventListener( 'pointercancel', release );

		nav.addEventListener( 'click', function ( event ) {
			if ( moved ) {
				moved = false;
				event.preventDefault();
				event.stopPropagation();
			}
		}, true );

		nav.addEventListener( 'dragstart', function ( event ) {
			event.preventDefault();
		} );

		room();
		window.addEventListener( 'resize', room );
		window.addEventListener( 'load', room );

		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( room );
		}

		root.addEventListener( 'custom-header:toggled', room );
	}

	function state( root ) {
		// The design begins the moment the page has moved at all — and the
		// menu, open, takes the moved bar's colours whatever the page has done.
		root.classList.toggle( 'is-scrolled', window.pageYOffset > 0 || root.classList.contains( 'is-open' ) );
	}

	// The menu is opened and shut by its own mark, shut by the Escape key, by
	// choosing a page, and by the window growing past the tiers it belongs to.
	function menu( root ) {
		var toggle = root.querySelector( '.custom-header__toggle' );

		if ( ! toggle ) {
			return;
		}

		function set( open ) {
			root.classList.toggle( 'is-open', open );
			document.documentElement.classList.toggle( 'custom-header-open', open );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );

			if ( root.classList.contains( 'custom-header--scroll' ) ) {
				state( root );
			}

			measure( root );
			root.dispatchEvent( new Event( 'custom-header:toggled' ) );
		}

		toggle.addEventListener( 'click', function () {
			set( ! root.classList.contains( 'is-open' ) );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Escape' && root.classList.contains( 'is-open' ) ) {
				set( false );
				toggle.focus();
			}
		} );

		root.addEventListener( 'click', function ( event ) {
			var link = event.target.closest ? event.target.closest( '.custom-header__menu a, .custom-header__button' ) : null;

			if ( link && root.classList.contains( 'is-open' ) ) {
				set( false );
			}
		} );

		window.addEventListener( 'resize', function () {
			if ( window.innerWidth > 1024 && root.classList.contains( 'is-open' ) ) {
				set( false );
			}
		} );
	}

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		root.dataset.wired = '1';

		measure( root );
		menu( root );
		pull( root );

		// Measured once and never again, the band keeps whatever the bar came to
		// before the fonts arrived or while the window was some other size — and
		// a band that no longer matches the bar is a strip of nothing under it.
		// Every occasion the bar can change on is watched, not one of them.
		if ( window.ResizeObserver ) {
			new window.ResizeObserver( function () {
				measure( root );
			} ).observe( root.querySelector( '.custom-header__bar' ) );
		}

		window.addEventListener( 'resize', function () {
			measure( root );
		} );

		window.addEventListener( 'load', function () {
			measure( root );
		} );

		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( function () {
				measure( root );
			} );
		}

		if ( ! root.classList.contains( 'custom-header--scroll' ) ) {
			return;
		}

		state( root );

		window.addEventListener(
			'scroll',
			function () {
				state( root );
			},
			{ passive: true }
		);
	}

	function start() {
		var roots = document.querySelectorAll( '.custom-header' );

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
		if ( ! window.elementorFrontend || ! window.elementorFrontend.hooks ) {
			return;
		}

		[ 'header-fixed', 'header-scroll' ].forEach( function ( name ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/' + name + '.default',
				function ( $scope ) {
					setUp( $scope[ 0 ].querySelector( '.custom-header' ) );
				}
			);
		} );
	} );
}() );
