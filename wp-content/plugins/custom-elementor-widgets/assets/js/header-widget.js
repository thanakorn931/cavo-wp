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
