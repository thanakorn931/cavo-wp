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

		if ( ! bar || ! spacer ) {
			return;
		}

		spacer.style.height = bar.offsetHeight + 'px';
	}

	function state( root ) {
		// The design begins the moment the page has moved at all.
		root.classList.toggle( 'is-scrolled', window.pageYOffset > 0 );
	}

	function setUp( root ) {
		if ( ! root || root.dataset.wired ) {
			return;
		}

		root.dataset.wired = '1';

		measure( root );

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
